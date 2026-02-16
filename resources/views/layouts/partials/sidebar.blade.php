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
                @include('layouts.partials._menu_item', ['menuItem' => $menu, 'level' => 0])
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