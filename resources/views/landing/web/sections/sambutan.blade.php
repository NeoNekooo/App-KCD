@php
    $dataSambutan = \DB::table('sambutan_kepala_sekolahs')->first(); 
    
    $judul   = $dataSambutan->judul_sambutan ?? 'Selamat Datang';
    $nama    = $dataSambutan->nama_kepala_sekolah ?? 'Kepala Sekolah';
    // Gunakan strip_tags untuk mengambil teks bersih, lalu potong max 400 karakter
    $isiRaw  = strip_tags($dataSambutan->isi_sambutan ?? 'Sambutan belum diisi.');
    $isi     = \Illuminate\Support\Str::limit($isiRaw, 400, '...');
    $foto    = $dataSambutan->foto ?? null;
@endphp

<section id="sambutan" class="py-20 lg:py-32 bg-gradient-to-br from-slate-50 to-white relative overflow-hidden">
    
    {{-- Hiasan Background Modern --}}
    <div class="absolute top-10 left-10 w-72 h-72 bg-blue-400/20 rounded-full mix-blend-multiply filter blur-3xl animate-pulse"></div>
    <div class="absolute bottom-10 right-10 w-72 h-72 bg-indigo-400/20 rounded-full mix-blend-multiply filter blur-3xl animate-pulse"></div>
    <div class="absolute top-1/2 left-1/3 w-96 h-96 bg-blue-300/10 rounded-full mix-blend-multiply filter blur-3xl"></div>

    <div class="container mx-auto px-6 lg:px-12 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-center">
            
            {{-- KOLOM KIRI: TEKS --}}
            <div class="text-center lg:text-left space-y-6">
                
                {{-- Badge --}}
                <div class="animate-element inline-flex items-center gap-2 px-4 py-2 rounded-full bg-blue-600 text-white text-xs font-bold tracking-widest uppercase shadow-lg shadow-blue-500/30 opacity-0 translate-y-8 transition-all duration-1000 ease-out" data-delay="0">
                    <i class='bx bxs-quote-alt-left text-lg'></i>
                    Sambutan Kepala Sekolah
                </div>
                
                {{-- Judul --}}
                <h2 class="animate-element text-4xl lg:text-5xl font-extrabold text-slate-900 leading-tight opacity-0 translate-y-8 transition-all duration-1000 ease-out" data-delay="150">
                    {{ $judul }}
                </h2>

                {{-- Nama Kepala Sekolah --}}
                <div class="animate-element flex items-center justify-center lg:justify-start gap-4 opacity-0 translate-y-8 transition-all duration-1000 ease-out" data-delay="300">
                    <div class="h-1 bg-gradient-to-r from-blue-400 to-blue-600 rounded-full" style="width: 0px;" data-target-width="64px"></div>
                    <h3 class="text-xl font-semibold text-slate-700">
                        {{ $nama }}
                    </h3>
                </div>

                {{-- Isi Sambutan (DIPOTONG/TRUNCATED) --}}
                {{-- Kita tampilkan sebagai teks biasa karena sudah di-strip_tags --}}
                <div class="animate-element text-lg text-slate-600 leading-relaxed text-justify opacity-0 translate-y-8 transition-all duration-1000 ease-out" data-delay="450">
                    <p>
                        {{ $isi }}
                    </p>
                </div>

                {{-- Tombol Baca Selengkapnya --}}
                <a href="{{ route('sambutan.lengkap') }}" class="animate-element group inline-flex items-center gap-3 px-8 py-4 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white text-base font-bold rounded-full transition-all duration-300 shadow-xl shadow-blue-500/30 transform hover:-translate-y-1 hover:shadow-2xl opacity-0 translate-y-8" data-delay="600">
                    <span>Baca Selengkapnya</span>
                    <i class='bx bx-right-arrow-alt text-xl transition-transform group-hover:translate-x-2'></i>
                </a>
            </div>

            {{-- KOLOM KANAN: FOTO --}}
            <div class="relative flex justify-center lg:justify-end">
                <div class="relative w-80 h-96 lg:h-full">
                    
                    {{-- Efek Foto --}}
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-100 to-blue-200 rounded-3xl transform rotate-6 shadow-2xl"></div>
                    <div class="absolute inset-4 bg-white rounded-3xl transform -rotate-3 shadow-xl border border-slate-100"></div>
                    
                    <div class="relative h-full rounded-3xl overflow-hidden shadow-2xl group cursor-pointer transform transition-all duration-500 hover:-rotate-2">
                        @if($foto)
                            <img src="{{ asset('storage/sambutan/'.$foto) }}" 
                                 class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" 
                                 alt="{{ $nama }}">
                            
                            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-80"></div>
                            
                            <div class="absolute bottom-0 left-0 w-full p-6 text-white">
                                <p class="text-2xl font-bold mb-1">{{ $nama }}</p>
                                <p class="text-sm font-medium uppercase tracking-widest opacity-90">Kepala Sekolah</p>
                            </div>
                        @else
                            <div class="w-full h-full bg-slate-200 flex flex-col items-center justify-center text-slate-400">
                                <i class='bx bxs-user-circle text-8xl opacity-50'></i>
                                <span class="text-sm mt-4 font-semibold">Foto Tidak Tersedia</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

    const sambutanSection = document.getElementById('sambutan');
    if (!sambutanSection) return;

    const observerOptions = {
        root: null, 
        rootMargin: '0px',
        threshold: 0.2 
    };

    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const elementsToAnimate = entry.target.querySelectorAll('.animate-element');
                
                elementsToAnimate.forEach(el => {
                    const delay = parseInt(el.dataset.delay) || 0;
                    
                    setTimeout(() => {
                        el.classList.remove('opacity-0', 'translate-y-8');
                        el.classList.add('opacity-100', 'translate-y-0');

                        // Animasi khusus untuk garis
                        const line = el.querySelector('[data-target-width]');
                        if (line) {
                            line.style.width = line.dataset.targetWidth;
                        }
                    }, delay);
                });

                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    observer.observe(sambutanSection);

});
</script>
@endpush