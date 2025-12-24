@extends('landing.web.layouts.master_web')

@section('title', 'Fasilitas Sekolah')

@section('content')

{{-- HERO SECTION FASILITAS --}}
<section class="relative pt-36 pb-24 bg-slate-900 overflow-hidden">
    
    {{-- 1. Background Image (Tema Gedung/Sekolah) --}}
    <div class="absolute inset-0 z-0">
        {{-- Gambar Ilustrasi Gedung Sekolah Modern (Unsplash) --}}
        <img src="https://images.unsplash.com/photo-1562774053-701939374585?q=80&w=1986&auto=format&fit=crop" 
             class="w-full h-full object-cover opacity-40" 
             alt="School Building">
        
        {{-- Overlay Gradient Biru --}}
        <div class="absolute inset-0 bg-gradient-to-r from-blue-900/90 to-indigo-900/80 mix-blend-multiply"></div>
        <div class="absolute inset-0 bg-slate-900/30"></div>
    </div>

    {{-- Content Hero --}}
    <div class="container mx-auto px-4 relative z-10 text-center">
        {{-- Breadcrumb --}}
        <a href="{{ route('home') }}" class="inline-flex items-center text-blue-200 hover:text-white text-sm font-semibold mb-6 transition-colors bg-white/10 px-4 py-2 rounded-full backdrop-blur-md border border-white/10">
            <i class='bx bx-home-alt mr-2'></i> Beranda
        </a>
        
        <h1 class="text-4xl md:text-6xl font-extrabold text-white mb-6 drop-shadow-xl tracking-tight">
            Fasilitas Sekolah
        </h1>
        
        <p class="text-blue-100 text-lg md:text-xl max-w-2xl mx-auto leading-relaxed font-light">
            Jelajahi sarana dan prasarana lengkap yang kami sediakan untuk mendukung kenyamanan dan keberhasilan belajar siswa.
        </p>
    </div>
</section>

{{-- KONTEN GALERI UTAMA --}}
<section class="py-20 bg-slate-50 relative">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Grid Masonry Style --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($fasilitas as $item)
                <div class="group bg-white rounded-3xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 border border-slate-100 flex flex-col h-full hover:-translate-y-2">
                    
                    {{-- Gambar --}}
                    <div class="relative h-64 overflow-hidden">
                        <img src="{{ $item->foto ? asset('storage/fasilitas/'.$item->foto) : 'https://placehold.co/600x400/e2e8f0/475569?text=Fasilitas' }}" 
                             class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" 
                             alt="{{ $item->nama_fasilitas }}"
                             loading="lazy">
                        
                        {{-- Overlay Gradient --}}
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        
                        {{-- Badge Icon --}}
                        <div class="absolute top-4 right-4 bg-white/90 backdrop-blur rounded-xl p-2 shadow-md text-blue-600">
                            <i class='bx bx-image-alt text-xl'></i>
                        </div>
                    </div>

                    {{-- Konten --}}
                    <div class="p-6 flex flex-col flex-grow">
                        <h3 class="text-xl font-bold text-slate-800 mb-3 group-hover:text-blue-600 transition-colors">
                            {{ $item->nama_fasilitas }}
                        </h3>
                        
                        <p class="text-slate-500 text-sm leading-relaxed mb-6 line-clamp-3 flex-grow">
                            {{ $item->deskripsi ?? 'Fasilitas penunjang kegiatan belajar mengajar yang nyaman dan modern.' }}
                        </p>

                        {{-- Footer Card --}}
                        <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                            <span class="text-xs font-bold text-blue-600 bg-blue-50 px-3 py-1 rounded-full uppercase tracking-wide">
                                Tersedia
                            </span>
                            
                            {{-- Tombol Zoom (Pemicu Modal) --}}
                            <button type="button" 
                                    onclick="openModal('{{ $item->foto ? asset('storage/fasilitas/'.$item->foto) : '' }}', '{{ $item->nama_fasilitas }}', `{{ $item->deskripsi }}`)"
                                    class="text-slate-400 hover:text-blue-600 transition-colors flex items-center gap-1 text-sm font-medium">
                                <i class='bx bx-zoom-in text-lg'></i> Perbesar
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-20 text-center">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-slate-100 rounded-full mb-6 text-slate-300">
                        <i class='bx bx-images text-4xl'></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-600 mb-2">Belum ada data fasilitas.</h3>
                    <p class="text-slate-400">Silakan hubungi admin untuk update data terbaru.</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination (Jika ada) --}}
        <div class="mt-12">
            {{-- $fasilitas->links() --}} {{-- Aktifkan jika pakai pagination di controller --}}
        </div>

    </div>
</section>

{{-- MODAL PREVIEW GAMBAR (Lightbox Sederhana) --}}
<div id="imageModal" class="fixed inset-0 z-50 hidden bg-black/95 backdrop-blur-md flex items-center justify-center p-4 transition-opacity opacity-0">
    <div class="relative max-w-5xl w-full">
        {{-- Tombol Close --}}
        <button onclick="closeModal()" class="absolute -top-12 right-0 text-white hover:text-blue-400 transition-colors">
            <i class='bx bx-x text-4xl'></i>
        </button>

        <div class="flex flex-col md:flex-row bg-white rounded-2xl overflow-hidden shadow-2xl">
            {{-- Gambar Besar --}}
            <div class="md:w-2/3 bg-black flex items-center justify-center h-[50vh] md:h-[70vh]">
                <img id="modalImg" src="" class="max-w-full max-h-full object-contain" alt="">
            </div>
            
            {{-- Keterangan di Samping --}}
            <div class="md:w-1/3 p-8 flex flex-col justify-center bg-white border-l border-slate-100">
                <h3 id="modalTitle" class="text-2xl font-bold text-slate-900 mb-4"></h3>
                <div class="w-12 h-1 bg-blue-600 rounded-full mb-6"></div>
                <p id="modalDesc" class="text-slate-600 leading-relaxed text-sm md:text-base"></p>
                
                <div class="mt-auto pt-8">
                    <button onclick="closeModal()" class="w-full py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition-colors">
                        Tutup Galeri
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SCRIPT MODAL --}}
<script>
    const modal = document.getElementById('imageModal');
    const modalImg = document.getElementById('modalImg');
    const modalTitle = document.getElementById('modalTitle');
    const modalDesc = document.getElementById('modalDesc');

    function openModal(imgSrc, title, desc) {
        if(!imgSrc) return; // Jangan buka jika tidak ada gambar
        
        modalImg.src = imgSrc;
        modalTitle.textContent = title;
        modalDesc.textContent = (desc && desc !== 'null') ? desc : 'Tidak ada deskripsi tambahan.';
        
        modal.classList.remove('hidden');
        // Animasi fade in
        setTimeout(() => {
            modal.classList.remove('opacity-0');
        }, 10);
        document.body.style.overflow = 'hidden'; // Disable scroll
    }

    function closeModal() {
        modal.classList.add('opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto'; // Enable scroll
        }, 300);
    }

    // Close on click outside
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            closeModal();
        }
    });
    
    // Close on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });
</script>

@endsection