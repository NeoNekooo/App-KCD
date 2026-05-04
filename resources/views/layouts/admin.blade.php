<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="{{ asset('assets/') }}" data-template="vertical-menu-template-free" data-layout="wide">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') | {{ $appSettings['site_name'] ?? 'MANDALA' }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ !empty($appSettings['favicon']) ? asset('storage/' . $appSettings['favicon']) : asset('assets/img/favicon/favicon.ico') }}" />

    <link rel="stylesheet" href="{{ asset('vendor/fonts/boxicons.css') }}" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body style="background-color: #f5f5f9;">
    @php 
        $user = Auth::guard('web')->user() ?? Auth::guard('pengguna')->user();
        // Hanya paksa 2FA untuk Admin/Pegawai KCD, jangan paksa Siswa/Guru
        $is2faForced = ($user instanceof \App\Models\User) && !$user->google2fa_enabled; 
    @endphp
    @include('layouts.partials.toast')

    <div class="layout-wrapper layout-content-navbar {{ $is2faForced ? 'layout-without-menu' : '' }}">
        <div class="layout-container">
            @if(!$is2faForced) @include('layouts.partials.sidebar') @endif
            <div class="layout-page">
                @if(!$is2faForced) @include('layouts.partials.topbar') @endif
                <div class="content-wrapper">
                    <div class="container flex-grow-1 container-p-y {{ $is2faForced ? 'd-flex justify-content-center align-items-center' : '' }}">
                        <div class="{{ $is2faForced ? 'w-100' : '' }}" style="{{ $is2faForced ? 'max-width: 1000px;' : '' }}">
                            @yield('content')
                        </div>
                    </div>
                    @include('layouts.partials.footer')
                </div>
            </div>
        </div>
    </div>

    <script async defer src="https://buttons.github.io/buttons.js"></script>
    @stack('scripts')
</body>
</html>
