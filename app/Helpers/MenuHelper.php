<?php

use Illuminate\Support\Facades\Route;

/* =========================================================
| 1. LOGIKA CEK MENU AKTIF
========================================================= */
if (!function_exists('menuIsActive')) {
    function menuIsActive(array $menu): bool
    {
        if (empty($menu['route'])) return false;

        // Cek apakah Route Valid
        if (!Route::has($menu['route'])) return false;

        // Cek apakah Route saat ini cocok
        if (!request()->routeIs($menu['route'])) {
            return false;
        }

        // Cek Parameter Spesifik (Opsional - misal ?kategori=mutasi)
        if (!empty($menu['params'])) {
            foreach ($menu['params'] as $key => $value) {
                // Cek di Query String (?kategori=...) ATAU di Route Param (/siswa/{kategori})
                if (request()->query($key) != $value && request()->route($key) != $value) {
                    return false;
                }
            }
            return true;
        }

        // PENTING: Jika URL punya parameter 'kategori', tapi menu ini polosan (tidak punya params),
        // Maka anggap menu ini TIDAK AKTIF (supaya tidak bentrok dengan menu spesifik).
        if (request()->has('kategori') && empty($menu['params'])) {
            return false;
        }

        return true;
    }
}

/* =========================================================
| 2. CEK APAKAH PARENT PUNYA ANAK AKTIF (Recursive)
========================================================= */
if (!function_exists('menuHasActiveChild')) {
    function menuHasActiveChild(array $menu): bool
    {
        // Jika dirinya sendiri aktif
        if (menuIsActive($menu)) return true;

        // Cek anak-anaknya
        foreach ($menu['submenu'] ?? [] as $child) {
            if (menuHasActiveChild($child)) return true;
        }

        return false;
    }
}

/* =========================================================
| 3. CEK HAK AKSES (ROLE PERMISSION) - [STRICT MODE]
========================================================= */
if (!function_exists('canAccessMenu')) {
    function canAccessMenu($slug, $parentSlug, $role, $subRole, $roleMap, $subRoleMap): bool {
        
        // 1. Admin Selalu Lolos (Case Insensitive)
        if (strcasecmp($role, 'admin') === 0) {
            return true;
        }

        // 2. Ambil Config Izin Sesuai Role
        $allowed = $roleMap[$role] ?? [];

        // Fallback: Jika key tidak ketemu langsung, cari case-insensitive
        if (empty($allowed)) {
            foreach ($roleMap as $key => $val) {
                if (strcasecmp($key, $role) === 0) {
                    $allowed = $val;
                    break;
                }
            }
        }

        // 3. Cek Sub Role (Khusus PTK atau role bercabang lain)
        if ($subRole && isset($subRoleMap[$subRole])) {
            // Merge atau Replace izin subrole (tergantung kebutuhan, di sini kita replace/add)
            $subAllowed = $subRoleMap[$subRole];
            if (!empty($subAllowed)) {
                $allowed = array_merge($allowed, $subAllowed);
            }
        }

        // 4. Cek Wildcard (*) - Akses Total
        if (in_array('*', $allowed, true)) return true;

        // 5. CEK SLUG SPESIFIK (STRICT)
        // Menu hanya boleh muncul jika slug-nya ADA di daftar allowed.
        if (in_array($slug, $allowed, true)) return true;

        // -------------------------------------------------------------
        // ❌ BAGIAN INI DIHAPUS AGAR FILTER KETAT BERJALAN ❌
        // -------------------------------------------------------------
        // Logika lama: "Jika Parent boleh, anak otomatis boleh".
        // Ini kita matikan supaya 'layanan-mutasi' harus ditulis eksplisit,
        // dan 'layanan-kgb' otomatis hilang walau parent-nya sama.
        /*
        if ($parentSlug && in_array($parentSlug, $allowed, true)) {
             if (in_array('!' . $slug, $allowed, true)) return false;
             return true;
        }
        */

        return false;
    }
}

/* =========================================================
| 4. RENDER SIDEBAR (HTML GENERATOR)
========================================================= */
if (!function_exists('renderSidebarMenu')) {
    function renderSidebarMenu(array $menus, $role, $subRole, $roleMap, $subRoleMap, $underConstructionRoutes, $badges = [], $parentSlug = null): void {
        
        foreach ($menus as $menu) {
            $slug = $menu['slug'] ?? null;
            $isHeader = $menu['is_header'] ?? false;

            // --- A. Render Header ---
            if ($isHeader) {
                 // Header hanya muncul jika permission mengizinkan (opsional)
                 // atau kamu bisa set header selalu muncul. Di sini kita cek permission slug header.
                 if ($slug && !canAccessMenu($slug, $parentSlug, $role, $subRole, $roleMap, $subRoleMap)) {
                    continue;
                 }
                 echo "<li class='menu-header small text-uppercase'><span class='menu-header-text'>{$menu['title']}</span></li>";
                 continue;
            }

            // --- B. Cek Permission Item Menu ---
            // Jika slug tidak diizinkan, SKIP (Jangan render HTML-nya)
            if ($slug && !canAccessMenu($slug, $parentSlug, $role, $subRole, $roleMap, $subRoleMap)) {
                continue;
            }

            // --- C. Hitung Status Aktif ---
            $hasSub   = !empty($menu['submenu']);
            $isActive = menuHasActiveChild($menu); 
            
            $liClass  = 'menu-item' . ($isActive ? ' active' : '') . ($hasSub && $isActive ? ' open' : '');
            $aClass   = 'menu-link' . ($hasSub ? ' menu-toggle' : '');

            // --- D. Tentukan URL ---
            if ($hasSub || (!empty($menu['route']) && in_array($menu['route'], $underConstructionRoutes))) {
                $href = 'javascript:void(0);';
            } elseif (!empty($menu['route']) && Route::has($menu['route'])) {
                // Aman pakai route() karena Route::has sudah dicek
                $href = route($menu['route'], $menu['params'] ?? []);
            } else {
                $href = $menu['url'] ?? '#';
            }

            // --- E. Render HTML ---
            echo "<li class='{$liClass}'>";
            echo "<a href='{$href}' class='{$aClass}'>";
            
            if (!empty($menu['icon'])) {
                echo "<i class='menu-icon tf-icons {$menu['icon']}'></i>";
            }
            
            echo "<div data-i18n='{$menu['title']}'>{$menu['title']}</div>";
            
            // Badge Notifikasi
            if (isset($menu['badge_key']) && !empty($badges[$menu['badge_key']])) {
                echo "<div class='badge bg-danger rounded-pill ms-auto'>{$badges[$menu['badge_key']]}</div>";
            }
            echo "</a>";

            // --- F. Render Submenu (Recursive) ---
            if ($hasSub) {
                echo "<ul class='menu-sub'>";
                // PENTING: Pass $slug saat ini sebagai $parentSlug ke anak-anaknya
                renderSidebarMenu($menu['submenu'], $role, $subRole, $roleMap, $subRoleMap, $underConstructionRoutes, $badges, $slug);
                echo "</ul>";
            }
            echo "</li>";
        }
    }
}