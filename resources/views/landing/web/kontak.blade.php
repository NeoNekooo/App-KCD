@extends('landing.web.layouts.master_web')

@section('title', 'Hubungi Kami')

@section('content')

{{-- =====================================================================
     1. HEADER SECTION (BIRU KONSISTEN)
====================================================================== --}}
<section class="pt-32 pb-20 bg-blue-600 relative overflow-hidden">
    {{-- Background Decoration --}}
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-white opacity-10 rounded-full blur-[80px]"></div>
        <div class="absolute bottom-0 left-0 w-72 h-72 bg-white opacity-5 rounded-full blur-[60px]"></div>
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 30px 30px;"></div>
    </div>
    
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
        <div class="inline-flex items-center gap-2 text-sm text-blue-100 mb-6 font-medium animate-fade-in-up">
            <a href="{{ route('home') }}" class="hover:text-white transition-colors">Beranda</a>
            <i class='bx bx-chevron-right text-blue-300'></i>
            <span class="bg-white/10 backdrop-blur-sm px-3 py-1 rounded-full text-white text-xs font-bold uppercase tracking-wide border border-white/20">
                Hubungi Kami
            </span>
        </div>

        <h1 class="text-3xl md:text-5xl font-extrabold text-white mb-6 leading-tight animate-fade-in-up delay-100">
            Layanan Informasi Sekolah
        </h1>
        
        <p class="text-blue-100 max-w-2xl mx-auto text-lg leading-relaxed animate-fade-in-up delay-200">
            Punya pertanyaan seputar PPDB atau informasi sekolah? Kami siap membantu Anda. Silakan hubungi kami melalui saluran resmi berikut.
        </p>
    </div>
</section>

{{-- =====================================================================
     2. INFO CARDS (TELEPON, EMAIL, ALAMAT)
====================================================================== --}}
<section class="py-16 md:py-24 bg-white relative -mt-10">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-20">
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            
            {{-- CARD 1: TELEPON & FAX --}}
            <div class="bg-white p-8 rounded-3xl shadow-xl shadow-blue-900/5 border border-slate-100 hover:-translate-y-2 hover:shadow-2xl hover:shadow-blue-900/10 transition-all duration-300 group animate-on-scroll opacity-0 translate-y-8">
                <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600 mb-6 group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300 shadow-sm">
                    <i class='bx bx-phone-call text-3xl'></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2">Telepon & Fax</h3>
                <p class="text-slate-500 text-sm mb-6">Layanan komunikasi langsung (Jam Kerja)</p>
                
                <div class="space-y-3">
                    <a href="tel:{{ $sekolah->nomor_telepon }}" class="flex items-center gap-3 text-slate-700 font-bold hover:text-blue-600 transition-colors p-3 rounded-xl hover:bg-slate-50 border border-transparent hover:border-slate-100">
                        <i class='bx bx-phone text-xl text-blue-500'></i>
                        {{ $sekolah->nomor_telepon ?? '-' }}
                    </a>
                    {{-- Jika ada Fax (Asumsi field fax ada, jika tidak pakai telp) --}}
                    @if(isset($sekolah->fax))
                    <div class="flex items-center gap-3 text-slate-600 font-medium p-3">
                        <i class='bx bx-printer text-xl text-slate-400'></i>
                        <span>Fax: {{ $sekolah->fax }}</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- CARD 2: EMAIL & WEB --}}
            <div class="bg-white p-8 rounded-3xl shadow-xl shadow-blue-900/5 border border-slate-100 hover:-translate-y-2 hover:shadow-2xl hover:shadow-blue-900/10 transition-all duration-300 group animate-on-scroll opacity-0 translate-y-8" style="animation-delay: 100ms;">
                <div class="w-14 h-14 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 mb-6 group-hover:bg-indigo-600 group-hover:text-white transition-colors duration-300 shadow-sm">
                    <i class='bx bx-envelope text-3xl'></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2">Email & Website</h3>
                <p class="text-slate-500 text-sm mb-6">Kirim pesan tertulis atau kunjungi kami</p>
                
                <div class="space-y-3">
                    <a href="mailto:{{ $sekolah->email }}" class="flex items-center gap-3 text-slate-700 font-bold hover:text-indigo-600 transition-colors p-3 rounded-xl hover:bg-slate-50 border border-transparent hover:border-slate-100 overflow-hidden">
                        <i class='bx bx-mail-send text-xl text-indigo-500'></i>
                        <span class="truncate">{{ $sekolah->email ?? '-' }}</span>
                    </a>
                    <a href="{{ url('/') }}" class="flex items-center gap-3 text-slate-700 font-bold hover:text-indigo-600 transition-colors p-3 rounded-xl hover:bg-slate-50 border border-transparent hover:border-slate-100 overflow-hidden">
                        <i class='bx bx-globe text-xl text-indigo-500'></i>
                        <span class="truncate">{{ request()->getHost() }}</span>
                    </a>
                </div>
            </div>

            {{-- CARD 3: LOKASI --}}
            <div class="bg-white p-8 rounded-3xl shadow-xl shadow-blue-900/5 border border-slate-100 hover:-translate-y-2 hover:shadow-2xl hover:shadow-blue-900/10 transition-all duration-300 group animate-on-scroll opacity-0 translate-y-8" style="animation-delay: 200ms;">
                <div class="w-14 h-14 bg-orange-50 rounded-2xl flex items-center justify-center text-orange-600 mb-6 group-hover:bg-orange-600 group-hover:text-white transition-colors duration-300 shadow-sm">
                    <i class='bx bx-map-pin text-3xl'></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2">Lokasi Sekolah</h3>
                <p class="text-slate-500 text-sm mb-6">Kunjungi kampus kami secara langsung</p>
                
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                    <p class="text-slate-700 font-medium leading-relaxed text-sm">
                        {{ $sekolah->alamat_jalan ?? 'Alamat belum diisi' }}<br>
                        {{ $sekolah->kecamatan ?? '' }}, {{ $sekolah->kabupaten_kota ?? '' }}<br>
                        Provinsi {{ $sekolah->provinsi ?? '' }}
                    </p>
                </div>
            </div>

        </div>

    </div>
</section>

{{-- =====================================================================
     3. SOCIAL MEDIA & MAPS
====================================================================== --}}
<section class="pb-24 bg-white">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12">
            
            {{-- KOLOM KIRI: SOSIAL MEDIA --}}
            <div class="lg:col-span-4 animate-on-scroll opacity-0 translate-y-8">
                <div class="bg-slate-50 rounded-3xl p-8 border border-slate-100 h-full">
                    <h3 class="text-xl font-bold text-slate-900 mb-6 flex items-center gap-2">
                        <i class='bx bxs-share-alt text-blue-600'></i> Ikuti Kami
                    </h3>
                    <p class="text-slate-500 text-sm mb-8 leading-relaxed">
                        Dapatkan informasi terbaru dan dokumentasi kegiatan sekolah melalui media sosial resmi kami.
                    </p>

                    <div class="grid grid-cols-1 gap-4">
                        @if($sekolah->facebook_url)
                        <a href="{{ $sekolah->facebook_url }}" target="_blank" class="flex items-center gap-4 p-4 bg-white rounded-2xl shadow-sm border border-slate-100 hover:shadow-md hover:border-blue-200 transition-all group">
                            <div class="w-10 h-10 rounded-full bg-[#1877F2]/10 flex items-center justify-center text-[#1877F2] group-hover:bg-[#1877F2] group-hover:text-white transition-colors">
                                <i class='bx bxl-facebook text-2xl'></i>
                            </div>
                            <span class="font-bold text-slate-700 group-hover:text-[#1877F2]">Facebook</span>
                        </a>
                        @endif

                        @if($sekolah->instagram_url)
                        <a href="{{ $sekolah->instagram_url }}" target="_blank" class="flex items-center gap-4 p-4 bg-white rounded-2xl shadow-sm border border-slate-100 hover:shadow-md hover:border-pink-200 transition-all group">
                            <div class="w-10 h-10 rounded-full bg-[#E4405F]/10 flex items-center justify-center text-[#E4405F] group-hover:bg-[#E4405F] group-hover:text-white transition-colors">
                                <i class='bx bxl-instagram text-2xl'></i>
                            </div>
                            <span class="font-bold text-slate-700 group-hover:text-[#E4405F]">Instagram</span>
                        </a>
                        @endif

                        @if($sekolah->youtube_url)
                        <a href="{{ $sekolah->youtube_url }}" target="_blank" class="flex items-center gap-4 p-4 bg-white rounded-2xl shadow-sm border border-slate-100 hover:shadow-md hover:border-red-200 transition-all group">
                            <div class="w-10 h-10 rounded-full bg-[#FF0000]/10 flex items-center justify-center text-[#FF0000] group-hover:bg-[#FF0000] group-hover:text-white transition-colors">
                                <i class='bx bxl-youtube text-2xl'></i>
                            </div>
                            <span class="font-bold text-slate-700 group-hover:text-[#FF0000]">YouTube</span>
                        </a>
                        @endif

                        @if($sekolah->tiktok_url)
                        <a href="{{ $sekolah->tiktok_url }}" target="_blank" class="flex items-center gap-4 p-4 bg-white rounded-2xl shadow-sm border border-slate-100 hover:shadow-md hover:border-slate-300 transition-all group">
                            <div class="w-10 h-10 rounded-full bg-black/10 flex items-center justify-center text-black group-hover:bg-black group-hover:text-white transition-colors">
                                <i class='bx bxl-tiktok text-2xl'></i>
                            </div>
                            <span class="font-bold text-slate-700 group-hover:text-black">TikTok</span>
                        </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: PETA LOKASI --}}
            <div class="lg:col-span-8 animate-on-scroll opacity-0 translate-y-8 delay-100">
                <div class="bg-white rounded-3xl p-2 border border-slate-200 shadow-lg h-full min-h-[400px]">
                    <div class="w-full h-full rounded-2xl overflow-hidden relative grayscale hover:grayscale-0 transition-all duration-700 [&>iframe]:w-full [&>iframe]:h-full">
                        @if($sekolah->peta)
                            {!! $sekolah->peta !!}
                        @else
                            <div class="w-full h-full bg-slate-50 flex flex-col items-center justify-center text-slate-400">
                                <i class='bx bx-map-alt text-6xl mb-4'></i>
                                <span class="font-medium">Peta lokasi belum disematkan.</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

@endsection

@push('styles')
<style>
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

    .animate-on-scroll {
        transition: opacity 0.8s ease-out, transform 0.8s ease-out;
    }
    .animate-on-scroll.visible {
        opacity: 1 !important;
        transform: translateY(0) !important;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
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