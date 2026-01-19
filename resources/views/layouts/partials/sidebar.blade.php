<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">

    {{-- ================= BRAND ================= --}}
    <div class="app-brand demo">
        <a href="{{ route('admin.dashboard') }}" class="app-brand-link">
            <span class="app-brand-text demo menu-text fw-bolder ms-2 text-uppercase">
                {{ config('app.name') }}
            </span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    {{-- ================= MENU LIST DARI DATABASE ================= --}}
    <ul class="menu-inner py-1">

        @if (isset($menus))
            @foreach ($menus as $menu)
                {{-- 1. CEK HEADER (LABEL PEMBATAS) --}}
                @if ($menu->is_header)
                    <li class="menu-header small text-uppercase">
                        <span class="menu-header-text">{{ $menu->title }}</span>
                    </li>
                    @continue
                @endif

                {{-- 2. LOGIC ACTIVE STATE (INDUK & ANAK) --}}
                @php
                    $isActive = false; // Untuk menu tanpa anak (Dashboard, Profil)
                    $isOpen   = false; // Untuk menu induk (Layanan GTK)

                    // A. Cek jika Menu ini adalah Single Link (Gak punya anak)
                    if ($menu->children->isEmpty()) {
                        if ($menu->route && Route::has($menu->route) && request()->routeIs($menu->route)) {
                            $isActive = true;
                        }
                    } 
                    // B. Cek jika Menu ini punya Anak (Submenu)
                    else {
                        foreach ($menu->children as $child) {
                            // Cek apakah route anak ini sedang diakses?
                            if ($child->route && Route::has($child->route) && request()->routeIs($child->route)) {
                                
                                // Jika route cocok, cek parameter (query string)
                                if (!empty($child->params)) {
                                    $allParamsMatch = true;
                                    foreach ($child->params as $key => $value) {
                                        // Bandingkan query string di URL dengan database
                                        if (request()->query($key) != $value) {
                                            $allParamsMatch = false;
                                            break; 
                                        }
                                    }
                                    if ($allParamsMatch) {
                                        $isOpen = true; // Buka induknya
                                        break; // Gak usah cek anak lain, udah ketemu
                                    }
                                } else {
                                    // Jika anak gak punya params spesifik, pastikan URL juga bersih
                                    // (Misal menu 'Data Umum' gak boleh nyala pas buka 'Mutasi')
                                    if (count(request()->query()) == 0) {
                                        $isOpen = true;
                                        break;
                                    }
                                }
                            }
                        }
                    }
                @endphp

                {{-- 3. RENDER MENU ITEM --}}
                <li class="menu-item {{ $isActive ? 'active' : '' }} {{ $isOpen ? 'open' : '' }}">

                    {{-- Tentukan Link Induk --}}
                    @php
                        $menuUrl = 'javascript:void(0);';
                        // Hanya kasih link kalau dia single menu
                        if ($menu->children->isEmpty() && $menu->route && Route::has($menu->route)) {
                            $menuUrl = route($menu->route, $menu->params ?? []);
                        }
                    @endphp

                    <a href="{{ $menuUrl }}"
                        class="menu-link {{ $menu->children->isNotEmpty() ? 'menu-toggle' : '' }}">

                        @if ($menu->icon)
                            <i class="menu-icon tf-icons {{ $menu->icon }}"></i>
                        @endif

                        <div data-i18n="{{ $menu->title }}">{{ $menu->title }}</div>

                        {{-- BADGE INDUK --}}
                        @if (isset($menu->badge_value) && $menu->badge_value > 0)
                            <span class="badge rounded-pill bg-danger ms-auto">{{ $menu->badge_value }}</span>
                        @endif
                    </a>

                    {{-- 4. RENDER SUBMENU --}}
                    @if ($menu->children->isNotEmpty())
                        <ul class="menu-sub">
                            @foreach ($menu->children as $child)
                                @php
                                    $isChildActive = false;

                                    if ($child->route && Route::has($child->route) && request()->routeIs($child->route)) {
                                        // Cek Params Anak
                                        if (!empty($child->params)) {
                                            $paramsMatch = true;
                                            foreach ($child->params as $key => $value) {
                                                if (request()->query($key) != $value) {
                                                    $paramsMatch = false;
                                                    break;
                                                }
                                            }
                                            if ($paramsMatch) $isChildActive = true;
                                        } else {
                                            // Jika anak gak punya params, URL juga harus bersih
                                            if (count(request()->query()) == 0) $isChildActive = true;
                                        }
                                    }
                                @endphp

                                <li class="menu-item {{ $isChildActive ? 'active' : '' }}">
                                    <a href="{{ $child->route && Route::has($child->route) ? route($child->route, $child->params ?? []) : 'javascript:void(0);' }}"
                                        class="menu-link d-flex justify-content-between align-items-center">

                                        <div data-i18n="{{ $child->title }}">{{ $child->title }}</div>

                                        {{-- BADGE ANAK --}}
                                        @if (isset($child->badge_value) && $child->badge_value > 0)
                                            <span class="badge rounded-pill bg-danger"
                                                style="font-size: 0.75rem;">{{ $child->badge_value }}</span>
                                        @endif
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </li>
            @endforeach
        @endif

        {{-- ================= TOMBOL LOGOUT ================= --}}
        <li class="menu-item mt-3">
            <form id="form-logout-sidebar" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>

            <a href="javascript:void(0);" class="menu-link text-danger"
                onclick="event.preventDefault(); document.getElementById('form-logout-sidebar').submit();">
                <i class="menu-icon tf-icons bx bx-power-off text-danger"></i>
                <div data-i18n="Keluar">Keluar</div>
            </a>
        </li>

    </ul>

</aside>