@extends('layouts.frontend')

@section('title', 'Satuan Pendidikan - Kantor Cabang Dinas')

@push('styles')
<style>
    .table-responsive::-webkit-scrollbar {
        height: 8px;
    }
    .table-responsive::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    .table-responsive::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    .filter-card {
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(226, 232, 240, 0.8);
    }
    .status-badge-negeri { background-color: #dcfce7; color: #166534; }
    .status-badge-swasta { background-color: #fef9c3; color: #854d0e; }
    .jenjang-badge { background-color: #e0f2fe; color: #075985; }
    
    .animate-in {
        opacity: 0;
        transform: translateY(20px);
        animation: fadeInUp 0.6s ease-out forwards;
    }
    @keyframes fadeInUp {
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

@section('content')
<!-- Header Section -->
<div class="bg-blue-900 py-16 relative overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
            <path d="M0 100 C 20 0 50 0 100 100 Z" fill="white"></path>
        </svg>
    </div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
        <h1 class="text-4xl md:text-5xl font-black text-white mb-4 tracking-tight uppercase animate-in">Satuan Pendidikan</h1>
        <p class="text-lg text-blue-100/80 max-w-2xl mx-auto font-light animate-in" style="animation-delay: 100ms;">
            Data resmi lembaga pendidikan yang berada di bawah naungan Cabang Dinas Pendidikan.
        </p>
    </div>
</div>

<!-- Main Section -->
<div class="bg-slate-50 min-h-screen py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Filter Panel -->
        <div class="filter-card rounded-[2rem] p-6 mb-8 shadow-sm animate-in" style="animation-delay: 200ms;">
            <form action="{{ url('/lembaga') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                
                <!-- Search -->
                <div class="lg:col-span-2">
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Cari Sekolah / NPSN</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                            <i class='bx bx-search fs-5'></i>
                        </span>
                        <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" 
                               class="w-full pl-11 pr-4 py-3 rounded-2xl border border-slate-200 focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition outline-none text-sm font-medium" 
                               placeholder="Nama sekolah atau NPSN...">
                    </div>
                </div>

                <!-- Kabupaten -->
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Kabupaten/Kota</label>
                    <select name="kabupaten" class="w-full px-4 py-3 rounded-2xl border border-slate-200 focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition outline-none text-sm font-bold text-slate-600 bg-white">
                        <option value="">Semua Wilayah</option>
                        @foreach($listKabupaten as $kab)
                            <option value="{{ $kab->kabupaten_kota }}" {{ ($filters['kabupaten'] ?? '') == $kab->kabupaten_kota ? 'selected' : '' }}>
                                {{ $kab->kabupaten_kota }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Jenjang -->
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Jenjang</label>
                    <select name="jenjang" class="w-full px-4 py-3 rounded-2xl border border-slate-200 focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition outline-none text-sm font-bold text-slate-600 bg-white">
                        <option value="">Semua Jenjang</option>
                        @foreach($listJenjang as $j)
                            <option value="{{ $j->bentuk_pendidikan_id_str }}" {{ ($filters['jenjang'] ?? '') == $j->bentuk_pendidikan_id_str ? 'selected' : '' }}>
                                {{ $j->bentuk_pendidikan_id_str }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-grow bg-blue-600 text-white font-black uppercase tracking-widest py-3 px-4 rounded-2xl hover:bg-blue-700 shadow-lg hover:shadow-blue-200 transition duration-300">
                        Filter
                    </button>
                    <a href="{{ url('/lembaga') }}" class="bg-slate-100 text-slate-500 p-3 rounded-2xl hover:bg-slate-200 transition" title="Reset Filter">
                        <i class='bx bx-refresh fs-4'></i>
                    </a>
                </div>
            </form>
        </div>

        <!-- Data Info -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 px-4 animate-in" style="animation-delay: 300ms;">
            <div class="mb-4 md:mb-0">
                <span class="text-sm text-slate-500 font-medium">Menampilkan <span class="text-blue-600 font-bold">{{ $sekolah->firstItem() ?? 0 }}-{{ $sekolah->lastItem() ?? 0 }}</span> dari <span class="text-slate-800 font-black">{{ $sekolah->total() }}</span> sekolah</span>
            </div>
        </div>

        <!-- Modern Data Table -->
        <div class="bg-white rounded-[2.5rem] shadow-xl overflow-hidden border border-slate-100 mb-8 animate-in" style="animation-delay: 400ms;">
            <div class="table-responsive">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100">
                            <th class="px-8 py-6 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">Nama Lembaga</th>
                            <th class="px-6 py-6 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">NPSN</th>
                            <th class="px-6 py-6 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">Jenjang</th>
                            <th class="px-6 py-6 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">Status</th>
                            <th class="px-6 py-6 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">Wilayah</th>
                            <th class="px-8 py-6 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($sekolah as $item)
                        <tr class="hover:bg-blue-50/30 transition-colors duration-200 group">
                            <td class="px-8 py-5">
                                <div class="text-sm font-black text-slate-800">{{ $item->nama }}</div>
                            </td>
                            <td class="px-6 py-5">
                                <span class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-600 text-xs font-mono font-bold tracking-wider border border-slate-200/50">
                                    {{ $item->npsn }}
                                </span>
                            </td>
                            <td class="px-6 py-5">
                                <span class="inline-flex px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest jenjang-badge">
                                    {{ $item->bentuk_pendidikan_id_str }}
                                </span>
                            </td>
                            <td class="px-6 py-5">
                                <span class="inline-flex px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest {{ strtolower($item->status_sekolah_str) == 'negeri' ? 'status-badge-negeri' : 'status-badge-swasta' }}">
                                    {{ $item->status_sekolah_str }}
                                </span>
                            </td>
                            <td class="px-6 py-5">
                                <div class="text-xs font-bold text-slate-700">{{ $item->kecamatan }}</div>
                                <div class="text-[10px] text-slate-400 font-black uppercase tracking-widest mt-1">{{ $item->kabupaten_kota }}</div>
                            </td>
                            <td class="px-8 py-5 text-center">
                                <a href="{{ url('/lembaga/'.$item->id) }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl bg-blue-600 text-white font-black uppercase tracking-widest text-[10px] hover:bg-blue-700 shadow-lg hover:shadow-blue-200 transition-all duration-300 group-hover:scale-105 border-0">
                                    <span>Lihat Detail</span>
                                    <i class='bx bx-right-arrow-alt fs-5'></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-8 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <i class='bx bx-search-alt text-slate-200' style="font-size: 5rem;"></i>
                                    <h3 class="text-xl font-bold text-slate-800 mt-4">Data tidak ditemukan</h3>
                                    <p class="text-slate-400 text-sm mt-2">Coba ganti kata kunci pencarian atau bersihkan filter.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="px-4">
            {{ $sekolah->links() }}
        </div>

    </div>
</div>
@endsection
