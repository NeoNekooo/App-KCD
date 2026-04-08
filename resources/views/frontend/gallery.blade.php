@extends('layouts.frontend')

@section('title', 'Galeri - Kantor Cabang Dinas')

@section('content')
<div class="bg-blue-900 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
        <h1 class="text-4xl font-extrabold mb-4">Galeri Kegiatan</h1>
        <p class="text-xl text-blue-100 max-w-2xl mx-auto">Dokumentasi momen-momen penting dan kegiatan di lingkungan Kantor Cabang Dinas.</p>
    </div>
</div>

<div class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-wrap justify-center space-x-4 mb-12">
            <button class="px-6 py-2 bg-blue-600 text-white rounded-full text-sm font-medium">Semua</button>
            <button class="px-6 py-2 bg-gray-100 text-gray-600 rounded-full text-sm font-medium hover:bg-gray-200 transition">Rapat</button>
            <button class="px-6 py-2 bg-gray-100 text-gray-600 rounded-full text-sm font-medium hover:bg-gray-200 transition">Kunjungan</button>
            <button class="px-6 py-2 bg-gray-100 text-gray-600 rounded-full text-sm font-medium hover:bg-gray-200 transition">Sosialisasi</button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @php
                $images = [
                    ['url' => 'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4', 'title' => 'Rapat Koordinasi Tahunan'],
                    ['url' => 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655', 'title' => 'Sosialisasi Kurikulum Merdeka'],
                    ['url' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3', 'title' => 'Kunjungan Lapangan ke SMK Negeri'],
                    ['url' => 'https://images.unsplash.com/photo-1531482615713-2afd69097998', 'title' => 'Pelatihan Administrasi Sekolah'],
                    ['url' => 'https://images.unsplash.com/photo-1523240795612-9a054b0db644', 'title' => 'Penyerahan Penghargaan Guru Berprestasi'],
                    ['url' => 'https://images.unsplash.com/photo-1577412647305-991150c7d163', 'title' => 'Forum Group Discussion (FGD)'],
                ];
            @endphp

            @foreach($images as $image)
            <div class="group relative overflow-hidden rounded-2xl aspect-square bg-gray-200 shadow-sm transition hover:shadow-xl">
                <img src="{{ $image['url'] }}?ixlib=rb-1.2.1&auto=format&fit=crop&w=600&q=80" alt="{{ $image['title'] }}" class="w-full h-full object-cover transition duration-500 group-hover:scale-110">
                <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-6">
                    <p class="text-white font-semibold text-lg">{{ $image['title'] }}</p>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-16 text-center">
            <button class="px-8 py-3 border-2 border-blue-600 text-blue-600 font-bold rounded-lg hover:bg-blue-600 hover:text-white transition duration-300">
                Lihat Lebih Banyak
            </button>
        </div>
    </div>
</div>
@endsection
