@extends('layouts.frontend')
@section('title', $berita->judul)
@section('content')
<div class="bg-slate-900 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <a href="{{ url('/berita') }}" class="inline-flex items-center text-blue-300 hover:text-white text-sm font-medium mb-6 transition">
            <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Berita
        </a>
        <h1 class="text-3xl md:text-4xl font-extrabold text-white mb-4 leading-tight">{{ $berita->judul }}</h1>
        <div class="flex flex-wrap items-center gap-4 text-sm text-blue-200/70">
            <span class="flex items-center gap-1"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg> {{ $berita->penulis ?? 'Admin' }}</span>
            <span class="flex items-center gap-1"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg> {{ ($berita->published_at ?? $berita->created_at)->format('d F Y') }}</span>
        </div>
    </div>
</div>

<div class="py-12 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($berita->gambar)
        <div class="rounded-2xl overflow-hidden shadow-lg mb-10">
            <img src="{{ Storage::url($berita->gambar) }}" class="w-full h-auto max-h-[500px] object-cover">
        </div>
        @endif
        <div class="prose prose-lg prose-blue max-w-none text-gray-700 leading-relaxed">
            {!! nl2br(e($berita->isi)) !!}
        </div>
    </div>
</div>
@endsection
