@extends('landing.web.layouts.master_web')

@section('title', $berita->judul)

@section('content')

{{-- 1. HEADER SECTION (SPLIT LAYOUT) --}}
<section class="relative pt-32 pb-12 bg-white border-b border-slate-100">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            {{-- KOLOM KIRI: TEKS JUDUL & META --}}
            <div class="lg:col-span-7 order-2 lg:order-1">
                
                {{-- Breadcrumb --}}
                <div class="flex flex-wrap items-center gap-2 text-sm text-slate-500 mb-6 font-medium">
                    <a href="{{ route('home') }}" class="hover:text-blue-600 transition-colors">Beranda</a>
                    <i class='bx bx-chevron-right text-slate-300'></i>
                    <a href="{{ route('home') }}#berita" class="hover:text-blue-600 transition-colors">Berita</a>
                    <i class='bx bx-chevron-right text-slate-300'></i>
                    <span class="text-blue-600 bg-blue-50 px-2 py-0.5 rounded text-xs uppercase tracking-wide font-bold">Terbaru</span>
                </div>

                {{-- Judul Utama --}}
                <h1 class="text-3xl md:text-5xl lg:text-6xl font-extrabold text-slate-900 leading-[1.1] mb-6 tracking-tight">
                    {{ $berita->judul }}
                </h1>

                {{-- Info Penulis & Tanggal --}}
                <div class="flex items-center gap-6 pt-4 border-t border-slate-100">
                    <div class="flex items-center gap-3">
                        {{-- Avatar Penulis --}}
                        <div class="w-11 h-11 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 border border-slate-200">
                            <i class='bx bxs-user text-xl'></i>
                        </div>
                        <div>
                            <p class="text-slate-900 font-bold text-sm leading-none mb-1">{{ $berita->penulis ?? 'Tim Redaksi' }}</p>
                            <p class="text-slate-500 text-xs">Penulis</p>
                        </div>
                    </div>

                    <div class="h-8 w-px bg-slate-200"></div>

                    <div class="text-sm text-slate-500">
                        <p class="flex items-center gap-1.5 mb-0.5">
                            <i class='bx bx-calendar text-blue-500'></i>
                            <span class="font-medium text-slate-700">{{ \Carbon\Carbon::parse($berita->created_at)->isoFormat('D MMMM Y') }}</span>
                        </p>
                        <p class="text-xs">Diposting {{ \Carbon\Carbon::parse($berita->created_at)->diffForHumans() }}</p>
                    </div>
                </div>

            </div>

            {{-- KOLOM KANAN: FOTO UTAMA (POSISI DI PINGGIR & KOTAK) --}}
            <div class="lg:col-span-5 order-1 lg:order-2">
                <div class="relative rounded-3xl overflow-hidden shadow-2xl shadow-blue-900/10 border border-slate-100 group">
                    
                    {{-- Rasio Gambar: aspect-[4/3] membuat kotak sedikit persegi panjang (standar foto), tidak memanjang --}}
                    <div class="aspect-[4/3] w-full bg-slate-100 relative overflow-hidden">
                        <img src="{{ $berita->gambar ? asset('storage/beritas/'.$berita->gambar) : 'https://placehold.co/800x600/1e293b/475569?text=Berita+Sekolah' }}" 
                             class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" 
                             alt="{{ $berita->judul }}">
                        
                        {{-- Overlay Halus --}}
                        <div class="absolute inset-0 bg-slate-900/5 group-hover:bg-transparent transition-colors"></div>
                    </div>

                    {{-- Badge Kategori Floating --}}
                    <div class="absolute top-4 left-4">
                        <span class="bg-white/90 backdrop-blur text-slate-800 px-3 py-1.5 rounded-lg text-xs font-bold shadow-sm flex items-center gap-1">
                            <i class='bx bxs-camera text-blue-500'></i> Dokumentasi
                        </span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- 2. KONTEN UTAMA --}}
<section class="py-16 bg-slate-50/50">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            
            {{-- KOLOM KIRI: ARTIKEL (8 Kolom) --}}
            <div class="lg:col-span-8">
                
                {{-- Artikel Wrapper --}}
                <div class="bg-white p-8 md:p-10 rounded-3xl shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] border border-slate-100">
                    
                    <article class="prose prose-lg prose-slate max-w-none 
                        prose-headings:text-slate-900 prose-headings:font-bold 
                        prose-p:text-slate-600 prose-p:leading-relaxed prose-p:mb-6
                        prose-a:text-blue-600 prose-a:no-underline hover:prose-a:underline 
                        prose-img:rounded-2xl prose-img:shadow-md 
                        prose-strong:text-slate-800
                        prose-blockquote:border-l-4 prose-blockquote:border-blue-500 prose-blockquote:bg-slate-50 prose-blockquote:py-4 prose-blockquote:px-6 prose-blockquote:not-italic prose-blockquote:rounded-r-lg">
                        
                        {{-- Dropcap Modern --}}
                        <div class="first-letter:text-6xl first-letter:font-extrabold first-letter:text-slate-900 first-letter:float-left first-letter:mr-3 first-letter:mt-[-5px]">
                            {!! $berita->isi !!}
                        </div>
                    </article>

                    {{-- Tombol Share --}}
                    <div class="mt-12 pt-8 border-t border-slate-100 flex items-center justify-between">
                        <div class="text-sm font-medium text-slate-500">
                            Bagikan artikel ini ke temanmu:
                        </div>
                        <button onclick="copyLink()" class="flex items-center gap-2 px-5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-full text-sm font-bold transition-all active:scale-95">
                            <i class='bx bx-link'></i> Salin Tautan
                        </button>
                    </div>

                </div>

            </div>

            {{-- KOLOM KANAN: SIDEBAR (4 Kolom) --}}
            <div class="lg:col-span-4 space-y-8">
                
                {{-- Widget: Berita Lainnya --}}
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 sticky top-28">
                    <div class="flex items-center justify-between mb-6 pb-2 border-b border-slate-100">
                        <h3 class="font-bold text-slate-900 text-lg">Baca Juga</h3>
                    </div>

                    <div class="flex flex-col gap-6">
                        @forelse($beritaTerbaru as $item)
                        <a href="{{ route('berita.show', $item->id) }}" class="group flex gap-4 items-start">
                            {{-- Thumbnail Sidebar --}}
                            <div class="w-24 h-20 flex-shrink-0 rounded-xl overflow-hidden bg-slate-100 relative">
                                <img src="{{ $item->gambar ? asset('storage/beritas/'.$item->gambar) : 'https://placehold.co/150x150?text=News' }}" 
                                     class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" 
                                     alt="Thumbnail">
                            </div>
                            
                            {{-- Info --}}
                            <div class="flex-1">
                                <span class="text-[10px] uppercase font-bold text-blue-600 mb-1 block">Berita</span>
                                <h4 class="text-sm font-bold text-slate-800 leading-snug mb-1 group-hover:text-blue-600 transition-colors line-clamp-2">
                                    {{ $item->judul }}
                                </h4>
                                <span class="text-xs text-slate-400">
                                    {{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}
                                </span>
                            </div>
                        </a>
                        @empty
                        <div class="text-center py-4 text-slate-400 text-sm">Belum ada berita lain.</div>
                        @endforelse
                    </div>
                    
                    <div class="mt-6 pt-4 border-t border-slate-100 text-center">
                        <a href="{{ route('home') }}#berita" class="text-sm font-bold text-blue-600 hover:underline">Lihat Semua Berita</a>
                    </div>
                </div>

            </div>

        </div>
    </div>
</section>

{{-- Notifikasi Copy Link --}}
<div id="copyNotification" class="fixed top-24 right-5 z-[99] transform transition-all duration-500 ease-out opacity-0 translate-x-10 pointer-events-none">
    <div class="bg-slate-800 text-white px-5 py-3 rounded-xl shadow-xl flex items-center gap-3">
        <i class='bx bx-check-circle text-green-400 text-xl'></i>
        <span class="text-sm font-medium">Tautan berhasil disalin!</span>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function copyLink() {
        navigator.clipboard.writeText(window.location.href).then(() => {
            const notification = document.getElementById('copyNotification');
            notification.classList.remove('opacity-0', 'translate-x-10');
            notification.classList.add('opacity-100', 'translate-x-0');
            setTimeout(() => {
                notification.classList.remove('opacity-100', 'translate-x-0');
                notification.classList.add('opacity-0', 'translate-x-10');
            }, 3000);
        });
    }
</script>
@endpush