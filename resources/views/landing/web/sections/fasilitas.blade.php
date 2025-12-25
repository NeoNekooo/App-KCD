@if(isset($fasilitas) && $fasilitas->count() > 0)
<section id="fasilitas" class="py-24 bg-white relative overflow-hidden">
    
    {{-- Background Pattern Modern --}}
    <div class="absolute inset-0 opacity-[0.03] pointer-events-none" 
         style="background-image: radial-gradient(#2563eb 1px, transparent 1px); background-size: 32px 32px;">
    </div>
    
    {{-- Gradient Accent Kiri Bawah --}}
    <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-blue-50 rounded-full mix-blend-multiply filter blur-3xl opacity-50 -z-10 translate-y-1/2 -translate-x-1/4"></div>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        
        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-6">
            <div class="max-w-2xl">
                <span class="animate-on-scroll text-blue-600 font-bold tracking-wider uppercase text-sm bg-blue-50 px-3 py-1 rounded-full border border-blue-100 mb-4 inline-block opacity-0 translate-y-4" data-animate="fade-in-up">
                    Sarana & Prasarana
                </span>
                <h2 class="animate-on-scroll text-3xl md:text-4xl lg:text-5xl font-extrabold text-slate-900 leading-tight mb-4 opacity-0 translate-y-4" data-animate="fade-in-up" data-delay="100">
                    Fasilitas <span class="text-blue-600">Unggulan</span>
                </h2>
                <p class="animate-on-scroll text-slate-500 text-lg leading-relaxed opacity-0 translate-y-4" data-animate="fade-in-up" data-delay="200">
                    Menunjang kegiatan belajar mengajar dengan sarana modern, nyaman, dan berstandar industri untuk mencetak generasi kompeten.
                </p>
            </div>

            {{-- Tombol Lihat Semua (Desktop) --}}
            <div class="hidden md:block animate-on-scroll opacity-0 translate-y-4" data-animate="fade-in-up" data-delay="300">
                <a href="{{ route('fasilitas.semua') }}" class="group inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-full shadow-lg shadow-blue-500/30 transition-all transform hover:-translate-y-1">
                    <span>Lihat Semua</span>
                    <i class='bx bx-right-arrow-alt text-xl group-hover:translate-x-1 transition-transform'></i>
                </a>
            </div>
        </div>

        {{-- Grid Gallery (Seragam & Modern) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($fasilitas->take(4) as $key => $item) 
                <a href="{{ route('fasilitas.semua') }}" class="animate-on-scroll group relative h-80 rounded-2xl overflow-hidden shadow-md hover:shadow-xl transition-all duration-500 transform hover:-translate-y-2 opacity-0" data-animate="fade-in-up" data-delay="{{ $key * 100 }}">
                    
                    {{-- Gambar Fasilitas --}}
                    <img src="{{ $item->foto ? asset('storage/fasilitas/'.$item->foto) : 'https://placehold.co/600x400/e2e8f0/475569?text=Fasilitas' }}" 
                         class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" 
                         alt="{{ $item->nama_fasilitas }}"
                         loading="lazy">
                
                    {{-- Overlay Gradient --}}
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                
                    {{-- Konten Teks Overlay --}}
                    <div class="absolute bottom-0 left-0 w-full p-6 text-white transform translate-y-full group-hover:translate-y-0 transition-transform duration-500">
                        
                        {{-- Icon Kategori (Muncul saat Hover) --}}
                        <div class="mb-3 opacity-0 group-hover:opacity-100 transition-all duration-300 delay-75">
                            <div class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-md flex items-center justify-center">
                                <i class='bx bx-buildings text-xl'></i>
                            </div>
                        </div>

                        <h3 class="text-lg font-bold leading-tight mb-1">
                            {{ $item->nama_fasilitas }}
                        </h3>
                        
                        <p class="text-blue-100 text-sm line-clamp-2">
                            {{ $item->deskripsi ?? 'Fasilitas penunjang pembelajaran siswa.' }}
                        </p>
                    </div>
                </a>
            @endforeach
        </div>

        {{-- Tombol Lihat Semua (Mobile) --}}
        <div class="mt-12 text-center md:hidden animate-on-scroll opacity-0 translate-y-4" data-animate="fade-in-up" data-delay="400">
            <a href="{{ route('fasilitas.semua') }}" class="group inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white font-bold rounded-full w-full justify-center shadow-lg">
                Lihat Semua Fasilitas
                <i class='bx bx-right-arrow-alt text-xl'></i>
            </a>
        </div>

    </div>
</section>
@endif

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