<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator; // 1. Tambahkan baris ini
use App\Models\ProfilSekolah;
use App\Models\KontakPpdb;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;


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
        Paginator::useBootstrapFive(); // 2. Tambahkan baris ini
        // Mengirim semua data siswa ke view tertentu


        // Mengirim data ProfilSekolah ke semua view
        View::share('profilSekolah', ProfilSekolah::first());


        // Mengirim data KontakPpdb ke semua view
            if (Schema::hasTable('kontak_ppdbs')) {
            $kontak = KontakPpdb::first();
            view()->share('kontak', $kontak);
        }

    }
}

