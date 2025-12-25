<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    {{-- SEO Meta Tags --}}
    <meta name="description" content="Website Resmi Sekolah Menengah Kejuruan - Mencetak Generasi Unggul">
    <meta name="keywords" content="sekolah, smk, pendidikan, ppdb, indonesia, kejuruan">
    <meta name="author" content="Tim IT Sekolah">
    
    {{-- Favicon (Ganti href dengan logo sekolah Anda) --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    
    <title>@yield('title', 'Website Sekolah Resmi') - SEKOLAHKU</title>
    
    {{-- 1. FONT (Plus Jakarta Sans) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- 2. ICONS (Boxicons) --}}
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    {{-- 3. LIBRARY CSS (Swiper JS & AOS Animation) --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    {{-- 4. TAILWIND CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        primary: '#2563eb', /* Blue 600 */
                        secondary: '#4f46e5', /* Indigo 600 */
                        dark: '#0f172a', /* Slate 900 */
                    }
                }
            }
        }
    </script>

    {{-- 5. CUSTOM STYLES --}}
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* Hide Scrollbar util */
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    </style>

    {{-- Stack untuk CSS Tambahan per Halaman --}}
    @stack('styles')
</head>

<body class="flex flex-col min-h-screen bg-white text-slate-800 antialiased selection:bg-blue-600 selection:text-white">

    {{-- NAVBAR --}}
    {{-- Pastikan path ini sesuai dengan struktur folder Anda --}}
    @include('layouts.partials.web.navbar')

    {{-- KONTEN UTAMA --}}
    {{-- Main wrapper --}}
    <main class="flex-grow">
        @yield('content')
    </main>

    {{-- FOOTER --}}
    {{-- Pastikan path ini sesuai dengan struktur folder Anda --}}
    @include('layouts.partials.web.footer')


    {{-- ============================================================ --}}
    {{-- GLOBAL TOAST NOTIFICATION (Logic Notifikasi Otomatis)        --}}
    {{-- ============================================================ --}}
    @if(session()->has('success_testimoni') || session()->has('error_testimoni') || session()->has('success') || session()->has('error'))
        @php
            $msgSuccess = session('success_testimoni') ?? session('success');
            $msgError   = session('error_testimoni') ?? session('error');
            $isSuccess  = $msgSuccess ? true : false;
            $message    = $msgSuccess ?? $msgError;
        @endphp

        <div id="global-toast" class="fixed top-24 right-5 z-[9999] transition-all duration-500 ease-out transform translate-x-0 opacity-100">
            <div class="flex items-center w-full max-w-xs p-4 mb-4 text-gray-500 bg-white rounded-xl shadow-2xl border-l-4 {{ $isSuccess ? 'border-blue-600' : 'border-red-500' }}" role="alert">
                
                {{-- Icon --}}
                <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 {{ $isSuccess ? 'text-blue-600 bg-blue-100' : 'text-red-500 bg-red-100' }} rounded-lg">
                    <i class='bx {{ $isSuccess ? 'bx-check' : 'bx-error-circle' }} text-xl'></i>
                </div>
                
                {{-- Text --}}
                <div class="ml-3 text-sm font-medium text-slate-700 pr-4">
                    {{ $message }}
                </div>
                
                {{-- Close Button --}}
                <button type="button" class="ml-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex h-8 w-8" onclick="closeGlobalToast()">
                    <span class="sr-only">Close</span>
                    <i class='bx bx-x text-xl'></i>
                </button>
            </div>
        </div>

        <script>
            // Fungsi menutup toast manual
            function closeGlobalToast() {
                const toast = document.getElementById('global-toast');
                if (toast) {
                    toast.classList.add('translate-x-full', 'opacity-0');
                    setTimeout(() => { toast.remove(); }, 500);
                }
            }

            // Auto close setelah 5 detik
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(function() {
                    closeGlobalToast();
                }, 5000); 
            });
        </script>
    @endif


    {{-- LIBRARY SCRIPTS --}}
    {{-- Swiper JS --}}
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    {{-- AOS Animation --}}
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Init AOS (Animate On Scroll)
        AOS.init({
            once: true,
            duration: 800,
            offset: 100,
        });
    </script>

    {{-- Stack untuk JS Tambahan per Halaman --}}
    @stack('scripts')
    
</body>
</html>