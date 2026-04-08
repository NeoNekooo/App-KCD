@extends('layouts.frontend')
@section('title', 'Pengumuman - Kantor Cabang Dinas')
@section('content')
<div class="bg-slate-900 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-4 tracking-tight">Pengumuman</h1>
        <p class="text-lg text-blue-200/80 max-w-2xl mx-auto">Informasi resmi dan pengumuman penting dari Kantor Cabang Dinas.</p>
    </div>
</div>
<div class="py-16 bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        @forelse($pengumuman as $item)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition {{ $item->prioritas == 'urgent' ? 'border-l-4 border-l-red-500' : ($item->prioritas == 'penting' ? 'border-l-4 border-l-amber-400' : 'border-l-4 border-l-blue-400') }}">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">{{ $item->judul }}</h3>
                    <span class="text-xs text-gray-400">{{ $item->tanggal_terbit ? $item->tanggal_terbit->format('d F Y') : $item->created_at->format('d F Y') }}</span>
                </div>
                @if($item->prioritas == 'urgent')
                <span class="bg-red-100 text-red-700 text-xs font-bold px-3 py-1 rounded-full">Urgent</span>
                @elseif($item->prioritas == 'penting')
                <span class="bg-amber-100 text-amber-700 text-xs font-bold px-3 py-1 rounded-full">Penting</span>
                @endif
            </div>
            <div class="text-gray-600 text-sm leading-relaxed">{!! nl2br(e(Str::limit($item->isi, 300))) !!}</div>
            @if($item->lampiran)
            <a href="{{ Storage::url($item->lampiran) }}" target="_blank" class="inline-flex items-center gap-1 mt-4 text-blue-600 text-sm font-semibold hover:text-blue-800 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Unduh Lampiran
            </a>
            @endif
        </div>
        @empty
        <div class="text-center py-20">
            <h3 class="text-xl font-bold text-gray-900">Belum ada pengumuman</h3>
            <p class="mt-2 text-gray-500">Pengumuman akan muncul di sini setelah ditambahkan.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
