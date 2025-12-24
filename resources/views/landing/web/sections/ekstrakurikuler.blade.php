{{-- FILE: resources/views/landing/web/sections/ekstrakurikuler.blade.php --}}

@if(isset($ekskuls) && $ekskuls->count() > 0)
<section id="ekskul" class="py-20 bg-white relative overflow-hidden">
    
    {{-- Background Pattern Dots --}}
    <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-50 rounded-full blur-3xl opacity-60 -translate-y-1/2 translate-x-1/2"></div>
    <div class="absolute bottom-0 left-0 w-64 h-64 bg-blue-50 rounded-full blur-3xl opacity-60 translate-y-1/2 -translate-x-1/2"></div>

    <div class="container mx-auto px-6 md:px-12 relative z-10">
        
        {{-- Header Section --}}
        <div class="text-center mb-16 max-w-3xl mx-auto animate__animated animate__fadeInUp">
            <span class="text-blue-600 font-bold tracking-widest uppercase text-xs mb-3 block">
                Pengembangan Diri
            </span>
            <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 leading-tight mb-4">
                Ekstrakurikuler
            </h2>
            <div class="w-16 h-1.5 bg-blue-500 mx-auto rounded-full mb-6"></div>
            <p class="text-gray-500">
                Wadah bagi siswa untuk mengembangkan bakat, minat, dan kepribadian di luar jam pelajaran akademik.
            </p>
        </div>

        {{-- Grid Ekskul --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($ekskuls as $ekskul)
            
            {{-- LOGIKA GAMBAR (Disamakan dengan Backend) --}}
            @php
                // Backend path: storage/ekstrakurikulers/
                // Frontend path lama (salah): storage/ekskul/
                $gambarUrl = asset('storage/ekstrakurikulers/'.$ekskul->foto);
            @endphp

            <div class="group relative h-80 rounded-2xl overflow-hidden cursor-pointer shadow-md hover:shadow-2xl hover:shadow-indigo-500/20 transition-all duration-500 hover:-translate-y-2 animate__animated animate__fadeInUp bg-white border border-gray-100">
                
                {{-- 1. Gambar Background (Full Height) --}}
                <div class="absolute inset-0 h-full w-full">
                    <img src="{{ $gambarUrl }}" 
                         class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" 
                         alt="{{ $ekskul->nama_ekskul }}"
                         onerror="this.src='https://placehold.co/400x600/e2e8f0/475569?text=Ekskul'">
                </div>
                
                {{-- 2. Overlay Gradient (Bawah Gelap) --}}
                <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent opacity-80 group-hover:opacity-90 transition-opacity duration-300"></div>

                {{-- 3. Overlay Warna Biru (Saat Hover) --}}
                <div class="absolute inset-0 bg-indigo-900/80 opacity-0 group-hover:opacity-100 transition-opacity duration-300 backdrop-blur-[2px]"></div>

                {{-- 4. Konten Teks --}}
                <div class="absolute inset-0 flex flex-col justify-end p-6 text-white">
                    
                    {{-- Icon Kategori (Hiasan) --}}
                    <div class="mb-auto opacity-0 group-hover:opacity-100 transition-all duration-500 -translate-y-4 group-hover:translate-y-0">
                        <span class="inline-flex items-center justify-center w-10 h-10 bg-white/20 backdrop-blur rounded-full">
                            <i class='bx bx-star text-xl text-yellow-400'></i>
                        </span>
                    </div>

                    {{-- Nama Ekskul --}}
                    <h3 class="text-xl font-bold mb-2 group-hover:-translate-y-1 transition-transform duration-300 leading-tight">
                        {{ $ekskul->nama_ekskul }}
                    </h3>

                    {{-- Garis Pemisah --}}
                    <div class="w-12 h-1 bg-yellow-400 rounded-full mb-4 opacity-100 group-hover:opacity-0 transition-opacity duration-300"></div>

                    {{-- Detail Info (Muncul saat Hover) --}}
                    <div class="h-0 opacity-0 group-hover:h-auto group-hover:opacity-100 transition-all duration-500 overflow-hidden">
                        <div class="space-y-2 text-sm text-indigo-100">
                            @if($ekskul->pembina)
                            <div class="flex items-start gap-2">
                                <i class='bx bx-user text-yellow-400 mt-0.5'></i>
                                <span class="font-medium">Pembina: {{ $ekskul->pembina }}</span>
                            </div>
                            @endif
                            
                            @if($ekskul->jadwal)
                            <div class="flex items-start gap-2">
                                <i class='bx bx-time text-yellow-400 mt-0.5'></i>
                                <span>{{ $ekskul->jadwal }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
            @endforeach
        </div>

    </div>
</section>
@endif