{{-- FILE: resources/views/landing/web/sections/prestasi_galeri.blade.php --}}

<section class="py-24 bg-white border-t border-slate-50 relative overflow-hidden">
    
    {{-- Dekorasi Latar Belakang Halus --}}
    <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 bg-blue-50 rounded-full blur-3xl opacity-60 pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-96 h-96 bg-indigo-50 rounded-full blur-3xl opacity-60 pointer-events-none"></div>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        
        {{-- SECTION TITLE --}}
        <div class="flex flex-col md:flex-row justify-between items-end mb-12 gap-6">
            <div class="max-w-2xl">
                <span class="text-blue-600 font-bold tracking-widest uppercase text-xs mb-3 block">
                    Keunggulan & Aktivitas
                </span>
                <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 leading-tight">
                    Prestasi & <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600">Dokumentasi</span>
                </h2>
                <div class="w-20 h-1.5 bg-blue-600 rounded-full mt-4"></div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 xl:gap-16 items-start">
            
            {{-- =================================================================
                 KOLOM KIRI: PRESTASI TERBARU (List Layout)
            ================================================================== --}}
            <div class="h-full flex flex-col">
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center text-blue-600 shadow-sm">
                        <i class='bx bxs-trophy text-xl'></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-slate-800">Prestasi Terbaru</h3>
                        <p class="text-xs text-slate-500 font-medium">Kebanggaan siswa & guru</p>
                    </div>
                </div>

                @if(isset($prestasis) && $prestasis->count() > 0)
                    <div class="flex flex-col gap-4">
                        @foreach($prestasis as $prestasi)
                        <a href="{{ route('prestasi.show', $prestasi->id) }}" class="group bg-white rounded-2xl p-4 border border-slate-100 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 flex gap-4 items-start">
                            
                            {{-- Thumbnail Prestasi (Kiri) --}}
                            <div class="w-20 h-20 sm:w-24 sm:h-24 flex-shrink-0 rounded-xl overflow-hidden bg-slate-100 relative">
                                @if($prestasi->foto)
                                    <img src="{{ asset('storage/prestasis/'.$prestasi->foto) }}" 
                                         class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" 
                                         alt="{{ $prestasi->judul }}">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-blue-300">
                                        <i class='bx bxs-medal text-3xl'></i>
                                    </div>
                                @endif
                            </div>
                            
                            {{-- Info Prestasi (Kanan) --}}
                            <div class="flex-grow min-w-0"> {{-- min-w-0 fix flex truncation --}}
                                <div class="flex flex-wrap gap-2 mb-2">
                                    <span class="text-[10px] font-bold uppercase tracking-wide bg-blue-50 text-blue-600 px-2 py-0.5 rounded border border-blue-100">
                                        {{ $prestasi->tingkat }}
                                    </span>
                                    <span class="text-[10px] font-bold uppercase tracking-wide bg-indigo-50 text-indigo-600 px-2 py-0.5 rounded border border-indigo-100">
                                        {{ $prestasi->kategori }}
                                    </span>
                                </div>
                                <h4 class="text-base font-bold text-slate-800 group-hover:text-blue-600 transition-colors line-clamp-1 mb-1">
                                    {{ $prestasi->judul }}
                                </h4>
                                <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed">
                                    {{ $prestasi->deskripsi_singkat ?? 'Prestasi membanggakan dari siswa terbaik sekolah kami.' }}
                                </p>
                            </div>
                        </a>
                        @endforeach
                    </div>
                @else
                    <div class="p-8 bg-slate-50 rounded-2xl text-center border-2 border-dashed border-slate-200">
                        <i class='bx bx-trophy text-4xl text-slate-300 mb-3 block'></i>
                        <span class="text-slate-500 font-medium text-sm">Belum ada data prestasi.</span>
                    </div>
                @endif
            </div>

            {{-- =================================================================
                 KOLOM KANAN: GALERI KEGIATAN (Masonry/Grid Modern)
            ================================================================== --}}
            <div class="h-full flex flex-col">
                
                {{-- Header Galeri --}}
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center text-indigo-600 shadow-sm">
                            <i class='bx bxs-photo-album text-xl'></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-slate-800">Galeri Kegiatan</h3>
                            <p class="text-xs text-slate-500 font-medium">Dokumentasi aktivitas</p>
                        </div>
                    </div>
                    
                    {{-- Link Lihat Semua (Desktop) --}}
                    <a href="{{ route('galeri.index') }}" class="hidden md:inline-flex items-center gap-1 text-sm font-bold text-blue-600 hover:text-blue-700 transition-colors group">
                        Lihat Semua <i class='bx bx-right-arrow-alt transform group-hover:translate-x-1 transition-transform'></i>
                    </a>
                </div>

                @if(isset($galeris) && $galeris->count() > 0)
                    <div class="flex flex-col gap-4">
                        
                        {{-- 1. FOTO UTAMA (BESAR - ASPECT VIDEO) --}}
                        @if(isset($galeris[0]))
                        <div class="relative group rounded-2xl overflow-hidden shadow-lg aspect-video w-full">
                            <img src="{{ asset('storage/galeris/'.$galeris[0]->foto) }}" 
                                 class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" 
                                 alt="Galeri Utama">
                            
                            {{-- Overlay Gradient --}}
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-transparent to-transparent opacity-80 transition-opacity"></div>
                            
                            {{-- Info Floating --}}
                            <div class="absolute bottom-0 left-0 p-6 translate-y-2 group-hover:translate-y-0 transition-transform duration-300 w-full">
                                <span class="bg-blue-600 text-white text-[10px] font-bold px-2 py-1 rounded uppercase tracking-wider mb-2 inline-block shadow-sm">
                                    Terbaru
                                </span>
                                <h4 class="text-white font-bold text-lg leading-tight line-clamp-1 drop-shadow-md">
                                    {{ $galeris[0]->judul }}
                                </h4>
                            </div>
                        </div>
                        @endif

                        {{-- 2. GRID FOTO KECIL (2 KOLOM) --}}
                        <div class="grid grid-cols-2 gap-4">
                            @foreach($galeris->skip(1)->take(2) as $galeri)
                            <div class="relative group rounded-xl overflow-hidden shadow-md aspect-[4/3]">
                                <img src="{{ asset('storage/galeris/'.$galeri->foto) }}" 
                                     class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" 
                                     alt="Galeri Kecil">
                                
                                {{-- Overlay Hover Simple --}}
                                <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                                    <div class="w-10 h-10 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center text-white border border-white/30 transform scale-75 group-hover:scale-100 transition-transform">
                                        <i class='bx bx-zoom-in text-xl'></i>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        {{-- Tombol Lihat Semua (Mobile Only) --}}
                        <a href="{{ route('galeri.index') }}" class="md:hidden mt-2 w-full py-3 bg-slate-100 text-slate-700 font-bold rounded-xl text-center text-sm hover:bg-slate-200 transition-colors">
                            Lihat Semua Galeri
                        </a>

                    </div>
                @else
                    {{-- Empty State Galeri --}}
                    <div class="h-64 bg-slate-50 rounded-2xl flex flex-col items-center justify-center text-slate-400 border-2 border-dashed border-slate-200">
                        <i class='bx bxs-image text-4xl mb-2 opacity-50'></i>
                        <span class="text-sm font-medium">Belum ada dokumentasi.</span>
                    </div>
                @endif
            </div>

        </div>
    </div>
</section>