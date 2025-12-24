<section id="jurusan" class="py-24 bg-slate-50 relative">
    
    {{-- Background Pattern Halus --}}
    <div class="absolute inset-0 z-0 opacity-[0.03]" style="background-image: radial-gradient(#2563eb 1px, transparent 1px); background-size: 24px 24px;"></div>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        
        {{-- Header Section --}}
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="animate-on-scroll text-blue-600 font-bold tracking-wider uppercase text-xs bg-blue-50 px-3 py-1 rounded-full border border-blue-100 inline-block opacity-0 translate-y-4" data-animate="fade-in-up">
                Program Pendidikan
            </span>
            <h2 class="animate-on-scroll text-3xl md:text-4xl font-extrabold text-slate-800 mt-4 mb-4 opacity-0 translate-y-4" data-animate="fade-in-up" data-delay="100">
                Kompetensi Keahlian
            </h2>
            <p class="animate-on-scroll text-slate-500 text-lg opacity-0 translate-y-4" data-animate="fade-in-up" data-delay="200">
                Pilih jurusan sesuai minatmu. Kami mencetak lulusan siap kerja.
            </p>
        </div>

        {{-- Grid System --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($jurusans as $item)
                {{-- Card Item (Style Overlay) --}}
                <a href="{{ route('jurusan.show', $item->id) }}" class="animate-on-scroll group block relative h-80 rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 opacity-0" data-animate="fade-in-up">
                    
                    {{-- 1. Gambar sebagai Latar Belakang --}}
                    <div class="absolute inset-0">
                        @if($item->gambar)
                            <img src="{{ asset('storage/jurusans/'.$item->gambar) }}" 
                                 alt="{{ $item->nama_jurusan }}" 
                                 class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                        @else
                            {{-- Fallback Image --}}
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-400 to-slate-600">
                                <i class='bx bxs-graduation text-8xl text-white/30'></i>
                            </div>
                        @endif
                    </div>

                    {{-- 2. Overlay Gradient untuk Keterbacaan Teks --}}
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent opacity-80 group-hover:opacity-95 transition-opacity duration-500"></div>

                    {{-- 3. Konten Kartu (Teks & Tombol) --}}
                    <div class="relative z-10 h-full p-6 flex flex-col justify-between text-white">
                        
                        {{-- Bagian Atas: Badge & Judul --}}
                        <div>
                            {{-- Badge Singkatan --}}
                            <span class="inline-block bg-blue-600/80 backdrop-blur-sm text-white text-xs font-bold px-3 py-1.5 rounded-lg mb-4">
                                {{ $item->singkatan }}
                            </span>
                            
                            {{-- Judul Jurusan --}}
                            <h3 class="text-2xl font-bold leading-tight mb-2">
                                {{ $item->nama_jurusan }}
                            </h3>
                        </div>

                        {{-- Bagian Bawah: Deskripsi & Tombol --}}
                        <div>
                            {{-- Deskripsi --}}
                            <p class="text-sm text-white/80 line-clamp-2 mb-4">
                                {{ $item->deskripsi ?? 'Program unggulan dengan kurikulum berbasis industri.' }}
                            </p>

                            {{-- Tombol Detail (Muncul saat Hover) --}}
                            <div class="transform translate-y-full group-hover:translate-y-0 transition-transform duration-500 opacity-0 group-hover:opacity-100">
                                <span class="inline-flex items-center px-4 py-2 bg-white text-blue-600 text-sm font-bold rounded-lg transition-all duration-300">
                                    Lihat Detail
                                    <i class='bx bx-right-arrow-alt ml-2 text-lg'></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                {{-- State Kosong --}}
                <div class="col-span-full py-16 text-center bg-white rounded-3xl border border-dashed border-slate-300">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-slate-50 rounded-full mb-4 text-slate-400">
                        <i class='bx bx-folder-open text-4xl'></i>
                    </div>
                    <h3 class="text-slate-600 font-medium text-lg">Belum ada data jurusan.</h3>
                </div>
            @endforelse
        </div>

    </div>
</section>

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
                    element.classList.remove('opacity-0', 'translate-y-4');
                    element.classList.add('opacity-100', 'translate-y-0');
                }, delay);

                observer.unobserve(element);
            }
        });
    }, {
        threshold: 0.1
    });

    animatedElements.forEach(el => {
        el.style.transition = 'all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
        observer.observe(el);
    });
});
</script>
@endpush