
        <footer class="py-10 bg-footer-dark text-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 md:grid-cols-3 gap-8 lg:gap-12 pb-6">

                    <!-- Kolom 1: Profil -->
                    <div data-aos="fade-up" data-aos-delay="100" class="col-span-2 md:col-span-1">
                        <h4 class="text-xl font-extrabold mb-4 text-secondary-green">{{ $profilSekolah->singkatan ?? ''}}</h4>
                        <p class="text-gray-400 text-sm leading-relaxed">
                            Mencetak generasi unggul yang siap menghadapi tantangan global dengan integritas dan
                            inovasi.
                        </p>
                    </div>

                    <!-- Kolom 2: Tautan Cepat -->
                    <div data-aos="fade-up" data-aos-delay="200">
                        <h4 class="text-lg font-semibold mb-4 text-gray-300">Tautan Cepat</h4>
                        <ul class="space-y-3 text-sm">
                            <li><a href="{{ route('ppdb.kompetensiKeahlian') }}"
                                    class="text-gray-400 hover:text-secondary-green transition duration-300">Kompetensi
                                    Keahlian</a></li>
                            <li><a href="{{ route('ppdb.beranda') }}/#program"
                                    class="text-gray-400 hover:text-secondary-green transition duration-300">Keunggulan
                                    Sekolah</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-secondary-green transition duration-300">FAQ
                                    PPDB</a></li>
                            <li><a href="#"
                                    class="text-gray-400 hover:text-secondary-green transition duration-300">Biaya
                                    Pendidikan</a></li>
                        </ul>
                    </div>

                    <!-- Kolom 3: Ikuti Kami (Dipindahkan dari Section Kontak) -->
                    <div data-aos="fade-up" data-aos-delay="300" class="col-span-1">
                        <h4 class="text-lg font-semibold mb-4 text-gray-300">Ikuti Kami</h4>
                        <div class="flex space-x-4 text-2xl">
                            <!-- Facebook - Tambahkan class 'social-link' dan 'facebook' -->
                            <a href="https://facebook.com/{{ $kontakPpdb->facebook }}" target="_blank"
                                class="social-link facebook text-gray-400 transition duration-300">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <!-- Instagram - Tambahkan class 'social-link' dan 'instagram' -->
                            <a href="https://instagram.com/{{ $kontakPpdb->instagram }}" target="_blank"
                                class="social-link instagram text-gray-400 transition duration-300">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <!-- YouTube - Tambahkan class 'social-link' dan 'youtube' -->
                            <a href="https://youtube.com/{{ $kontakPpdb->youtube }}" target="_blank"
                                class="social-link youtube text-gray-400 transition duration-300">
                                <i class="fab fa-youtube"></i>
                            </a>
                        </div>
                    </div>

                </div>

                <!-- Copyright Line -->
                <hr class="border-gray-700 mt-6 mb-4">
                <div class="text-center">
                    <!-- TAHUN HAK CIPTA DIGANTI DI SINI -->
                    <p class="text-sm text-gray-500">&copy; 2025 SMAKNIS. Hak Cipta Dilindungi.</p>
                </div>
            </div>
        </footer>