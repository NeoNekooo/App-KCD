<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\ProfilSekolah;
use App\Models\KontakPpdb;
use App\Models\PengajuanSekolah; 
use App\Models\TugasPegawaiKcd; 

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void { }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Pengaturan default Laravel
        Paginator::useBootstrapFive();
        Carbon::setLocale('id');

        // Berbagi data profil sekolah ke seluruh view jika tabel ada
        if (Schema::hasTable('profil_sekolahs')) {
            try { View::share('profilSekolah', ProfilSekolah::first()); } catch (\Exception $e) {}
        }
        if (Schema::hasTable('kontak_ppdbs')) {
            try { View::share('kontakPpdb', KontakPpdb::first()); } catch (\Exception $e) {}
        }

        /**
         * LOGIKA BADGE NOTIFIKASI SIDEBAR (MEJA KERJA PEJABAT)
         * Menghitung jumlah surat yang sedang menunggu tindakan user yang login.
         */
        View::composer('*', function ($view) {
            $notif_data = [];
            $sidebarCount = 0; 
            
            // Default nilai agar tidak error di blade jika tidak login
            $view->with('sidebarCount', '')->with('notif_data', [])->with('badges', []);

            if (Auth::check() && Schema::hasTable('pengajuan_sekolahs')) {
                $user = Auth::user();
                $userRole = strtolower($user->role ?? '');
                
                // 1. IDENTIFIKASI ROLE & JABATAN SECARA RELEVAN
                $isKasubag = ($user->pegawaiKcd && strcasecmp($user->pegawaiKcd->jabatan, 'Kasubag') === 0) || $userRole === 'kasubag';
                $isKepala = $userRole === 'kepala';

                // Tentukan daftar status "Meja Kerja" masing-masing
                if ($isKasubag) {
                    $targetStatus = ['Verifikasi Kasubag'];
                } elseif ($isKepala) {
                    $targetStatus = ['Verifikasi Kepala'];
                } else {
                    $targetStatus = ['Proses', 'Verifikasi Berkas'];
                }

                // 2. LOGIKA FILTER KATEGORI UNTUK STAF
                $kategoriTugas = null;
                if (!$isKasubag && !$isKepala && $user->pegawai_kcd_id) {
                    $tugas = TugasPegawaiKcd::where('pegawai_kcd_id', $user->pegawai_kcd_id)
                                            ->where('is_active', 1)
                                            ->first();
                    $kategoriTugas = $tugas ? $tugas->kategori_layanan : null;
                }

                // 3. QUERY DASAR BERDASARKAN MEJA KERJA
                $baseQuery = PengajuanSekolah::whereIn('status', $targetStatus);

                // Jika Staf memiliki spesialisasi tugas, batasi query hanya pada kategori tugasnya
                if ($kategoriTugas && is_array($kategoriTugas)) {
                    $baseQuery->where(function($q) use ($kategoriTugas) {
                        foreach ($kategoriTugas as $kategori) {
                            $q->orWhere('kategori', 'LIKE', '%' . $kategori . '%')
                              ->orWhere('kategori', 'LIKE', '%' . str_replace('-', ' ', $kategori) . '%');
                        }
                    });
                }

                // 4. HITUNG PER SUB-MENU (DENGAN CLONE QUERY AGAR TIDAK SALING GANGGU)
                $kp = (clone $baseQuery)->where('kategori', 'LIKE', '%pangkat%')->count();
                $kgb = (clone $baseQuery)->where('kategori', 'LIKE', '%kgb%')->count();
                $mutasi = (clone $baseQuery)->where('kategori', 'LIKE', '%mutasi%')->count();
                $relokasi = (clone $baseQuery)->where('kategori', 'LIKE', '%relokasi%')->count();
                $satya = (clone $baseQuery)->where('kategori', 'LIKE', '%satya%')->count();
                $hukdis = (clone $baseQuery)->where('kategori', 'LIKE', '%hukuman%')->count();

                // Masukkan ke array notif_data (Ubah 0 jadi string kosong agar badge tidak muncul jika kosong)
                $notif_data['notif_kp'] = $kp > 0 ? $kp : '';
                $notif_data['notif_kgb'] = $kgb > 0 ? $kgb : '';
                $notif_data['notif_mutasi'] = $mutasi > 0 ? $mutasi : '';
                $notif_data['notif_relokasi'] = $relokasi > 0 ? $relokasi : '';
                $notif_data['notif_satya'] = $satya > 0 ? $satya : '';
                $notif_data['notif_hukdis'] = $hukdis > 0 ? $hukdis : '';

                // 5. HITUNG TOTAL UNTUK MENU INDUK (LAYANAN GTK)
                // Ini yang akan dipasang di parent menu agar tetap terlihat saat dropdown tertutup
                $totalGtk = (clone $baseQuery)->count();
                $notif_data['total_layanan_gtk'] = $totalGtk > 0 ? $totalGtk : '';
                
                // Variabel cadangan untuk sidebar utama
                $sidebarCount = $totalGtk;

                // Kirim variabel ke view
                $view->with('sidebarCount', $sidebarCount > 0 ? $sidebarCount : '')
                     ->with('notif_data', $notif_data)
                     ->with('badges', $notif_data);
            }
        });
    }
}