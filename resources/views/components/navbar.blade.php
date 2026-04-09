<nav x-data="{ mobileMenuOpen: false, scrolled: false, openDropdown: null }" 
     @scroll.window="scrolled = (window.pageYOffset > 20) ? true : false"
     :class="{ 'bg-white/95 backdrop-blur-md shadow-lg py-3': scrolled, 'bg-blue-900 py-4 md:py-5': !scrolled }"
     class="fixed w-full top-0 z-50 transition-all duration-500 ease-in-out">
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center">
            <!-- Logo Section -->
            <div class="flex items-center min-w-0 flex-1 lg:flex-initial">
                <a href="/" class="flex items-center space-x-3 group min-w-0">
                    <div class="transition-transform duration-500 group-hover:scale-110 flex-shrink-0 flex items-center justify-center overflow-hidden w-12 h-12 md:w-14 md:h-14">
                        @php
                            $instansi = \App\Models\Instansi::first();
                            $siteLogo = $instansi ? $instansi->logo : null;
                            $siteName = $instansi ? $instansi->nama_instansi : 'Kantor Cabang Dinas';
                            $siteSlogan = $instansi ? ($instansi->wilayah ?? 'Provinsi Jawa Barat') : 'Provinsi Jawa Barat';
                        @endphp
                        @if($siteLogo)
                            <img src="{{ Storage::url($siteLogo) }}" 
                                 class="max-w-full max-h-full object-contain filter drop-shadow-md" 
                                 alt="Logo"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div :class="{ 'bg-blue-600 text-white': scrolled, 'bg-white text-blue-600': !scrolled }" 
                                 class="w-full h-full rounded-2xl items-center justify-center font-black text-xl shadow-lg transition-colors duration-500" style="display:none;">
                                KCD
                            </div>
                        @else
                            <div :class="{ 'bg-blue-600 text-white': scrolled, 'bg-white text-blue-600': !scrolled }" 
                                 class="w-full h-full rounded-2xl flex items-center justify-center font-black text-xl shadow-lg transition-colors duration-500">
                                KCD
                            </div>
                        @endif
                    </div>
                    <div class="flex flex-col min-w-0">
                        <span :class="{ 'text-blue-900': scrolled, 'text-white': !scrolled }" 
                              class="font-black text-sm md:text-base lg:text-lg leading-tight tracking-tight transition-colors duration-500">
                            {{ $siteName }}
                        </span>
                        <span :class="{ 'text-blue-500': scrolled, 'text-blue-100': !scrolled }" 
                              class="text-[10px] md:text-[11px] font-bold uppercase tracking-[0.15em] mt-0.5 transition-colors duration-500">
                            {{ $siteSlogan }}
                        </span>
                    </div>
                </a>
            </div>

            <!-- Desktop Navigation -->
            <div class="hidden lg:block">
                <div class="flex items-center space-x-1">
                    <!-- Home -->
                    <a href="/" class="px-4 py-2 text-sm font-bold tracking-wide transition-all rounded-xl"
                       :class="{ 'text-blue-900 hover:bg-blue-50': scrolled, 'text-white hover:bg-white/10': !scrolled }">
                        Beranda
                    </a>

                    <!-- Profil Dropdown -->
                    <div class="relative" @mouseenter="openDropdown = 'profil'" @mouseleave="openDropdown = null">
                        <button class="flex items-center px-4 py-2 text-sm font-bold tracking-wide transition-all rounded-xl outline-none"
                                :class="{ 'text-blue-900 hover:bg-blue-50': scrolled, 'text-white hover:bg-white/10': !scrolled }">
                            <span>Profil</span>
                            <svg class="w-4 h-4 ml-1 transition-transform duration-200" :class="{'rotate-180': openDropdown === 'profil'}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="openDropdown === 'profil'" x-transition class="absolute left-0 mt-1 w-56 bg-white rounded-2xl shadow-2xl border border-gray-100 py-3 z-50">
                            <a href="/tentang-kami" class="block px-6 py-2.5 text-sm font-bold text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition">Tentang Kami</a>
                            <a href="/struktur-organisasi" class="block px-6 py-2.5 text-sm font-bold text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition">Struktur Organisasi</a>
                        </div>
                    </div>

                    <!-- Layanan Dropdown -->
                    <div class="relative" @mouseenter="openDropdown = 'layanan'" @mouseleave="openDropdown = null">
                        <button class="flex items-center px-4 py-2 text-sm font-bold tracking-wide transition-all rounded-xl outline-none"
                                :class="{ 'text-blue-900 hover:bg-blue-50': scrolled, 'text-white hover:bg-white/10': !scrolled }">
                            <span>Layanan</span>
                            <svg class="w-4 h-4 ml-1 transition-transform duration-200" :class="{'rotate-180': openDropdown === 'layanan'}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="openDropdown === 'layanan'" x-transition class="absolute left-0 mt-1 w-64 bg-white rounded-2xl shadow-2xl border border-gray-100 py-3 z-50">
                            <a href="/layanan/pengaduan" class="block px-6 py-2.5 text-sm font-bold text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition">Layanan Pengaduan</a>
                            <a href="/layanan/administrasi-ptk" class="block px-6 py-2.5 text-sm font-bold text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition">Administrasi PTK</a>
                            <a href="/layanan/tata-kelola" class="block px-6 py-2.5 text-sm font-bold text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition">Tata Kelola Sekolah</a>
                        </div>
                    </div>

                    <!-- Satuan Pendidikan Dropdown (NEW) -->
                    <div class="relative" @mouseenter="openDropdown = 'satdik'" @mouseleave="openDropdown = null" x-data="{ openSub: false }">
                        <button class="flex items-center px-4 py-2 text-sm font-bold tracking-wide transition-all rounded-xl outline-none"
                                :class="{ 'text-blue-900 hover:bg-blue-50': scrolled, 'text-white hover:bg-white/10': !scrolled }">
                            <span>Satuan Pendidikan</span>
                            <svg class="w-4 h-4 ml-1 transition-transform duration-200" :class="{'rotate-180': openDropdown === 'satdik'}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="openDropdown === 'satdik'" x-transition class="absolute left-0 mt-1 w-56 bg-white rounded-2xl shadow-2xl border border-gray-100 py-3 z-50">
                            
                            <!-- Submenu: Lembaga -->
                            <div class="relative group/sub" @mouseenter="openSub = true" @mouseleave="openSub = false">
                                <div class="flex items-center justify-between px-6 py-2.5 text-sm font-bold text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition cursor-pointer">
                                    <span>Lembaga</span>
                                    <svg class="w-4 h-4 -rotate-90" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                                
                                <!-- Nested Dropdown (Kabupaten/Kota) -->
                                <div x-show="openSub" x-transition class="absolute left-full top-0 ml-0.5 w-64 bg-white rounded-2xl shadow-2xl border border-gray-100 py-3 z-50">
                                    @php
                                        $kabupatens = \App\Models\Sekolah::select('kabupaten_kota')->distinct()->whereNotNull('kabupaten_kota')->orderBy('kabupaten_kota', 'asc')->get();
                                    @endphp
                                    @forelse($kabupatens as $kab)
                                        <a href="{{ url('/lembaga?kabupaten=' . urlencode($kab->kabupaten_kota)) }}" class="block px-6 py-2.5 text-sm font-bold text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition">
                                            {{ $kab->kabupaten_kota }}
                                        </a>
                                    @empty
                                        <span class="block px-6 py-2.5 text-sm text-gray-400 italic">Data belum tersedia</span>
                                    @endforelse
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Informasi Dropdown -->
                    <div class="relative" @mouseenter="openDropdown = 'informasi'" @mouseleave="openDropdown = null">
                        <button class="flex items-center px-4 py-2 text-sm font-bold tracking-wide transition-all rounded-xl outline-none"
                                :class="{ 'text-blue-900 hover:bg-blue-50': scrolled, 'text-white hover:bg-white/10': !scrolled }">
                            <span>Informasi</span>
                            <svg class="w-4 h-4 ml-1 transition-transform duration-200" :class="{'rotate-180': openDropdown === 'informasi'}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="openDropdown === 'informasi'" x-transition class="absolute left-0 mt-1 w-56 bg-white rounded-2xl shadow-2xl border border-gray-100 py-3 z-50">
                            <a href="/berita" class="block px-6 py-2.5 text-sm font-bold text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition">Berita</a>
                            <a href="/pengumuman" class="block px-6 py-2.5 text-sm font-bold text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition">Pengumuman</a>
                            <a href="/galeri" class="block px-6 py-2.5 text-sm font-bold text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition">Galeri Kegiatan</a>
                            <a href="/unduhan" class="block px-6 py-2.5 text-sm font-bold text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition">Unduhan</a>
                            <div class="border-t border-gray-100 my-2"></div>
                            <a href="/kontak" class="block px-6 py-2.5 text-sm font-bold text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition">Kontak</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile Menu Button -->
            <div class="lg:hidden flex items-center">
                <button @click="mobileMenuOpen = !mobileMenuOpen" :class="{ 'text-blue-900': scrolled, 'text-white': !scrolled }" class="p-2 outline-none">
                    <svg class="h-7 w-7" x-show="!mobileMenuOpen" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                    </svg>
                    <svg class="h-7 w-7" x-show="mobileMenuOpen" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-cloak>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu Container -->
    <div x-show="mobileMenuOpen" x-transition class="lg:hidden absolute top-full left-0 w-full bg-white shadow-2xl border-t border-gray-100 overflow-y-auto max-h-[80vh] py-4" x-cloak>
        <div class="px-4 space-y-1">
            <a href="/" class="block px-6 py-4 rounded-2xl text-base font-bold text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition">Beranda</a>
            
            <!-- Profil Mobile -->
            <div x-data="{ open: false }">
                <button @click="open = !open" class="w-full flex items-center justify-between px-6 py-4 rounded-2xl text-base font-bold text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition outline-none">
                    <span>Profil</span>
                    <svg class="w-4 h-4 transition-transform" :class="{'rotate-180': open}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" class="pl-6 space-y-1 bg-gray-50 rounded-2xl mb-2">
                    <a href="/tentang-kami" class="block px-6 py-3 text-sm font-bold text-gray-500">Tentang Kami</a>
                    <a href="/struktur-organisasi" class="block px-6 py-3 text-sm font-bold text-gray-500">Struktur Organisasi</a>
                </div>
            </div>

            <!-- Layanan Mobile -->
            <div x-data="{ open: false }">
                <button @click="open = !open" class="w-full flex items-center justify-between px-6 py-4 rounded-2xl text-base font-bold text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition outline-none">
                    <span>Layanan</span>
                    <svg class="w-4 h-4 transition-transform" :class="{'rotate-180': open}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" class="pl-6 space-y-1 bg-gray-50 rounded-2xl mb-2">
                    <a href="/layanan/pengaduan" class="block px-6 py-3 text-sm font-bold text-gray-500">Layanan Pengaduan</a>
                    <a href="/layanan/administrasi-ptk" class="block px-6 py-3 text-sm font-bold text-gray-500">Administrasi PTK</a>
                    <a href="/layanan/tata-kelola" class="block px-6 py-3 text-sm font-bold text-gray-500">Tata Kelola Sekolah</a>
                </div>
            </div>

            <!-- Satuan Pendidikan Mobile (NEW) -->
            <div x-data="{ open: false, openLembaga: false }">
                <button @click="open = !open" class="w-full flex items-center justify-between px-6 py-4 rounded-2xl text-base font-bold text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition outline-none">
                    <span>Satuan Pendidikan</span>
                    <svg class="w-4 h-4 transition-transform" :class="{'rotate-180': open}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" class="pl-6 space-y-1 bg-gray-50 rounded-2xl mb-2">
                    <button @click="openLembaga = !openLembaga" class="w-full flex items-center justify-between px-6 py-3 text-sm font-bold text-gray-500 outline-none">
                        <span>Lembaga</span>
                        <svg class="w-3 h-3 transition-transform" :class="{'rotate-180': openLembaga}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="openLembaga" class="pl-4 pb-2 space-y-1">
                        @php
                            $kabupatens = \App\Models\Sekolah::select('kabupaten_kota')->distinct()->whereNotNull('kabupaten_kota')->orderBy('kabupaten_kota', 'asc')->get();
                        @endphp
                        @forelse($kabupatens as $kab)
                            <a href="{{ url('/lembaga?kabupaten=' . urlencode($kab->kabupaten_kota)) }}" class="block px-6 py-2 text-xs font-bold text-gray-400 hover:text-blue-600">
                                {{ $kab->kabupaten_kota }}
                            </a>
                        @empty
                            <span class="block px-6 py-2 text-xs text-gray-400 italic">Data belum tersedia</span>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Informasi Mobile -->
            <div x-data="{ open: false }">
                <button @click="open = !open" class="w-full flex items-center justify-between px-6 py-4 rounded-2xl text-base font-bold text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition outline-none">
                    <span>Informasi</span>
                    <svg class="w-4 h-4 transition-transform" :class="{'rotate-180': open}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" class="pl-6 space-y-1 bg-gray-50 rounded-2xl mb-2">
                    <a href="/berita" class="block px-6 py-3 text-sm font-bold text-gray-500">Berita</a>
                    <a href="/pengumuman" class="block px-6 py-3 text-sm font-bold text-gray-500">Pengumuman</a>
                    <a href="/galeri" class="block px-6 py-3 text-sm font-bold text-gray-500">Galeri Kegiatan</a>
                    <a href="/unduhan" class="block px-6 py-3 text-sm font-bold text-gray-500">Unduhan</a>
                    <a href="/kontak" class="block px-6 py-3 text-sm font-bold text-gray-500 border-t border-gray-200 mt-1 pt-2">Kontak</a>
                </div>
            </div>
        </div>
    </div>
</nav>
