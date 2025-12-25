@extends('landing.web.layouts.master_web')

@section('title', 'Program Keahlian')

@section('content')

{{-- 1. HERO SECTION (DARK & ELEGANT) --}}
<section class="relative pt-32 pb-24 lg:pt-48 lg:pb-32 overflow-hidden bg-slate-900">
    {{-- Dynamic Background --}}
    <div class="absolute inset-0">
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
        <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-blue-600/20 rounded-full blur-[120px] -translate-y-1/2 translate-x-1/3 animate-pulse"></div>
        <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-indigo-600/20 rounded-full blur-[120px] translate-y-1/3 -translate-x-1/3 animate-pulse" style="animation-delay: 2s"></div>
    </div>

    <div class="container mx-auto px-4 relative z-10 text-center">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-300 text-xs font-bold uppercase tracking-widest mb-6 backdrop-blur-sm animate-fade-in-down">
            <span class="w-2 h-2 rounded-full bg-blue-400 animate-ping"></span>
            Akademik Unggulan
        </div>
        <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-white tracking-tight mb-6 animate-fade-in-up leading-tight">
            Pilih Masa Depanmu <br>
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-indigo-400">Bersama Kami</span>
        </h1>
        <p class="text-lg text-slate-400 max-w-xl mx-auto leading-relaxed animate-fade-in-up delay-100 font-light">
            Temukan jurusan yang sesuai dengan minat dan bakatmu. Kami mencetak lulusan yang kompeten dan siap kerja di dunia industri.
        </p>
    </div>
</section>

{{-- 2. JURUSAN GRID (COMPACT & MODERN) --}}
<section class="py-20 bg-slate-50 relative -mt-16">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-20">
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($jurusans as $index => $item)
                {{-- Compact Card --}}
                <div class="group relative bg-white rounded-2xl shadow-sm hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 border border-slate-200 overflow-hidden h-full flex flex-col animate-on-scroll opacity-0 translate-y-8" style="transition-delay: {{ $index * 50 }}ms;">
                    
                    {{-- Image Header (Compact Height) --}}
                    <div class="relative h-48 overflow-hidden bg-slate-100 group-hover:brightness-110 transition-all">
                        @if($item->gambar)
                            <img src="{{ asset('storage/jurusans/'.$item->gambar) }}" 
                                 class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" 
                                 alt="{{ $item->nama_jurusan }}">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-slate-300">
                                <i class='bx bxs-graduation text-5xl'></i>
                            </div>
                        @endif
                        
                        {{-- Overlay Gradient --}}
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-60 group-hover:opacity-40 transition-opacity"></div>

                        {{-- Floating Badge --}}
                        <div class="absolute top-3 right-3">
                            <span class="bg-white/90 backdrop-blur text-slate-900 text-[10px] font-bold px-2 py-1 rounded shadow-sm uppercase tracking-wide">
                                {{ $item->singkatan ?? 'PRODI' }}
                            </span>
                        </div>
                    </div>

                    {{-- Body Content (Compact) --}}
                    <div class="p-5 flex-1 flex flex-col relative">
                        {{-- Icon Float (Optional decoration) --}}
                        <div class="absolute -top-6 right-4 w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center text-white shadow-lg transform group-hover:rotate-12 transition-transform duration-300">
                            <i class='bx bx-book-reader text-xl'></i>
                        </div>

                        <h3 class="text-lg font-bold text-slate-800 mb-2 leading-tight group-hover:text-blue-600 transition-colors line-clamp-2">
                            {{ $item->nama_jurusan }}
                        </h3>
                        
                        <p class="text-xs text-slate-500 line-clamp-3 mb-4 flex-1 leading-relaxed">
                            {{ $item->deskripsi ?? 'Program keahlian unggulan sekolah kami.' }}
                        </p>

                        <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                            <a href="{{ route('jurusan.show', $item->id) }}" class="text-xs font-bold text-blue-600 uppercase tracking-wider hover:underline flex items-center gap-1">
                                Selengkapnya <i class='bx bx-right-arrow-alt'></i>
                            </a>
                            {{-- Mini icons indicator (Example: Lab, Praktek, dll) --}}
                            <div class="flex gap-2 text-slate-400">
                                <i class='bx bx-laptop' title="Lab Komputer"></i>
                                <i class='bx bx-wifi' title="WiFi Area"></i>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-16 text-center">
                    <div class="inline-block p-4 bg-slate-100 rounded-full mb-3 text-slate-400">
                        <i class='bx bx-folder-open text-3xl'></i>
                    </div>
                    <p class="text-slate-500 text-sm font-medium">Data jurusan belum tersedia.</p>
                </div>
            @endforelse
        </div>

    </div>
</section>

{{-- 3. STATISTICS / FEATURES STRIP --}}
<section class="py-12 bg-white border-t border-slate-100">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center divide-x divide-slate-100">
            
            {{-- Statistik 1: Jumlah Jurusan (Dinamis dari variabel $jurusans) --}}
            <div class="space-y-1">
                <h4 class="text-3xl font-extrabold text-blue-600">{{ $jurusans->count() }}</h4>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">Program Keahlian</p>
            </div>

            {{-- Statistik 2: Akreditasi (Statis / Bisa diambil dari config sekolah jika ada) --}}
            <div class="space-y-1">
                <h4 class="text-3xl font-extrabold text-blue-600">A+</h4>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">Akreditasi</p>
            </div>

            {{-- Statistik 3: Serapan Kerja (Statis / Estimasi) --}}
            <div class="space-y-1">
                <h4 class="text-3xl font-extrabold text-blue-600">98%</h4>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">Terserap Kerja</p>
            </div>

            {{-- Statistik 4: Jumlah Mitra (Dinamis dari Controller) --}}
            <div class="space-y-1">
                {{-- Menggunakan format angka jika jumlahnya ribuan, misal 1,200 --}}
                <h4 class="text-3xl font-extrabold text-blue-600">{{ number_format($jumlahMitra ?? 0) }}+</h4>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">Mitra Industri</p>
            </div>

        </div>
    </div>
</section>

{{-- 4. CTA SECTION --}}
<section class="py-20 bg-slate-900 relative overflow-hidden">
    {{-- Decoration --}}
    <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')]"></div>
    
    <div class="container mx-auto px-4 text-center relative z-10">
        <h2 class="text-2xl md:text-3xl font-bold text-white mb-6">Siap Menentukan Pilihan?</h2>
        <div class="flex justify-center gap-4">
            <a href="{{ route('ppdb.cek_status') }}" class="px-6 py-3 bg-blue-600 hover:bg-blue-500 text-white text-sm font-bold rounded-lg shadow-lg shadow-blue-500/30 transition-all transform hover:-translate-y-1">
                Daftar Sekarang
            </a>
            <a href="{{ route('kontak') }}" class="px-6 py-3 bg-transparent border border-slate-600 hover:border-white text-slate-300 hover:text-white text-sm font-bold rounded-lg transition-all">
                Hubungi Kami
            </a>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Animasi Scroll Ringan
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.remove('opacity-0', 'translate-y-8');
                entry.target.classList.add('opacity-100', 'translate-y-0');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.animate-on-scroll').forEach(el => observer.observe(el));
});
</script>
@endpush