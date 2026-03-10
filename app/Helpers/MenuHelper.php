<?php

use Illuminate\Support\Facades\Route;

if (!function_exists('checkRouteActive')) {
    /**
     * Helper function to check if a route is active (considering parameters)
     *
     * @param string|null $route
     * @param array $params
     * @return bool
     */
    function checkRouteActive($route, $params)
    {
        if (!$route) {
            return false;
        }

        /* 
         * 1. Deteksi Active Melalui Pattern Route Prefix 
         * Jika route saat ini adalah `admin.gtk.guru.show`, maka dia harus menyalakan sidebar yang rutenya tersetting `admin.gtk.guru.index`
         */
        $currentRoute = request()->route() ? request()->route()->getName() : '';
        $isRouteMatch = request()->routeIs($route);
        
        // Coba cocokkan jika dia berada di dalam prefix yang sama (contoh admin.gtk.guru.show cocok dengan admin.gtk.guru.index)
        if (!$isRouteMatch && str_ends_with($route, '.index')) {
            $baseRoutePrefix = substr($route, 0, -5); // Hapus kata "index"
            if (str_starts_with($currentRoute, $baseRoutePrefix)) {
                $isRouteMatch = true;
            }
        }

        if (!$isRouteMatch) {
            return false; // Jika secara pola string rute juga tak cocok, baru return false
        }

        /*
         * 2. Toleransi Query Params (Search / Filter)
         * Jika menu memiliki params spesifik di DB (contoh menu jenis="guru" -> `?jenis=guru`), pastikan dicocokkan.
         * TAPI jika menu TIDAK memiliki params spesifik (seperti menu biasa), biarkan dia tetap Active walau sedang melakukan Pencarian (?search=x)
         */
        if (!empty($params) && is_array($params)) {
            foreach ($params as $key => $value) {
                // Jika route punya required param di DB, dan URL saat ini beda nilai query-nya, salahkan
                if (request()->query($key) != $value) {
                     return false;
                }
            }
        }
        
        // Berhasil melewati validasi route (dan tak terbentur param ketat), berarti Menu Active!
        return true;
    }
}

if (!function_exists('checkMenuStatusRecursive')) {
    /**
     * Check for active state recursively for current menu item or any of its descendants.
     *
     * @param \App\Models\Menu $menu
     * @param bool $currentRouteIsActive
     * @return array ['isActive' => bool, 'isOpen' => bool]
     */
    function checkMenuStatusRecursive($menu, $currentRouteIsActive = false)
    {
        if ($currentRouteIsActive) {
            return ['isActive' => true, 'isOpen' => true];
        }
        
        $hasActiveChild = false;
        if ($menu->childrenRecursive->isNotEmpty()) {
            foreach ($menu->childrenRecursive as $child) {
                $childRouteActive = checkRouteActive($child->route, $child->params);
                $status = checkMenuStatusRecursive($child, $childRouteActive);
                if ($status['isActive'] || $status['isOpen']) {
                    $hasActiveChild = true;
                    break;
                }
            }
        }
        return ['isActive' => $currentRouteIsActive, 'isOpen' => $hasActiveChild];
    }
}
