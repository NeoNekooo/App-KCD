<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'KCD - Kantor Cabang Dinas')</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <!-- Frontend-only Styles & Scripts -->
    @vite(['resources/css/frontend.css', 'resources/js/frontend.js'])
    
    {{-- Force Refresh for Hosting --}}
    @if(app()->environment('production'))
        <link rel="stylesheet" href="{{ asset('build/assets/frontend-RPixff0h.css?v=' . time()) }}">
        <script src="{{ asset('build/assets/frontend-Y67pf_WM.js?v=' . time()) }}" defer></script>
    @endif
    @stack('styles')
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 flex flex-col min-h-screen">
    <x-navbar />

    <main class="flex-grow pt-24 md:pt-28">
        @yield('content')
    </main>

    <x-footer />
    @stack('scripts')
</body>
</html>

