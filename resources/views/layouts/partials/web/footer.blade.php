{{-- FILE: resources/views/layouts/partials/web/footer.blade.php --}}

@php
    $sekolah = \App\Models\Sekolah::first();
    $namaSekolah = $sekolah ? $sekolah->nama : 'SEKOLAHKU';
    $alamat = $sekolah ? $sekolah->alamat_jalan : 'Alamat belum diisi';
    $kota = $sekolah ? $sekolah->kabupaten_kota : '';
    $telepon = $sekolah ? $sekolah->nomor_telepon : null;
    $email = $sekolah ? $sekolah->email : null;
    $peta = $sekolah ? $sekolah->peta : null;
    $fb = $sekolah ? $sekolah->facebook_url : null;
    $ig = $sekolah ? $sekolah->instagram_url : null;
    $yt = $sekolah ? $sekolah->youtube_url : null;
    $tt = $sekolah ? $sekolah->tiktok_url : null;
@endphp

<footer class="bg-gray-900 text-white pt-16 pb-8 font-sans relative">
    <div class="container mx-auto px-6 md:px-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-12">
            
            {{-- KOLOM 1: IDENTITAS & SOSMED --}}
            <div>
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-500/30">
                        <i class='bx bxs-school text-2xl'></i>
                    </div>
                    <h3 class="text-xl font-bold tracking-tight text-white">{{ Str::limit($namaSekolah, 20) }}</h3>
                </div>
                <p class="text-gray-400 text-sm leading-relaxed mb-6">
                    Mewujudkan generasi berprestasi, berkarakter, dan berwawasan global dengan pendidikan berkualitas.
                </p>
                
                {{-- Social Media Icons --}}
                <div class="flex gap-3">
                    @if($fb)
                        <a href="{{ $fb }}" target="_blank" class="w-10 h-10 rounded-lg bg-gray-800 flex items-center justify-center text-gray-400 hover:bg-[#1877F2] hover:text-white transition-all duration-300 transform hover:scale-110" title="Facebook">
                            <i class='bx bxl-facebook text-xl'></i>
                        </a>
                    @endif
                    @if($ig)
                        <a href="{{ $ig }}" target="_blank" class="w-10 h-10 rounded-lg bg-gray-800 flex items-center justify-center text-gray-400 hover:bg-[#E4405F] hover:text-white transition-all duration-300 transform hover:scale-110" title="Instagram">
                            <i class='bx bxl-instagram text-xl'></i>
                        </a>
                    @endif
                    @if($yt)
                        <a href="{{ $yt }}" target="_blank" class="w-10 h-10 rounded-lg bg-gray-800 flex items-center justify-center text-gray-400 hover:bg-[#FF0000] hover:text-white transition-all duration-300 transform hover:scale-110" title="YouTube">
                            <i class='bx bxl-youtube text-xl'></i>
                        </a>
                    @endif
                    @if($tt)
                        <a href="{{ $tt }}" target="_blank" class="w-10 h-10 rounded-lg bg-gray-800 flex items-center justify-center text-gray-400 hover:bg-black hover:text-white transition-all duration-300 transform hover:scale-110" title="TikTok">
                            <i class='bx bxl-tiktok text-xl'></i>
                        </a>
                    @endif
                </div>
            </div>

            {{-- KOLOM 2: JELAJAHI (LINK DIPERBAIKI) --}}
            <div>
                <h4 class="text-lg font-bold mb-6 text-white border-b border-blue-500/50 pb-2 inline-block">Jelajahi</h4>
                <ul class="space-y-3 text-sm text-gray-400">
                    {{-- Link Absolut ke Home --}}
                    <li><a href="{{ route('home') }}" class="hover:text-blue-400 transition-colors duration-300 flex items-center gap-2 group"><i class='bx bx-chevron-right text-blue-400/50 group-hover:translate-x-1 transition-transform'></i> Beranda</a></li>
                    
                    {{-- Link ke Section di Home --}}
                    <li><a href="{{ route('home') }}#profil" class="hover:text-blue-400 transition-colors duration-300 flex items-center gap-2 group"><i class='bx bx-chevron-right text-blue-400/50 group-hover:translate-x-1 transition-transform'></i> Profil Sekolah</a></li>
                    <li><a href="{{ route('home') }}#jurusan" class="hover:text-blue-400 transition-colors duration-300 flex items-center gap-2 group"><i class='bx bx-chevron-right text-blue-400/50 group-hover:translate-x-1 transition-transform'></i> Kompetensi Keahlian</a></li>
                    <li><a href="{{ route('home') }}#fasilitas" class="hover:text-blue-400 transition-colors duration-300 flex items-center gap-2 group"><i class='bx bx-chevron-right text-blue-400/50 group-hover:translate-x-1 transition-transform'></i> Fasilitas</a></li>
                    
                    {{-- Link ke Halaman Lain (Tetap Aman) --}}
                    <li><a href="/spmb/cek_status" class="hover:text-blue-400 transition-colors duration-300 flex items-center gap-2 group"><i class='bx bx-chevron-right text-blue-400/50 group-hover:translate-x-1 transition-transform'></i> Info SPMB</a></li>
                </ul>
            </div>

            {{-- KOLOM 3: HUBUNGI KAMI --}}
            <div>
                <h4 class="text-lg font-bold mb-6 text-white border-b border-blue-500/50 pb-2 inline-block">Hubungi Kami</h4>
                <ul class="space-y-4 text-sm text-gray-400">
                    <li class="flex items-start gap-3">
                        <div class="mt-1 bg-gray-800 p-2 rounded-lg text-blue-400">
                            <i class='bx bxs-map'></i>
                        </div>
                        <span class="leading-relaxed">
                            {{ $alamat }} {{ $kota ? ', '.$kota : '' }}
                        </span>
                    </li>
                    @if($telepon)
                    <li class="flex items-center gap-3">
                        <div class="bg-gray-800 p-2 rounded-lg text-blue-400">
                            <i class='bx bxs-phone'></i>
                        </div>
                        <a href="tel:{{ $telepon }}" class="hover:text-blue-400 transition-colors duration-300">
                            {{ $telepon }}
                        </a>
                    </li>
                    @endif
                    @if($email)
                    <li class="flex items-center gap-3">
                        <div class="bg-gray-800 p-2 rounded-lg text-blue-400">
                            <i class='bx bxs-envelope'></i>
                        </div>
                        <a href="mailto:{{ $email }}" class="hover:text-blue-400 transition-colors duration-300">
                            {{ $email }}
                        </a>
                    </li>
                    @endif
                </ul>
            </div>

            {{-- KOLOM 4: LOKASI --}}
            <div>
                <h4 class="text-lg font-bold mb-6 text-white border-b border-blue-500/50 pb-2 inline-block">Lokasi Sekolah</h4>
                <div class="w-full h-48 bg-gray-800 rounded-xl overflow-hidden shadow-inner border border-gray-700 relative group">
                    @if($peta)
                        <div class="w-full h-full [&>iframe]:w-full [&>iframe]:h-full grayscale group-hover:grayscale-0 transition-all duration-500">
                            {!! $peta !!}
                        </div>
                    @else
                        <div class="w-full h-full flex flex-col items-center justify-center text-gray-500 gap-2">
                            <i class='bx bxs-map-alt text-4xl opacity-50'></i>
                            <span class="text-xs">Peta belum tersedia</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- COPYRIGHT --}}
        <div class="border-t border-gray-800 pt-8 mt-8 text-center md:text-left flex flex-col md:flex-row justify-between items-center text-sm text-gray-500">
            <p>© {{ date('Y') }} <strong class="text-gray-300">{{ $namaSekolah }}</strong>. All rights reserved.</p>
            <p class="mt-2 md:mt-0 flex items-center gap-1">
                Dikembangkan oleh <a href="#" class="text-blue-400 hover:underline">Teaching Factory HexaNusa</a>
            </p>
        </div>
    </div>

    {{-- BACK TO TOP BUTTON --}}
    <button id="back-to-top" class="fixed bottom-8 right-8 bg-blue-600 text-white p-3 rounded-full shadow-lg shadow-blue-500/50 opacity-0 invisible transition-all duration-300 hover:bg-blue-700 focus:outline-none z-50">
        <i class='bx bx-up-arrow-alt text-xl'></i>
    </button>
</footer>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // --- Elements ---
        const backToTopBtn = document.getElementById('back-to-top');

        // --- 1. Back to Top Button Logic ---
        // Tampilkan tombol saat scroll ke bawah
        window.addEventListener('scroll', () => {
            if (window.scrollY > 300) {
                backToTopBtn.classList.remove('opacity-0', 'invisible');
                backToTopBtn.classList.add('opacity-100', 'visible');
            } else {
                backToTopBtn.classList.add('opacity-0', 'invisible');
                backToTopBtn.classList.remove('opacity-100', 'visible');
            }
        });

        // Smooth scroll ke atas saat tombol diklik
        backToTopBtn.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    });
</script>