@php
    $level = $level ?? 0;
    $menuItem = $menuItem ?? $menu; // Use menuItem for consistency in partial
    $hasChildren = $menuItem->childrenRecursive->isNotEmpty();

    // LOGIC ACTIVE STATE (INDUK & ANAK)
    $isActive = false; // Untuk menu tanpa anak (Dashboard, Profil)
    $isOpen   = false; // Untuk menu induk (Layanan GTK)

    $currentRouteActiveForThisItem = checkRouteActive($menuItem->route, $menuItem->params);
    $status = checkMenuStatusRecursive($menuItem, $currentRouteActiveForThisItem);

    $isActive = $status['isActive'];
    $isOpen = $status['isOpen'];
@endphp

{{-- HEADER (LABEL PEMBATAS) --}}
@if ($menuItem->is_header)
    <li class="menu-header small text-uppercase">
        <span class="menu-header-text">{{ $menuItem->title }}</span>
    </li>
@else
    {{-- MENU ITEM --}}
    <li class="menu-item {{ $isActive ? 'active' : '' }} {{ $isOpen ? 'open' : '' }}">
        {{-- Tentukan Link --}}
        @php
            $menuUrl = 'javascript:void(0);';
            if ($menuItem->childrenRecursive->isEmpty() && $menuItem->route && Route::has($menuItem->route)) {
                $menuUrl = route($menuItem->route, $menuItem->params ?? []);
            }
        @endphp

        <a href="{{ $menuUrl }}"
            class="menu-link {{ $hasChildren ? 'menu-toggle' : '' }}">

            @if ($menuItem->icon)
                <i class="menu-icon tf-icons {{ $menuItem->icon }}"></i>
            @endif

            <div data-i18n="{{ $menuItem->title }}">{{ $menuItem->title }}</div>

            {{-- BADGE --}}
            @if (isset($menuItem->badge_value) && $menuItem->badge_value > 0)
                <span class="badge rounded-pill bg-danger ms-auto">{{ $menuItem->badge_value }}</span>
            @endif
        </a>

        {{-- RENDER CHILDREN RECURSIVELY --}}
        @if ($hasChildren)
            <ul class="menu-sub">
                @foreach ($menuItem->childrenRecursive as $child)
                    @include('layouts.partials._menu_item', ['menuItem' => $child, 'level' => $level + 1])
                @endforeach
            </ul>
        @endif
    </li>
@endif
