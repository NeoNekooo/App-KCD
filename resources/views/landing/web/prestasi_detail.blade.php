@extends('landing.web.layouts.master_web')

@section('title', $prestasi->judul)

@section('content')

{{-- HERO HEADER --}}
<section class="relative pt-36 pb-24 bg-slate-900 overflow-hidden">
    
    {{-- 1. Background Image (Tema Prestasi/Juara) --}}
    <div class="absolute inset-0 z-0">
        {{-- Gambar Ilustrasi Juara (Unsplash) --}}
        <img src="https://images.unsplash.com/photo-1578269174936-2709b6aeb913?q=80&w=2071&auto=format&fit=crop" 
             class="w-full h-full object-cover opacity-30 blur-sm" 
             alt="Prestasi Background">
        
        {{-- Overlay Gradient --}}
        <div class="absolute inset-0 bg-gradient-to-br from-blue-900/90 via-slate-900/80 to-slate-900/90 mix-blend-multiply"></div>
    </div>

    {{-- Content Hero --}}
    <div class="container mx-auto px-4 relative z-10 text-center">
        
        <a href="{{ route('home') }}#prestasi" class="inline-flex items-center text-blue-200 hover:text-white text-sm font-semibold mb-8 transition-colors bg-white/10 px-4 py-2 rounded-full backdrop-blur-md border border-white/10 hover:bg-white/20">
            <i class='bx bx-arrow-back mr-2'></i> Kembali ke Daftar Prestasi
        </a>

        <div class="flex items-center justify-center gap-3 mb-6">
            <span class="px-4 py-1.5 rounded-full bg-blue-600 text-white text-xs font-bold uppercase tracking-wider shadow-lg border border-blue-500">
                {{ $prestasi->tingkat }}
            </span>
            <span class="px-4 py-1.5 rounded-full bg-indigo-600 text-white text-xs font-bold uppercase tracking-wider shadow-lg border border-indigo-500">
                {{ $prestasi->kategori }}
            </span>
        </div>

        <h1 class="text-3xl md:text-5xl font-extrabold text-white mb-8 leading-tight drop-shadow-lg max-w-4xl mx-auto">
            {{ $prestasi->judul }}
        </h1>

        @if($prestasi->pemenang)
        <div class="inline-flex items-center gap-3 bg-white/10 backdrop-blur-md px-6 py-3 rounded-2xl border border-white/10 shadow-xl">
            <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white text-lg shadow-md">
                <i class='bx bxs-user'></i>
            </div>
            <div class="text-left">
                <p class="text-xs text-blue-200 uppercase font-bold tracking-wider">Peraih Prestasi</p>
                <p class="text-white font-bold">{{ $prestasi->pemenang }}</p>
            </div>
        </div>
        @endif
    </div>
</section>

{{-- KONTEN DETAIL --}}
<section class="py-16 md:py-20 bg-white relative">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-16">
            
            {{-- AREA KONTEN UTAMA (8 Kolom) --}}
            <div class="lg:col-span-8">
                
                {{-- Foto Utama (Ukuran Disesuaikan) --}}
                <div class="relative w-full max-w-3xl mx-auto mb-10 group">
                    <div class="relative rounded-2xl overflow-hidden shadow-2xl border border-slate-100 bg-slate-50">
                        @if($prestasi->foto)
                            {{-- Aspect Ratio 16:9 agar rapi --}}
                            <div class="aspect-video w-full overflow-hidden">
                                <img src="{{ asset('storage/prestasis/'.$prestasi->foto) }}" 
                                     class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" 
                                     alt="{{ $prestasi->judul }}">
                            </div>
                        @else
                            {{-- Fallback jika tidak ada foto --}}
                            <div class="aspect-video w-full bg-slate-100 flex flex-col items-center justify-center text-slate-300">
                                <i class='bx bxs-trophy text-6xl mb-2'></i>
                                <span class="text-sm font-medium">Tidak ada foto dokumentasi</span>
                            </div>
                        @endif
                        
                        {{-- Badge Tanggal Floating --}}
                        <div class="absolute top-4 left-4 bg-white/90 backdrop-blur px-3 py-1.5 rounded-lg text-sm font-bold text-slate-800 shadow-md flex items-center gap-2">
                            <i class='bx bx-calendar text-blue-600'></i> 
                            {{ $prestasi->created_at->format('d M Y') }}
                        </div>
                    </div>
                    
                    {{-- Shadow Dekorasi di Bawah --}}
                    <div class="absolute -bottom-4 -right-4 w-full h-full bg-blue-50 rounded-2xl -z-10"></div>
                </div>

                {{-- Artikel Konten --}}
                <div class="bg-white p-2">
                    <h3 class="text-2xl font-bold text-slate-900 mb-6 flex items-center gap-3">
                        <span class="w-1.5 h-8 bg-blue-600 rounded-full"></span>
                        Detail Prestasi
                    </h3>
                    
                    <div class="prose prose-lg prose-slate max-w-none text-justify leading-relaxed text-slate-600">
                        {{-- Logika Menampilkan Deskripsi sesuai Database --}}
                        @if($prestasi->deskripsi)
                            {{-- Tampilkan deskripsi dengan format paragraf (nl2br) --}}
                            {!! nl2br(e($prestasi->deskripsi)) !!}
                        @else
                            {{-- Jika kosong --}}
                            <p class="italic text-slate-500 border-l-4 border-slate-200 pl-4 py-2">
                                Belum ada deskripsi detail untuk prestasi ini.
                            </p>
                        @endif
                    </div>
                </div>

                {{-- Tombol Share (Copy Link) --}}
                <div class="mt-12 pt-8 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <span class="text-sm font-bold text-slate-500 uppercase tracking-wide">Bagikan Kabar Baik Ini</span>
                    
                    <button onclick="copyToClipboard()" class="group relative inline-flex items-center gap-2 px-6 py-2.5 bg-slate-100 hover:bg-blue-600 hover:text-white text-slate-700 font-bold rounded-full transition-all duration-300">
                        <i class='bx bx-link-alt text-xl'></i>
                        <span>Salin Tautan</span>
                        
                        {{-- Tooltip "Tersalin!" (Hidden by default) --}}
                        <span id="copyTooltip" class="absolute -top-10 left-1/2 transform -translate-x-1/2 bg-black text-white text-xs py-1 px-2 rounded opacity-0 transition-opacity duration-300">
                            Tersalin!
                        </span>
                    </button>
                </div>

            </div>

            {{-- SIDEBAR KANAN (4 Kolom) --}}
            <div class="lg:col-span-4">
                <div class="sticky top-28 bg-white rounded-3xl p-6 border border-slate-100 shadow-lg shadow-slate-100/50">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100">
                        <div class="w-10 h-10 rounded-full bg-yellow-50 flex items-center justify-center text-yellow-500">
                            <i class='bx bxs-star text-xl'></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-800">Prestasi Lainnya</h3>
                    </div>

                    <div class="flex flex-col gap-5">
                        @forelse($prestasiLain as $item)
                        <a href="{{ route('prestasi.show', $item->id) }}" class="flex gap-4 group items-start">
                            <div class="w-20 h-20 flex-shrink-0 rounded-xl overflow-hidden bg-slate-50 border border-slate-200 relative">
                                @if($item->foto)
                                    <img src="{{ asset('storage/prestasis/'.$item->foto) }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" alt="Thumbnail">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-blue-200"><i class='bx bxs-trophy'></i></div>
                                @endif
                            </div>
                            <div>
                                <h5 class="text-sm font-bold text-slate-800 group-hover:text-blue-600 transition-colors line-clamp-2 mb-1 leading-snug">
                                    {{ $item->judul }}
                                </h5>
                                <div class="flex items-center gap-2">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase bg-slate-50 px-1.5 py-0.5 rounded">{{ $item->kategori }}</span>
                                </div>
                            </div>
                        </a>
                        @empty
                        <div class="text-center py-6">
                            <p class="text-sm text-slate-400">Belum ada prestasi lain.</p>
                        </div>
                        @endforelse
                    </div>

                    <div class="mt-8 pt-6 border-t border-slate-100 text-center">
                        <a href="{{ route('home') }}#prestasi" class="inline-flex items-center text-sm font-bold text-blue-600 hover:text-blue-800 transition-colors group/link">
                            Lihat Semua Prestasi
                            <i class='bx bx-right-arrow-alt ml-1 transform group-hover/link:translate-x-1 transition-transform'></i>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- SCRIPT COPY LINK --}}
<script>
    function copyToClipboard() {
        // Ambil URL halaman saat ini
        const url = window.location.href;
        
        // Salin ke clipboard
        navigator.clipboard.writeText(url).then(() => {
            // Tampilkan tooltip
            const tooltip = document.getElementById('copyTooltip');
            tooltip.classList.remove('opacity-0');
            
            // Sembunyikan tooltip setelah 2 detik
            setTimeout(() => {
                tooltip.classList.add('opacity-0');
            }, 2000);
        }).catch(err => {
            console.error('Gagal menyalin: ', err);
        });
    }
</script>

@endsection