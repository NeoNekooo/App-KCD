<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use App\Models\ProfilSekolah;
use App\Models\KontakPpdb;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // Mengatur lokal default untuk Carbon (penanganan tanggal & waktu) ke Bahasa Indonesia
        Carbon::setLocale('id');

        // Mengirim data ProfilSekolah ke semua view
        if (Schema::hasTable('profil_sekolahs')) {
            $profilSekolah = ProfilSekolah::first();
            view()->share('profilSekolah', $profilSekolah);
        }

        // Mengirim data KontakPpdb ke semua view
        if (Schema::hasTable('kontak_ppdbs')) {
            $kontakPpdb = KontakPpdb::first();
            view()->share('kontakPpdb', $kontakPpdb);
        }
    }
}
