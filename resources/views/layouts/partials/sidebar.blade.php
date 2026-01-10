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

    {{-- ================= MENU LIST ================= --}}
    <ul class="menu-inner py-1">
        
        {{-- 1. Render Menu Otomatis --}}
        @php
            // Ambil role & subrole
            $userRole = (string) (auth()->user()->role ?? '');
            $userSubRole = (string) ($subRole ?? '');

            /** * AMBIL DATA BADGES 
             * Tadi di AppServiceProvider kita passing dengan nama 'notif_data'
             * Kita pastikan variabelnya tersedia agar tidak error
             */
            $badgeData = $notif_data ?? []; 

            renderSidebarMenu(
                $menus ?? [],
                $userRole,      
                $userSubRole,   
                $roleMap ?? [],
                $subRoleMap ?? [],
                $underConstructionRoutes ?? [],
                $badgeData // Data jumlah notifikasi masuk ke sini bre
            );
        @endphp

        {{-- 2. Tombol Keluar --}}
        <li class="menu-item mt-2">
            <form id="form-logout-sidebar" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>

            <a href="javascript:void(0);" class="menu-link text-danger" onclick="event.preventDefault(); document.getElementById('form-logout-sidebar').submit();">
                <i class="menu-icon tf-icons bx bx-power-off text-danger"></i>
                <div data-i18n="Keluar">Keluar</div>
            </a>
        </li>

    </ul>

</aside>