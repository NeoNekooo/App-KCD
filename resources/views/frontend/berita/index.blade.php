@extends('layouts.frontend')
@section('title', 'Berita - Kantor Cabang Dinas')
@section('content')
<div class="bg-slate-900 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="inline-flex items-center gap-2 bg-blue-500/20 backdrop-blur-sm border border-blue-400/30 text-blue-200 text-sm font-medium px-4 py-1.5 rounded-full mb-6">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
            Informasi Terkini
        </div>
        <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-4 tracking-tight">Berita & Artikel</h1>
        <p class="text-lg text-blue-200/80 max-w-2xl mx-auto">Informasi terbaru seputar kegiatan dan kebijakan Kantor Cabang Dinas Pendidikan.</p>
    </div>
</div>

<div class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($berita as $item)
            <a href="{{ url('/berita/'.$item->slug) }}" class="group bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                @if($item->gambar)
                <div class="h-48 overflow-hidden">
                    <img src="{{ Storage::url($item->gambar) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                </div>
                @else
                <div class="h-48 bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center">
                    <svg class="w-16 h-16 text-blue-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                </div>
                @endif
                <div class="p-6">
                    <div class="flex items-center gap-3 mb-3">
                        <span class="text-xs font-bold text-blue-600 bg-blue-50 px-3 py-1 rounded-full">Berita</span>
                        <span class="text-xs text-gray-400">{{ $item->published_at ? $item->published_at->format('d M Y') : $item->created_at->format('d M Y') }}</span>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2 group-hover:text-blue-600 transition line-clamp-2">{{ $item->judul }}</h3>
                    <p class="text-sm text-gray-500 line-clamp-3">{{ $item->ringkasan ?? Str::limit(strip_tags($item->isi), 120) }}</p>
                </div>
            </a>
            @empty
            <div class="col-span-full text-center py-20">
                <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                <h3 class="mt-4 text-xl font-bold text-gray-900">Belum ada berita</h3>
                <p class="mt-2 text-gray-500">Berita akan muncul di sini setelah ditambahkan.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
