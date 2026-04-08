@extends('layouts.frontend')
@section('title', 'Unduhan - Kantor Cabang Dinas')
@section('content')
<div class="bg-slate-900 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-4 tracking-tight">Pusat Unduhan</h1>
        <p class="text-lg text-blue-200/80 max-w-2xl mx-auto">Download dokumen, formulir, dan peraturan resmi.</p>
    </div>
</div>
<div class="py-16 bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
        @forelse($unduhan as $item)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center justify-between hover:shadow-md transition">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <h4 class="font-bold text-gray-900">{{ $item->judul }}</h4>
                    @if($item->deskripsi)<p class="text-sm text-gray-500 mt-0.5">{{ Str::limit($item->deskripsi, 100) }}</p>@endif
                    <div class="flex items-center gap-3 mt-1">
                        <span class="text-xs font-medium bg-blue-50 text-blue-700 px-2 py-0.5 rounded-full">{{ $item->kategori }}</span>
                        <span class="text-xs text-gray-400">{{ $item->jumlah_unduhan }}x diunduh</span>
                    </div>
                </div>
            </div>
            <a href="{{ Storage::url($item->file) }}" target="_blank" class="flex-shrink-0 ml-4 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-semibold text-sm transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Unduh
            </a>
        </div>
        @empty
        <div class="text-center py-20">
            <h3 class="text-xl font-bold text-gray-900">Belum ada file unduhan</h3>
        </div>
        @endforelse
    </div>
</div>
@endsection
