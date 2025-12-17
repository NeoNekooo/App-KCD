<?php

use Illuminate\Support\Facades\Route;

/* =========================================================
| ACTIVE CHECK
========================================================= */
if (!function_exists('menuIsActive')) {
    function menuIsActive(array $menu): bool
    {
        return !empty($menu['route'])
            && Route::has($menu['route'])
            && request()->routeIs($menu['route']);
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
| ACCESS CHECK (* + !exclude SUPPORT)
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

        if (empty($allowed)) {
            return false;
        }

        // explicit exclude: !slug
        if (in_array('!' . $slug, $allowed, true)) {
            return false;
        }

        // wildcard full access
        if (in_array('*', $allowed, true)) {
            return true;
        }

        // menu induk
        if ($parentSlug === null) {
            return in_array($slug, $allowed, true);
        }

        // menu anak: induk wajib ada
        if (!in_array($parentSlug, $allowed, true)) {
            return false;
        }

        // whitelist anak
        $specificChildren = array_filter(
            $allowed,
            fn ($item) => str_starts_with($item, $parentSlug . '-')
        );

        return empty($specificChildren)
            ? true
            : in_array($slug, $specificChildren, true);
    }
}

/* =========================================================
| RENDER SIDEBAR (ANTI DOUBLE CLICK)
========================================================= */
if (!function_exists('renderSidebarMenu')) {
    function renderSidebarMenu(
        array $menus,
        string $role,
        ?string $subRole,
        array $roleMap,
        array $subRoleMap,
        array $underConstructionRoutes,
        ?string $parentSlug = null
    ): void {

        foreach ($menus as $menu) {

            $slug = $menu['slug'] ?? null;

            if (
                $slug &&
                !canAccessMenu(
                    $slug,
                    $parentSlug,
                    $role,
                    $subRole,
                    $roleMap,
                    $subRoleMap
                )
            ) {
                continue;
            }

            $hasSub   = !empty($menu['submenu']);
            $isActive = menuHasActiveChild($menu);

            $liClass = 'menu-item' . ($isActive ? ' active open' : '');
            $aClass  = 'menu-link' . ($hasSub ? ' menu-toggle' : '');

            /* ================= HREF (URUTAN WAJIB) ================= */
            if ($hasSub) {
                // dropdown WAJIB void
                $href = 'javascript:void(0);';
            } elseif (
                !empty($menu['route']) &&
                in_array($menu['route'], $underConstructionRoutes, true)
            ) {
                $href = 'javascript:void(0);';
            } elseif (!empty($menu['route']) && Route::has($menu['route'])) {
                $href = route($menu['route']);
            } else {
                $href = '#';
            }

            echo "<li class='{$liClass}'>";
            echo "<a href='{$href}' class='{$aClass}'>";

            if (!empty($menu['icon'])) {
                echo "<i class='menu-icon tf-icons {$menu['icon']}'></i>";
            }

            echo "<div>{$menu['title']}</div>";
            echo "</a>";

            if ($hasSub) {
                echo "<ul class='menu-sub'>";
                renderSidebarMenu(
                    $menu['submenu'],
                    $role,
                    $subRole,
                    $roleMap,
                    $subRoleMap,
                    $underConstructionRoutes,
                    $slug
                );
                echo "</ul>";
            }

            echo "</li>";
        }
    }
}
