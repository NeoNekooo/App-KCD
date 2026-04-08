@extends('layouts.frontend')
@section('title', 'Lembaga - Satuan Pendidikan')
@section('content')
<div class="bg-slate-900 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="inline-flex items-center gap-2 bg-blue-500/20 backdrop-blur-sm border border-blue-400/30 text-blue-200 text-sm font-medium px-4 py-1.5 rounded-full mb-6">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            Satuan Pendidikan Binaan
        </div>
        <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-4 tracking-tight">Lembaga Pendidikan</h1>
        <p class="text-lg text-blue-200/80 max-w-2xl mx-auto">Daftar satuan pendidikan yang berada di bawah naungan Kantor Cabang Dinas Pendidikan.</p>
    </div>
</div>

<div class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Search & Filter -->
        <div class="mb-10 max-w-xl mx-auto">
            <div class="relative">
                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" id="searchSekolah" placeholder="Cari nama sekolah..." class="w-full pl-12 pr-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition text-sm">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="sekolahGrid">
            @forelse($sekolah as $item)
            <a href="{{ url('/lembaga/'.$item->id) }}" class="sekolah-card group bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-xl hover:-translate-y-1 transition-all duration-300" data-nama="{{ strtolower($item->nama) }}">
                <div class="flex items-start gap-4">
                    <div class="w-14 h-14 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0 group-hover:bg-blue-600 group-hover:text-white transition-all duration-300">
                        @if($item->logo)
                        <img src="{{ asset('storage/'.$item->logo) }}" class="w-10 h-10 object-contain">
                        @else
                        <svg class="w-7 h-7 text-blue-600 group-hover:text-white transition" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        @endif
                    </div>
                    <div class="min-w-0">
                        <h3 class="font-bold text-gray-900 text-sm leading-tight group-hover:text-blue-600 transition line-clamp-2">{{ $item->nama }}</h3>
                        <p class="text-xs text-gray-400 mt-1">NPSN: {{ $item->npsn ?? '-' }}</p>
                        <div class="flex flex-wrap items-center gap-2 mt-2">
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $item->status_sekolah_str == 'Negeri' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">{{ $item->status_sekolah_str ?? '-' }}</span>
                            <span class="text-[10px] font-medium text-gray-400">{{ $item->kecamatan ?? '' }}</span>
                        </div>
                    </div>
                </div>
            </a>
            @empty
            <div class="col-span-full text-center py-20">
                <h3 class="text-xl font-bold text-gray-900">Belum ada data satuan pendidikan</h3>
            </div>
            @endforelse
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('searchSekolah')?.addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.sekolah-card').forEach(card => {
        card.style.display = card.dataset.nama.includes(q) ? '' : 'none';
    });
});
</script>
@endpush
@endsection
