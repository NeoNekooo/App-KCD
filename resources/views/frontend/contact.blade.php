@extends('layouts.frontend')

@section('title', 'Kontak - Kantor Cabang Dinas')

@section('content')
@php
    $instansi = \App\Models\Instansi::first();
    $sosmed = $instansi->social_media ?? [];
@endphp

<div class="bg-blue-900 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
        <h1 class="text-4xl font-extrabold mb-4 tracking-tight">Hubungi Kami</h1>
        <p class="text-xl text-blue-100 max-w-2xl mx-auto opacity-90">{{ $instansi->nama_instansi ?? 'Kantor Cabang Dinas' }} siap melayani pertanyaan dan bantuan administratif Anda.</p>
    </div>
</div>

<div class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
            <div>
                <h2 class="text-3xl font-black text-gray-900 mb-8 uppercase tracking-widest border-l-4 border-blue-600 pl-4">Informasi Kontak</h2>
                <div class="space-y-8">
                    <div class="flex items-start group">
                        <div class="flex-shrink-0 bg-blue-100 p-3 rounded-2xl text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-all duration-300">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.828a1.6 1.6 0 01-2.263 0l-4.243-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div class="ml-5">
                            <h3 class="text-lg font-bold text-gray-900 uppercase tracking-wide">Alamat Kantor</h3>
                            <p class="mt-1 text-gray-600 leading-relaxed">{{ $instansi->alamat ?? 'Jl. Pendidikan No. 123, Wilayah KCD.' }}</p>
                        </div>
                    </div>

                    <div class="flex items-start group">
                        <div class="flex-shrink-0 bg-blue-100 p-3 rounded-2xl text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-all duration-300">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </div>
                        <div class="ml-5">
                            <h3 class="text-lg font-bold text-gray-900 uppercase tracking-wide">Telepon & Website</h3>
                            <p class="mt-1 text-gray-600">{{ $instansi->telepon ?? '-' }}</p>
                            @if($instansi->website)
                                <a href="{{ str_starts_with($instansi->website, 'http') ? $instansi->website : 'https://'.$instansi->website }}" target="_blank" class="text-blue-600 font-bold hover:underline text-sm">{{ $instansi->website }}</a>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-start group">
                        <div class="flex-shrink-0 bg-blue-100 p-3 rounded-2xl text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-all duration-300">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="ml-5">
                            <h3 class="text-lg font-bold text-gray-900 uppercase tracking-wide">Email Resmi</h3>
                            <p class="mt-1 text-gray-600">{{ $instansi->email ?? 'info@kcd.go.id' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Media Sosial Section -->
                @if(collect($sosmed)->filter()->isNotEmpty())
                <div class="mt-12">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 uppercase tracking-widest border-l-4 border-blue-600 pl-4">Ikuti Kami</h3>
                    <div class="flex flex-wrap gap-4">
                        @if(!empty($sosmed['facebook']))
                            <a href="{{ $sosmed['facebook'] }}" target="_blank" class="bg-blue-100 p-4 rounded-2xl text-blue-600 hover:bg-blue-600 hover:text-white transition shadow-sm">
                                <i class="fab fa-facebook-f text-xl"></i>
                            </a>
                        @endif
                        @if(!empty($sosmed['instagram']))
                            <a href="{{ $sosmed['instagram'] }}" target="_blank" class="bg-pink-100 p-4 rounded-2xl text-pink-600 hover:bg-pink-600 hover:text-white transition shadow-sm">
                                <i class="fab fa-instagram text-xl"></i>
                            </a>
                        @endif
                        @if(!empty($sosmed['twitter']))
                            <a href="{{ $sosmed['twitter'] }}" target="_blank" class="bg-slate-100 p-4 rounded-2xl text-slate-900 hover:bg-slate-900 hover:text-white transition shadow-sm">
                                <i class="fab fa-twitter text-xl"></i>
                            </a>
                        @endif
                        @if(!empty($sosmed['youtube']))
                            <a href="{{ $sosmed['youtube'] }}" target="_blank" class="bg-red-100 p-4 rounded-2xl text-red-600 hover:bg-red-600 hover:text-white transition shadow-sm">
                                <i class="fab fa-youtube text-xl"></i>
                            </a>
                        @endif
                    </div>
                </div>
                @endif

                <div class="mt-12">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 uppercase tracking-widest border-l-4 border-blue-600 pl-4">Lokasi Kami</h3>
                    <div class="w-full h-80 bg-gray-100 rounded-3xl overflow-hidden shadow-inner relative border border-gray-200">
                        @if($instansi->peta)
                            {!! $instansi->peta !!}
                        @else
                            <div class="absolute inset-0 flex flex-col items-center justify-center text-center p-8">
                                <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7l5-2.5 5.447 2.724A1 1 0 0121 8.118v10.764a1 1 0 01-1.447.894L15 17l-6 3z" />
                                </svg>
                                <p class="text-gray-400 font-bold uppercase tracking-widest text-sm">Peta Belum Tersedia</p>
                                <p class="text-gray-400 text-xs mt-2">Atur embed code Google Maps di Panel Admin.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 p-10 rounded-[3rem] border border-gray-200 shadow-xl relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-blue-600/5 rounded-full -mr-16 -mt-16"></div>
                <h2 class="text-2xl font-black text-gray-900 mb-8 text-center uppercase tracking-widest">Kirim Pesan</h2>
                <form action="#" method="POST" class="space-y-6">
                    <!-- Form tetap sama namun dengan styling yang lebih bold -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Nama Lengkap</label>
                            <input type="text" id="name" class="w-full px-5 py-4 rounded-2xl border border-gray-200 focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition outline-none text-sm font-medium" placeholder="Nama Anda">
                        </div>
                        <div>
                            <label for="email" class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Alamat Email</label>
                            <input type="email" id="email" class="w-full px-5 py-4 rounded-2xl border border-gray-200 focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition outline-none text-sm font-medium" placeholder="Email@anda.com">
                        </div>
                    </div>
                    <div>
                        <label for="subject" class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Subjek Pesan</label>
                        <select id="subject" class="w-full px-5 py-4 rounded-2xl border border-gray-200 focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition outline-none text-sm font-bold text-gray-600 bg-white">
                            <option>Informasi Layanan</option>
                            <option>Pengaduan</option>
                            <option>Kerjasama</option>
                            <option>Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label for="message" class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Pesan Anda</label>
                        <textarea id="message" rows="5" class="w-full px-5 py-4 rounded-2xl border border-gray-200 focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition outline-none text-sm font-medium" placeholder="Tuliskan pesan Anda di sini..."></textarea>
                    </div>
                    <button type="submit" class="w-full bg-blue-600 text-white font-black uppercase tracking-widest py-5 rounded-2xl hover:bg-blue-700 shadow-xl hover:shadow-blue-200 transition duration-300 transform hover:-translate-y-1">
                        Kirim Pesan Sekarang
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
