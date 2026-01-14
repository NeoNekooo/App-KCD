<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TugasPegawaiKcd; // <--- JANGAN LUPA IMPORT INI

class CheckMenuAccess
{
    public function handle(Request $request, Closure $next, string $slug)
    {
        $user = Auth::user();
        
        // 1. Pastikan user login
        if (!$user) {
            return redirect('/');
        }

        // ==================================================================
        // A. JALUR VIP ADMIN (BYPASS SEMUA)
        // ==================================================================
        if (strtolower($user->role) === 'admin') {
            return $next($request);
        }

        // ==================================================================
        // B. JALUR KHUSUS PEGAWAI (CEK TUGAS DI DB) - [FIXED CASE INSENSITIVE]
        // ==================================================================
        // FIX: Pakai strcasecmp biar 'PEGAWAI' == 'Pegawai'
        if (strcasecmp($user->role, 'Pegawai') === 0 && $user->pegawai_kcd_id) {
            
            // 1. Cek Tugas Aktif
            $penugasan = TugasPegawaiKcd::where('pegawai_kcd_id', $user->pegawai_kcd_id)
                                        ->where('is_active', 1)
                                        ->first();

            // 2. Mapping Slug (Sama kayak di AppServiceProvider)
            $mapKategoriToSlug = [
                'kenaikan-pangkat' => 'layanan-kp',
                'kgb'              => 'layanan-kgb',
                'mutasi'           => 'layanan-mutasi',
                'relokasi'         => 'layanan-relokasi',
                'satya-lencana'    => 'layanan-satya',
                'hukuman-disiplin' => 'layanan-hukdis',
                'verifikasi-surat' => 'verifikasi-surat',
            ];

            // 3. Logic Pengecekan
            if ($penugasan && isset($mapKategoriToSlug[$penugasan->kategori_layanan])) {
                $allowedSlug = $mapKategoriToSlug[$penugasan->kategori_layanan];

                // Izin diberikan jika:
                // a. Slug yang diminta adalah Induk Layanan ('layanan-gtk')
                // b. Slug yang diminta adalah Tugas Spesifik dia (misal 'layanan-mutasi')
                if ($slug === 'layanan-gtk' || $slug === $allowedSlug) {
                    return $next($request); // LOLOS!
                }
            }
            
            // Kalau Pegawai akses menu umum (Dashboard/Profil), lanjut ke pengecekan standar di bawah
        }

        // ==================================================================
        // C. JALUR UMUM (OPERATOR, SEKOLAH, & PEGAWAI MENU DASAR)
        // ==================================================================
        $role    = $user->role; 
        $subRole = session('sub_role');
        $roleMap = config('sidebar_menu.role_map', []);
        $subRoleMap = config('sidebar_menu.sub_role_map', []);
        
        // PENTING: Cek Helper canAccessMenu
        if (!canAccessMenu(
            $slug,      // 1. Slug
            null,       // 2. Parent Slug
            $role,      // 3. Role
            $subRole,   // 4. Sub Role
            $roleMap,   // 5. Config Role
            $subRoleMap // 6. Config Sub Role
        )) {
            abort(403, "AKSES DITOLAK. ROLE: " . strtoupper($role) . " TIDAK PUNYA IZIN KE MENU: " . strtoupper($slug));
        }

        return $next($request);
    }
}