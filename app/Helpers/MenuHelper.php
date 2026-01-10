<?php

use Illuminate\Support\Facades\Route;

/* =========================================================
| ACTIVE CHECK (FIXED: CEK ROUTE + PARAMETER)
========================================================= */
if (!function_exists('menuIsActive')) {
    function menuIsActive(array $menu): bool
    {
        // 1. Validasi dasar: Route harus ada dan terdaftar
        if (empty($menu['route']) || !Route::has($menu['route'])) {
            return false;
        }

        // 2. Cek apakah Nama Route cocok dengan URL saat ini
        if (!request()->routeIs($menu['route'])) {
            return false;
        }

        // 3. CEK PARAMETER (PENTING untuk filter kategori)
        if (!empty($menu['params'])) {
            $currentParams = request()->route()->parameters();

            foreach ($menu['params'] as $key => $value) {
                // Bandingkan parameter di URL dengan parameter di config
                if (!isset($currentParams[$key]) || $currentParams[$key] != $value) {
                    return false; 
                }
            }
        }

        return true;
    }
}

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
| ACCESS CHECK
========================================================= */
if (!function_exists('canAccessMenu')) {
    function canAccessMenu(
        string $slug,
        ?string $parentSlug,
        string $role,
        ?string $subRole,
        array $roleMap,
        array $subRoleMap
    ): bool {

        $allowed = ($role === 'PTK' && $subRole)
            ? ($subRoleMap[$subRole] ?? [])
            : ($roleMap[$role] ?? []);

        if (empty($allowed)) return false;

        if (in_array('!' . $slug, $allowed, true)) return false;

        if (in_array('*', $allowed, true)) return true;

        if ($parentSlug === null) {
            return in_array($slug, $allowed, true);
        }

        if (!in_array($parentSlug, $allowed, true)) return false;

        $specificChildren = array_filter(
            $allowed,
            fn ($item) => str_starts_with($item, $parentSlug . '-')
        );

        return empty($specificChildren) ? true : in_array($slug, $specificChildren, true);
    }
}

/* =========================================================
| RENDER SIDEBAR (SUPPORT PARAMETERS & BADGES)
========================================================= */
if (!function_exists('renderSidebarMenu')) {
    function renderSidebarMenu(
        array $menus,
        string $role,
        ?string $subRole,
        array $roleMap,
        array $subRoleMap,
        array $underConstructionRoutes,
        array $badges = [], 
        ?string $parentSlug = null
    ): void {

        foreach ($menus as $menu) {
            $slug = $menu['slug'] ?? null;

            // 1. Cek Hak Akses
            if ($slug && !canAccessMenu($slug, $parentSlug, $role, $subRole, $roleMap, $subRoleMap)) {
                continue;
            }

            $hasSub   = !empty($menu['submenu']);
            $isActive = menuHasActiveChild($menu); 

            // Kelas CSS (Active Open untuk parent yang punya anak aktif)
            $liClass = 'menu-item' . ($isActive ? ' active' : '') . ($hasSub && $isActive ? ' open' : '');
            $aClass  = 'menu-link' . ($hasSub ? ' menu-toggle' : '');

            // 2. Logic Href
            if ($hasSub) {
                $href = 'javascript:void(0);';
            } elseif (!empty($menu['route']) && in_array($menu['route'], $underConstructionRoutes, true)) {
                $href = 'javascript:void(0);';
            } elseif (!empty($menu['route']) && Route::has($menu['route'])) {
                try {
                    $href = route($menu['route'], $menu['params'] ?? []);
                } catch (\Exception $e) {
                    $href = '#'; 
                }
            } else {
                $href = $menu['url'] ?? '#';
            }

            // 3. Render HTML
            echo "<li class='{$liClass}'>";
            echo "<a href='{$href}' class='{$aClass}'>";

            if (!empty($menu['icon'])) {
                echo "<i class='menu-icon tf-icons {$menu['icon']}'></i>";
            }

            echo "<div data-i18n='{$menu['title']}'>{$menu['title']}</div>";

            // LOGIC BADGE (Dukungan notif_data)
            if (isset($menu['badge_key']) && !empty($badges[$menu['badge_key']])) {
                $count = $badges[$menu['badge_key']];
                echo "<div class='badge bg-danger rounded-pill ms-auto'>{$count}</div>";
            }

            echo "</a>";

            // 4. Render Submenu (Rekursif)
            if ($hasSub) {
                echo "<ul class='menu-sub'>";
                renderSidebarMenu(
                    $menu['submenu'],
                    $role,
                    $subRole,
                    $roleMap,
                    $subRoleMap,
                    $underConstructionRoutes,
                    $badges,
                    $slug
                );
                echo "</ul>";
            }

            echo "</li>";
        }
    }
}