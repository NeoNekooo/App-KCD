@extends('layouts.frontend')

@section('title', 'Berita & Kegiatan - Kantor Cabang Dinas')

@section('content')
<div class="bg-slate-900 py-20 relative overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-0 right-0 w-96 h-96 bg-blue-500 rounded-full blur-[100px]"></div>
    </div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
        <h1 class="text-3xl md:text-5xl font-black text-white uppercase tracking-tight mb-4 italic">Berita & Kegiatan</h1>
        <p class="text-blue-100 text-lg max-w-2xl mx-auto font-light opacity-80">Informasi terbaru seputar kebijakan, prestasi, dan kegiatan di lingkungan Cabang Dinas Pendidikan.</p>
    </div>
</div>

<div class="py-20 bg-slate-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
            @php
                $beritas = \App\Models\Berita::where('status', 'publish')->latest()->paginate(9);
            @endphp

            @forelse($beritas as $item)
            <a href="/berita/{{ $item->slug }}" class="group flex flex-col bg-white rounded-[2.5rem] border border-slate-100 overflow-hidden shadow-sm hover:shadow-2xl transition-all duration-500">
                <div class="aspect-[16/10] overflow-hidden relative">
                    @if($item->gambar)
                        <img src="{{ Storage::url($item->gambar) }}" class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110">
                    @endif
                    <div class="absolute top-5 left-5">
                        <span class="px-3 py-1 rounded-lg bg-blue-600 text-white text-[8px] font-black uppercase tracking-widest shadow-lg">
                            {{ $item->kategori->nama ?? 'Update' }}
                        </span>
                    </div>
                </div>
                <div class="p-8">
                    <div class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mb-3 flex items-center gap-2">
                        <i class='bx bx-calendar-alt text-blue-500'></i> {{ $item->created_at->translatedFormat('d M Y') }}
                    </div>
                    <h3 class="text-base font-black text-slate-800 leading-tight group-hover:text-blue-600 transition-colors line-clamp-2 uppercase">
                        {{ $item->judul }}
                    </h3>
                    <p class="mt-4 text-xs text-slate-500 line-clamp-3 leading-relaxed">
                        {{ strip_tags($item->isi) }}
                    </p>
                    <div class="mt-6 pt-6 border-t border-slate-50 flex justify-between items-center">
                        <span class="text-[9px] font-black text-blue-600 uppercase tracking-widest">Baca Selengkapnya</span>
                        <i class='bx bx-right-arrow-alt text-blue-600 text-xl group-hover:translate-x-1 transition-transform'></i>
                    </div>
                </div>
            </a>
            @empty
            <div class="col-span-full py-20 text-center bg-white rounded-[3rem] shadow-inner border-2 border-dashed border-slate-100">
                <i class='bx bx-news text-6xl text-slate-200 mb-4'></i>
                <p class="text-slate-400 font-bold uppercase tracking-widest">Belum ada berita yang diterbitkan</p>
            </div>
            @endforelse
        </div>

        <div class="mt-16">
            {{ $beritas->links() }}
        </div>
    </div>
</div>
@endsection
