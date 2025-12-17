<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class SidebarServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer('layouts.partials.sidebar', function ($view) {

            $user = auth()->user();

            $role     = session('role');
            $subRole  = session('sub_role');

            $menus = config('menu_access.sidebar_menu');
            $roleMap = config('menu_access.role_map');
            $subRoleMap = config('menu_access.sub_role_map');

            $hasFullAccess = $role === 'Admin';

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
