@extends('layouts.frontend')

@section('title', 'Tentang Kami - ' . ($instansi->nama_instansi ?? 'Kantor Cabang Dinas'))

@push('styles')
<!-- Swiper CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<style>
    .cms-content {
        font-size: 1.1rem;
        line-height: 1.8;
        color: #334155;
    }
    .cms-content p { margin-bottom: 1.5rem; }
    
    /* Layout kartu visi misi */
    .card-visi {
        background-color: #f0f9ff;
        border-left: 6px solid #0284c7;
    }
    .card-misi {
        background-color: #f0fdf4;
        border-left: 6px solid #16a34a;
    }
    
    /* Slider Container */
    .about-slider-frame {
        position: relative;
        border-radius: 2rem;
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(0,0,0,0.1);
        background: #f1f5f9;
    }
    
    .about-swiper {
        width: 100%;
        height: 100%;
    }

    .swiper-slide img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Custom Swiper Buttons */
    .swiper-button-next, .swiper-button-prev {
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        width: 40px;
        height: 40px;
        border-radius: 12px;
        color: #fff;
    }
    .swiper-button-next:after, .swiper-button-prev:after {
        font-size: 18px;
        font-weight: bold;
    }
    .swiper-pagination-bullet-active {
        background: #fff !important;
        width: 24px !important;
        border-radius: 5px !important;
    }

    .animate-up {
        opacity: 0;
        transform: translateY(20px);
        animation: fadeInUp 0.6s ease-out forwards;
    }
    @keyframes fadeInUp {
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

@section('content')
<!-- Hero Header Simpel -->
<div class="bg-blue-900 py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-3xl md:text-5xl font-black text-white uppercase tracking-tight mb-4">Tentang Kami</h1>
        <p class="text-blue-100 text-lg max-w-2xl mx-auto opacity-80">{{ $instansi->nama_instansi ?? 'Kantor Cabang Dinas' }}</p>
    </div>
</div>

<div class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Bagian Sejarah & Slider Foto -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start mb-20">
            
            <!-- Teks Sejarah (Kiri) -->
            <div class="lg:col-span-7 animate-up">
                <h2 class="text-2xl font-black text-blue-900 uppercase tracking-wide mb-6 flex items-center gap-3">
                    <span class="w-10 h-1 bg-blue-600 rounded-full"></span>
                    Sejarah Singkat
                </h2>
                <div class="cms-content">
                    @if($instansi && $instansi->sejarah_singkat)
                        {!! $instansi->sejarah_singkat !!}
                    @else
                        <p class="text-slate-400 italic">Data sejarah belum tersedia.</p>
                    @endif
                </div>
            </div>

            <!-- Frame Slider Foto (Kanan) -->
            <div class="lg:col-span-5 animate-up sticky top-28" style="animation-delay: 200ms;">
                <div class="about-slider-frame h-[400px] md:h-[500px]">
                    @php
                        $fotos = [];
                        if($instansi->foto_profil) $fotos[] = $instansi->foto_profil;
                        if($instansi->foto_sejarah && is_array($instansi->foto_sejarah)) {
                            $fotos = array_merge($fotos, $instansi->foto_sejarah);
                        }
                    @endphp

                    @if(count($fotos) > 0)
                        <div class="swiper about-swiper">
                            <div class="swiper-wrapper">
                                @foreach($fotos as $foto)
                                    <div class="swiper-slide">
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent z-10"></div>
                                        <img src="{{ Storage::url($foto) }}" alt="Dokumentasi Instansi">
                                    </div>
                                @endforeach
                            </div>
                            <!-- Navigasi jika foto lebih dari 1 -->
                            @if(count($fotos) > 1)
                                <div class="swiper-button-next"></div>
                                <div class="swiper-button-prev"></div>
                                <div class="swiper-pagination"></div>
                            @endif
                        </div>
                    @else
                        <div class="w-full h-full flex flex-col items-center justify-center text-slate-400 bg-slate-50">
                            <i class='bx bx-image-alt fs-1 mb-2'></i>
                            <p class="text-xs font-bold uppercase tracking-widest">Belum ada foto</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Bagian Visi & Misi Simpel Modern -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 animate-up" style="animation-delay: 400ms;">
            
            <!-- Kartu Visi -->
            <div class="card-visi p-8 md:p-12 rounded-[2rem] shadow-sm">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-12 h-12 rounded-2xl bg-blue-600 text-white flex items-center justify-center shadow-lg">
                        <i class='bx bx-target-lock fs-3'></i>
                    </div>
                    <h3 class="text-2xl font-black text-blue-900 uppercase tracking-tight">Visi</h3>
                </div>
                <div class="text-slate-700 text-lg leading-relaxed font-medium italic">
                    @if($instansi && $instansi->visi)
                        {!! $instansi->visi !!}
                    @else
                        "Terwujudnya pelayanan pendidikan yang berkualitas dan berdaya saing."
                    @endif
                </div>
            </div>

            <!-- Kartu Misi -->
            <div class="card-misi p-8 md:p-12 rounded-[2rem] shadow-sm">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-12 h-12 rounded-2xl bg-green-600 text-white flex items-center justify-center shadow-lg">
                        <i class='bx bx-list-check fs-3'></i>
                    </div>
                    <h3 class="text-2xl font-black text-green-900 uppercase tracking-tight">Misi</h3>
                </div>
                <div class="cms-content text-slate-700">
                    @if($instansi && $instansi->misi)
                        {!! $instansi->misi !!}
                    @else
                        <ul class="space-y-2">
                            <li>Meningkatkan kualitas sumber daya manusia.</li>
                            <li>Mengoptimalkan tata kelola administrasi.</li>
                        </ul>
                    @endif
                </div>
            </div>

        </div>

    </div>
</div>

@push('scripts')
<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const swiper = new Swiper('.about-swiper', {
            loop: true,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            effect: 'fade',
            fadeEffect: {
                crossFade: true
            },
        });
    });
</script>
@endpush
@endsection
