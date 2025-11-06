<!DOCTYPE html>
<html lang="id">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>PPDB SMAKNIS - Pendaftaran Siswa Baru</title>
        <!-- Memuat Tailwind CSS CDN -->
        <script src="https://cdn.tailwindcss.com"></script>
        <!-- Memuat Lucide Icons untuk ikon yang cantik -->
        <script src="https://unpkg.com/lucide@latest"></script>
        <!-- Memuat Font Awesome untuk ikon sosial media -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <!-- Memuat AOS CSS untuk animasi Scroll -->
        <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">

        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">

        <link rel="stylesheet" href="{{ asset('assets/css/ppdb.css') }}">
        {{-- Hubungkan dengan Vite agar ikut hot reload --}}
        {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}


        <!-- Konfigurasi Tailwind untuk warna dan font -->
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['Inter', 'sans-serif'],
                        },
                        colors: {
                            'primary-blue': '#1e3a8a', // Warna biru tua SMAKNIS
                            'secondary-green': '#10b981', // Warna hijau cerah untuk sukses
                            'neutral-light': '#f3f4f6', // Latar belakang ringan
                            'footer-dark': '#0f172a', // Latar belakang footer yang gelap
                        }
                    }
                }
            }
        </script>
    </head>

    <body class="antialiased">

        <!-- Notification Container (for success/error messages) -->
        <div id="notification" class="text-white p-4 rounded-lg shadow-xl flex items-center space-x-3 max-w-sm">
        </div>
        <!-- Navbar -->
        @include('layouts.partials.ppdb.navbar')

        @yield('content-ppdb')

        <!-- Footer -->
        @include('layouts.partials.ppdb.footer')

        <!-- Memuat AOS JS -->
        <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>

        <!-- Script Module untuk Logika Aplikasi dan Firebase -->
        <script type="module" src="{{ asset('assets/js/ppdb.js') }}"></script>
        
        <script>
            window.showNotification = function(message, isError = false) {
                const notification = document.getElementById('notification');
                notification.classList.remove('bg-secondary-green', 'bg-red-500');
                notification.classList.add(isError ? 'bg-red-500' : 'bg-secondary-green');
                const iconHtml = `<i data-lucide="${isError ? 'x-octagon' : 'check-circle'}" class="w-6 h-6"></i>`;
                notification.innerHTML = `${iconHtml}<span id="notification-message" class="ml-2">${message}</span>`;
                notification.classList.remove('hidden');
                notification.classList.add('show');
                setTimeout(() => {
                    notification.classList.add('hidden');
                    notification.classList.remove('show');
                }, 5000);
                lucide.createIcons();
            };


            document.addEventListener('DOMContentLoaded', function() {
                @if(session('success'))
                    showNotification("{{ session('success') }}", false);
                @endif
            });
        </script>
    </body>

</html>