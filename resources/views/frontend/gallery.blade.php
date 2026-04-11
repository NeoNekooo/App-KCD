@extends('layouts.frontend')

@section('title', 'Galeri Kegiatan - Kantor Cabang Dinas')

@section('content')
<div class="bg-slate-900 py-20 relative overflow-hidden">
    <div class="absolute inset-0 opacity-20">
        <div class="absolute top-0 left-0 w-full h-full bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
    </div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
        <h1 class="text-3xl md:text-5xl font-black text-white uppercase tracking-tight mb-4 italic">Galeri Kegiatan</h1>
        <p class="text-blue-100 text-lg max-w-2xl mx-auto font-light opacity-80">Dokumentasi visual berbagai momentum penting dan kegiatan inspiratif kami.</p>
    </div>
</div>

<div class="py-20 bg-slate-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
            @php
                $galeris = \App\Models\Galeri::with('items')->latest()->paginate(9);
            @endphp

            @forelse($galeris as $album)
            <div class="group bg-white rounded-[3rem] overflow-hidden shadow-sm hover:shadow-2xl transition-all duration-700 hover:-translate-y-2 border border-slate-100">
                <div class="relative aspect-square overflow-hidden">
                    @if($album->foto)
                        <img src="{{ Storage::url($album->foto) }}" class="w-full h-full object-cover transition-transform duration-[2s] group-hover:scale-110">
                    @else
                        <div class="w-full h-full bg-slate-100 flex items-center justify-center text-slate-300">
                            <i class='bx bx-image text-6xl'></i>
                        </div>
                    @endif
                    
                    <!-- Overlay Info -->
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-transparent opacity-60 group-hover:opacity-100 transition-opacity duration-500 flex flex-col justify-end p-10 text-white">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="px-3 py-1 rounded-full bg-blue-600 text-[8px] font-black uppercase tracking-widest">
                                <i class='bx bx-images mr-1'></i> {{ $album->items->count() }} Foto
                            </span>
                            @if($album->tanggal)
                            <span class="text-[9px] font-bold uppercase tracking-widest opacity-80">
                                {{ \Carbon\Carbon::parse($album->tanggal)->translatedFormat('M Y') }}
                            </span>
                            @endif
                        </div>
                        <h3 class="text-lg font-black leading-tight uppercase tracking-tight group-hover:text-blue-400 transition-colors line-clamp-2">
                            {{ $album->judul }}
                        </h3>
                    </div>
                </div>
                
                @if($album->deskripsi)
                <div class="p-8">
                    <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed italic">
                        "{{ $album->deskripsi }}"
                    </p>
                </div>
                @endif
            </div>
            @empty
            <div class="col-span-full py-20 text-center bg-white rounded-[3rem] shadow-inner border-2 border-dashed border-slate-100">
                <i class='bx bx-images text-6xl text-slate-200 mb-4'></i>
                <p class="text-slate-400 font-bold uppercase tracking-widest">Belum ada album galeri</p>
            </div>
            @endforelse
        </div>

        <div class="mt-16">
            {{ $galeris->links() }}
        </div>
    </div>
</div>
@endsection
