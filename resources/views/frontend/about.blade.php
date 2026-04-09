@extends('layouts.frontend')

@section('title', 'Profil & Tentang Kami - ' . ($instansi->nama_instansi ?? 'Kantor Cabang Dinas'))

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<style>
    .about-swiper {
        width: 100%;
        height: 100%;
        border-radius: 2.5rem;
    }
    .swiper-pagination-bullet-active {
        background: #fff !important;
        width: 24px !important;
        border-radius: 5px !important;
    }
    .cms-content {
        font-size: 1.05rem;
        line-height: 1.8;
        color: #475569;
    }
    .cms-content p { margin-bottom: 1.5rem; }
    .glass-card {
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.5);
    }
    .animate-in {
        opacity: 0;
        transform: translateY(30px);
        animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
    @keyframes fadeInUp {
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

@section('content')
<!-- Premium Hero Section -->
<div class="relative bg-blue-950 py-28 overflow-hidden">
    <div class="absolute inset-0">
        <div class="absolute top-0 right-0 w-full h-full bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-blue-600 rounded-full blur-[120px] opacity-20"></div>
        <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-indigo-600 rounded-full blur-[120px] opacity-20"></div>
    </div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center animate-in">
        <span class="inline-flex items-center px-4 py-1.5 rounded-full bg-blue-500/20 text-blue-300 text-[10px] font-black uppercase tracking-[0.3em] mb-8 border border-blue-400/30">
            Mengenal Lebih Dekat
        </span>
        <h1 class="text-4xl md:text-6xl font-black text-white mb-8 leading-tight tracking-tight uppercase">
            {{ $instansi->nama_instansi ?? 'Kantor Cabang Dinas' }}
        </h1>
        <p class="text-xl text-blue-100/70 max-w-3xl mx-auto font-light leading-relaxed">
            Berkomitmen dalam menyelenggarakan pelayanan pendidikan yang transparan, akuntabel, dan berorientasi pada masa depan generasi bangsa.
        </p>
    </div>
</div>

<!-- Main Content Area -->
<div class="py-24 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Grid 1: Sejarah & Media -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 mb-32 items-start">
            <!-- Teks Sejarah -->
            <div class="lg:col-span-7 animate-in" style="animation-delay: 100ms;">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-12 h-12 rounded-2xl bg-blue-600 text-white flex items-center justify-center shadow-lg shadow-blue-200">
                        <i class='bx bx-history fs-3'></i>
                    </div>
                    <h2 class="text-3xl font-black text-slate-800 uppercase tracking-tight">Sejarah Singkat</h2>
                </div>
                
                <div class="cms-content">
                    @if($instansi && $instansi->sejarah_singkat)
                        {!! $instansi->sejarah_singkat !!}
                    @else
                        <p class="italic text-slate-400">Narasi sejarah instansi belum dicantumkan oleh administrator.</p>
                    @endif
                </div>
            </div>

            <!-- Slider Foto -->
            <div class="lg:col-span-5 sticky top-28 animate-in" style="animation-delay: 200ms;">
                <div class="relative group">
                    <div class="absolute -inset-4 bg-blue-600/5 rounded-[3rem] blur-xl group-hover:bg-blue-600/10 transition-colors"></div>
                    <div class="relative rounded-[2.5rem] overflow-hidden shadow-2xl bg-slate-200 lg:h-[550px]">
                        @php
                            $fotos = [];
                            if($instansi->foto_profil) $fotos[] = $instansi->foto_profil;
                            if($instansi->foto_sejarah && is_array($instansi->foto_sejarah)) {
                                $fotos = array_merge($fotos, $instansi->foto_sejarah);
                            }
                        @endphp

                        @if(count($fotos) > 1)
                            <div class="swiper about-swiper">
                                <div class="swiper-wrapper">
                                    @foreach($fotos as $foto)
                                    <div class="swiper-slide">
                                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-transparent to-transparent z-10"></div>
                                        <img src="{{ Storage::url($foto) }}" class="w-full h-full object-cover">
                                    </div>
                                    @endforeach
                                </div>
                                <div class="swiper-pagination !bottom-8"></div>
                                <div class="swiper-button-next !text-white/50 hover:!text-white transition after:!text-xl"></div>
                                <div class="swiper-button-prev !text-white/50 hover:!text-white transition after:!text-xl"></div>
                            </div>
                        @elseif(count($fotos) == 1)
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-transparent to-transparent z-10"></div>
                            <img src="{{ Storage::url($fotos[0]) }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center text-slate-400 bg-white">
                                <i class='bx bx-image fs-1 opacity-20'></i>
                                <span class="text-[10px] font-black uppercase mt-4 tracking-widest">Foto belum tersedia</span>
                            </div>
                        @endif

                        <!-- Floating Badge -->
                        <div class="absolute top-6 left-6 z-20">
                            <div class="glass-card px-4 py-2 rounded-xl text-[10px] font-black text-slate-800 uppercase tracking-widest border border-white/50">
                                Dokumentasi Resmi
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grid 2: Visi & Misi (Modern Cards) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
            <!-- Visi -->
            <div class="relative animate-in" style="animation-delay: 300ms;">
                <div class="bg-white rounded-[3rem] p-10 md:p-14 shadow-xl shadow-slate-200/50 border border-slate-100 h-full overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-500/5 rounded-full -mr-16 -mt-16"></div>
                    <div class="w-16 h-16 rounded-3xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-8 shadow-inner">
                        <i class='bx bx-target-lock fs-1'></i>
                    </div>
                    <h3 class="text-3xl font-black text-slate-800 uppercase tracking-tight mb-6">Visi Kami</h3>
                    <div class="cms-content italic text-xl font-medium text-slate-600 leading-relaxed border-l-4 border-emerald-400 pl-6">
                        @if($instansi && $instansi->visi)
                            {!! $instansi->visi !!}
                        @else
                            "Menjadi pusat pelayanan pendidikan yang unggul dan berdaya saing global."
                        @endif
                    </div>
                </div>
            </div>

            <!-- Misi -->
            <div class="relative animate-in" style="animation-delay: 400ms;">
                <div class="bg-white rounded-[3rem] p-10 md:p-14 shadow-xl shadow-slate-200/50 border border-slate-100 h-full overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-blue-500/5 rounded-full -mr-16 -mt-16"></div>
                    <div class="w-16 h-16 rounded-3xl bg-blue-50 text-blue-600 flex items-center justify-center mb-8 shadow-inner">
                        <i class='bx bx-list-check fs-1'></i>
                    </div>
                    <h3 class="text-3xl font-black text-slate-800 uppercase tracking-tight mb-6">Misi Kami</h3>
                    <div class="cms-content">
                        @if($instansi && $instansi->misi)
                            {!! $instansi->misi !!}
                        @else
                            <ul class="space-y-4">
                                <li class="flex items-start gap-3"><i class='bx bx-check-double text-blue-500 fs-4'></i> Meningkatkan tata kelola pendidikan.</li>
                                <li class="flex items-start gap-3"><i class='bx bx-check-double text-blue-500 fs-4'></i> Mengoptimalkan layanan publik.</li>
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        const swiper = new Swiper('.about-swiper', {
            loop: true,
            autoplay: { delay: 5000, disableOnInteraction: false },
            pagination: { el: '.swiper-pagination', clickable: true },
            navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
            effect: 'fade',
            fadeEffect: { crossFade: true },
        });
    </script>
@endpush
@endsection
