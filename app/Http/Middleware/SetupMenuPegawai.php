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

            if ($tugas && is_array($tugas->kategori_layanan)) {
                // 1. Cek Mode "DEWA"
                $masterKeys = ['umum', 'all', 'semua-layanan', 'koordinator'];
                $isUmum = collect($tugas->kategori_layanan)->contains(fn($k) => in_array(strtolower($k), $masterKeys));
                
                if ($isUmum) {
                    return $next($request); 
                }

                // 2. Mode SPESIFIK
                $map = [
                    'mutasi'           => 'layanan-mutasi',
                    'kenaikan-pangkat' => 'layanan-kp',
                    'kgb'              => 'layanan-kgb',
                    'relokasi'         => 'layanan-relokasi',
                    'satya-lencana'    => 'layanan-satya',
                    'hukuman-disiplin' => 'layanan-hukdis',
                    'verifikasi-surat' => 'verifikasi-surat',
                    'peserta-didik'    => 'layanan-peserta-didik',
                ];

                $slugsTarget = collect($tugas->kategori_layanan)->map(fn($k) => $map[strtolower($k)] ?? null)->filter()->all();
                
                if (!empty($slugsTarget)) {
                    $currentMenu = Config::get('menu_access.role_map.Pegawai', []);
                    $blacklistSlugs = array_values($map); // Daftar semua layanan

                    // HAPUS semua layanan dari menu dia
                    $cleanMenu = array_diff($currentMenu, $blacklistSlugs);

                    // Masukkan Induk & Anak Spesifik
                    if (!in_array('layanan-gtk', $cleanMenu)) $cleanMenu[] = 'layanan-gtk';
                    foreach ($slugsTarget as $st) {
                        if (!in_array($st, $cleanMenu)) $cleanMenu[] = $st;
                    }

                    $finalMenu = array_values($cleanMenu);
                    Config::set('menu_access.role_map.Pegawai', $finalMenu);
                    Config::set('menu_access.role_map.' . $user->role, $finalMenu);
                }
            }
        }

        return $next($request);
    }
}