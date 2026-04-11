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

    <!-- Global Assets -->
    @if(app()->environment('local'))
        @vite(['resources/css/frontend.css', 'resources/js/frontend.js'])
    @else
        @php
            // Logika otomatis mencari file build terbaru di folder assets
            $manifestPath = public_path('build/manifest.json');
            $cssFile = '';
            $jsFile = '';
            
            if (file_exists($manifestPath)) {
                $manifest = json_decode(file_get_contents($manifestPath), true);
                $cssFile = asset('build/' . ($manifest['resources/css/frontend.css']['file'] ?? ''));
                $jsFile = asset('build/' . ($manifest['resources/js/frontend.js']['file'] ?? ''));
            }
        @endphp
        
        @if($cssFile && $jsFile)
            <link rel="stylesheet" href="{{ $cssFile }}">
            <script src="{{ $jsFile }}" type="module" defer></script>
        @else
            {{-- Fallback jika manifest belum terupdate sempurna --}}
            @vite(['resources/css/frontend.css', 'resources/js/frontend.js'])
        @endif
    @endif

    @stack('styles')
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
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
