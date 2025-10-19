@extends('layouts.ppdb')

@section('content-ppdb')
    
    <!-- Kontak Section (Peta sudah disematkan di sini) -->
        <section id="kontak" class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 text-center mb-4" data-aos="fade-up">
                    Hubungi Kami & Kunjungi Sekolah
                </h2>
                <p class="text-xl text-gray-600 text-center mb-12 max-w-3xl mx-auto" data-aos="fade-up"
                    data-aos-delay="100">
                    Kami siap menjawab semua pertanyaan Anda terkait proses PPDB dan program unggulan {{ $profilSekolah->singkatan ?? ''}}.
                </p>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                    <!-- Kontainer Peta Google Maps Live -->
                    <div class="lg:col-span-2 relative h-80 w-full rounded-xl overflow-hidden shadow-2xl map-iframe-container"
                        data-aos="zoom-in" data-aos-duration="1000">
                        <!-- IFRAME PETA LOKASI SMK Nurul Islam Affandiyah Cianjur -->
                        <iframe
                            src="https://maps.google.com/maps?q={{ urlencode($profilSekolah->alamat ?? '') }}&t=&z=15&ie=UTF8&iwloc=&output=embed"
                            frameborder="0" style="border:0;" allowfullscreen="" aria-hidden="false" tabindex="0"
                            loading="lazy" title="Google Map Lokasi SMK Nurul Islam Affandiyah Cianjur"
                            class="absolute inset-0 w-full h-full"></iframe>
                    </div>

                    <!-- Contact Info Panel -->
                    <div class="lg:col-span-1 bg-gray-50 p-8 rounded-xl shadow-xl border-t-4 border-primary-blue"
                        data-aos="fade-left" data-aos-delay="200">
                        <h3 class="text-2xl font-bold text-primary-blue mb-6">Informasi Kontak</h3>
                        <ul class="space-y-6 text-base">
                            <li class="flex items-start">
                                <i data-lucide="map-pin"
                                    class="w-6 h-6 mr-4 mt-0.5 text-secondary-green flex-shrink-0"></i>
                                <span class="text-gray-700 font-medium">Jl. Raya Cianjur Bandung Km. 09 Desa Selajambe
                                    Kec. Sukaluyu Kab. Cianjur</span>
                            </li>
                            <li class="flex items-start">
                                <i data-lucide="phone"
                                    class="w-6 h-6 mr-4 mt-0.5 text-secondary-green flex-shrink-0"></i>
                                <span class="text-gray-700 font-medium">{{ $profilSekolah->telepon ?? '-'}} (Layanan PPDB)</span>
                            </li>
                            <li class="flex items-start">
                                <i data-lucide="mail"
                                    class="w-6 h-6 mr-4 mt-0.5 text-secondary-green flex-shrink-0"></i>
                                <span class="text-gray-700 font-medium">{{ $profilSekolah->email ?? 'example@gmail.com' }}</span>
                            </li>
                            <li class="flex items-start">
                                <i data-lucide="clock"
                                    class="w-6 h-6 mr-4 mt-0.5 text-secondary-green flex-shrink-0"></i>
                                <span class="text-gray-700 font-medium">Jam Pelayanan PPDB: Hari Senin-Sabtu Jam 08.00 -
                                    15.00 WIB</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

@endsection