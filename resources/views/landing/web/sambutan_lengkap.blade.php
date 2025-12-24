@extends('landing.web.layouts.master_web')

@section('title', 'Sambutan Kepala Sekolah')

@push('styles')
<style>
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

{{-- LOGIKA PHP: HANDLING DATA KOSONG --}}
@php
    $judul = $sambutan->judul_sambutan ?? 'Sambutan Kepala Sekolah';
    $nama  = $sambutan->nama_kepala_sekolah ?? 'Kepala Sekolah';
    $isi   = $sambutan->isi_sambutan ?? 'Mohon maaf, isi sambutan belum tersedia saat ini.';
    $foto  = $sambutan->foto ?? null;
@endphp

{{-- HEADER / BREADCRUMB SECTION --}}
<section class="relative bg-gradient-to-br from-slate-50 to-blue-50 pt-32 pb-16 overflow-hidden">
    <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%239C92AC" fill-opacity="0.05"%3E%3Cpath d="M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-0.5"></div>
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center max-w-3xl mx-auto">
            <a href="{{ route('home') }}" class="inline-flex items-center text-sm font-semibold text-blue-600 hover:text-blue-800 transition-all duration-300 mb-8 group">
                <i class='bx bx-arrow-back mr-2 transition-transform group-hover:-translate-x-1'></i> Kembali ke Beranda
            </a>
            <h1 class="animate-on-scroll text-4xl md:text-6xl font-extrabold text-slate-900 tracking-tight mb-8 opacity-0 translate-y-8 transition-all duration-1000 ease-out">
                {{ $judul }}
            </h1>
            <div class="animate-on-scroll h-1.5 w-32 bg-gradient-to-r from-blue-400 to-blue-600 mx-auto rounded-full opacity-0 translate-y-8 transition-all duration-1000 ease-out" data-delay="200"></div>
        </div>
    </div>
</section>

{{-- MAIN CONTENT SECTION --}}
<section class="bg-white py-24 lg:py-32 relative">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 lg:gap-24">
            
            {{-- SIDEBAR KIRI: FOTO & PROFIL (Sticky) --}}
            <div class="lg:col-span-4">
                <div class="animate-on-scroll sticky top-32 opacity-0 translate-x-8 transition-all duration-1000 ease-out" data-delay="400">
                    <div class="relative group">
                        {{-- Frame Foto --}}
                        <div class="relative rounded-3xl overflow-hidden shadow-2xl bg-slate-100 aspect-[3/4]">
                            @if($foto)
                                <img src="{{ asset('storage/sambutan/'.$foto) }}" 
                                     class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" 
                                     alt="{{ $nama }}">
                            @else
                                <div class="w-full h-full flex flex-col items-center justify-center text-slate-400">
                                    <i class='bx bxs-user text-8xl opacity-20'></i>
                                </div>
                            @endif
                            
                            {{-- Overlay Nama --}}
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                            <div class="absolute bottom-0 left-0 w-full p-6 text-white transform translate-y-full group-hover:translate-y-0 transition-transform duration-500">
                                <p class="text-xs font-bold uppercase tracking-widest text-blue-200 mb-1">Kepala Sekolah</p>
                                <h3 class="text-xl font-bold leading-tight">{{ $nama }}</h3>
                            </div>
                        </div>
                    </div>

                    {{-- Informasi Tambahan --}}
                    <div class="mt-12 p-6 bg-gradient-to-br from-blue-50 to-slate-50 rounded-2xl border border-blue-100 text-center shadow-lg">
                        <i class='bx bxs-quote-alt-left text-3xl text-blue-500 mb-4'></i>
                        <p class="text-slate-600 text-sm italic font-medium">
                            "Membangun generasi cerdas dan berkarakter untuk masa depan yang gemilang."
                        </p>
                    </div>
                </div>
            </div>

            {{-- KONTEN KANAN: TEKS SAMBUTAN --}}
            <div class="lg:col-span-8 space-y-8">
                
                {{-- Pembuka --}}
                <div class="animate-on-scroll opacity-0 translate-y-8 transition-all duration-1000 ease-out" data-delay="600">
                    <p class="text-xl md:text-2xl text-slate-600 leading-relaxed font-light">
                        Assalamualaikum Warahmatullahi Wabarakatuh,<br>
                        Salam sejahtera bagi kita semua.
                    </p>
                </div>

                {{-- Isi Utama --}}
                <article class="animate-on-scroll prose prose-lg prose-slate max-w-none prose-headings:text-slate-800 prose-a:text-blue-600 hover:prose-a:text-blue-700 text-justify first-letter opacity-0 translate-y-8 transition-all duration-1000 ease-out" data-delay="800">
                    {!! nl2br(e($isi)) !!}
                </article>

                {{-- Penutup / Tanda Tangan --}}
                <div class="animate-on-scroll mt-20 pt-10 border-t border-slate-200 flex flex-col items-end opacity-0 translate-y-8 transition-all duration-1000 ease-out" data-delay="1000">
                    <p class="text-slate-500 mb-4 text-sm font-medium uppercase tracking-wider">Hormat Kami,</p>
                    <div class="text-right">
                        <h4 class="text-2xl font-bold text-slate-900">{{ $nama }}</h4>
                        <p class="text-blue-600 text-sm font-semibold">Kepala Sekolah</p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

{{-- QUOTE SECTION (DIPINDAHKAN KE BAWAH) --}}
<section class="relative py-24 bg-gradient-to-r from-slate-50 to-blue-50">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="animate-on-scroll max-w-4xl mx-auto text-center opacity-0 translate-y-8 transition-all duration-1000 ease-out" data-delay="1200">
            <i class='bx bxs-quote-alt-left text-6xl text-blue-200 mb-6'></i>
            <blockquote class="text-3xl md:text-4xl font-serif font-bold text-slate-800 leading-relaxed">
                Pendidikan adalah seni menyalakan api, bukan sekadar mengisi wadah.
            </blockquote>
        </div>
    </div>
</section>

{{-- CALL TO ACTION (CTA) SECTION --}}
<section class="animate-on-scroll relative bg-gradient-to-br from-blue-600 to-blue-800 py-20 overflow-hidden opacity-0 translate-y-8 transition-all duration-1000 ease-out" data-delay="1400">
    <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.05"%3E%3Cpath d="M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')]"></div>
    <div class="container mx-auto px-4 text-center relative z-10">
        <h2 class="text-3xl md:text-4xl font-bold text-white mb-8">Bergabunglah Menjadi Bagian dari Kami</h2>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('ppdb.cek_status') }}" class="group relative inline-flex items-center justify-center px-6 py-3 overflow-hidden font-bold text-white rounded-full shadow-lg transition-all duration-300 bg-white text-blue-700 hover:bg-slate-100 transform hover:-translate-y-1">
                <span class="relative flex items-center gap-2 text-sm">
                    <i class='bx bxs-user-plus text-lg'></i>
                    Daftar SPMB Sekarang
                </span>
            </a>
            <a href="{{ route('home') }}" class="group relative inline-flex items-center justify-center px-6 py-3 overflow-hidden font-bold text-white rounded-full border-2 border-white/30 hover:border-white transition-all duration-300">
                <span class="relative flex items-center gap-2 text-sm">
                    <i class='bx bx-search-alt text-lg'></i>
                    Jelajahi Sekolah
                </span>
            </a>
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
                const delay = parseInt(element.dataset.delay) || 0;
                
                setTimeout(() => {
                    element.classList.remove('opacity-0', 'translate-y-8', 'translate-x-8');
                    element.classList.add('opacity-100', 'translate-y-0', 'translate-x-0');
                }, delay);

                observer.unobserve(element);
            }
        });
    }, {
        threshold: 0.1
    });

    animatedElements.forEach(el => {
        el.style.transition = 'all 1s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
        observer.observe(el);
    });

});
</script>
@endpush