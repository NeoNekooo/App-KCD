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
        | 3. GLOBAL BADGES (NOTIF: PROSES & VERIFIKASI BERKAS)
        |--------------------------------------------------------------------------
        */
        View::composer('*', function ($view) {
            
            $notif_data = [];

            if (Auth::check()) {
                
                // Status yang dianggap butuh tindakan Admin KCD
                $statusPerluTindakan = ['Proses', 'Verifikasi Berkas']; 

                // Query Hitung per Kategori
                $countKP = PengajuanSekolah::where('kategori', 'LIKE', '%kenaikan pangkat%')
                            ->whereIn('status', $statusPerluTindakan)->count();

                $countKGB = PengajuanSekolah::where('kategori', 'LIKE', '%kgb%')
                            ->whereIn('status', $statusPerluTindakan)->count();

                $countMutasi = PengajuanSekolah::where('kategori', 'LIKE', '%mutasi%')
                            ->whereIn('status', $statusPerluTindakan)->count();

                $countRelokasi = PengajuanSekolah::where('kategori', 'LIKE', '%relokasi%')
                            ->whereIn('status', $statusPerluTindakan)->count();
                
                $countSatya = PengajuanSekolah::where('kategori', 'LIKE', '%satya%')
                            ->whereIn('status', $statusPerluTindakan)->count();

                $countHukdis = PengajuanSekolah::where('kategori', 'LIKE', '%hukuman%')
                            ->whereIn('status', $statusPerluTindakan)->count();

                // Hitung Total untuk Menu Parent 'Layanan GTK'
                $totalLayanan = $countKP + $countKGB + $countMutasi + $countRelokasi + $countSatya + $countHukdis;

                $notif_data = [
                    'total_layanan_gtk' => $totalLayanan, 
                    'notif_kp'       => $countKP,
                    'notif_kgb'      => $countKGB,
                    'notif_mutasi'   => $countMutasi,
                    'notif_relokasi' => $countRelokasi,
                    'notif_satya'    => $countSatya,
                    'notif_hukdis'   => $countHukdis,
                ];
            }

            $view->with('notif_data', $notif_data);
        });
    }
}