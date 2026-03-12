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
                $remainder = substr($currentRoute, strlen($baseRoutePrefix));
                // DAFTAR PUTIH: Hanya rute aksi resource standar yang boleh mengaktifkan menu INDEX
                // Kita blokir rute seperti 'rekapitulasi' jika rute tersebut memiliki menu sendiri.
                $standardActions = ['show', 'edit', 'create', 'duplicate', 'store', 'update', 'rekap']; // 'rekap' ditambahkan jika perlu, tapi 'rekapitulasi' biasanya menu sendiri
                
                if (strpos($remainder, '.') === false) {
                    $cleanRemainder = trim($remainder, '.');
                    // Jika remainder kosong (identik) atau merupakan aksi standar
                    if ($remainder === '' || in_array($cleanRemainder, $standardActions)) {
                        $isRouteMatch = true;
                    }
                }
            }
        }

        if (!$isRouteMatch) {
            return false; // Jika secara pola string rute juga tak cocok, baru return false
        }

        /*
         * 2. Toleransi Query Params (Search / Filter)
         * Terdapat Query Param yang sifatnya sebagai "Identitas Halaman" (seperti 'kategori'). 
         * Bila URL punya 'kategori', Menu juga harus punya 'kategori' yang identik nilainya, jika tidak, salahkan.
         */
        $identityParams = ['kategori', 'jenis', 'tipe', 'tab'];
        foreach ($identityParams as $idKey) {
            $reqHas = request()->has($idKey);
            $menuHas = !empty($params) && isset($params[$idKey]);
            
            if ($reqHas && $menuHas) {
                if (request()->query($idKey) != $params[$idKey]) return false;
            } elseif ($reqHas && !$menuHas) {
                return false; // URL punya identifier (kategori), sementara Menu ini polosan (untuk semua/lainnya)
            } elseif (!$reqHas && $menuHas) {
                return false; // URL polosan, tapi Menu ini punya target spesifik
            }
        }

        // Params tambahan lain di DB
        if (!empty($params) && is_array($params)) {
            foreach ($params as $key => $value) {
                if (!in_array($key, $identityParams) && request()->query($key) != $value) {
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
