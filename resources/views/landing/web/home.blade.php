@extends('landing.web.layouts.master_web')

@section('title', 'Beranda - Website Sekolah Modern')

@section('content')

    {{-- A. KONFIGURASI STYLE GLOBAL (Tailwind & Font) --}}
    {{-- Kita taruh di sini agar file sections tetap bersih --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        primary: '#4F46E5', /* Indigo 600 - Warna Utama Modern */
                        secondary: '#F59E0B', /* Amber 500 - Warna Aksen */
                        dark: '#0f172a',
                    },
                    animation: {
                        'slow-zoom': 'zoom 20s infinite alternate',
                    },
                    keyframes: {
                        zoom: {
                            '0%': { transform: 'scale(1)' },
                            '100%': { transform: 'scale(1.15)' },
                        }
                    }
                }
            }
        }
    </script>
    <style>
        /* Global CSS Fixes */
        body { overflow-x: hidden; }
        .carousel-item { transition: transform 0.8s cubic-bezier(0.4, 0, 0.2, 1); }
    </style>

    {{-- B. PEMANGGILAN SECTIONS (KOMPONEN) --}}
    
    {{-- 1. Hero Slider Section --}}
    @include('landing.web.sections.hero')

    {{-- 2. Sambutan Kepala Sekolah --}}
    @include('landing.web.sections.sambutan') 
    
    {{-- 3. Jurusan / Kompetensi --}}
    @include('landing.web.sections.jurusan')

    {{-- 4. Fasilitas --}}
    @include('landing.web.sections.fasilitas')

    {{-- 5. Berita Terkini --}}
    @include('landing.web.sections.berita')

    {{-- 6. Prestasi & Galeri --}}
    @include('landing.web.sections.prestasi_galeri')

    {{-- 7. Mitra Industri --}}
    @include('landing.web.sections.mitra')

    {{-- 8. Ekstrakurikuler --}}
    @include('landing.web.sections.ekstrakurikuler')

    {{-- 9. Agenda Sekolah --}}
    @include('landing.web.sections.agenda')
    
    {{-- 10. Testimoni --}}
    @include('landing.web.sections.testimoni')

    {{-- ... (Kita akan buat file lainnya satu per satu) ... --}}

@endsection