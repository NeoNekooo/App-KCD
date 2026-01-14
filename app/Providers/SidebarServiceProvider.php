<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth; // Tambahkan ini

class SidebarServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer('layouts.partials.sidebar', function ($view) {

            $user = Auth::user();

            // FIX: Prioritaskan Session, tapi kalau kosong ambil dari Auth User
            // Ini biar menu tetap muncul meski session belum ke-set
            $role    = session('role') ?? $user?->role; 
            $subRole = session('sub_role');

            // Baca Config (Sekarang isinya sudah di-inject oleh Middleware)
            $menus      = config('menu_access.sidebar_menu');
            $roleMap    = config('menu_access.role_map');
            $subRoleMap = config('menu_access.sub_role_map');

            // Cek Admin (Case Insensitive biar aman)
            $hasFullAccess = $role && strcasecmp($role, 'Admin') === 0;

            $adminExcluded = [
                'profil-guru',
                'pelanggaran-guru',
            ];

            $underConstructionRoutes = config('menu_access.under_construction', []);

            $view->with(compact(
                'menus',
                'role',
                'subRole',
                'roleMap',
                'subRoleMap',
                'hasFullAccess',
                'adminExcluded',
                'underConstructionRoutes'
            ));
        });
    }
}