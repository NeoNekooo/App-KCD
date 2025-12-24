<section id="testimoni" class="py-24 bg-slate-50 relative overflow-hidden">
    {{-- Background Decoration (Modern Blobs) --}}
    <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 bg-blue-100 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-pulse"></div>
    <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 bg-cyan-100 rounded-full mix-blend-multiply filter blur-3xl opacity-50"></div>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        
        {{-- Header Section --}}
        <div class="text-center mb-16 max-w-3xl mx-auto">
            <span class="text-blue-600 font-bold tracking-wider uppercase text-sm">Testimoni</span>
            <h2 class="text-3xl md:text-4xl font-extrabold text-slate-800 mt-2 mb-4">Apa Kata Mereka?</h2>
            <div class="h-1.5 w-24 bg-blue-600 mx-auto rounded-full mb-6"></div>
            <p class="text-slate-500 text-lg">
                Cerita nyata dari siswa, alumni, dan orang tua tentang pengalaman pendidikan terbaik di sekolah kami.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
            
            {{-- BAGIAN KIRI: SLIDER TESTIMONI (OUTPUT) --}}
            <div class="lg:col-span-7 w-full order-2 lg:order-1">
                <div class="swiper mySwiperTestimoni !pb-14">
                    <div class="swiper-wrapper">
                        @forelse($testimonis as $item)
                        <div class="swiper-slide">
                            <div class="bg-white p-8 md:p-10 rounded-3xl shadow-lg border border-slate-100 h-full relative mx-2 my-2">
                                {{-- Icon Quote Besar --}}
                                <div class="absolute top-6 right-8 text-blue-100">
                                    <i class='bx bxs-quote-right text-6xl'></i>
                                </div>

                                {{-- Isi Pesan --}}
                                <div class="relative z-10 mb-8">
                                    <div class="flex text-yellow-400 mb-4 text-lg">
                                        <i class='bx bxs-star'></i><i class='bx bxs-star'></i><i class='bx bxs-star'></i><i class='bx bxs-star'></i><i class='bx bxs-star'></i>
                                    </div>
                                    <p class="text-slate-600 text-lg leading-relaxed italic font-medium">
                                        "{{ $item->isi }}"
                                    </p>
                                </div>

                                {{-- Profil User --}}
                                <div class="flex items-center gap-4 pt-6 border-t border-slate-100">
                                    <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-700 rounded-full flex items-center justify-center text-white font-bold text-2xl shadow-md ring-4 ring-blue-50">
                                        {{ substr($item->nama, 0, 1) }}
                                    </div>
                                    <div>
                                        <h4 class="text-slate-800 font-bold text-lg">{{ $item->nama }}</h4>
                                        <span class="text-blue-600 text-sm font-semibold tracking-wide bg-blue-50 px-2 py-0.5 rounded-md">{{ $item->status }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="swiper-slide">
                            <div class="bg-white p-10 rounded-3xl text-center border border-dashed border-slate-300">
                                <i class='bx bx-message-square-dots text-4xl text-slate-300 mb-3'></i>
                                <p class="text-slate-400">Belum ada testimoni yang ditampilkan.</p>
                            </div>
                        </div>
                        @endforelse
                    </div>
                    
                    {{-- Navigasi Slider Custom --}}
                    <div class="swiper-pagination !bottom-0"></div>
                </div>
            </div>

            {{-- BAGIAN KANAN: FORM INPUT (INPUT) --}}
            <div class="lg:col-span-5 w-full order-1 lg:order-2">
                <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden sticky top-24">
                    <div class="bg-blue-600 p-6 text-center">
                        <h3 class="text-xl font-bold text-white">Bagikan Pengalaman Anda</h3>
                        <p class="text-blue-100 text-sm mt-1">Masukan Anda menjadi motivasi bagi kami.</p>
                    </div>
                    
                    <div class="p-8 space-y-5">
                        <form action="{{ route('testimoni.store') }}" method="POST" id="formTestimoni">
                            @csrf
                            
                            {{-- Input Nama --}}
                            <div class="group">
                                <label class="block text-slate-700 text-sm font-bold mb-2 ml-1">Nama Lengkap</label>
                                <div class="relative transition-all duration-300">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class='bx bx-user text-slate-400 text-lg group-focus-within:text-blue-500 transition-colors'></i>
                                    </div>
                                    <input type="text" name="nama" class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all placeholder-slate-400" placeholder="Ketik nama Anda..." required>
                                </div>
                            </div>

                            {{-- Input Status --}}
                            <div class="group">
                                <label class="block text-slate-700 text-sm font-bold mb-2 ml-1">Status / Jabatan</label>
                                <div class="relative transition-all duration-300">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class='bx bx-id-card text-slate-400 text-lg group-focus-within:text-blue-500 transition-colors'></i>
                                    </div>
                                    <input type="text" name="status" class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all placeholder-slate-400" placeholder="Contoh: Alumni 2023" required>
                                </div>
                            </div>

                            {{-- Input Pesan --}}
                            <div class="group">
                                <label class="block text-slate-700 text-sm font-bold mb-2 ml-1">Testimoni</label>
                                <textarea name="isi" rows="4" class="w-full p-4 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all placeholder-slate-400 resize-none" placeholder="Tuliskan cerita pengalaman Anda di sini..." required></textarea>
                            </div>

                            <button type="submit" class="w-full bg-blue-600 text-white font-bold py-4 rounded-xl hover:bg-blue-700 active:scale-[0.98] transition-all shadow-lg shadow-blue-600/30 flex items-center justify-center gap-2 mt-2">
                                <span>Kirim Testimoni</span>
                                <i class='bx bx-send'></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- 
        [DIHAPUS] KODE TOAST NOTIFICATION DI SINI DIHAPUS 
        KARENA SUDAH DITANGANI OLEH MASTER LAYOUT 
    --}}

</section>

{{-- Library Assets --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Setup Swiper (Slider) - HANYA INI YANG DISISAKAN DI SINI
        var swiper = new Swiper(".mySwiperTestimoni", {
            slidesPerView: 1, 
            spaceBetween: 20,
            loop: true, 
            autoplay: {
                delay: 4000,
                disableOnInteraction: false,
            },
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            },
            breakpoints: {
                768: {
                    slidesPerView: 1, 
                    spaceBetween: 30,
                },
                1024: {
                    slidesPerView: 1.2, 
                    spaceBetween: 30,
                }
            },
            grabCursor: true,
        });

        // [DIHAPUS] LOGIC TOAST DI SINI DIHAPUS AGAR TIDAK BENTROK
    });
</script>

<style>
    /* Custom Styling untuk Pagination Dots Swiper */
    .swiper-pagination-bullet {
        width: 12px;
        height: 12px;
        background-color: #cbd5e1; /* Slate-300 */
        opacity: 1;
        transition: all 0.3s ease;
    }
    .swiper-pagination-bullet-active {
        background-color: #2563eb; /* Blue-600 */
        width: 30px; /* Memanjang saat aktif */
        border-radius: 6px;
    }
</style>