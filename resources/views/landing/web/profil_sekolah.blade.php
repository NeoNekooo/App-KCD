@extends('landing.web.layouts.master_web')

@section('title', 'Profil Sekolah - ' . ($sekolah->nama ?? 'Sekolah'))

@section('content')

{{-- =====================================================================
     1. HEADER / HERO SECTION
====================================================================== --}}
<section class="pt-32 pb-20 bg-blue-600 relative overflow-hidden">
    
    {{-- Decorative Background --}}
    <div class="absolute inset-0 pointer-events-none">
        {{-- Circle Blur --}}
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-white opacity-10 rounded-full blur-[80px]"></div>
        <div class="absolute bottom-0 left-0 w-72 h-72 bg-white opacity-5 rounded-full blur-[60px]"></div>
        {{-- Grid Pattern --}}
        <div class="absolute inset-0 opacity-10" 
             style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 30px 30px;">
        </div>
    </div>
    
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
        {{-- Breadcrumb --}}
        <div class="inline-flex items-center gap-2 text-sm text-blue-100 mb-6 font-medium animate-fade-in-up">
            <a href="{{ route('home') }}" class="hover:text-white transition-colors">Beranda</a>
            <i class='bx bx-chevron-right text-blue-300'></i>
            <span class="bg-white/10 backdrop-blur-sm px-3 py-1 rounded-full text-white text-xs font-bold uppercase tracking-wide border border-white/20">
                Profil Sekolah
            </span>
        </div>

        <h1 class="text-3xl md:text-5xl font-extrabold text-white mb-6 leading-tight animate-fade-in-up delay-100 drop-shadow-sm">
            Mengenal {{ $sekolah->nama ?? 'Sekolah Kami' }}
        </h1>
        
        <p class="text-blue-100 max-w-3xl mx-auto text-lg leading-relaxed animate-fade-in-up delay-200">
            Pusat keunggulan pendidikan yang berdedikasi mencetak generasi berkarakter, berprestasi, dan siap bersaing di era global.
        </p>
    </div>
</section>

{{-- =====================================================================
     2. MAIN CONTENT (VISI, MISI, SEJARAH)
====================================================================== --}}
<section class="py-16 md:py-24 bg-white relative">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
            
            {{-- KOLOM KIRI: KONTEN UTAMA (8 Kolom) --}}
            <div class="lg:col-span-8 space-y-12">
                
                {{-- 1. VISI & MISI (HIGHLIGHT SECTION) --}}
                <div class="animate-on-scroll opacity-0 translate-y-8">
                    <div class="grid grid-cols-1 gap-8">
                        
                        {{-- KARTU VISI (Modern Blue Gradient) --}}
                        <div class="relative bg-gradient-to-br from-blue-600 to-blue-800 rounded-3xl p-8 md:p-10 text-white shadow-2xl shadow-blue-900/20 overflow-hidden">
                            {{-- Dekorasi Ikon Besar Transparan --}}
                            <div class="absolute -right-6 -top-6 text-white opacity-10">
                                <i class='bx bxs-bulb text-[150px]'></i>
                            </div>
                            
                            <div class="relative z-10 text-center">
                                <div class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-md px-4 py-1.5 rounded-full text-sm font-bold uppercase tracking-widest mb-6 border border-white/30">
                                    <i class='bx bxs-compass'></i> Visi Sekolah
                                </div>
                                <h3 class="text-2xl md:text-3xl font-bold leading-relaxed font-serif italic">
                                    "{!! strip_tags($sambutan->visi ?? 'Visi belum diatur oleh sekolah.') !!}"
                                </h3>
                            </div>
                        </div>

                        {{-- KARTU MISI (Clean White) --}}
                        <div class="bg-slate-50 rounded-3xl p-8 md:p-10 border border-slate-100 shadow-sm relative group hover:border-blue-200 transition-colors">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-600/30">
                                    <i class='bx bxs-rocket text-2xl'></i>
                                </div>
                                <h3 class="text-2xl font-bold text-slate-800">Misi Sekolah</h3>
                            </div>
                            
                            <div class="prose prose-lg prose-slate max-w-none text-slate-600">
                                {{-- Menggunakan {!! !!} agar format list dari CKEditor backend muncul rapi --}}
                                {!! $sambutan->misi ?? '<p class="italic text-slate-400">Misi belum diatur.</p>' !!}
                            </div>
                        </div>

                    </div>
                </div>

                {{-- 2. PROGRAM KERJA (NEW SECTION) --}}
                <div class="animate-on-scroll opacity-0 translate-y-8">
                    <div class="bg-white rounded-3xl p-8 md:p-10 border-2 border-dashed border-slate-200 hover:border-blue-300 transition-colors">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center text-indigo-600">
                                <i class='bx bxs-briefcase-alt-2 text-2xl'></i>
                            </div>
                            <h3 class="text-2xl font-bold text-slate-800">Program Kerja Unggulan</h3>
                        </div>
                        
                        <div class="prose prose-lg prose-slate max-w-none text-slate-600">
                            {!! $sambutan->program_kerja ?? '<p class="italic text-slate-400">Program kerja belum diatur.</p>' !!}
                        </div>
                    </div>
                </div>

                {{-- 3. SEJARAH / PROFIL SINGKAT --}}
                <div class="animate-on-scroll opacity-0 translate-y-8">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="w-1.5 h-8 bg-blue-600 rounded-full"></span>
                        <h2 class="text-2xl md:text-3xl font-bold text-slate-900">Sejarah & Latar Belakang</h2>
                    </div>
                    
                    {{-- Gambar Sekolah --}}
                    <div class="w-full h-[300px] md:h-[400px] bg-slate-100 rounded-3xl overflow-hidden mb-8 shadow-md">
                        @if(isset($sekolah->foto_gedung))
                             <img src="{{ asset('storage/' . $sekolah->foto_gedung) }}" class="w-full h-full object-cover" alt="Gedung Sekolah">
                        @else
                             {{-- Placeholder keren --}}
                             <img src="https://images.unsplash.com/photo-1562774053-701939374585?q=80&w=1000&auto=format&fit=crop" 
                                  class="w-full h-full object-cover transform hover:scale-105 transition-transform duration-700" 
                                  alt="Gedung Sekolah">
                        @endif
                    </div>

                    <div class="prose prose-lg prose-slate max-w-none text-slate-600 leading-relaxed text-justify">
                        {!! $sekolah->sejarah ?? $sekolah->deskripsi ?? '<p>Deskripsi sejarah sekolah belum diisi.</p>' !!}
                    </div>
                </div>

            </div>

            {{-- KOLOM KANAN: IDENTITAS & SIDEBAR (4 Kolom) --}}
            <div class="lg:col-span-4 space-y-8 lg:sticky lg:top-28 animate-on-scroll opacity-0 translate-y-8 delay-100">
                
                {{-- Widget Identitas Resmi --}}
                <div class="bg-white rounded-3xl p-8 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100">
                    <div class="text-center mb-8">
                        <div class="w-24 h-24 mx-auto bg-white rounded-full flex items-center justify-center mb-4 p-4 border border-slate-100 shadow-md">
                            @if($sekolah->logo)
                                <img src="{{ asset('storage/' . $sekolah->logo) }}" class="w-full h-full object-contain" alt="Logo">
                            @else
                                <i class='bx bxs-school text-4xl text-blue-600'></i>
                            @endif
                        </div>
                        <h3 class="text-lg font-bold text-slate-900">{{ $sekolah->nama }}</h3>
                        <div class="inline-block px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold mt-2">
                            Terakreditasi {{ $sekolah->akreditasi ?? '-' }}
                        </div>
                    </div>

                    <div class="space-y-5">
                        {{-- NPSN --}}
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 flex-shrink-0">
                                <i class='bx bxs-id-card text-xl'></i>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">NPSN</p>
                                <p class="text-slate-800 font-bold">{{ $sekolah->npsn ?? '-' }}</p>
                            </div>
                        </div>

                        {{-- Kepala Sekolah --}}
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 flex-shrink-0">
                                <i class='bx bxs-user-badge text-xl'></i>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">Kepala Sekolah</p>
                                <p class="text-slate-800 font-bold text-sm">{{ $sekolah->nama_kepala_sekolah ?? '-' }}</p>
                            </div>
                        </div>

                        {{-- Alamat --}}
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-full bg-orange-50 flex items-center justify-center text-orange-600 flex-shrink-0">
                                <i class='bx bxs-map text-xl'></i>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">Lokasi</p>
                                <p class="text-slate-800 font-medium text-sm leading-snug">
                                    {{ $sekolah->alamat_jalan ?? '-' }}
                                </p>
                            </div>
                        </div>

                        {{-- Kontak --}}
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center text-red-600 flex-shrink-0">
                                <i class='bx bxs-phone-call text-xl'></i>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">Hubungi Kami</p>
                                <p class="text-slate-800 font-medium text-sm">{{ $sekolah->nomor_telepon ?? '-' }}</p>
                                <p class="text-slate-500 text-xs mt-1">{{ $sekolah->email ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Widget Map --}}
                @if($sekolah->peta)
                <div class="bg-white rounded-3xl overflow-hidden shadow-md border border-slate-100 h-64 relative group">
                    <div class="w-full h-full [&>iframe]:w-full [&>iframe]:h-full grayscale group-hover:grayscale-0 transition-all duration-500">
                        {!! $sekolah->peta !!}
                    </div>
                    {{-- Overlay Klik --}}
                    <a href="https://maps.google.com" target="_blank" class="absolute inset-0 bg-blue-900/0 group-hover:bg-blue-900/10 transition-colors pointer-events-none"></a>
                </div>
                @endif

            </div>

        </div>
    </div>
</section>

@endsection

@push('styles')
<style>
    /* Animasi Dasar */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-up {
        animation: fadeInUp 0.8s ease-out forwards;
        opacity: 0;
    }
    .delay-100 { animation-delay: 0.1s; }
    .delay-200 { animation-delay: 0.2s; }

    /* Animasi Scroll Reveal */
    .animate-on-scroll {
        transition: opacity 0.8s ease-out, transform 0.8s ease-out;
    }
    .animate-on-scroll.visible {
        opacity: 1 !important;
        transform: translateY(0) !important;
    }

    /* Styling List untuk Misi & Program (dari CKEditor) agar rapi */
    .prose ul {
        list-style-type: none;
        padding-left: 0;
    }
    .prose ul li {
        position: relative;
        padding-left: 1.5rem;
        margin-bottom: 0.5rem;
    }
    .prose ul li::before {
        content: "\2713"; /* Checkmark symbol */
        position: absolute;
        left: 0;
        color: #2563eb; /* Blue-600 */
        font-weight: bold;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Intersection Observer untuk animasi scroll
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.animate-on-scroll').forEach(el => observer.observe(el));
    });
</script>
@endpush