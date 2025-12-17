<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">

    {{-- ================= BRAND ================= --}}
    <div class="app-brand demo">
        <a href="{{ route('admin.dashboard') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                {{-- SVG LOGO (TIDAK DIUBAH) --}}
                {{-- ... SVG panjang lu tetap di sini, gue tidak sentuh ... --}}
            </span>

            <span class="app-brand-text demo menu-text fw-bolder ms-2 text-uppercase">
                {{ config('app.name') }}
            </span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>


    {{-- ================= MENU ================= --}}
    <ul class="menu-inner py-1">
        @php
            renderSidebarMenu(
                $menus,
                $role,
                $subRole,
                $roleMap,
                $subRoleMap,
                $underConstructionRoutes
            );
        @endphp
    </ul>



</aside>
