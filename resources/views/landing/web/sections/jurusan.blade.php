<section id="jurusan" class="py-20 bg-white relative overflow-hidden">
    
    {{-- Dekorasi Latar --}}
    <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 bg-blue-50 rounded-full opacity-50 blur-3xl"></div>
    <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 bg-indigo-50 rounded-full opacity-50 blur-3xl"></div>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        
        {{-- Header Section Compact --}}
        <div class="flex flex-col md:flex-row justify-between items-end mb-10 gap-6">
            <div class="max-w-2xl">
                <span class="text-blue-600 font-bold tracking-wider uppercase text-xs bg-blue-50 px-3 py-1 rounded-full border border-blue-100 inline-block mb-3 animate-on-scroll opacity-0 translate-y-4">
                    Program Pendidikan
                </span>
                <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 leading-tight animate-on-scroll opacity-0 translate-y-4" data-delay="100">
                    Kompetensi <span class="text-blue-600">Keahlian</span>
                </h2>
                <p class="text-slate-500 mt-2 animate-on-scroll opacity-0 translate-y-4" data-delay="200">
                    Pilihan jurusan unggulan untuk mencetak lulusan siap kerja.
                </p>
            </div>
            
            {{-- Tombol Desktop --}}
            <div class="hidden md:block animate-on-scroll opacity-0 translate-y-4" data-delay="300">
                <a href="{{ route('jurusan.lengkap') }}" class="group inline-flex items-center gap-2 text-slate-600 font-semibold hover:text-blue-600 transition-colors">
                    Lihat Semua Jurusan
                    <i class='bx bx-right-arrow-alt text-xl transition-transform group-hover:translate-x-1'></i>
                </a>
            </div>
        </div>

        {{-- Grid System --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($jurusans as $item)
                {{-- Card Jurusan Compact (Height 72 = 18rem = 288px) --}}
                <a href="{{ route('jurusan.show', $item->id) }}" class="animate-on-scroll group relative h-72 rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500 opacity-0 translate-y-8 transform hover:-translate-y-1" data-delay="{{ 100 * $loop->iteration }}">
                    
                    {{-- Gambar Background --}}
                    <div class="absolute inset-0">
                        @if($item->gambar)
                            <img src="{{ asset('storage/jurusans/'.$item->gambar) }}" 
                                 alt="{{ $item->nama_jurusan }}" 
                                 class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                        @else
                            <div class="w-full h-full bg-slate-200 flex items-center justify-center">
                                <i class='bx bxs-graduation text-6xl text-slate-400'></i>
                            </div>
                        @endif
                    </div>

                    {{-- Gradient Overlay --}}
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/60 to-transparent opacity-90 group-hover:opacity-80 transition-opacity duration-500"></div>

                    {{-- Konten Card --}}
                    <div class="relative h-full p-6 flex flex-col justify-end text-white z-10">
                        {{-- Kode/Singkatan --}}
                        <div class="mb-2">
                            <span class="inline-block px-2 py-1 bg-white/20 backdrop-blur-md border border-white/10 text-xs font-bold rounded text-white uppercase tracking-wider">
                                {{ $item->singkatan ?? 'PRODI' }}
                            </span>
                        </div>

                        {{-- Judul --}}
                        <h3 class="text-xl font-bold leading-tight mb-1 group-hover:text-blue-300 transition-colors">
                            {{ $item->nama_jurusan }}
                        </h3>

                        {{-- Deskripsi Singkat (Muncul saat hover di desktop) --}}
                        <div class="h-0 overflow-hidden group-hover:h-auto group-hover:mt-2 transition-all duration-500 ease-in-out md:group-hover:max-h-20">
                            <p class="text-xs text-slate-300 line-clamp-2">
                                {{ Str::limit($item->deskripsi, 80) }}
                            </p>
                        </div>
                        
                        {{-- Indikator Panah --}}
                        <div class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-all duration-500 transform translate-x-4 group-hover:translate-x-0">
                            <div class="w-8 h-8 rounded-full bg-white/20 backdrop-blur flex items-center justify-center">
                                <i class='bx bx-right-arrow-alt text-xl'></i>
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full py-12 text-center bg-slate-50 rounded-2xl border border-dashed border-slate-300">
                    <p class="text-slate-500">Data jurusan belum tersedia.</p>
                </div>
            @endforelse
        </div>

        {{-- Tombol Mobile Only --}}
        <div class="mt-8 text-center md:hidden animate-on-scroll opacity-0 translate-y-4">
            <a href="{{ route('jurusan.lengkap') }}" class="inline-flex items-center justify-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-full shadow-lg hover:bg-blue-700 transition-all w-full">
                Lihat Semua Jurusan
                <i class='bx bx-right-arrow-alt ml-2 text-lg'></i>
            </a>
        </div>

    </div>
</section>