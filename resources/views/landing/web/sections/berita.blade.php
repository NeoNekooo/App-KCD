{{-- FILE: resources/views/landing/web/sections/berita.blade.php --}}

@if(isset($beritas) && $beritas->count() > 0)
<section id="berita" class="py-20 bg-white relative border-t border-slate-50">
    
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">

        {{-- Header Section (Minimalis & Rapi) --}}
        <div class="flex flex-col md:flex-row justify-between items-end mb-12 gap-4">
            <div class="max-w-2xl" data-aos="fade-right">
                <span class="text-blue-600 font-bold uppercase text-xs tracking-widest mb-2 block">
                    Kabar Terbaru
                </span>
                <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 leading-tight">
                    Berita & Artikel Sekolah
                </h2>
            </div>
            
            {{-- Tombol Lihat Semua (Opsional) --}}
            {{-- 
            <a href="#" class="group flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-blue-600 transition-colors" data-aos="fade-left">
                Lihat Arsip Berita
                <i class='bx bx-right-arrow-alt text-xl transform group-hover:translate-x-1 transition-transform'></i>
            </a> 
            --}}
        </div>

        {{-- Grid Berita (Layout Modern) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-10">
            @foreach($beritas as $key => $berita)

            @php
                $pathGambar = 'beritas/' . $berita->gambar;
                $gambarUrl = asset('storage/' . $pathGambar);
            @endphp

            {{-- START CARD --}}
            <article class="group flex flex-col h-full bg-transparent" data-aos="fade-up" data-aos-delay="{{ $key * 100 }}">
                
                {{-- 1. Gambar Thumbnail (Rounded & Clean) --}}
                <a href="{{ route('berita.show', $berita->id) }}" class="block overflow-hidden rounded-2xl relative mb-5 shadow-sm border border-slate-100 aspect-[16/9]">
                    <img src="{{ $gambarUrl }}"
                         class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                         alt="{{ $berita->judul }}"
                         onerror="this.src='https://placehold.co/600x400/f1f5f9/94a3b8?text=News'">
                    
                    {{-- Badge Kategori (Pojok Kiri Atas) --}}
                    <div class="absolute top-3 left-3">
                        <span class="bg-white/95 backdrop-blur-sm text-slate-800 text-[10px] font-bold px-3 py-1.5 rounded-lg shadow-sm border border-slate-100 uppercase tracking-wide">
                            Berita
                        </span>
                    </div>
                </a>

                {{-- 2. Konten Teks --}}
                <div class="flex flex-col flex-grow">
                    
                    {{-- Meta Data (Tanggal & Penulis) --}}
                    <div class="flex items-center gap-3 text-xs text-slate-400 font-medium mb-3">
                        <span class="flex items-center gap-1 text-blue-600">
                            <i class='bx bx-calendar'></i>
                            {{ \Carbon\Carbon::parse($berita->created_at)->format('d M Y') }}
                        </span>
                        <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                        <span class="flex items-center gap-1">
                            <i class='bx bx-user'></i>
                            {{ $berita->penulis ?? 'Admin' }}
                        </span>
                    </div>

                    {{-- Judul Berita --}}
                    <h3 class="text-xl font-bold text-slate-900 mb-3 leading-[1.4] group-hover:text-blue-600 transition-colors line-clamp-2">
                        <a href="{{ route('berita.show', $berita->id) }}">
                            {{ $berita->judul }}
                        </a>
                    </h3>

                    {{-- Deskripsi Singkat --}}
                    <p class="text-slate-500 text-sm leading-relaxed line-clamp-3 mb-4 flex-grow">
                        {{ $berita->ringkasan ?? Str::limit(strip_tags($berita->isi), 120) }}
                    </p>

                    {{-- Link Baca Selengkapnya (Minimalis) --}}
                    <div class="mt-auto pt-2">
                        <a href="{{ route('berita.show', $berita->id) }}" class="inline-flex items-center gap-1 text-sm font-bold text-slate-900 hover:text-blue-600 transition-colors border-b-2 border-transparent hover:border-blue-600 pb-0.5">
                            Baca Selengkapnya
                            <i class='bx bx-right-arrow-alt text-lg'></i>
                        </a>
                    </div>

                </div>
            </article>
            {{-- END CARD --}}

            @endforeach
        </div>

    </div>
</section>
@endif