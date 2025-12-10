
        <!-- Navbar -->
        <header class="bg-white shadow-md sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
                <div class="text-2xl font-extrabold text-primary-blue">
                    PPDB {{ $kontakPpdb->singkatan ?? ''}}
                </div>
                <!-- Navigasi Desktop -->
                <nav class="hidden md:flex space-x-8 text-gray-600 font-medium h-full items-center">
                    <!-- Setiap link dibungkus dalam div untuk mengontrol border bawah tanpa memengaruhi flexbox utama -->
                    <a href="{{ route('ppdb.beranda') }}#beranda"
                        class="nav-link nav-link-desktop hover:text-primary-blue transition duration-300 border-b-2 border-transparent py-3.5">Beranda</a>
                    <a href="{{ route('ppdb.beranda') }}#program"
                        class="nav-link nav-link-desktop hover:text-primary-blue transition duration-300 border-b-2 border-transparent py-3.5">Keunggulan</a>
                    <a href="{{ route('ppdb.beranda') }}#alur"
                        class="nav-link nav-link-desktop hover:text-primary-blue transition duration-300 border-b-2 border-transparent py-3.5">Alur
                        Pendaftaran</a>
                    <a href="{{ route('ppdb.kompetensiKeahlian') }}"
                        class="nav-link nav-link-desktop hover:text-primary-blue transition duration-300 border-b-2 border-transparent py-3.5">Kompetensi
                        Keahlian</a>
                    <a href="{{ route('ppdb.daftarCalonSiswa') }}"
                        class="nav-link nav-link-desktop hover:text-primary-blue transition duration-300 border-b-2 border-transparent py-3.5">Daftar
                        Calon Siswa</a>
                    <a href="{{ route('ppdb.kontak') }}"
                        class="nav-link nav-link-desktop hover:text-primary-blue transition duration-300 border-b-2 border-transparent py-3.5">Kontak</a>
                </nav>
                <!-- Tombol CTA Navigasi Utama (Ukuran Ditingkatkan) -->
                <a href="{{ route('ppdb.formulirPendaftaran') }}" class="hidden md:block 
                bg-secondary-green hover:bg-green-600 
                text-white font-bold text-lg 
                py-3 px-8 rounded-full 
                shadow-xl shadow-secondary-green/50 
                transform hover:scale-105 
                transition duration-300 ease-in-out 
                ring-2 ring-transparent hover:ring-secondary-green/50 hover:ring-opacity-70">
                    Daftar PPDB
                </a>
                <!-- Mobile Menu Button (Hamburger) -->
                <button class="md:hidden text-gray-800 focus:outline-none"
                    onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>
            </div>
            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-gray-100">
                <nav class="flex flex-col space-y-2 p-2 text-gray-700 font-medium">
                    <!-- Menambahkan kelas nav-link-mobile dan mengatur padding/    margin -->
                    <a href="/#beranda"
                        class="nav-link nav-link-mobile py-2 px-4 rounded-lg hover:bg-gray-50 transition duration-300 border-l-4 border-transparent">Beranda</a>
                    <a href="/#program"
                        class="nav-link nav-link-mobile py-2 px-4 rounded-lg hover:bg-gray-50 transition duration-300 border-l-4 border-transparent">Keunggulan</a>
                    <a href="/#alur"
                        class="nav-link nav-link-mobile py-2 px-4 rounded-lg hover:bg-gray-50 transition duration-300 border-l-4 border-transparent">Alur
                        Pendaftaran</a>
                    <a href="{{ route('ppdb.kompetensiKeahlian') }}"
                        class="nav-link nav-link-mobile py-2 px-4 rounded-lg hover:bg-gray-50 transition duration-300 border-l-4 border-transparent">Kompetensi
                        Keahlian</a>
                    <!-- Tautan Daftar Calon Siswa di mobile -->
                    <a href="{{ route('ppdb.daftarCalonSiswa') }}"
                        class="nav-link nav-link-mobile py-2 px-4 rounded-lg hover:bg-gray-50 transition duration-300 border-l-4 border-transparent">Daftar
                        Calon Siswa</a>
                    <a href="{{ route('ppdb.kontak') }}"
                        class="nav-link nav-link-mobile py-2 px-4 rounded-lg hover:bg-gray-50 transition duration-300 border-l-4 border-transparent">Kontak</a>
                    <!-- Tombol Daftar di mobile menu -->
                    <a href="{{ route('ppdb.formulirPendaftaran') }}"
                        class="bg-secondary-green hover:bg-green-600 text-white text-center font-semibold py-3 mt-4 rounded-lg transition duration-300 shadow-md">Daftar
                        Sekarang</a>
                </nav>
            </div>
        </header>