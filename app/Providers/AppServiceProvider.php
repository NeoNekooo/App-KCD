<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

// --- IMPORT MODEL ---
use App\Models\ProfilSekolah;
use App\Models\KontakPpdb;
use App\Models\PengajuanSekolah; 

class AppServiceProvider extends ServiceProvider
{
    public function register(): void { }

    public function boot(): void
    {
        Paginator::useBootstrapFive();
        Carbon::setLocale('id');

        // 1. GLOBAL VARIABLE: PROFIL SEKOLAH
        if (Schema::hasTable('profil_sekolahs')) {
            try {
                view()->share('profilSekolah', ProfilSekolah::first());
            } catch (\Exception $e) {}
        }

        // 2. GLOBAL VARIABLE: KONTAK PPDB
        if (Schema::hasTable('kontak_ppdbs')) {
            try {
                view()->share('kontakPpdb', KontakPpdb::first());
            } catch (\Exception $e) {}
        }

        /* |--------------------------------------------------------------------------
        | 3. GLOBAL BADGES (NOTIFIKASI DINAMIS BERDASARKAN ROLE)
        |--------------------------------------------------------------------------
        */
        View::composer('*', function ($view) {
            
            $notif_data = [];

            // Pastikan User Login & Tabel Ada
            if (Auth::check() && Schema::hasTable('pengajuan_sekolahs')) {
                
                $user = Auth::user();
                $targetStatus = [];

                // --- A. TENTUKAN STATUS YANG DIHITUNG BERDASARKAN ROLE ---
                if ($user->role == 'kasubag') {
                    // Kasubag hanya dikasih notif yang statusnya 'Verifikasi Kasubag'
                    $targetStatus = ['Verifikasi Kasubag'];
                } 
                elseif ($user->role == 'kepala') {
                    // Kepala hanya dikasih notif yang statusnya 'Verifikasi Kepala'
                    $targetStatus = ['Verifikasi Kepala'];
                } 
                else {
                    // Admin melihat 'Proses' (Tiket Baru) dan 'Verifikasi Berkas' (Sedang dicek admin)
                    $targetStatus = ['Proses', 'Verifikasi Berkas'];
                }

                // --- B. HITUNG DATA PER KATEGORI ---
                // Kita gunakan array map untuk menghitung sekaligus biar rapi
                
                $countKP = PengajuanSekolah::where('kategori', 'LIKE', '%kenaikan pangkat%')
                            ->whereIn('status', $targetStatus)->count();

                $countKGB = PengajuanSekolah::where('kategori', 'LIKE', '%kgb%')
                            ->whereIn('status', $targetStatus)->count();

                $countMutasi = PengajuanSekolah::where('kategori', 'LIKE', '%mutasi%')
                            ->whereIn('status', $targetStatus)->count();

                $countRelokasi = PengajuanSekolah::where('kategori', 'LIKE', '%relokasi%')
                            ->whereIn('status', $targetStatus)->count();
                
                $countSatya = PengajuanSekolah::where('kategori', 'LIKE', '%satya%')
                            ->whereIn('status', $targetStatus)->count();

                $countHukdis = PengajuanSekolah::where('kategori', 'LIKE', '%hukuman%')
                            ->whereIn('status', $targetStatus)->count();

                // --- C. HITUNG TOTAL PARENT ---
                $totalLayanan = $countKP + $countKGB + $countMutasi + $countRelokasi + $countSatya + $countHukdis;

                // --- D. PACKING DATA (Kunci Array harus sama dengan 'badge_key' di config/menu.php) ---
                $notif_data = [
                    'total_layanan_gtk' => $totalLayanan > 0 ? $totalLayanan : '', // Kalau 0 string kosong biar ga muncul badge
                    'notif_kp'       => $countKP > 0 ? $countKP : '',
                    'notif_kgb'      => $countKGB > 0 ? $countKGB : '',
                    'notif_mutasi'   => $countMutasi > 0 ? $countMutasi : '',
                    'notif_relokasi' => $countRelokasi > 0 ? $countRelokasi : '',
                    'notif_satya'    => $countSatya > 0 ? $countSatya : '',
                    'notif_hukdis'   => $countHukdis > 0 ? $countHukdis : '',
                ];
            }

            // Share ke semua View sebagai variabel $notif_data (atau $badges di sidebar helper)
            // Agar kompatibel dengan Helper Sidebar sebelumnya, kita kirim sbg $badges juga.
            $view->with('notif_data', $notif_data);
            $view->with('badges', $notif_data); // Duplicate variable biar aman sama helper renderSidebarMenu
        });
    }
}