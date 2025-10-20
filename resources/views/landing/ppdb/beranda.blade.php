@extends('layouts.ppdb')

@section('content-ppdb')

        <!-- Hero Section (Beranda) -->
        <section id="beranda" class="bg-primary-blue pt-16 pb-24 sm:pt-24 sm:pb-32 relative overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="lg:flex lg:items-center lg:justify-between">
                    <div class="lg:w-1/2">
                        <!-- TAHUN AJARAN DIGANTI DI SINI -->
                        <span
                            class="text-sm font-semibold text-white uppercase tracking-wider bg-white bg-opacity-20 inline-block px-3 py-1 rounded-full mb-4"
                            data-aos="fade-right" data-aos-duration="800">TAHUN AJARAN {{ $tahunAktif->tahun_pelajaran ?? '-'}}</span>
                        @php
                          $sloganParts = explode('||', $beranda->slogan_utama ?? '');
                        @endphp

                        <h1 class="text-5xl sm:text-6xl lg:text-7xl font-extrabold text-white leading-tight mb-6"
                            data-aos="fade-right" data-aos-delay="100" data-aos-duration="800">
                            <span class="block">{{ $sloganParts[0] ?? '' }}</span>
                            <span class="block text-yellow-300">{{ $sloganParts[1] ?? '' }}</span>
                        </h1>

                        <p class="text-xl text-white text-opacity-90 mb-10 max-w-lg" data-aos="fade-right"
                            data-aos-delay="200" data-aos-duration="800">
                            {{ $beranda->deskripsi_singkat ?? ''}}
                        </p>
                        <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4" data-aos="fade-up"
                            data-aos-delay="300" data-aos-duration="800">
                            <!-- Tombol Hero Utama (Ukuran Ditingkatkan) -->
                            <a href="{{ route('ppdb.formulirPendaftaran') }}"
                                class="bg-yellow-400 hover:bg-yellow-500 text-primary-blue font-bold text-xl py-5 px-10 rounded-xl shadow-2xl transform hover:scale-105 transition duration-300 ease-in-out">
                                Daftar Sekarang!
                            </a>
                            <a href="{{ route('ppdb.kompetensiKeahlian') }}"
                                class="bg-white bg-opacity-10 hover:bg-opacity-20 text-white font-semibold text-lg py-4 px-8 rounded-xl border border-white border-opacity-30 transition duration-300 ease-in-out flex items-center justify-center">
                                <i data-lucide="briefcase" class="w-5 h-5 mr-2"></i>
                                Lihat Jurusan
                            </a>
                        </div>
                    </div>
                    <div class="lg:w-1/2 mt-12 lg:mt-0 flex justify-center" data-aos="zoom-in" data-aos-delay="400"
                        data-aos-duration="800">
                        <div
                            class="hidden lg:block w-96 h-96 rounded-3xl bg-white bg-opacity-10 backdrop-blur-sm flex items-center justify-center p-8">
                            <i data-lucide="graduation-cap" class="w-24 h-24 text-white text-opacity-80"></i>
                            @php
                                $points = explode(',', $beranda->point_keunggulan_1 ?? '');
                            @endphp

                            <div class="mt-6 space-y-2">
                                @foreach(explode(',', $beranda->point_keunggulan_1 ?? '') as $point)
                                    <div class="flex items-center justify-center space-x-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-300" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414L9 14.414l-3.707-3.707a1 1 0 011.414-1.414L9 11.586l6.293-6.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                        <p class="text-white text-opacity-80 font-semibold">{{ trim($point) }}</p>
                                    </div>
                                @endforeach
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="absolute inset-0 bg-pattern opacity-10"></div>
        </section>

        <!-- Keunggulan Section (Program) -->
        <section id="program" class="py-20 bg-white">
          <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Judul & Deskripsi -->
            <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 text-center mb-4" data-aos="fade-up"
                data-aos-duration="600">
                {{ $keunggulanList->first()->judul_keunggulan ?? '' }}
            </h2>
        
            <p class="text-xl text-gray-600 text-center mb-16 max-w-3xl mx-auto" data-aos="fade-up"
                data-aos-delay="100" data-aos-duration="600">
                {{ $keunggulanList->first()->deskripsi_keunggulan ?? '' }}
            </p>
        
            <!-- Daftar Keunggulan -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
              @foreach ($keunggulanList as $index => $item)
                <div class="bg-gray-50 p-8 rounded-xl shadow-lg hover:shadow-xl transition duration-300 transform hover:-translate-y-1 border-t-4 border-primary-blue"
                    data-aos="fade-up" data-aos-delay="{{ 200 + ($index * 100) }}">
                  <div class="flex items-center justify-center w-12 h-12 rounded-full mb-4 overflow-hidden">
                      @if($item->icon)
                          <img src="{{ asset('storage/'.$item->icon) }}" 
                               alt="Icon" 
                               class="w-full h-full object-cover">
                      @else
                          <div class="w-full h-full rounded-full bg-gray-200 flex items-center justify-center">
                              <i data-lucide="image" class="w-6 h-6 text-gray-500"></i>
                          </div>
                      @endif
                  </div>
              
                  <h3 class="text-xl font-bold text-gray-900 mb-3">{{ $item->judul_item }}</h3>
                  <p class="text-gray-600">{{ $item->deskripsi_item }}</p>
                </div>
              @endforeach
            </div>
          </div>
        </section>


        <!-- Alur Pendaftaran Section (Alur) -->
        <section id="alur" class="py-20 bg-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 text-center mb-16" data-aos="zoom-in"
                    data-aos-duration="600">
                    4 Langkah Mudah Pendaftaran PPDB SMAKNIS
                </h2>

                <div class="relative flex flex-col items-center">
                    <div class="hidden md:block absolute top-10 left-0 right-0 h-1 bg-primary-blue opacity-30 mx-24">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-12 w-full">
                        <div class="flex flex-col items-center text-center p-4 relative z-10" data-aos="fade-right"
                            data-aos-delay="100">
                            <div
                                class="w-16 h-16 bg-primary-blue text-white rounded-full flex items-center justify-center text-2xl font-extrabold mb-4 shadow-custom">
                                1</div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Daftar Akun Online</h3>
                            <p class="text-gray-600">Buat akun pendaftar di portal resmi kami. Pastikan email dan nomor
                                HP aktif.</p>
                        </div>
                        <div class="flex flex-col items-center text-center p-4 relative z-10" data-aos="fade-right"
                            data-aos-delay="250">
                            <div
                                class="w-16 h-16 bg-primary-blue text-white rounded-full flex items-center justify-center text-2xl font-extrabold mb-4 shadow-custom">
                                2</div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Isi Formulir & Unggah Dokumen</h3>
                            <p class="text-gray-600">Lengkapi data diri calon siswa dan unggah dokumen persyaratan yang
                                diminta.</p>
                        </div>
                        <div class="flex flex-col items-center text-center p-4 relative z-10" data-aos="fade-left"
                            data-aos-delay="400">
                            <div
                                class="w-16 h-16 bg-primary-blue text-white rounded-full flex items-center justify-center text-2xl font-extrabold mb-4 shadow-custom">
                                3</div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Seleksi & Verifikasi</h3>
                            <p class="text-gray-600">Data akan diverifikasi oleh panitia. Hasil seleksi akan diumumkan
                                pada tanggal yang ditetapkan.</p>
                        </div>
                        <div class="flex flex-col items-center text-center p-4 relative z-10" data-aos="fade-left"
                            data-aos-delay="550">
                            <div
                                class="w-16 h-16 bg-primary-blue text-white rounded-full flex items-center justify-center text-2xl font-extrabold mb-4 shadow-custom">
                                4</div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Daftar Ulang</h3>
                            <p class="text-gray-600">Jika lolos, lakukan pembayaran biaya dan konfirmasi kehadiran untuk
                                daftar ulang.</p>
                        </div>
                    </div>

                    <a href="{{ route('ppdb.formulirPendaftaran') }}"
                        class="mt-12 bg-secondary-green hover:bg-green-600 text-white font-bold text-xl py-4 px-10 rounded-full shadow-xl transform hover:scale-105 transition duration-300 ease-in-out"
                        data-aos="zoom-in" data-aos-delay="700">
                        Mulai Pendaftaran Anda Sekarang!
                    </a>
                </div>
            </div>
        </section>

@endsection