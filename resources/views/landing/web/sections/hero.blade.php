{{-- FILE: resources/views/landing/web/sections/hero.blade.php --}}

@if(isset($sliders) && $sliders->count() > 0)
<section id="hero-section" class="relative w-full h-[600px] md:h-[700px] lg:h-[90vh] bg-slate-900 overflow-hidden group">
    
    {{-- Carousel Wrapper --}}
    <div id="heroCarousel" class="relative w-full h-full">
        
        {{-- Slide Items --}}
        <div class="slides-wrapper relative w-full h-full">
            @foreach($sliders as $key => $item)
            <div class="slide absolute inset-0 w-full h-full transition-opacity duration-1000 ease-in-out {{ $key == 0 ? 'opacity-100 z-10' : 'opacity-0 z-0' }}" data-index="{{ $key }}">
                
                {{-- 1. Gambar Background --}}
                <div class="absolute inset-0 overflow-hidden">
                    <img src="{{ asset('storage/sliders/'.$item->gambar) }}" 
                         class="hero-bg w-full h-full object-cover will-change-transform {{ $key == 0 ? 'animate-slide-and-focus' : '' }}" 
                         alt="Slider Image">
                </div>
                
                {{-- 2. Overlay Gradient (Dipertebal agar teks lebih terbaca) --}}
                <div class="absolute inset-0 bg-slate-900/60 z-10"></div>
                <div class="absolute inset-0 bg-gradient-to-r from-slate-900/90 via-slate-900/40 to-transparent z-10"></div>
                
                {{-- 3. Konten Teks (Center Alignment Fixed) --}}
                <div class="absolute inset-0 z-20 flex flex-col justify-center items-center text-center px-4 md:px-12 w-full h-full">
                    <div class="container mx-auto">
                        <div class="max-w-4xl mx-auto">
                            
                            {{-- Judul Utama --}}
                            <h1 class="hero-title text-3xl md:text-5xl lg:text-7xl font-extrabold text-white leading-tight mb-6 opacity-0 transform translate-y-8 transition-all duration-1000 ease-out drop-shadow-lg">
                                {{ $item->judul }}
                            </h1>

                            {{-- Deskripsi --}}
                            <p class="hero-desc text-base md:text-xl lg:text-2xl text-gray-200 mb-8 font-medium leading-relaxed opacity-0 transform translate-y-8 transition-all duration-1000 ease-out max-w-2xl mx-auto drop-shadow-md">
                                {{ $item->deskripsi }}
                            </p>

                            {{-- Tombol Aksi --}}
                            @if($item->link_tombol)
                                <div class="hero-btn opacity-0 transform translate-y-8 transition-all duration-1000 ease-out flex justify-center">
                                    <a href="{{ $item->link_tombol }}" class="group relative inline-flex items-center justify-center px-8 py-4 overflow-hidden font-bold text-white rounded-full shadow-lg transition-all duration-300 bg-blue-600 hover:bg-blue-700 hover:scale-105 ring-4 ring-blue-600/30">
                                        <span class="absolute w-0 h-0 transition-all duration-500 ease-out bg-white rounded-full group-hover:w-56 group-hover:h-56 opacity-20"></span>
                                        <span class="relative flex items-center gap-3 text-sm md:text-base">
                                            {{ $item->teks_tombol ?? 'Jelajahi Lebih Lanjut' }}
                                            <i class='bx bx-right-arrow-alt text-xl transition-transform group-hover:translate-x-1'></i>
                                        </span>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
            @endforeach
        </div>

        {{-- Indikator (Dots) --}}
        <div class="carousel-indicators absolute bottom-8 left-1/2 transform -translate-x-1/2 flex gap-3 z-30">
            @foreach($sliders as $key => $item)
                <button type="button" 
                    data-index="{{ $key }}" 
                    class="indicator w-2.5 h-2.5 md:w-3 md:h-3 rounded-full bg-white/40 transition-all duration-500 hover:bg-white {{ $key == 0 ? 'bg-blue-500 w-8 md:w-10 scale-100' : '' }}" 
                    aria-label="Slide {{ $key + 1 }}">
                </button>
            @endforeach
        </div>

        {{-- Navigasi Panah (Tengah Vertikal Absolut) --}}
        <button id="prevBtn" class="nav-btn absolute top-1/2 left-4 md:left-8 z-30 transform -translate-y-1/2 group outline-none" aria-label="Previous slide">
            <span class="inline-flex items-center justify-center w-12 h-12 md:w-14 md:h-14 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-white transition-all duration-300 group-hover:bg-blue-600 group-hover:border-blue-600 group-hover:scale-110 shadow-lg">
                <i class='bx bx-chevron-left text-3xl md:text-4xl'></i>
            </span>
        </button>

        <button id="nextBtn" class="nav-btn absolute top-1/2 right-4 md:right-8 z-30 transform -translate-y-1/2 group outline-none" aria-label="Next slide">
            <span class="inline-flex items-center justify-center w-12 h-12 md:w-14 md:h-14 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-white transition-all duration-300 group-hover:bg-blue-600 group-hover:border-blue-600 group-hover:scale-110 shadow-lg">
                <i class='bx bx-chevron-right text-3xl md:text-4xl'></i>
            </span>
        </button>

    </div>
</section>

@push('styles')
<style>
    @keyframes slide-and-focus {
        0% {
            transform: scale(1.1);
            filter: brightness(0.8);
        }
        100% {
            transform: scale(1);
            filter: brightness(1);
        }
    }
    .animate-slide-and-focus {
        animation: slide-and-focus 10s ease-out forwards;
    }
</style>
@endpush

<script>
document.addEventListener('DOMContentLoaded', () => {
    const slides = document.querySelectorAll('.slide');
    const indicators = document.querySelectorAll('.indicator');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const heroSection = document.getElementById('hero-section');
    
    let currentIndex = 0;
    let slideInterval;
    const intervalTime = 7000;

    const showSlide = (index) => {
        slides.forEach((slide) => {
            slide.classList.remove('opacity-100', 'z-10');
            slide.classList.add('opacity-0', 'z-0');
            
            // Reset Animasi Background
            const bg = slide.querySelector('.hero-bg');
            if(bg) {
                bg.classList.remove('animate-slide-and-focus');
                void bg.offsetWidth; // Force Reflow
            }

            // Reset Animasi Konten
            const content = [slide.querySelector('.hero-title'), slide.querySelector('.hero-desc'), slide.querySelector('.hero-btn')];
            content.forEach(el => {
                if(el) {
                    el.classList.remove('opacity-100', 'translate-y-0');
                    el.classList.add('opacity-0', 'translate-y-8');
                }
            });
        });

        // Reset Indicators
        indicators.forEach(indicator => {
            indicator.classList.remove('bg-blue-500', 'w-8', 'md:w-10');
            indicator.classList.add('bg-white/40', 'w-2.5', 'h-2.5', 'md:w-3', 'md:h-3');
        });

        // Activate Current Slide
        const activeSlide = slides[index];
        activeSlide.classList.remove('opacity-0', 'z-0');
        activeSlide.classList.add('opacity-100', 'z-10');
        
        const activeBg = activeSlide.querySelector('.hero-bg');
        if(activeBg) activeBg.classList.add('animate-slide-and-focus');

        // Animasi Teks Berurutan (Staggered)
        setTimeout(() => {
            const title = activeSlide.querySelector('.hero-title');
            if(title) {
                title.classList.remove('opacity-0', 'translate-y-8');
                title.classList.add('opacity-100', 'translate-y-0');
            }
        }, 200);

        setTimeout(() => {
            const desc = activeSlide.querySelector('.hero-desc');
            if(desc) {
                desc.classList.remove('opacity-0', 'translate-y-8');
                desc.classList.add('opacity-100', 'translate-y-0');
            }
        }, 400);

        setTimeout(() => {
            const btn = activeSlide.querySelector('.hero-btn');
            if(btn) {
                btn.classList.remove('opacity-0', 'translate-y-8');
                btn.classList.add('opacity-100', 'translate-y-0');
            }
        }, 600);

        // Activate Current Indicator
        indicators[index].classList.remove('bg-white/40', 'w-2.5', 'h-2.5', 'md:w-3', 'md:h-3');
        indicators[index].classList.add('bg-blue-500', 'w-8', 'md:w-10');
    };

    const nextSlide = () => {
        currentIndex = (currentIndex + 1) % slides.length;
        showSlide(currentIndex);
    };

    const prevSlide = () => {
        currentIndex = (currentIndex - 1 + slides.length) % slides.length;
        showSlide(currentIndex);
    };
    
    const startSlideShow = () => {
        slideInterval = setInterval(nextSlide, intervalTime);
    };

    const stopSlideShow = () => {
        clearInterval(slideInterval);
    };

    nextBtn.addEventListener('click', () => {
        nextSlide();
        stopSlideShow();
        startSlideShow();
    });

    prevBtn.addEventListener('click', () => {
        prevSlide();
        stopSlideShow();
        startSlideShow();
    });

    indicators.forEach((indicator, index) => {
        indicator.addEventListener('click', () => {
            currentIndex = index;
            showSlide(currentIndex);
            stopSlideShow();
            startSlideShow();
        });
    });

    heroSection.addEventListener('mouseenter', stopSlideShow);
    heroSection.addEventListener('mouseleave', startSlideShow);

    showSlide(currentIndex);
    startSlideShow();
});
</script>
@endif