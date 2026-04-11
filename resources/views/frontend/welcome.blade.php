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
}" class="relative h-[85dvh] min-h-[450px] max-h-[750px] overflow-hidden bg-slate-900 -mt-24 md:-mt-28">
    
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
            
            <div class="absolute inset-0 bg-slate-900/40 z-10"></div>
            <img :src="slide.image" class="w-full h-full object-cover" :alt="slide.title">

            <div class="absolute inset-0 z-20 flex items-center justify-center px-4">
                <div class="text-center max-w-4xl">
                    <h1 x-text="slide.title" class="text-3xl md:text-5xl lg:text-6xl font-black text-white mb-6 tracking-tight drop-shadow-2xl"></h1>
                    <p x-text="slide.subtitle" class="text-sm md:text-lg text-blue-50 font-medium mb-10 drop-shadow-lg uppercase tracking-[0.2em] opacity-90"></p>
                    <div class="flex flex-col sm:flex-row items-center justify-center space-y-4 sm:space-y-0 sm:space-x-4">
                        <a href="/lembaga" class="w-full sm:w-auto px-8 py-3.5 bg-blue-600 text-white font-black uppercase tracking-widest text-[10px] rounded-xl shadow-xl hover:bg-blue-700 transition duration-300 transform hover:-translate-y-1">
                            Satuan Pendidikan
                        </a>
                        <a href="/tentang-kami" class="w-full sm:w-auto px-8 py-3.5 bg-white/10 backdrop-blur-md border border-white/20 text-white font-black uppercase tracking-widest text-[10px] rounded-xl hover:bg-white/20 transition duration-300 transform hover:-translate-y-1">
                            Mengenal Kami
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </template>

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
<!-- Welcome Message -->
<div class="py-24 bg-white overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 items-start">
            <div class="lg:col-span-4 relative group">
                <div class="absolute -inset-4 bg-blue-600/5 rounded-[3rem] -rotate-2 group-hover:rotate-0 transition duration-700"></div>
                <div class="relative rounded-[2.5rem] overflow-hidden shadow-2xl border-8 border-white aspect-[4/5] bg-slate-100">
                    @if($welcome->image)
                        <img src="{{ Storage::url($welcome->image) }}" class="w-full h-full object-cover transition duration-700 group-hover:scale-110">
                    @else
                        <div class="w-full h-full flex items-center justify-center"><i class='bx bxs-user-circle text-8xl text-slate-200'></i></div>
                    @endif
                </div>
                <div class="absolute -bottom-6 -right-2 bg-slate-900 p-6 rounded-2xl shadow-2xl text-white hidden md:block z-10">
                    <h4 class="font-black text-sm leading-tight tracking-wide">{{ $welcome->pimpinan_name ?? 'Pimpinan KCD' }}</h4>
                    <p class="text-blue-400 text-[9px] font-bold uppercase mt-1 tracking-widest opacity-80">{{ $welcome->pimpinan_role ?? 'Kepala Kantor' }}</p>
                </div>
            </div>

            <div class="lg:col-span-8 space-y-8">
                <div>
                    <span class="text-[10px] font-black text-blue-600 uppercase tracking-[0.3em] mb-4 block">Kata Sambutan</span>
                    <h2 class="text-2xl md:text-4xl font-black text-slate-900 leading-tight tracking-tight">
                        {{ $welcome->title }}
                    </h2>
                </div>
                <div class="prose prose-slate text-slate-600 font-medium leading-relaxed max-w-none">
                    {!! $welcome->content !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Features -->
<div class="py-20 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="p-10 bg-white rounded-[3rem] shadow-sm border border-slate-100 hover:shadow-xl transition-all duration-500 hover:-translate-y-1 text-center">
                <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-6"><i class='bx bx-book-content text-2xl'></i></div>
                <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-3">Administrasi PTK</h3>
                <p class="text-xs text-slate-500 leading-relaxed">Pengurusan data Pendidik dan Tenaga Kependidikan yang akurat.</p>
            </div>
            <div class="p-10 bg-white rounded-[3rem] shadow-sm border border-slate-100 hover:shadow-xl transition-all duration-500 hover:-translate-y-1 text-center">
                <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center mx-auto mb-6"><i class='bx bx-buildings text-2xl'></i></div>
                <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-3">Tata Kelola</h3>
                <p class="text-xs text-slate-500 leading-relaxed">Penjaminan mutu dan pembinaan satuan pendidikan binaan.</p>
            </div>
            <div class="p-10 bg-white rounded-[3rem] shadow-sm border border-slate-100 hover:shadow-xl transition-all duration-500 hover:-translate-y-1 text-center">
                <div class="w-14 h-14 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center mx-auto mb-6"><i class='bx bx-info-circle text-2xl'></i></div>
                <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-3">Layanan Publik</h3>
                <p class="text-xs text-slate-500 leading-relaxed">Keterbukaan informasi dan wadah aspirasi masyarakat.</p>
            </div>
        </div>
    </div>
</div>

<!-- News & Announcements -->
<div class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-16">
            
            <!-- Berita (8 columns) -->
            <div class="lg:col-span-8">
                <div class="flex items-center justify-between mb-12">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-slate-900 text-white flex items-center justify-center shadow-xl"><i class='bx bx-news text-2xl'></i></div>
                        <h2 class="text-2xl font-black text-slate-900 uppercase tracking-tight">Berita Terbaru</h2>
                    </div>
                    <a href="/berita" class="text-[10px] font-black text-blue-600 uppercase tracking-widest hover:underline">Lihat Semua <i class='bx bx-right-arrow-alt'></i></a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    @forelse($latestBerita as $berita)
                    <a href="/berita/{{ $berita->slug }}" class="group flex flex-col bg-white rounded-[2.5rem] border border-slate-100 overflow-hidden shadow-sm hover:shadow-2xl transition-all duration-500">
                        <div class="aspect-[16/10] overflow-hidden relative">
                            @if($berita->gambar)
                                <img src="{{ Storage::url($berita->gambar) }}" class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110">
                            @endif
                            <div class="absolute top-5 left-5">
                                <span class="px-3 py-1 rounded-lg bg-blue-600 text-white text-[8px] font-black uppercase tracking-widest shadow-lg">News</span>
                            </div>
                        </div>
                        <div class="p-8">
                            <div class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mb-3 flex items-center gap-2">
                                <i class='bx bx-calendar-alt text-blue-500'></i> {{ $berita->created_at->translatedFormat('d M Y') }}
                            </div>
                            <h3 class="text-base font-bold text-slate-800 leading-tight group-hover:text-blue-600 transition-colors line-clamp-2">{{ $berita->judul }}</h3>
                        </div>
                    </a>
                    @empty
                    <div class="col-span-full p-12 text-center text-slate-400 border-2 border-dashed rounded-[3rem]">Belum ada berita terbaru</div>
                    @endforelse
                </div>
            </div>

            <!-- Pengumuman (4 columns) -->
            <div class="lg:col-span-4">
                <div class="flex items-center gap-4 mb-12">
                    <div class="w-12 h-12 rounded-2xl bg-amber-500 text-white flex items-center justify-center shadow-xl shadow-amber-200">
                        <i class='bx bxs-megaphone text-2xl'></i>
                    </div>
                    <h2 class="text-2xl font-black text-slate-900 uppercase tracking-tight">Pengumuman</h2>
                </div>

                <div class="space-y-4">
                    @forelse($latestPengumuman as $pengumuman)
                    <a href="/pengumuman" class="block p-6 rounded-3xl bg-slate-50 border border-slate-100 hover:bg-white hover:shadow-xl transition-all duration-300 group">
                        <div class="text-[8px] text-amber-600 font-black uppercase tracking-widest mb-2 flex items-center gap-1">
                            <i class='bx bx-time'></i> {{ $pengumuman->created_at->diffForHumans() }}
                        </div>
                        <h3 class="text-sm font-bold text-slate-800 group-hover:text-blue-600 transition-colors line-clamp-2">{{ $pengumuman->judul }}</h3>
                    </a>
                    @empty
                    <div class="p-12 text-center text-slate-400 border-2 border-dashed rounded-[3rem]">Tidak ada pengumuman</div>
                    @endforelse
                    <a href="/pengumuman" class="block w-full py-4 text-center rounded-2xl bg-slate-900 text-white text-[9px] font-black uppercase tracking-widest hover:bg-blue-600 transition-all shadow-lg mt-6">Lihat Semua Pengumuman</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Gallery -->
<div class="py-24 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="text-[10px] font-black text-blue-600 uppercase tracking-[0.4em] block mb-3">Visual Journal</span>
            <h2 class="text-3xl font-black text-slate-900 leading-tight uppercase tracking-tight">Galeri Kegiatan</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
            @forelse($latestGaleri as $galeri)
            <div class="group relative aspect-square rounded-[3rem] overflow-hidden shadow-xl bg-white border-4 border-white">
                <img src="{{ Storage::url($galeri->foto) }}" class="w-full h-full object-cover transition-transform duration-[2s] group-hover:scale-110">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 flex flex-col justify-end p-10 text-white">
                    <span class="text-[8px] font-black text-blue-400 uppercase tracking-widest mb-2">{{ \Carbon\Carbon::parse($galeri->tanggal)->translatedFormat('d M Y') }}</span>
                    <h3 class="text-sm font-bold leading-tight line-clamp-2 uppercase tracking-wide">{{ $galeri->judul }}</h3>
                </div>
            </div>
            @empty
            <div class="col-span-full p-20 text-center text-slate-300">Galeri belum tersedia</div>
            @endforelse
        </div>

        <div class="text-center">
            <a href="/galeri" class="inline-flex items-center gap-3 px-12 py-4 rounded-full bg-white text-slate-900 text-[10px] font-black uppercase tracking-widest hover:bg-slate-900 hover:text-white transition-all shadow-xl border border-slate-100">
                Jelajahi Galeri <i class='bx bx-right-arrow-alt text-xl'></i>
            </a>
        </div>
    </div>
</div>
@endsection
