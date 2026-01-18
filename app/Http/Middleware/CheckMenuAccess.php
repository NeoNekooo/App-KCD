<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TugasPegawaiKcd;

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
        // B. JALUR KEPALA KCD (Terbatas ke GTK saja dulu)
        // ==================================================================
        if (strtolower($user->role) === 'kepala') {
            // Sementara hanya diberikan akses ke dashboard layanan GTK
            if ($slug === 'layanan-gtk') {
                return $next($request);
            }
        }

        // ==================================================================
        // B. JALUR KHUSUS PEGAWAI (BIASA & KASUBAG)
        // ==================================================================
        if (strcasecmp($user->role, 'Pegawai') === 0 && $user->pegawai_kcd_id) {
            
            // --- LOGIKA KHUSUS JABATAN KASUBAG ---
            // Mengambil data pegawai melalui relasi yang didefinisikan di Canvas (User Model)
            $pegawai = $user->pegawaiKcd; 

            if ($pegawai && isset($pegawai->jabatan) && strcasecmp(trim($pegawai->jabatan), 'Kasubag') === 0) {
                // Daftar menu yang diizinkan untuk Kasubag (Bypass pengecekan tugas spesifik)
                $allowedKasubag = [
                    'dashboard', 
                    'profil-saya', 
                    'layanan-gtk', 
                    'layanan-kp', 
                    'layanan-kgb', 
                    'layanan-mutasi', 
                    'layanan-relokasi', 
                    'layanan-satya', 
                    'layanan-hukdis', 
                    'verifikasi-surat'
                ];
                
                if (in_array($slug, $allowedKasubag)) {
                    return $next($request); // Lolos sebagai Kasubag
                }
            }

            // --- LOGIKA PEGAWAI BIASA (CEK TABEL TUGAS) ---
            $penugasan = TugasPegawaiKcd::where('pegawai_kcd_id', $user->pegawai_kcd_id)
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

            if ($penugasan && isset($mapKategoriToSlug[$penugasan->kategori_layanan])) {
                $allowedSlug = $mapKategoriToSlug[$penugasan->kategori_layanan];

                // Akses diberikan jika membuka menu induk atau sub-layanan spesifiknya
                if ($slug === 'layanan-gtk' || $slug === $allowedSlug) {
                    return $next($request); 
                }
            }
        }


        // ==================================================================
        // C. JALUR UMUM (OPERATOR, SEKOLAH, & PEGAWAI MENU DASAR)
        // ==================================================================
        $role    = $user->role; 
        $subRole = session('sub_role');
        $roleMap = config('sidebar_menu.role_map', []);
        $subRoleMap = config('sidebar_menu.sub_role_map', []);
        
        if (!canAccessMenu(
            $slug,
            null,
            $role,
            $subRole,
            $roleMap,
            $subRoleMap
        )) {
            abort(403, "AKSES DITOLAK. ROLE: " . strtoupper($role) . " TIDAK PUNYA IZIN KE MENU: " . strtoupper($slug));
        }

        return $next($request);
    }
}