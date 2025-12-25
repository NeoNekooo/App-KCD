@extends('landing.web.layouts.master_web')

@section('title', 'Galeri Sekolah')

@section('content')

{{-- =====================================================================
     1. HEADER SECTION (BIRU KONSISTEN)
====================================================================== --}}
<section class="pt-32 pb-16 bg-blue-600 relative overflow-hidden">
    
    {{-- Decorative Background (Sama seperti navbar/button) --}}
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-white opacity-10 rounded-full blur-[80px]"></div>
        <div class="absolute bottom-0 left-0 w-72 h-72 bg-white opacity-5 rounded-full blur-[60px]"></div>
    </div>
    
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
        
        {{-- Breadcrumb (Putih Bersih) --}}
        <div class="inline-flex items-center gap-2 text-sm text-blue-50 mb-6 font-medium animate-fade-in-up">
            <a href="{{ route('home') }}" class="hover:text-white transition-colors">Beranda</a>
            <i class='bx bx-chevron-right text-blue-200'></i>
            <span class="bg-white/10 backdrop-blur-sm px-3 py-1 rounded-full text-white text-xs font-bold uppercase tracking-wide border border-white/20">
                Galeri
            </span>
        </div>

        <h1 class="text-3xl md:text-5xl font-extrabold text-white mb-6 leading-tight animate-fade-in-up delay-100 drop-shadow-sm">
            Galeri Kegiatan Sekolah
        </h1>
        
        <p class="text-blue-100 max-w-2xl mx-auto text-lg leading-relaxed animate-fade-in-up delay-200">
            Merekam jejak langkah, prestasi, dan momen kebersamaan civitas akademika dalam mewujudkan visi sekolah yang unggul.
        </p>
    </div>
</section>

{{-- =====================================================================
     2. GRID GALERI UTAMA
====================================================================== --}}
<section class="py-16 md:py-24 bg-white relative">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        
        @if($galeris->count() > 0)
            {{-- Grid Layout Responsif --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 lg:gap-8">
                
                @foreach($galeris as $key => $item)
                {{-- Kartu Galeri (Tambahkan onclick untuk membuka Lightbox) --}}
                <div class="group relative rounded-3xl overflow-hidden bg-white shadow-sm border border-slate-100 hover:shadow-2xl hover:shadow-blue-600/10 transition-all duration-500 transform hover:-translate-y-1 animate-on-scroll opacity-0 translate-y-8 cursor-pointer" 
                     style="animation-delay: {{ $key * 50 }}ms;"
                     onclick="openLightbox('{{ asset('storage/galeris/'.$item->foto) }}', '{{ $item->judul }}')">
                    
                    {{-- Container Gambar (Aspect Square 1:1) --}}
                    <div class="aspect-square relative overflow-hidden bg-slate-100">
                        <img src="{{ asset('storage/galeris/'.$item->foto) }}" 
                             class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" 
                             loading="lazy"
                             alt="{{ $item->judul }}">
                        
                        {{-- Overlay Hover Biru Konsisten --}}
                        <div class="absolute inset-0 bg-blue-600/80 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center backdrop-blur-[2px]">
                            <div class="w-14 h-14 bg-white rounded-full flex items-center justify-center text-blue-600 shadow-lg transform scale-50 group-hover:scale-100 transition-transform duration-300 delay-100">
                                <i class='bx bx-zoom-in text-2xl'></i>
                            </div>
                        </div>
                    </div>

                    {{-- Informasi Singkat --}}
                    <div class="p-5">
                        <div class="flex items-center gap-2 mb-2 text-xs font-bold text-slate-400 uppercase tracking-wider">
                            <i class='bx bx-calendar text-blue-600 text-sm'></i>
                            {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 leading-snug line-clamp-2 group-hover:text-blue-600 transition-colors">
                            {{ $item->judul }}
                        </h3>
                    </div>

                </div>
                @endforeach

            </div>

            {{-- Pagination --}}
            <div class="mt-16 flex justify-center">
                {{ $galeris->links('pagination::tailwind') }}
            </div>

        @else
            {{-- Tampilan Kosong --}}
            <div class="flex flex-col items-center justify-center py-20 text-center animate-fade-in-up">
                <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mb-6 text-slate-300 border border-slate-100">
                    <i class='bx bxs-image-alt text-5xl'></i>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">Galeri Belum Tersedia</h3>
                <p class="text-slate-500 max-w-md mx-auto">
                    Saat ini belum ada foto kegiatan yang diunggah. Silakan kembali lagi nanti.
                </p>
                <a href="{{ route('home') }}" class="mt-8 inline-flex items-center gap-2 px-6 py-3 bg-blue-600 border border-transparent rounded-full text-white font-bold hover:bg-blue-700 transition-all shadow-lg hover:shadow-blue-600/30">
                    <i class='bx bx-arrow-back'></i> Kembali ke Beranda
                </a>
            </div>
        @endif

    </div>
</section>

{{-- =====================================================================
     3. LIGHTBOX MODAL (POPUP ZOOM FOTO)
====================================================================== --}}
<div id="lightbox" class="fixed inset-0 z-[999] bg-black/95 hidden opacity-0 transition-opacity duration-300 flex items-center justify-center p-4">
    
    {{-- Tombol Close --}}
    <button onclick="closeLightbox()" class="absolute top-6 right-6 text-white/70 hover:text-white transition-colors p-2 focus:outline-none z-50">
        <i class='bx bx-x text-5xl'></i>
    </button>

    {{-- Kontainer Foto --}}
    <div class="relative max-w-5xl w-full max-h-[85vh] flex flex-col items-center">
        <img id="lightbox-img" src="" alt="Zoom" class="max-w-full max-h-[80vh] object-contain rounded-lg shadow-2xl transform scale-95 transition-transform duration-300">
        
        {{-- Caption Foto --}}
        <p id="lightbox-caption" class="text-white text-center mt-4 font-medium text-lg tracking-wide"></p>
    </div>
</div>

@endsection

@push('styles')
<style>
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-up {
        animation: fadeInUp 0.8s ease-out forwards;
        opacity: 0;
    }
    .delay-100 { animation-delay: 0.1s; }
    .delay-200 { animation-delay: 0.2s; }

    .animate-on-scroll {
        transition: opacity 0.6s ease-out, transform 0.6s ease-out;
    }
    .animate-on-scroll.visible {
        opacity: 1 !important;
        transform: translateY(0) !important;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // 1. Animasi Scroll
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.animate-on-scroll').forEach(el => observer.observe(el));
});

// 2. Fungsi Lightbox (Zoom Foto)
const lightbox = document.getElementById('lightbox');
const lightboxImg = document.getElementById('lightbox-img');
const lightboxCaption = document.getElementById('lightbox-caption');

function openLightbox(imageUrl, title) {
    // Set sumber gambar dan caption
    lightboxImg.src = imageUrl;
    lightboxCaption.textContent = title;

    // Tampilkan modal (hapus hidden, tambah flex)
    lightbox.classList.remove('hidden');
    lightbox.classList.add('flex');

    // Animasi Fade In (sedikit delay agar transisi CSS berjalan)
    setTimeout(() => {
        lightbox.classList.remove('opacity-0');
        lightboxImg.classList.remove('scale-95');
        lightboxImg.classList.add('scale-100');
    }, 10);

    // Kunci scroll body
    document.body.style.overflow = 'hidden';
}

function closeLightbox() {
    // Animasi Fade Out
    lightbox.classList.add('opacity-0');
    lightboxImg.classList.remove('scale-100');
    lightboxImg.classList.add('scale-95');

    // Sembunyikan modal setelah animasi selesai
    setTimeout(() => {
        lightbox.classList.remove('flex');
        lightbox.classList.add('hidden');
        lightboxImg.src = ''; // Reset gambar
    }, 300); // Sesuai durasi transition-opacity duration-300

    // Buka kunci scroll body
    document.body.style.overflow = '';
}

// Tutup lightbox jika klik di luar gambar (area gelap)
lightbox.addEventListener('click', (e) => {
    if (e.target === lightbox) {
        closeLightbox();
    }
});

// Tutup dengan tombol ESC keyboard
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && !lightbox.classList.contains('hidden')) {
        closeLightbox();
    }
});
</script>
@endpush