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

        // ==================================================================
        // A. CEK DATABASE (STRICT MODE)
        // ==================================================================
        // Berlaku untuk SEMUA (Admin, Kasubag, Kepala, Staff/Pegawai)
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
        // B. FILTER TAMBAHAN: KHUSUS STAFF / PEGAWAI (Verifikator)
        // ==================================================================
        // KASUBAG tidak masuk ke sini lagi karena rolenya sudah mandiri
        if ($role === 'staff' || $role === 'pegawai') { 
            
            $layananSlugs = [
                'layanan-kp', 'layanan-kgb', 'layanan-mutasi', 
                'layanan-relokasi', 'layanan-satya', 'layanan-hukdis', 'verifikasi-surat'
            ];

            if (in_array($slug, $layananSlugs)) {
                
                $tugas = TugasPegawaiKcd::where('pegawai_kcd_id', $user->pegawai_kcd_id)
                                        ->where('is_active', 1)
                                        ->first();

                $mapKategoriToSlug = [
                    'kenaikan-pangkat' => 'layanan-kp',
                    'kgb'              => 'layanan-kgb',
                    'mutasi'           => 'layanan-mutasi',
                    'relokasi'         => 'layanan-relokasi',
                    'satya-lencana'    => 'layanan-satya',
                    'hukuman-disiplin' => 'layanan-hukdis',
                    'verifikasi-surat' => 'verifikasi-surat',
                ];

                if (!$tugas) {
                    abort(403, "AKSES DITOLAK. ANDA TIDAK MEMILIKI TUGAS LAYANAN AKTIF.");
                }

                $isUmum = in_array(strtolower($tugas->kategori_layanan), ['umum', 'all']);
                
                if (!$isUmum) {
                    $slugTugas = $mapKategoriToSlug[$tugas->kategori_layanan] ?? '';
                    if ($slug !== $slugTugas) {
                        abort(403, "AKSES DITOLAK. TUGAS ANDA (" . strtoupper($tugas->kategori_layanan) . ") TIDAK SESUAI.");
                    }
                }
            }
        }

        return $next($request);
    }
}