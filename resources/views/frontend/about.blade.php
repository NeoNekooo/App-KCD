@extends('layouts.frontend')

@section('title', 'Tentang Kami - ' . ($instansi->nama_instansi ?? 'Kantor Cabang Dinas'))

@push('styles')
<!-- Swiper CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<style>
    .cms-content {
        font-size: 1.05rem;
        line-height: 1.7;
        color: #475569;
    }
    .cms-content p { margin-bottom: 1.2rem; }
    
    /* Slider Container */
    .about-slider-frame {
        position: relative;
        border-radius: 1.5rem;
        overflow: hidden;
        box-shadow: 0 15px 35px rgba(0,0,0,0.08);
        background: #f1f5f9;
    }
    .about-swiper { width: 100%; height: 100%; }
    .swiper-slide img { width: 100%; height: 100%; object-fit: cover; }

    /* Premium Section Vision Mission */
    .section-divider {
        height: 1px;
        background: linear-gradient(to right, transparent, #e2e8f0, transparent);
        margin: 4rem 0;
    }

    .mission-list {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    .mission-item {
        background: #f8fafc;
        padding: 1.5rem;
        border-radius: 1.25rem;
        display: flex;
        gap: 1.25rem;
        transition: all 0.3s ease;
        border: 1px solid #f1f5f9;
    }
    .mission-item:hover {
        background: #fff;
        box-shadow: 0 10px 20px rgba(0,0,0,0.03);
        transform: translateY(-3px);
        border-color: #3b82f6;
    }
    .mission-icon {
        flex-shrink: 0;
        width: 2.5rem;
        height: 2.5rem;
        background: #dbeafe;
        color: #2563eb;
        border-radius: 0.75rem;
        display: flex;
        items-center: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    .animate-up {
        opacity: 0;
        transform: translateY(20px);
        animation: fadeInUp 0.6s ease-out forwards;
    }
    @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }
</style>
@endpush

@section('content')
<!-- Hero Header -->
<div class="bg-blue-900 py-16 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
        <h1 class="text-2xl md:text-4xl font-black text-white uppercase tracking-tight mb-2">Profil & Sejarah</h1>
        <p class="text-blue-200 text-sm md:text-base max-w-2xl mx-auto opacity-80">{{ $instansi->nama_instansi ?? 'Kantor Cabang Dinas' }}</p>
    </div>
</div>

<!-- Main Section -->
<div class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Grid: Sejarah & Foto -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start mb-24">
            <!-- Kiri: Sejarah -->
            <div class="lg:col-span-7 animate-up">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-lg bg-blue-50 text-blue-600 text-[10px] font-black uppercase tracking-widest mb-6">
                    <i class='bx bx-time-five'></i> Kilas Balik
                </div>
                <h2 class="text-2xl font-black text-slate-900 uppercase tracking-tight mb-8">Sejarah Singkat</h2>
                <div class="cms-content text-justify">
                    @if($instansi && $instansi->sejarah_singkat)
                        {!! $instansi->sejarah_singkat !!}
                    @else
                        <p class="text-slate-400 italic">Data sejarah belum tersedia.</p>
                    @endif
                </div>
            </div>

            <!-- Kanan: Slider Foto -->
            <div class="lg:col-span-5 animate-up sticky top-32" style="animation-delay: 200ms;">
                <div class="about-slider-frame h-[350px] md:h-[450px]">
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
                                        <img src="{{ Storage::url($foto) }}" class="w-full h-full object-cover">
                                    </div>
                                @endforeach
                            </div>
                            @if(count($fotos) > 1)
                                <div class="swiper-pagination"></div>
                            @endif
                        </div>
                    @else
                        <div class="w-full h-full flex flex-col items-center justify-center text-slate-300 bg-slate-50">
                            <i class='bx bx-image fs-1 mb-2 opacity-20'></i>
                            <p class="text-[10px] font-black uppercase tracking-widest">Foto tidak tersedia</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="section-divider"></div>

        <!-- Section: Visi -->
        <div class="max-w-4xl mx-auto text-center mb-24 animate-up" style="animation-delay: 300ms;">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-lg bg-emerald-50 text-emerald-600 text-[10px] font-black uppercase tracking-widest mb-6">
                <i class='bx bx-target-lock'></i> Visi Utama
            </div>
            <div class="text-xl md:text-3xl font-bold text-slate-800 leading-relaxed italic">
                @if($instansi && $instansi->visi)
                    {!! str_replace(['<p>', '</p>'], '', $instansi->visi) !!}
                @else
                    "Terwujudnya pelayanan pendidikan yang berkualitas, transparan, dan berdaya saing global."
                @endif
            </div>
        </div>

        <div class="section-divider"></div>

        <!-- Section: Misi -->
        <div class="animate-up" style="animation-delay: 400ms;">
            <div class="text-center mb-12">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-lg bg-blue-50 text-blue-600 text-[10px] font-black uppercase tracking-widest mb-4">
                    <i class='bx bx-list-ul'></i> Misi Strategis
                </div>
                <h2 class="text-2xl font-black text-slate-900 uppercase tracking-tight">Langkah & Upaya Kami</h2>
            </div>

            <div class="max-w-5xl mx-auto">
                <div class="mission-list">
                    @if($instansi && $instansi->misi)
                        <div class="cms-content p-6 bg-slate-50 rounded-[2rem]">
                            {!! $instansi->misi !!}
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="mission-item">
                                <div class="mission-icon"><i class='bx bx-check-double'></i></div>
                                <p class="text-sm font-medium text-slate-600 leading-relaxed">Optimalisasi sistem informasi pendidikan berbasis teknologi digital yang terintegrasi secara menyeluruh.</p>
                            </div>
                            <div class="mission-item">
                                <div class="mission-icon"><i class='bx bx-check-double'></i></div>
                                <p class="text-sm font-medium text-slate-600 leading-relaxed">Peningkatan kualitas layanan administrasi yang responsif, akuntabel, dan berorientasi kepuasan masyarakat.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        new Swiper('.about-swiper', {
            loop: true,
            autoplay: { delay: 5000, disableOnInteraction: false },
            pagination: { el: '.swiper-pagination', clickable: true },
            effect: 'fade',
            fadeEffect: { crossFade: true },
        });
    });
</script>
@endpush
@endsection
