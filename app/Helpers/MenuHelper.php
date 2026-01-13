<?php

use Illuminate\Support\Facades\Route;

/* =========================================================
| 1. LOGIKA CEK MENU AKTIF (SMART LOGIC)
========================================================= */
if (!function_exists('menuIsActive')) {
    function menuIsActive(array $menu): bool
    {
        // A. Validasi Dasar: Jika tidak ada route, cek URL manual (opsional) atau return false
        if (empty($menu['route'])) {
            return false;
        }

        // B. Cek Nama Route (Basic Check)
        // Jika nama route di URL sekarang tidak sama dengan config, langsung False.
        if (!Route::has($menu['route']) || !request()->routeIs($menu['route'])) {
            return false;
        }

        // C. LOGIKA PARAMETER (Advanced Check)
        // 1. Jika menu punya parameter spesifik (misal: kategori=mutasi)
        if (!empty($menu['params'])) {
            foreach ($menu['params'] as $key => $value) {
                // Cek di Query String (?kategori=...) ATAU Route Param ({id})
                $queryParam = request()->query($key);
                $routeParam = request()->route($key);

                // Jika salah satu tidak cocok, berarti menu ini BUKAN yang sedang aktif
                if ($queryParam != $value && $routeParam != $value) {
                    return false;
                }
            }
            return true; // Route Cocok & Param Cocok -> AKTIF
        }

        // 2. Jika menu TIDAK punya parameter (Menu Default / "Lainnya")
        // Kasus: "Verifikasi Surat Lainnya" (params kosong).
        // Aturan: Jika URL saat ini MEMILIKI parameter 'kategori', maka menu default ini HARUS MATI.
        // Supaya dia tidak ikut menyala saat kita buka menu "Kenaikan Pangkat".
        if (request()->has('kategori')) {
            return false;
        }

        // Jika lolos semua cek (Route sama, tidak ada syarat param, URL bersih) -> AKTIF
        return true;
    }
}

/* =========================================================
| 2. CEK APAKAH PARENT PUNYA ANAK AKTIF
========================================================= */
if (!function_exists('menuHasActiveChild')) {
    function menuHasActiveChild(array $menu): bool
    {
        if (menuIsActive($menu)) {
            return true;
        }

        foreach ($menu['submenu'] ?? [] as $child) {
            if (menuHasActiveChild($child)) {
                return true;
            }
        }

        return false;
    }
}

/* =========================================================
| 3. CEK HAK AKSES (ROLE PERMISSION)
========================================================= */
if (!function_exists('canAccessMenu')) {
    function canAccessMenu($slug, $parentSlug, $role, $subRole, $roleMap, $subRoleMap): bool {
        $allowed = ($role === 'PTK' && $subRole) ? ($subRoleMap[$subRole] ?? []) : ($roleMap[$role] ?? []);
        
        if (empty($allowed)) return false;
        if (in_array('!' . $slug, $allowed, true)) return false;
        if (in_array('*', $allowed, true)) return true;
        
        if ($parentSlug === null) return in_array($slug, $allowed, true);
        if (!in_array($parentSlug, $allowed, true)) return false;

        $specificChildren = array_filter($allowed, fn ($item) => str_starts_with($item, $parentSlug . '-'));
        return empty($specificChildren) ? true : in_array($slug, $specificChildren, true);
    }
}

/* =========================================================
| 4. RENDER SIDEBAR (HTML GENERATOR)
========================================================= */
if (!function_exists('renderSidebarMenu')) {
    function renderSidebarMenu(array $menus, $role, $subRole, $roleMap, $subRoleMap, $underConstructionRoutes, $badges = [], $parentSlug = null): void {
        
        foreach ($menus as $menu) {
            $slug = $menu['slug'] ?? null;

            // Cek Permission
            if ($slug && !canAccessMenu($slug, $parentSlug, $role, $subRole, $roleMap, $subRoleMap)) {
                continue;
            }

            // Cek Status Aktif
            $hasSub   = !empty($menu['submenu']);
            $isActive = menuHasActiveChild($menu); 
            
            // Generate CSS Classes
            $liClass  = 'menu-item' . ($isActive ? ' active' : '') . ($hasSub && $isActive ? ' open' : '');
            $aClass   = 'menu-link' . ($hasSub ? ' menu-toggle' : '');

            // Generate URL (dengan Parameter jika ada)
            if ($hasSub || (!empty($menu['route']) && in_array($menu['route'], $underConstructionRoutes))) {
                $href = 'javascript:void(0);';
            } elseif (!empty($menu['route']) && Route::has($menu['route'])) {
                // INI PENTING: Masukkan params ke route() generator
                $href = route($menu['route'], $menu['params'] ?? []);
            } else {
                $href = $menu['url'] ?? '#';
            }

            // Output HTML
            echo "<li class='{$liClass}'>";
            echo "<a href='{$href}' class='{$aClass}'>";
            
            if (!empty($menu['icon'])) {
                echo "<i class='menu-icon tf-icons {$menu['icon']}'></i>";
            }
            
            echo "<div data-i18n='{$menu['title']}'>{$menu['title']}</div>";
            
            // Render Badge (Notifikasi Angka)
            if (isset($menu['badge_key']) && !empty($badges[$menu['badge_key']])) {
                echo "<div class='badge bg-danger rounded-pill ms-auto'>{$badges[$menu['badge_key']]}</div>";
            }
            echo "</a>";

            // Render Submenu (Rekursif)
            if ($hasSub) {
                echo "<ul class='menu-sub'>";
                renderSidebarMenu($menu['submenu'], $role, $subRole, $roleMap, $subRoleMap, $underConstructionRoutes, $badges, $slug);
                echo "</ul>";
            }
            echo "</li>";
        }
    }
}