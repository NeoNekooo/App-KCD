@php
$sekolah = \App\Models\Sekolah::first();
$namaSekolah = $sekolah ? $sekolah->nama : 'SEKOLAHKU';
$logoSekolah = ($sekolah && $sekolah->logo) ? asset('storage/' . $sekolah->logo) : null;


$host = request()->getHost();
$spmbHost = preg_replace('/^[^.]+/', 'spmb', $host);
$spmbUrl = request()->getScheme() . '://' . $spmbHost;

@endphp

<header>
    {{-- Navbar Utama --}}
    <nav id="navbar"
        class="fixed w-full z-50 top-0 transition-all duration-500 ease-in-out bg-white/95 backdrop-blur-md shadow-lg border-b border-gray-100 text-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">

                {{-- 1. LOGO & BRANDING --}}
                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                        <div
                            class="relative w-11 h-11 flex items-center justify-center overflow-hidden transition-transform duration-300 group-hover:scale-105">
                            @if($logoSekolah)
                            <img src="{{ $logoSekolah }}" alt="Logo {{ $namaSekolah }}"
                                class="w-full h-full object-contain">
                            @else
                            <i class='bx bxs-school text-4xl text-blue-600'></i>
                            @endif
                        </div>
                        <div class="flex flex-col">
                            <span id="nav-brand-text"
                                class="font-bold text-lg md:text-xl tracking-tight leading-none text-gray-900 group-hover:text-blue-600 transition-colors uppercase font-sans">
                                {{ Str::limit($namaSekolah, 20) }}
                            </span>
                        </div>
                    </a>
                </div>

                {{-- 2. DESKTOP MENU --}}
                <div class="hidden lg:flex items-center space-x-1">

                    {{-- A. BERANDA --}}
                    <a href="{{ route('home') }}"
                        class="nav-link px-3 py-2 text-sm font-semibold text-gray-700 hover:text-blue-600 transition-all duration-300 relative group">
                        Beranda
                        <span
                            class="absolute bottom-0 left-0 w-full h-0.5 bg-blue-600 transform scale-x-0 transition-transform duration-300 group-hover:scale-x-100"></span>
                    </a>

                    {{-- B. DROPDOWN INFORMASI (Agenda, Berita) --}}
                    <div class="relative dropdown-group" data-target="informasi">
                        <button type="button"
                            class="dropdown-btn px-3 py-2 text-sm font-semibold text-gray-700 hover:text-blue-600 transition-all duration-300 flex items-center gap-1 focus:outline-none relative group">
                            Informasi
                            <span
                                class="absolute bottom-0 left-0 w-full h-0.5 bg-blue-600 transform scale-x-0 transition-transform duration-300 group-hover:scale-x-100"></span>
                            <i class='bx bx-chevron-down text-lg transition-transform duration-300 dropdown-arrow'></i>
                        </button>

                        <div
                            class="dropdown-menu absolute left-0 mt-4 w-48 bg-white rounded-xl shadow-xl border border-blue-100/50 opacity-0 invisible transform -translate-y-2 transition-all duration-300 z-50">
                            <div
                                class="absolute -top-2 left-6 w-4 h-4 bg-white border-t border-l border-blue-100/50 transform rotate-45">
                            </div>
                            <div class="relative bg-white rounded-xl overflow-hidden py-1">
                                <a href="{{ route('home') }}#agenda"
                                    class="block px-5 py-3 text-sm font-medium text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition-colors border-b border-gray-50 flex items-center gap-2">
                                    <i class='bx bx-calendar text-blue-500'></i> Agenda
                                </a>
                                <a href="{{ route('home') }}#berita"
                                    class="block px-5 py-3 text-sm font-medium text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition-colors flex items-center gap-2">
                                    <i class='bx bx-news text-blue-500'></i> Berita
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- C. DROPDOWN PROFIL (LENGKAP) --}}
                    <div class="relative dropdown-group" data-target="profil">
                        <button type="button"
                            class="dropdown-btn px-3 py-2 text-sm font-semibold text-gray-700 hover:text-blue-600 transition-all duration-300 flex items-center gap-1 focus:outline-none relative group">
                            Profil
                            <span
                                class="absolute bottom-0 left-0 w-full h-0.5 bg-blue-600 transform scale-x-0 transition-transform duration-300 group-hover:scale-x-100"></span>
                            <i class='bx bx-chevron-down text-lg transition-transform duration-300 dropdown-arrow'></i>
                        </button>

                        <div
                            class="dropdown-menu absolute left-0 mt-4 w-64 bg-white rounded-xl shadow-xl border border-blue-100/50 opacity-0 invisible transform -translate-y-2 transition-all duration-300 z-50">
                            <div
                                class="absolute -top-2 left-6 w-4 h-4 bg-white border-t border-l border-blue-100/50 transform rotate-45">
                            </div>
                            <div class="relative bg-white rounded-xl overflow-hidden py-1">
                                <a href="{{ route('profil.sekolah') }}"
                                    class="block px-5 py-3 text-sm font-medium text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition-colors border-b border-gray-50 flex items-center gap-2">
                                    <i class='bx bx-building text-blue-500'></i> Tentang Sekolah
                                </a>
                                <a href="{{ route('home') }}#jurusan"
                                    class="block px-5 py-3 text-sm font-medium text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition-colors border-b border-gray-50 flex items-center gap-2">
                                    <i class='bx bx-book-bookmark text-blue-500'></i> Program Keahlian
                                </a>
                                <a href="{{ route('home') }}#fasilitas"
                                    class="block px-5 py-3 text-sm font-medium text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition-colors border-b border-gray-50 flex items-center gap-2">
                                    <i class='bx bx-buildings text-blue-500'></i> Fasilitas
                                </a>

                                {{-- EKSTRAKURIKULER (DISINI) --}}
                                <a href="{{ route('home') }}#ekskul"
                                    class="block px-5 py-3 text-sm font-medium text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition-colors border-b border-gray-50 flex items-center gap-2">
                                    <i class='bx bx-basketball text-blue-500'></i> Ekstrakurikuler
                                </a>

                                {{-- MITRA INDUSTRI (DISINI) --}}
                                <a href="{{ route('home') }}#mitra"
                                    class="block px-5 py-3 text-sm font-medium text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition-colors border-b border-gray-50 flex items-center gap-2">
                                    <i class='bx bx-briefcase-alt text-blue-500'></i> Mitra Industri
                                </a>

                                <a href="{{ route('galeri.index') }}"
                                    class="block px-5 py-3 text-sm font-medium text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition-colors border-b border-gray-50 flex items-center gap-2">
                                    <i class='bx bx-photo-album text-blue-500'></i> Galeri
                                </a>
                                <a href="#"
                                    class="block px-5 py-3 text-sm font-medium text-gray-400 hover:bg-gray-50 hover:text-gray-500 transition-colors flex items-center gap-2 cursor-not-allowed"
                                    title="Segera Hadir">
                                    <i class='bx bx-user-voice'></i> Daftar Guru
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- D. DROPDOWN HUBUNGI KAMI (Kontak, Info SPMB) --}}
                    <div class="relative dropdown-group" data-target="kontak">
                        <button type="button"
                            class="dropdown-btn px-3 py-2 text-sm font-semibold text-gray-700 hover:text-blue-600 transition-all duration-300 flex items-center gap-1 focus:outline-none relative group">
                            Hubungi Kami
                            <span
                                class="absolute bottom-0 left-0 w-full h-0.5 bg-blue-600 transform scale-x-0 transition-transform duration-300 group-hover:scale-x-100"></span>
                            <i class='bx bx-chevron-down text-lg transition-transform duration-300 dropdown-arrow'></i>
                        </button>

                        <div
                            class="dropdown-menu absolute left-0 mt-4 w-52 bg-white rounded-xl shadow-xl border border-blue-100/50 opacity-0 invisible transform -translate-y-2 transition-all duration-300 z-50">
                            <div
                                class="absolute -top-2 left-6 w-4 h-4 bg-white border-t border-l border-blue-100/50 transform rotate-45">
                            </div>
                            <div class="relative bg-white rounded-xl overflow-hidden py-1">
                                <a href="{{ route('kontak') }}"
                                    class="block px-5 py-3 text-sm font-medium text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition-colors border-b border-gray-50 flex items-center gap-2">
                                    <i class='bx bx-phone text-blue-500'></i> Kontak
                                </a>
                                <a href="{{ $spmbUrl }}/cek_status"
                                    class="block px-5 py-3 text-sm font-medium text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition-colors flex items-center gap-2">
                                    <i class='bx bxs-user-plus text-blue-500'></i> Info SPMB
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- E. DATA ALUMNI --}}
                    <a href="#"
                        class="nav-link px-3 py-2 text-sm font-semibold text-gray-400 hover:text-gray-500 transition-all duration-300 relative group cursor-not-allowed"
                        title="Segera Hadir">
                        Data Alumni
                    </a>

                </div>

                {{-- 3. CTA BUTTON (DESKTOP) --}}
                <div class="hidden lg:flex items-center ml-4">
                    <a href="{{ $spmbUrl }}/cek_status"
                        class="group relative inline-flex items-center justify-center px-6 py-2.5 overflow-hidden font-bold text-white transition-all duration-300 bg-blue-600 rounded-full hover:bg-blue-700 shadow-md hover:shadow-lg hover:shadow-blue-500/30 focus:outline-none ring-offset-2 focus:ring-2 ring-blue-500">
                        <span
                            class="absolute w-0 h-0 transition-all duration-500 ease-out bg-white rounded-full group-hover:w-56 group-hover:h-56 opacity-10"></span>
                        <span class="relative flex items-center gap-2 text-sm">
                            <i class='bx bxs-user-plus text-lg'></i>
                            Daftar SPMB
                        </span>
                    </a>
                </div>

                {{-- 4. MOBILE MENU TOGGLE BUTTON --}}
                <div class="flex items-center lg:hidden">
                    <button id="mobile-menu-btn" type="button"
                        class="inline-flex items-center justify-center p-2 rounded-xl text-gray-700 hover:text-blue-600 hover:bg-blue-50 focus:outline-none transition-all active:scale-95"
                        aria-label="Buka menu navigasi">
                        <i class='bx bx-menu-alt-right text-3xl' id="menu-icon"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- 5. MOBILE MENU FULL SCREEN --}}
        <div id="mobile-menu"
            class="lg:hidden fixed top-0 right-0 w-full max-w-sm h-screen bg-white shadow-2xl transition-all duration-500 ease-in-out transform translate-x-full opacity-0 z-50 overflow-y-auto">

            <div class="flex flex-col h-full relative">

                {{-- Header Mobile Menu --}}
                <div class="bg-gradient-to-r from-blue-600 to-blue-800 h-28 absolute top-0 left-0 w-full -z-10"></div>

                <div class="flex items-center justify-between p-6 pt-8 mb-2 text-white">
                    <div>
                        <span class="font-bold text-xl tracking-wide block">Menu Navigasi</span>
                        <span class="text-blue-100 text-sm">{{ Str::limit($namaSekolah, 25) }}</span>
                    </div>
                    <button id="mobile-menu-close-btn"
                        class="p-2 rounded-full bg-white/20 text-white hover:bg-white/30 backdrop-blur-sm transition-all focus:outline-none shadow-sm active:scale-90">
                        <i class='bx bx-x text-2xl'></i>
                    </button>
                </div>

                {{-- Menu Card Container --}}
                <div class="flex-1 px-4 pb-8 -mt-2">
                    <div
                        class="bg-white rounded-2xl shadow-xl shadow-blue-900/5 border border-blue-50 overflow-hidden font-medium">
                        <div class="py-2">

                            {{-- Mobile: Beranda --}}
                            <a href="{{ route('home') }}"
                                class="mobile-nav-link flex items-center gap-4 px-6 py-4 text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-all border-b border-gray-50">
                                <div
                                    class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600">
                                    <i class='bx bx-home-alt text-xl'></i>
                                </div>
                                <span class="text-base">Beranda</span>
                            </a>

                            {{-- Mobile: Informasi (Accordion) --}}
                            <div class="border-b border-gray-50">
                                <button
                                    class="mobile-submenu-btn w-full flex items-center justify-between px-6 py-4 text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-all group"
                                    data-target="mobile-informasi">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600">
                                            <i class='bx bx-info-circle text-xl'></i>
                                        </div>
                                        <span class="text-base">Informasi</span>
                                    </div>
                                    <i
                                        class='bx bx-chevron-down text-xl transition-transform duration-300 group-hover:text-blue-600 arrow-icon'></i>
                                </button>
                                <div id="mobile-informasi"
                                    class="max-h-0 overflow-hidden transition-all duration-500 ease-in-out bg-blue-50/50">
                                    <div class="pl-[4.5rem] pr-6 py-3 space-y-1">
                                        <a href="{{ route('home') }}#agenda"
                                            class="mobile-link block py-2 text-sm text-gray-600 hover:text-blue-700 border-l-2 border-transparent hover:border-blue-500 pl-3 transition-all">Agenda</a>
                                        <a href="{{ route('home') }}#berita"
                                            class="mobile-link block py-2 text-sm text-gray-600 hover:text-blue-700 border-l-2 border-transparent hover:border-blue-500 pl-3 transition-all">Berita</a>
                                    </div>
                                </div>
                            </div>

                            {{-- Mobile: Profil (Accordion) --}}
                            <div class="border-b border-gray-50">
                                <button
                                    class="mobile-submenu-btn w-full flex items-center justify-between px-6 py-4 text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-all group"
                                    data-target="mobile-profil">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600">
                                            <i class='bx bx-user-pin text-xl'></i>
                                        </div>
                                        <span class="text-base">Profil</span>
                                    </div>
                                    <i
                                        class='bx bx-chevron-down text-xl transition-transform duration-300 group-hover:text-blue-600 arrow-icon'></i>
                                </button>
                                <div id="mobile-profil"
                                    class="max-h-0 overflow-hidden transition-all duration-500 ease-in-out bg-blue-50/50">
                                    <div class="pl-[4.5rem] pr-6 py-3 space-y-1">
                                        <a href="{{ route('profil.sekolah') }}"
                                            class="mobile-link block py-2 text-sm text-gray-600 hover:text-blue-700 border-l-2 border-transparent hover:border-blue-500 pl-3 transition-all">Tentang
                                            Sekolah</a>
                                        <a href="{{ route('home') }}#jurusan"
                                            class="mobile-link block py-2 text-sm text-gray-600 hover:text-blue-700 border-l-2 border-transparent hover:border-blue-500 pl-3 transition-all">Program
                                            Keahlian</a>
                                        <a href="{{ route('home') }}#fasilitas"
                                            class="mobile-link block py-2 text-sm text-gray-600 hover:text-blue-700 border-l-2 border-transparent hover:border-blue-500 pl-3 transition-all">Fasilitas</a>

                                        {{-- Mobile Submenu Additions --}}
                                        <a href="{{ route('home') }}#ekskul"
                                            class="mobile-link block py-2 text-sm text-gray-600 hover:text-blue-700 border-l-2 border-transparent hover:border-blue-500 pl-3 transition-all">Ekstrakurikuler</a>
                                        <a href="{{ route('home') }}#mitra"
                                            class="mobile-link block py-2 text-sm text-gray-600 hover:text-blue-700 border-l-2 border-transparent hover:border-blue-500 pl-3 transition-all">Mitra
                                            Industri</a>

                                        <a href="{{ route('galeri.index') }}"
                                            class="mobile-link block py-2 text-sm text-gray-600 hover:text-blue-700 border-l-2 border-transparent hover:border-blue-500 pl-3 transition-all">Galeri</a>
                                        <a href="#"
                                            class="mobile-link block py-2 text-sm text-gray-400 cursor-not-allowed pl-3 transition-all">Daftar
                                            Guru (Soon)</a>
                                    </div>
                                </div>
                            </div>

                            {{-- Mobile: Hubungi Kami (Accordion) --}}
                            <div class="border-b border-gray-50">
                                <button
                                    class="mobile-submenu-btn w-full flex items-center justify-between px-6 py-4 text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-all group"
                                    data-target="mobile-kontak">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600">
                                            <i class='bx bx-phone-call text-xl'></i>
                                        </div>
                                        <span class="text-base">Hubungi Kami</span>
                                    </div>
                                    <i
                                        class='bx bx-chevron-down text-xl transition-transform duration-300 group-hover:text-blue-600 arrow-icon'></i>
                                </button>
                                <div id="mobile-kontak"
                                    class="max-h-0 overflow-hidden transition-all duration-500 ease-in-out bg-blue-50/50">
                                    <div class="pl-[4.5rem] pr-6 py-3 space-y-1">
                                        <a href="{{ route('kontak') }}"
                                            class="block px-5 py-3 text-sm font-medium text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition-colors border-b border-gray-50 flex items-center gap-2">
                                            <i class='bx bx-phone text-blue-500'></i> Kontak
                                        </a>
                                        <a href="{{ $spmbUrl }}/cek_status"
                                            class="mobile-link block py-2 text-sm text-gray-600 hover:text-blue-700 border-l-2 border-transparent hover:border-blue-500 pl-3 transition-all">Info
                                            SPMB</a>
                                    </div>
                                </div>
                            </div>

                            {{-- Mobile: Data Alumni --}}
                            <a href="#"
                                class="mobile-nav-link flex items-center gap-4 px-6 py-4 text-gray-400 hover:bg-gray-50 transition-all cursor-not-allowed">
                                <div
                                    class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-400">
                                    <i class='bx bxs-graduation text-xl'></i>
                                </div>
                                <span class="text-base">Data Alumni</span>
                            </a>

                        </div>
                    </div>

                    {{-- CTA Button Mobile --}}
                    <div class="mt-8">
                        <a href="{{ $spmbUrl }}/cek_status"
                            class="flex w-full items-center justify-center px-6 py-4 border border-transparent text-base font-bold rounded-xl text-white bg-gradient-to-r from-blue-600 to-blue-800 hover:from-blue-700 hover:to-blue-900 shadow-lg shadow-blue-500/30 transition-all transform hover:-translate-y-1 active:scale-98">
                            <i class='bx bxs-user-plus mr-2 text-xl'></i>
                            Daftar SPMB Sekarang
                        </a>
                    </div>
                </div>

                {{-- Footer Kecil di Menu --}}
                <div class="p-6 text-center text-xs text-gray-400 border-t border-gray-50 mt-auto bg-white">
                    &copy; {{ date('Y') }} {{ Str::limit($namaSekolah, 20) }}
                </div>
            </div>
        </div>

        {{-- Overlay Gelap --}}
        <div id="mobile-menu-overlay"
            class="fixed inset-0 bg-blue-900/20 backdrop-blur-sm z-40 hidden opacity-0 transition-opacity duration-500">
        </div>
    </nav>
</header>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // --- Elements ---
    const navbar = document.getElementById('navbar');

    // 1. DESKTOP DROPDOWN LOGIC (GENERIC UNTUK BANYAK DROPDOWN)
    const dropdownGroups = document.querySelectorAll('.dropdown-group');

    function closeAllDropdowns() {
        dropdownGroups.forEach(group => {
            const menu = group.querySelector('.dropdown-menu');
            const arrow = group.querySelector('.dropdown-arrow');
            const btn = group.querySelector('.dropdown-btn');

            menu.classList.add('opacity-0', 'invisible', '-translate-y-2');
            menu.classList.remove('opacity-100', 'visible', 'translate-y-0');
            arrow.classList.remove('rotate-180');
            btn.setAttribute('aria-expanded', 'false');
            group.classList.remove('open');
        });
    }

    dropdownGroups.forEach(group => {
        const btn = group.querySelector('.dropdown-btn');
        const menu = group.querySelector('.dropdown-menu');
        const arrow = group.querySelector('.dropdown-arrow');

        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const isOpen = group.classList.contains('open');

            // Tutup semua dropdown lain dulu
            closeAllDropdowns();

            // Jika tadi belum terbuka, sekarang buka
            if (!isOpen) {
                menu.classList.remove('opacity-0', 'invisible', '-translate-y-2');
                menu.classList.add('opacity-100', 'visible', 'translate-y-0');
                arrow.classList.add('rotate-180');
                btn.setAttribute('aria-expanded', 'true');
                group.classList.add('open');
            }
        });
    });

    // Tutup saat klik di luar
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.dropdown-group')) {
            closeAllDropdowns();
        }
    });

    // 2. MOBILE MENU LOGIC
    const mobileBtn = document.getElementById('mobile-menu-btn');
    const mobileCloseBtn = document.getElementById('mobile-menu-close-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    const mobileOverlay = document.getElementById('mobile-menu-overlay');

    function openMobileMenu() {
        mobileOverlay.classList.remove('hidden');
        setTimeout(() => mobileOverlay.classList.remove('opacity-0'), 10);
        mobileMenu.classList.remove('translate-x-full', 'opacity-0');
        mobileMenu.classList.add('translate-x-0', 'opacity-100');
        document.body.style.overflow = 'hidden';
    }

    function closeMobileMenu() {
        mobileMenu.classList.add('translate-x-full', 'opacity-0');
        mobileMenu.classList.remove('translate-x-0', 'opacity-100');
        mobileOverlay.classList.add('opacity-0');
        setTimeout(() => mobileOverlay.classList.add('hidden'), 500);
        document.body.style.overflow = '';
    }

    mobileBtn?.addEventListener('click', (e) => {
        e.stopPropagation();
        openMobileMenu();
    });

    mobileCloseBtn?.addEventListener('click', closeMobileMenu);
    mobileOverlay?.addEventListener('click', closeMobileMenu);

    // 3. MOBILE SUBMENU ACCORDION LOGIC
    const mobileSubmenuBtns = document.querySelectorAll('.mobile-submenu-btn');

    mobileSubmenuBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const targetId = btn.getAttribute('data-target');
            const targetMenu = document.getElementById(targetId);
            const arrow = btn.querySelector('.arrow-icon');

            const isOpen = targetMenu.style.maxHeight && targetMenu.style.maxHeight !== '0px';

            if (isOpen) {
                targetMenu.style.maxHeight = '0px';
                arrow.classList.remove('rotate-180');
            } else {
                targetMenu.style.maxHeight = targetMenu.scrollHeight + "px";
                arrow.classList.add('rotate-180');
            }
        });
    });

    // 4. SCROLL EFFECT
    window.addEventListener('scroll', () => {
        if (window.scrollY > 20) {
            navbar.classList.remove('shadow-lg');
            navbar.classList.add('shadow-md');
        } else {
            navbar.classList.add('shadow-lg');
            navbar.classList.remove('shadow-md');
        }
    });

    // 5. ACTIVE STATE
    function setActiveLink() {
        const currentPath = window.location.pathname;
        const allLinks = document.querySelectorAll('.nav-link, .mobile-nav-link'); // Hanya link utama

        allLinks.forEach(link => {
            try {
                const linkUrl = new URL(link.getAttribute('href'));
                if (linkUrl.pathname === currentPath && currentPath !== '/') {
                    link.classList.remove('text-gray-700');
                    link.classList.add('text-blue-600');
                }
            } catch (e) {}
        });
    }
    setActiveLink();
});
</script>