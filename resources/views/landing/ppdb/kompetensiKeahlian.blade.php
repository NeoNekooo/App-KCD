@extends('layouts.ppdb')

@section('content-ppdb')
    
        <!-- Jurusan Section (Kompetensi Keahlian) -->
        <section id="jurusan" class="py-20 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 text-center mb-4" data-aos="fade-up"
                    data-aos-duration="600">
                    {{ $kompetensiList->first()->judul_kompetensi ?? '' }}
                </h2>
                <p class="text-xl text-gray-600 text-center mb-16 max-w-3xl mx-auto" data-aos="fade-up"
                    data-aos-delay="100" data-aos-duration="600">
                    {{ $kompetensiList->first()->deskripsi_kompetensi ?? '' }}
                </p>

                {{--nah ini untuk bagian daftar kompetensi --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($kompetensiList as $index => $kompetensi)
                        <div class="bg-white p-8 rounded-xl shadow-lg border-b-4 border-primary-blue 
                                    hover:shadow-2xl transition duration-300 text-left h-full flex flex-col justify-start"
                             data-aos="zoom-in" data-aos-delay="{{ ($index + 1) * 100 }}">
                                
                            <div class="flex items-center mb-4">
                                @if ($kompetensi->icon)
                                    <img src="{{ asset('storage/' . $kompetensi->icon) }}" 
                                         alt="{{ $kompetensi->nama_kompetensi }}" 
                                         class="w-8 h-8 object-contain mr-3">
                                @else
                                    <i data-lucide="briefcase" class="w-8 h-8 text-primary-blue mr-3"></i>
                                @endif
                                <h3 class="text-xl font-bold text-gray-900">{{ $kompetensi->kode_kompetensi }}</h3>
                            </div>
                        
                            <p class="text-sm font-semibold text-gray-700 mb-2">
                                ({{ $kompetensi->nama_kompetensi }})
                            </p>
                            
                            <p class="text-gray-600 text-base">
                                {{ $kompetensi->deskripsi_jurusan }}
                            </p>
                        </div>
                    @endforeach
                </div>

            </div>
        </section>

@endsection