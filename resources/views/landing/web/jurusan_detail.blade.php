@extends('landing.web.layouts.master_web')

@section('title', $jurusan->nama_jurusan)

@push('styles')
<style>
    /* Animasi untuk elemen melayang */
    @keyframes float {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-20px) rotate(3deg); }
    }
    .animate-float {
        animation: float 6s ease-in-out infinite;
    }
    /* Gaya kustom untuk Drop Cap */
    .first-letter::first-letter {
        float: left;
        font-size: 5.5rem;
        line-height: 4rem;
        padding-right: 0.5rem;
        margin-top: -0.25rem;
        font-weight: 700;
        color: rgb(37 99 235); /* blue-600 */
    }
</style>
@endpush

@section('content')

{{-- HERO SECTION JURUSAN --}}
<section class="relative pt-36 pb-32 bg-slate-900 overflow-hidden">
    
    {{-- Background Image dengan Overlay --}}
    <div class="absolute inset-0 z-0">
        <img src="https://images.unsplash.com/photo-1519389950473-47ba0277781c?q=80&w=2070&auto=format&fit=crop" 
             class="w-full h-full object-cover opacity-30" 
             alt="Technology Background">
        <div class="absolute inset-0 bg-gradient-to-r from-blue-900/95 via-slate-900/90 to-slate-900/95"></div>
    </div>

    {{-- Elemen Grafis Melayang --}}
    <div class="absolute top-20 left-10 w-72 h-72 bg-blue-500/20 rounded-full mix-blend-color-dodge filter blur-3xl animate-float"></div>
    <div class="absolute bottom-20 right-10 w-96 h-96 bg-indigo-500/20 rounded-full mix-blend-color-dodge filter blur-3xl animate-float" style="animation-delay: 2s;"></div>

    {{-- Content Hero --}}
    <div class="container mx-auto px-4 relative z-10 text-center">
        <a href="{{ route('home') }}#jurusan" class="animate-on-scroll inline-flex items-center text-blue-200 hover:text-white text-sm font-semibold mb-8 transition-all duration-300 bg-white/10 px-5 py-2.5 rounded-full backdrop-blur-md border border-white/20 hover:bg-white/20 opacity-0" data-animate="fade-in-up">
            <i class='bx bx-arrow-back mr-2'></i> Kembali ke Daftar Jurusan
        </a>
        
        <h1 class="animate-on-scroll text-4xl md:text-6xl lg:text-7xl font-extrabold text-white mb-6 tracking-tight leading-tight opacity-0" data-animate="fade-in-up" data-delay="100">
            {{ $jurusan->nama_jurusan }}
        </h1>
        
        <div class="animate-on-scroll inline-flex items-center gap-3 px-6 py-3 bg-white rounded-full shadow-2xl opacity-0" data-animate="fade-in-up" data-delay="200">
            <span class="w-3 h-3 bg-green-500 rounded-full animate-pulse shadow-[0_0_10px_rgba(34,197,94,0.7)]"></span>
            <span class="text-slate-800 font-bold font-mono text-sm tracking-wide">KODE: {{ $jurusan->singkatan }}</span>
        </div>
    </div>
</section>

{{-- KONTEN UTAMA --}}
<section class="py-24 bg-gradient-to-b from-slate-50 to-white relative">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
            
            {{-- SIDEBAR KIRI (LOGO & INFO) --}}
            <div class="lg:col-span-4 order-2 lg:order-1">
                <div class="sticky top-28 space-y-8">
                    
                    {{-- Card Logo Jurusan (Glassmorphism) --}}
                    <div class="animate-on-scroll group relative bg-white/80 backdrop-blur-lg rounded-3xl shadow-2xl shadow-slate-300/50 p-8 border border-white/50 text-center overflow-hidden opacity-0" data-animate="fade-in-right">
                        {{-- Hiasan di belakang logo --}}
                        <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-blue-500 to-indigo-500"></div>
                        <div class="absolute -top-10 -right-10 w-32 h-32 bg-blue-500/20 rounded-full blur-2xl opacity-60 group-hover:scale-150 transition-transform duration-500"></div>

                        <div class="relative z-10 inline-block mb-4">
                            @if($jurusan->gambar)
                                <div class="w-40 h-40 mx-auto rounded-2xl bg-slate-50 flex items-center justify-center p-2 shadow-inner border border-slate-100">
                                    <img src="{{ asset('storage/jurusans/'.$jurusan->gambar) }}" 
                                         class="w-full h-full object-contain transform transition-transform duration-700 group-hover:scale-110 group-hover:rotate-3" 
                                         alt="{{ $jurusan->nama_jurusan }}">
                                </div>
                            @else
                                <div class="w-40 h-40 mx-auto bg-gradient-to-br from-blue-50 to-indigo-100 rounded-2xl flex items-center justify-center text-blue-300 shadow-inner">
                                    <i class='bx bxs-graduation text-6xl'></i>
                                </div>
                            @endif
                        </div>

                        <h3 class="text-lg font-bold text-slate-800 mb-1">Logo Kompetensi</h3>
                        <p class="text-xs text-slate-400">Identitas Jurusan</p>
                    </div>

                    {{-- Card Informasi Detail (Glassmorphism) --}}
                    <div class="animate-on-scroll bg-white/70 backdrop-blur-lg rounded-3xl shadow-xl border border-white/50 overflow-hidden opacity-0" data-animate="fade-in-right" data-delay="100">
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-white/50">
                            <h4 class="font-bold text-slate-800 flex items-center gap-2">
                                <i class='bx bx-info-circle text-blue-600 text-xl'></i>
                                Informasi Program
                            </h4>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex items-center justify-between p-3 bg-slate-50/50 rounded-xl">
                                <span class="text-slate-500 text-sm flex items-center gap-2"><i class='bx bx-time'></i> Durasi</span>
                                <span class="font-bold text-slate-800 text-sm">3 Tahun</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-slate-50/50 rounded-xl">
                                <span class="text-slate-500 text-sm flex items-center gap-2"><i class='bx bx-certification'></i> Sertifikasi</span>
                                <span class="font-bold text-slate-800 text-sm text-right">LSP P1 / BNSP</span>
                            </div>
                            @if($jurusan->kepala_jurusan)
                            <div class="p-3 bg-slate-50/50 rounded-xl">
                                <span class="text-slate-500 text-sm flex items-center gap-2 mb-2"><i class='bx bx-user-voice'></i> Kepala Program</span>
                                <span class="font-bold text-slate-800 text-sm bg-blue-100 p-3 rounded-lg border border-blue-200 block text-center">
                                    {{ $jurusan->kepala_jurusan }}
                                </span>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Tombol Daftar (Sidebar) --}}
                    <a href="/spmb/cek_status" class="animate-on-scroll group block w-full py-4 bg-gradient-to-r from-blue-600 to-blue-600 hover:from-blue-700 hover:to-blue-700 text-white font-bold rounded-2xl text-center shadow-xl shadow-blue-600/30 transition-all transform hover:-translate-y-1 opacity-0" data-animate="fade-in-right" data-delay="200">
                        <span class="flex items-center justify-center gap-2">
                            <i class='bx bxs-user-plus text-xl'></i>
                            Daftar Jurusan Ini Sekarang
                        </span>
                    </a>
                </div>
            </div>

            {{-- KONTEN KANAN (DESKRIPSI) --}}
            <div class="lg:col-span-8 order-1 lg:order-2">
                <div class="animate-on-scroll bg-white p-8 md:p-12 rounded-[3rem] shadow-2xl shadow-slate-200/60 border border-slate-100 opacity-0" data-animate="fade-in-up">
                    
                    {{-- Judul Section --}}
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-1.5 h-12 bg-gradient-to-b from-blue-500 to-indigo-500 rounded-full"></div>
                        <div>
                            <h2 class="text-3xl md:text-4xl font-bold text-slate-800">Tentang Jurusan</h2>
                            <p class="text-slate-400 text-sm mt-1">Gambaran umum dan kurikulum pembelajaran</p>
                        </div>
                    </div>
                    
                    {{-- Artikel Deskripsi --}}
                    <article class="prose prose-lg prose-slate max-w-none text-justify leading-relaxed text-slate-600">
                        <div class="first-letter">
                            {!! nl2br(e($jurusan->deskripsi)) !!}
                        </div>
                    </article>

                    {{-- Section Prospek Kerja (Grid Modern) --}}
                    <div class="mt-16 pt-12 border-t border-slate-200">
                        <h3 class="text-2xl font-bold text-slate-800 mb-8 flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600">
                                <i class='bx bx-briefcase text-xl'></i>
                            </div>
                            Prospek Karir & Alumni
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            {{-- Item 1 --}}
                            <div class="animate-on-scroll group p-6 rounded-2xl bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 hover:shadow-lg transition-all duration-500 hover:-translate-y-2 opacity-0" data-animate="fade-in-up" data-delay="300">
                                <div class="flex flex-col items-center text-center">
                                    <div class="w-16 h-16 rounded-2xl bg-white flex items-center justify-center text-blue-600 shadow-md mb-4 group-hover:scale-110 group-hover:rotate-6 transition-transform duration-300">
                                        <i class='bx bx-building-house text-3xl'></i>
                                    </div>
                                    <h5 class="font-bold text-slate-800 text-lg mb-2">Dunia Industri</h5>
                                    <p class="text-xs text-slate-500 leading-relaxed">Bekerja di perusahaan nasional maupun multinasional.</p>
                                </div>
                            </div>

                            {{-- Item 2 --}}
                            <div class="animate-on-scroll group p-6 rounded-2xl bg-gradient-to-br from-indigo-50 to-indigo-100 border border-indigo-200 hover:shadow-lg transition-all duration-500 hover:-translate-y-2 opacity-0" data-animate="fade-in-up" data-delay="400">
                                <div class="flex flex-col items-center text-center">
                                    <div class="w-16 h-16 rounded-2xl bg-white flex items-center justify-center text-indigo-600 shadow-md mb-4 group-hover:scale-110 group-hover:rotate-6 transition-transform duration-300">
                                        <i class='bx bx-rocket text-3xl'></i>
                                    </div>
                                    <h5 class="font-bold text-slate-800 text-lg mb-2">Wirausaha Muda</h5>
                                    <p class="text-xs text-slate-500 leading-relaxed">Membangun startup atau bisnis mandiri.</p>
                                </div>
                            </div>

                            {{-- Item 3 --}}
                            <div class="animate-on-scroll group p-6 rounded-2xl bg-gradient-to-br from-cyan-50 to-cyan-100 border border-cyan-200 hover:shadow-lg transition-all duration-500 hover:-translate-y-2 opacity-0" data-animate="fade-in-up" data-delay="500">
                                <div class="flex flex-col items-center text-center">
                                    <div class="w-16 h-16 rounded-2xl bg-white flex items-center justify-center text-cyan-600 shadow-md mb-4 group-hover:scale-110 group-hover:rotate-6 transition-transform duration-300">
                                        <i class='bx bxs-graduation text-3xl'></i>
                                    </div>
                                    <h5 class="font-bold text-slate-800 text-lg mb-2">Melanjutkan Kuliah</h5>
                                    <p class="text-xs text-slate-500 leading-relaxed">Melanjutkan pendidikan ke Perguruan Tinggi.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const animatedElements = document.querySelectorAll('.animate-on-scroll');

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                const element = entry.target;
                const animation = element.dataset.animate;
                const delay = parseInt(element.dataset.delay) || 0;

                setTimeout(() => {
                    element.classList.remove('opacity-0');
                    element.classList.add('opacity-100');
                    switch(animation) {
                        case 'fade-in-up':
                            element.style.transform = 'translateY(0)';
                            break;
                        case 'fade-in-right':
                            element.style.transform = 'translateX(0)';
                            break;
                    }
                }, delay);

                observer.unobserve(element);
            }
        });
    }, { threshold: 0.1 });

    animatedElements.forEach(el => {
        el.style.transition = 'all 1s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
        // Set initial state
        if (el.dataset.animate === 'fade-in-up') {
            el.style.transform = 'translateY(30px)';
        } else if (el.dataset.animate === 'fade-in-right') {
            el.style.transform = 'translateX(30px)';
        }
        observer.observe(el);
    });
});
</script>
@endpush