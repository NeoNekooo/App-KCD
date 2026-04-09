@extends('layouts.frontend')

@section('title', 'Beranda - Kantor Cabang Dinas')

@section('content')
<!-- Hero Slider Section -->
@php
    $sliders = \App\Models\Slider::where('is_active', true)->orderBy('order')->orderBy('created_at', 'desc')->get();
@endphp

<div x-data="{ 
    activeSlide: 1, 
    slides: [
        @foreach($sliders as $slider)
        { id: {{ $loop->iteration }}, image: '{{ Storage::url($slider->image) }}', title: '{{ addslashes($slider->title) }}', subtitle: '{{ addslashes($slider->subtitle) }}' },
        @endforeach
    ],
    @if($sliders->isEmpty())
    slides: [
        { id: 1, image: 'https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=1920&q=80', title: 'Melayani dengan Prima', subtitle: 'Membangun Masa Depan Pendidikan Indonesia' }
    ],
    @endif
    next() { this.activeSlide = this.activeSlide === this.slides.length ? 1 : this.activeSlide + 1 },
    prev() { this.activeSlide = this.activeSlide === 1 ? this.slides.length : this.activeSlide - 1 },
    init() { if(this.slides.length > 1) setInterval(() => this.next(), 6000) }
}" class="relative h-[100dvh] min-h-[500px] max-h-[800px] overflow-hidden bg-blue-900 -mt-24 md:-mt-28">
    
    <!-- Slides -->
    <template x-for="slide in slides" :key="slide.id">
        <div x-show="activeSlide === slide.id" 
             x-transition:enter="transition ease-out duration-1000"
             x-transition:enter-start="opacity-0 scale-105"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-1000"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="absolute inset-0">
            
            <!-- Background Image with Overlay -->
            <div class="absolute inset-0 bg-blue-950/50 z-10"></div>
            <img :src="slide.image" 
                 class="w-full h-full object-cover" 
                 :alt="slide.title">

            <!-- Content in the middle -->
            <div class="absolute inset-0 z-20 flex items-center justify-center px-4">
                <div class="text-center max-w-4xl">
                    <h1 x-text="slide.title" 
                        class="text-4xl md:text-6xl lg:text-7xl font-black text-white mb-6 tracking-tight drop-shadow-2xl">
                    </h1>
                    <p x-text="slide.subtitle" 
                       class="text-lg md:text-2xl text-blue-100 font-medium mb-10 drop-shadow-lg uppercase tracking-[0.2em]">
                    </p>
                    <div class="flex flex-col sm:flex-row items-center justify-center space-y-4 sm:space-y-0 sm:space-x-4">
                        <a href="/layanan" class="w-full sm:w-auto px-8 py-4 bg-white text-blue-900 font-bold rounded-xl shadow-xl hover:bg-blue-50 transition duration-300 transform hover:-translate-y-1">
                            Lihat Layanan
                        </a>
                        <a href="/tentang-kami" class="w-full sm:w-auto px-8 py-4 bg-transparent border-2 border-white/50 backdrop-blur-sm text-white font-bold rounded-xl hover:bg-white/10 transition duration-300 transform hover:-translate-y-1">
                            Profil Kami
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <!-- Navigation Buttons -->
    <button @click="prev()" class="absolute left-4 top-1/2 -translate-y-1/2 z-30 p-2 rounded-full bg-white/10 text-white hover:bg-white/30 transition backdrop-blur-md hidden md:block">
        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
    </button>
    <button @click="next()" class="absolute right-4 top-1/2 -translate-y-1/2 z-30 p-2 rounded-full bg-white/10 text-white hover:bg-white/30 transition backdrop-blur-md hidden md:block">
        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
    </button>

    <!-- Indicators -->
    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 z-30 flex space-x-3">
        <template x-for="slide in slides" :key="slide.id">
            <button @click="activeSlide = slide.id" 
                    :class="activeSlide === slide.id ? 'bg-white w-10' : 'bg-white/40 w-3'"
                    class="h-1.5 rounded-full transition-all duration-500">
            </button>
        </template>
    </div>
</div>

@php
    $welcome = \App\Models\WelcomeMessage::first();
@endphp

@if($welcome)
<!-- Welcome Message Section -->
<div class="py-24 bg-white overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 items-center">
            <!-- Foto Pimpinan -->
            <div class="lg:col-span-4 relative group">
                <div class="absolute -inset-4 bg-blue-600/5 rounded-[3rem] -rotate-3 group-hover:rotate-0 transition duration-700"></div>
                <div class="relative rounded-[2.5rem] overflow-hidden shadow-2xl border-8 border-white aspect-[4/5] max-w-[320px] mx-auto lg:mx-0">
                    @if($welcome->image)
                        <img src="{{ Storage::url($welcome->image) }}" class="w-full h-full object-cover transition duration-700 group-hover:scale-110">
                    @else
                        <div class="w-full h-full bg-gray-100 flex items-center justify-center">
                            <svg class="w-20 h-20 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                    @endif
                </div>
                <!-- Identity Badge -->
                <div class="absolute -bottom-6 -right-2 bg-blue-900 p-6 rounded-2xl shadow-2xl text-white hidden md:block z-10">
                    <h4 class="font-black text-base leading-tight uppercase tracking-widest">{{ $welcome->pimpinan_name ?? 'Pimpinan KCD' }}</h4>
                    <p class="text-blue-300 text-[10px] font-bold uppercase mt-1.5 tracking-[0.2em]">{{ $welcome->pimpinan_role ?? 'Kepala Kantor' }}</p>
                </div>
            </div>

            <!-- Isi Sambutan -->
            <div class="lg:col-span-8 space-y-8">
                <div>
                    <h2 class="text-[10px] font-black text-blue-600 uppercase tracking-[0.3em] mb-4">Kata Sambutan</h2>
                    <h3 class="text-4xl md:text-5xl font-black text-blue-950 leading-tight">
                        {{ $welcome->title }}
                    </h3>
                </div>
                
                <div class="prose prose-lg prose-blue text-gray-600 font-medium leading-relaxed max-w-none">
                    {!! $welcome->content !!}
                </div>

                <div class="pt-4 flex items-center space-x-4 md:hidden">
                    <div class="flex flex-col">
                        <span class="font-black text-blue-950 uppercase tracking-widest">{{ $welcome->pimpinan_name }}</span>
                        <span class="text-xs text-blue-600 font-bold uppercase tracking-widest">{{ $welcome->pimpinan_role }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Features/Services Highlight -->
<div class="py-20 bg-gray-50/50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <h2 class="text-[9px] font-black text-blue-600 uppercase tracking-[0.4em] mb-3">Core Services</h2>
            <p class="text-3xl md:text-4xl font-black text-blue-950 leading-tight mb-4">
                Komitmen Kami dalam Melayani Pendidikan
            </p>
            <p class="text-base text-gray-500 font-medium leading-relaxed italic">
                "Mewujudkan tata kelola pendidikan yang transparan, akuntabel, dan berorientasi pada kemajuan bersama."
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Service Card 1 -->
            <div class="group relative bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 hover:shadow-2xl hover:shadow-blue-200/40 transition-all duration-500 hover:-translate-y-1.5 overflow-hidden">
                <!-- Hover Accent -->
                <div class="absolute top-0 right-0 w-24 h-24 bg-blue-600/5 rounded-bl-full translate-x-8 -translate-y-8 group-hover:translate-x-0 group-hover:translate-y-0 transition-transform duration-700"></div>
                
                <div class="relative z-10">
                    <div class="h-16 w-16 bg-blue-100 rounded-2xl flex items-center justify-center text-blue-600 mb-8 group-hover:scale-110 group-hover:bg-blue-600 group-hover:text-white transition-all duration-500 shadow-inner">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-black text-blue-950 mb-3 tracking-tight">Administrasi PTK</h3>
                    <p class="text-sm text-gray-500 leading-relaxed font-medium mb-6">
                        Layanan pengurusan data Pendidik dan Tenaga Kependidikan dengan proses yang transparan dan akurat.
                    </p>
                    <a href="/layanan/administrasi-ptk" class="inline-flex items-center text-blue-600 font-black text-[10px] uppercase tracking-widest group/link">
                        Selengkapnya 
                        <svg class="w-3.5 h-3.5 ml-1.5 transform group-hover/link:translate-x-1.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M17 8l4 4m0 0l-4-4m4-4H3" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Service Card 2 -->
            <div class="group relative bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 hover:shadow-2xl hover:shadow-blue-200/40 transition-all duration-500 hover:-translate-y-1.5 overflow-hidden border-b-4 border-b-blue-600/20">
                <div class="absolute top-0 right-0 w-24 h-24 bg-blue-600/5 rounded-bl-full translate-x-8 -translate-y-8 group-hover:translate-x-0 group-hover:translate-y-0 transition-transform duration-700"></div>
                
                <div class="relative z-10">
                    <div class="h-16 w-16 bg-blue-100 rounded-2xl flex items-center justify-center text-blue-600 mb-8 group-hover:scale-110 group-hover:bg-blue-600 group-hover:text-white transition-all duration-500 shadow-inner">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-black text-blue-950 mb-3 tracking-tight">Tata Kelola Sekolah</h3>
                    <p class="text-sm text-gray-500 leading-relaxed font-medium mb-6">
                        Pembinaan intensif untuk memastikan standar mutu pendidikan tercapai di setiap satuan pendidikan binaan.
                    </p>
                    <a href="/layanan/tata-kelola" class="inline-flex items-center text-blue-600 font-black text-[10px] uppercase tracking-widest group/link">
                        Selengkapnya 
                        <svg class="w-3.5 h-3.5 ml-1.5 transform group-hover/link:translate-x-1.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M17 8l4 4m0 0l-4-4m4-4H3" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Service Card 3 -->
            <div class="group relative bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 hover:shadow-2xl hover:shadow-blue-200/40 transition-all duration-500 hover:-translate-y-1.5 overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-blue-600/5 rounded-bl-full translate-x-8 -translate-y-8 group-hover:translate-x-0 group-hover:translate-y-0 transition-transform duration-700"></div>
                
                <div class="relative z-10">
                    <div class="h-16 w-16 bg-blue-100 rounded-2xl flex items-center justify-center text-blue-600 mb-8 group-hover:scale-110 group-hover:bg-blue-600 group-hover:text-white transition-all duration-500 shadow-inner">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-black text-blue-950 mb-3 tracking-tight">Layanan Informasi</h3>
                    <p class="text-sm text-gray-500 leading-relaxed font-medium mb-6">
                        Keterbukaan informasi publik yang memudahkan akses data pendidikan bagi seluruh elemen masyarakat.
                    </p>
                    <a href="/layanan/pengaduan" class="inline-flex items-center text-blue-600 font-black text-[10px] uppercase tracking-widest group/link">
                        Selengkapnya 
                        <svg class="w-3.5 h-3.5 ml-1.5 transform group-hover/link:translate-x-1.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M17 8l4 4m0 0l-4-4m4-4H3" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Rest of the sections remain the same... -->
@endsection
