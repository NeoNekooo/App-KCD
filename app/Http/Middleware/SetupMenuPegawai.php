<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use App\Models\TugasPegawaiKcd;

class SetupMenuPegawai
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user && strcasecmp($user->role, 'Pegawai') === 0 && $user->pegawai_kcd_id) {
            
            $tugas = TugasPegawaiKcd::where('pegawai_kcd_id', $user->pegawai_kcd_id)
                                    ->where('is_active', 1)
                                    ->first();

            if ($tugas) {
                // 1. Cek Mode "DEWA" (Umum/Semua Layanan)
                // Kalau kategorinya 'umum', 'all', atau 'admin-layanan', dia berhak lihat semua.
                $masterKeys = ['umum', 'all', 'semua-layanan', 'koordinator'];
                
                if (in_array(strtolower($tugas->kategori_layanan), $masterKeys)) {
                    // JANGAN FILTER APAPUN. Biarkan dia melihat semua menu default dari Config.
                    // Langsung return next request.
                    return $next($request); 
                }

                // 2. Mode SPESIFIK (Logic Lama Kita)
                $map = [
                    'mutasi'           => 'layanan-mutasi',
                    'kenaikan-pangkat' => 'layanan-kp',
                    'kgb'              => 'layanan-kgb',
                    'relokasi'         => 'layanan-relokasi',
                    'satya-lencana'    => 'layanan-satya',
                    'hukuman-disiplin' => 'layanan-hukdis',
                    'verifikasi-surat' => 'verifikasi-surat',
                ];

                if (isset($map[$tugas->kategori_layanan])) {
                    $slugTarget = $map[$tugas->kategori_layanan];
                    $currentMenu = Config::get('menu_access.role_map.Pegawai', []);
                    $blacklistSlugs = array_values($map); // Daftar semua layanan

                    // HAPUS semua layanan dari menu dia
                    $cleanMenu = array_diff($currentMenu, $blacklistSlugs);

                    // Masukkan Induk & Anak Spesifik
                    if (!in_array('layanan-gtk', $cleanMenu)) $cleanMenu[] = 'layanan-gtk';
                    if (!in_array($slugTarget, $cleanMenu)) $cleanMenu[] = $slugTarget;

                    $finalMenu = array_values($cleanMenu);
                    Config::set('menu_access.role_map.Pegawai', $finalMenu);
                    Config::set('menu_access.role_map.' . $user->role, $finalMenu);
                }
            }
        }

        return $next($request);
    }
}