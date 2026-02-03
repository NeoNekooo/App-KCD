<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\TugasPegawaiKcd;

class CheckMenuAccess
{
    public function handle(Request $request, Closure $next, string $slug)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect('/');
        }

        // Normalisasi Role
        $role = strtolower(trim($user->role));
        
        // PRIORITAS 1: ADMIN/ADMINISTRATOR SELALU PUNYA AKSES
        if (in_array($role, ['admin', 'administrator'])) {
            return $next($request);
        }

        // ==================================================================
        // A. CEK DATABASE (STRICT MODE)
        // ==================================================================
        $menu = DB::table('menus')->where('slug', $slug)->first();

        if ($menu) {
            $hasAccess = DB::table('menu_accesses')
                            ->where(DB::raw('LOWER(role_name)'), $role) 
                            ->where('menu_id', $menu->id)
                            ->exists();

            if (!$hasAccess) {
                abort(403, "AKSES DITOLAK. ROLE ANDA (" . strtoupper($role) . ") BELUM DIBERI IZIN KE MENU INI.");
            }
        }

        // ==================================================================
        // B. FILTER TAMBAHAN: HANYA BERLAKU UNTUK ROLE PEGAWAI (atau role spesifik lainnya)
        if (!in_array($role, ['admin', 'administrator', 'kepala', 'kasubag'])) {
            
            // Mapping dari kategori di DB ke slug menu (dipindahkan ke sini agar bisa dipakai lebih awal)
            $mapKategoriToSlug = [
                'kenaikan-pangkat' => 'layanan-kp',
                'kgb'              => 'layanan-kgb',
                'mutasi'           => 'layanan-mutasi',
                'relokasi'         => 'layanan-relokasi',
                'satya-lencana'    => 'layanan-satya',
                'hukuman-disiplin' => 'layanan-hukdis',
                'verifikasi-surat' => 'verifikasi-surat', // Ini mungkin direct slug atau juga di VerifikasiController
            ];

            // List of slugs that require task-based check, including 'layanan-gtk' parent
            $layananSlugs = array_values($mapKategoriToSlug); // All specific service slugs
            $layananSlugs[] = 'layanan-gtk'; // The parent verification route slug
            
            // Determine the actual slug to check against user's allowed tasks
            $slugToCheck = $slug;
            
            // Special handling for the main 'layanan-gtk' route if it has a 'kategori' query param
            if ($slug === 'layanan-gtk' && $request->has('kategori')) {
                $requestedKategori = $request->query('kategori'); // e.g., 'hukuman-disiplin'
                if (isset($mapKategoriToSlug[$requestedKategori])) {
                    $slugToCheck = $mapKategoriToSlug[$requestedKategori]; // e.g., 'layanan-hukdis'
                } else {
                    abort(403, "AKSES DITOLAK. KATEGORI LAYANAN TIDAK VALID.");
                }
            } elseif ($slug === 'layanan-gtk' && !$request->has('kategori')) {
                // If accessing general '/admin/verifikasi' without category param
                // This means checking for general access to the Verifikasi page.
                // We'll represent this internally with a special slug.
                $slugToCheck = 'layanan-gtk-general-access'; 
            }

            // Only proceed with task-based check if $slugToCheck is one of the service slugs or the special general-access slug
            if (in_array($slugToCheck, $layananSlugs) || $slugToCheck === 'layanan-gtk-general-access') {
                
                // Ambil penugasan aktif milik user
                $tugas = TugasPegawaiKcd::where('pegawai_kcd_id', $user->pegawai_kcd_id)
                                          ->where('is_active', 1)
                                          ->first();

                if (!$tugas) {
                    abort(403, "AKSES DITOLAK. ANDA TIDAK MEMILIKI TUGAS LAYANAN AKTIF.");
                }

                $kategoriUser = $tugas->kategori_layanan; // Ini akan menjadi array karena casting di model

                // Cek jika user punya tugas 'umum' atau 'all' (general access)
                $hasGeneralTaskAccess = collect($kategoriUser)->contains(fn($k) => in_array(strtolower($k), ['umum', 'all']));

                // Build the list of slugs allowed for the user based on their assigned tasks
                $allowedSlugsFromTasks = collect($kategoriUser)->map(function ($kategori) use ($mapKategoriToSlug) {
                    return $mapKategoriToSlug[$kategori] ?? null;
                })->filter()->all();
                
                // If user has general task access AND the current request is for general access verification page, allow
                if ($hasGeneralTaskAccess && $slugToCheck === 'layanan-gtk-general-access') {
                    return $next($request); 
                } 
                // If no general task access and trying to access general page, deny
                elseif (!$hasGeneralTaskAccess && $slugToCheck === 'layanan-gtk-general-access') {
                    abort(403, "AKSES DITOLAK. ANDA TIDAK MEMILIKI AKSES UMUM KE LAYANAN VERIFIKASI.");
                } 
                // If not general access check, and specific slug not in list, deny
                elseif (!in_array($slugToCheck, $allowedSlugsFromTasks)) {
                    $namaTugasUser = implode(', ', collect($kategoriUser)->map(fn($t) => strtoupper($t))->toArray());
                    abort(403, "AKSES DITOLAK. TUGAS ANDA (" . $namaTugasUser . ") TIDAK SESUAI DENGAN MENU INI.");
                }
            }
        }

        return $next($request);
    }
}