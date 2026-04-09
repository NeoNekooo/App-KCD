@extends('layouts.frontend')
@section('title', 'Galeri Kegiatan - Kantor Cabang Dinas')
@section('content')
<div class="bg-slate-900 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-4 tracking-tight">Galeri Kegiatan</h1>
        <p class="text-lg text-blue-200/80 max-w-2xl mx-auto">Dokumentasi foto kegiatan dan acara Kantor Cabang Dinas Pendidikan.</p>
    </div>
</div>
<div class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($galeri as $album)
            <div class="group bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                <div class="relative h-56 overflow-hidden">
                    @if($album->foto)
                    <img src="{{ Storage::url($album->foto) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @else
                    <div class="w-full h-full bg-gradient-to-br from-indigo-50 to-blue-100 flex items-center justify-center">
                        <svg class="w-16 h-16 text-blue-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    @endif
                    <div class="absolute bottom-3 right-3 bg-black/60 backdrop-blur-sm text-white text-xs font-bold px-3 py-1 rounded-full">
                        <i class="bx bx-images mr-1"></i>{{ $album->items->count() }} Foto
                    </div>
                </div>
                <div class="p-5">
                    <h3 class="text-lg font-bold text-gray-900 mb-1">{{ $album->judul }}</h3>
                    @if($album->tanggal)<p class="text-xs text-gray-400 mb-2">{{ $album->tanggal->format('d F Y') }}</p>@endif
                    @if($album->deskripsi)<p class="text-sm text-gray-500 line-clamp-2">{{ $album->deskripsi }}</p>@endif
                </div>
                @if($album->items->count() > 0)
                <div class="px-5 pb-5">
                    <div class="grid grid-cols-4 gap-1.5 rounded-lg overflow-hidden">
                        @foreach($album->items->take(4) as $foto)
                        <img src="{{ Storage::url($foto->file) }}" class="w-full h-16 object-cover {{ $loop->first ? 'rounded-tl-lg' : '' }} {{ $loop->index == 3 ? 'rounded-br-lg' : '' }}">
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
            @empty
            <div class="col-span-full text-center py-20">
                <h3 class="text-xl font-bold text-gray-900">Belum ada galeri kegiatan</h3>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
