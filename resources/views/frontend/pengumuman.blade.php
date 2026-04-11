@extends('layouts.frontend')

@section('title', 'Pengumuman Resmi - Kantor Cabang Dinas')

@section('content')
<div class="bg-blue-900 py-20 relative overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-white rounded-full blur-3xl"></div>
    </div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
        <h1 class="text-3xl md:text-5xl font-black text-white uppercase tracking-tight mb-4 italic">Pengumuman Resmi</h1>
        <p class="text-blue-100 text-lg max-w-2xl mx-auto font-light opacity-80">Informasi administratif, edaran, dan pengumuman penting bagi seluruh satuan pendidikan.</p>
    </div>
</div>

<div class="py-20 bg-slate-50 min-h-screen">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="space-y-8">
            @php
                $pengumumans = \App\Models\Pengumuman::where('status', 'publish')->latest()->paginate(10);
            @endphp

            @forelse($pengumumans as $item)
            <div class="group relative bg-white rounded-[2.5rem] border border-slate-100 p-8 md:p-10 shadow-sm hover:shadow-xl transition-all duration-500 {{ $item->prioritas == 'urgent' ? 'ring-2 ring-red-500/20' : '' }}">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl {{ $item->prioritas == 'urgent' ? 'bg-red-50 text-red-600' : ($item->prioritas == 'penting' ? 'bg-amber-50 text-amber-600' : 'bg-blue-50 text-blue-600') }} flex items-center justify-center shadow-inner">
                            <i class='bx {{ $item->prioritas == 'urgent' ? 'bxs-error-circle' : 'bxs-info-circle' }} text-2xl'></i>
                        </div>
                        <div>
                            <div class="text-[9px] text-slate-400 font-black uppercase tracking-[0.2em] mb-1">
                                {{ $item->tanggal_terbit ? $item->tanggal_terbit->translatedFormat('d F Y') : $item->created_at->translatedFormat('d F Y') }}
                            </div>
                            <h3 class="text-lg font-black text-slate-800 leading-tight uppercase group-hover:text-blue-600 transition-colors">
                                {{ $item->judul }}
                            </h3>
                        </div>
                    </div>
                    
                    @if($item->prioritas == 'urgent')
                        <span class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full bg-red-600 text-white text-[9px] font-black uppercase tracking-widest shadow-lg shadow-red-200">
                            <i class='bx bxs-zap text-xs'></i> Urgent
                        </span>
                    @elseif($item->prioritas == 'penting')
                        <span class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full bg-amber-500 text-white text-[9px] font-black uppercase tracking-widest shadow-lg shadow-amber-200">
                            Penting
                        </span>
                    @endif
                </div>

                <div class="cms-content text-sm text-slate-600 leading-relaxed line-clamp-3 mb-8">
                    {!! $item->isi !!}
                </div>

                <div class="flex flex-wrap items-center gap-4 pt-8 border-t border-slate-50">
                    @if($item->lampiran)
                    <a href="{{ Storage::url($item->lampiran) }}" target="_blank" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-blue-600 text-white text-[10px] font-black uppercase tracking-widest hover:bg-blue-700 shadow-lg shadow-blue-200 transition-all">
                        <i class='bx bx-download text-lg'></i> Unduh Lampiran
                    </a>
                    @endif
                    <div class="ml-auto flex items-center text-[10px] text-slate-400 font-bold uppercase tracking-widest">
                        <i class='bx bx-show-alt text-lg mr-2'></i> Publik
                    </div>
                </div>
            </div>
            @empty
            <div class="py-20 text-center bg-white rounded-[3rem] shadow-inner border-2 border-dashed border-slate-100">
                <i class='bx bx-megaphone text-6xl text-slate-200 mb-4'></i>
                <p class="text-slate-400 font-bold uppercase tracking-widest">Belum ada pengumuman terbaru</p>
            </div>
            @endforelse
        </div>

        <div class="mt-16">
            {{ $pengumumans->links() }}
        </div>
    </div>
</div>
@endsection
