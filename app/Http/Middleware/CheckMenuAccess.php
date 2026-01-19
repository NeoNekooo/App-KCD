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
        
        // 1. Pastikan user login
        if (!$user) {
            return redirect('/');
        }

        // Normalisasi Role (huruf kecil semua biar aman)
        $role = strtolower(trim($user->role));

        // ==================================================================
        // A. CEK DATABASE (STRICT MODE) - BERLAKU UNTUK SEMUA ROLE
        // ==================================================================
        // Tidak ada bypass untuk Admin/Kasubag. Semua wajib terdaftar di DB.
        
        // 1. Cari ID menu berdasarkan slug
        $menu = DB::table('menus')->where('slug', $slug)->first();

        // Jika menu ada di database, kita WAJIB cek izinnya
        if ($menu) {
            // Cek apakah Role ini punya izin akses ke Menu ini di tabel 'menu_accesses'
            $hasAccess = DB::table('menu_accesses')
                            ->where(DB::raw('LOWER(role_name)'), $role) 
                            ->where('menu_id', $menu->id)
                            ->exists();

            // Jika tidak ada di tabel akses, TOLAK (Siapapun dia, mau Admin/Kasubag tetep tolak)
            if (!$hasAccess) {
                 abort(403, "AKSES DITOLAK. ROLE ANDA (" . strtoupper($role) . ") BELUM DIBERI IZIN KE MENU INI VIA DATABASE.");
            }
        }
        // Catatan: Jika $menu tidak ditemukan di DB (misal route internal), 
        // secara default lolos (return $next) atau bisa kamu abort(404) kalau mau super ketat.

        // ==================================================================
        // B. FILTER TAMBAHAN: KHUSUS STAFF / PEGAWAI (Verifikator)
        // ==================================================================
        // Setelah lolos cek database di atas, khusus Staff kita cek lagi Tugas-nya.
        // Karena Staff mungkin punya akses menu 'Layanan', tapi cuma boleh kategori tertentu.
        
        if ($role === 'staff' || $role === 'pegawai') { 
            
            // Daftar slug layanan yang butuh pengecekan tugas spesifik
            $layananSlugs = [
                'layanan-kp', 'layanan-kgb', 'layanan-mutasi', 
                'layanan-relokasi', 'layanan-satya', 'layanan-hukdis', 'verifikasi-surat'
            ];

            // Jika menu yang dibuka adalah salah satu layanan sensitif
            if (in_array($slug, $layananSlugs)) {
                
                // Ambil tugas aktif pegawai
                $tugas = TugasPegawaiKcd::where('pegawai_kcd_id', $user->pegawai_kcd_id)
                                        ->where('is_active', 1)
                                        ->first();

                // Mapping Kategori di DB Tugas -> Slug Menu
                $mapKategoriToSlug = [
                    'kenaikan-pangkat' => 'layanan-kp',
                    'kgb'              => 'layanan-kgb',
                    'mutasi'           => 'layanan-mutasi',
                    'relokasi'         => 'layanan-relokasi',
                    'satya-lencana'    => 'layanan-satya',
                    'hukuman-disiplin' => 'layanan-hukdis',
                    'verifikasi-surat' => 'verifikasi-surat',
                ];

                // Jika tidak punya tugas sama sekali -> Tolak
                if (!$tugas) {
                    abort(403, "AKSES DITOLAK. ANDA TIDAK MEMILIKI TUGAS LAYANAN AKTIF.");
                }

                // Jika tugasnya 'Umum' atau 'All', boleh akses semua -> Lolos
                $isUmum = in_array(strtolower($tugas->kategori_layanan), ['umum', 'all']);
                
                if (!$isUmum) {
                    // Cek apakah slug menu cocok dengan tugasnya
                    $slugTugas = $mapKategoriToSlug[$tugas->kategori_layanan] ?? '';
                    
                    if ($slug !== $slugTugas) {
                        abort(403, "AKSES DITOLAK. TUGAS ANDA (" . strtoupper($tugas->kategori_layanan) . ") TIDAK SESUAI DENGAN MENU INI.");
                    }
                }
            }
        }

        return $next($request);
    }
}