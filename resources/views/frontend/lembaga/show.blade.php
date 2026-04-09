@extends('layouts.frontend')
@section('title', $sekolah->nama)
@section('content')
<div class="bg-slate-900 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <a href="{{ url('/lembaga') }}" class="inline-flex items-center text-blue-300 hover:text-white text-sm font-medium mb-6 transition">
            <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Daftar Lembaga
        </a>
        <div class="flex items-center gap-5">
            <div class="w-20 h-20 rounded-2xl bg-white/10 backdrop-blur flex items-center justify-center flex-shrink-0">
                @if($sekolah->logo)
                <img src="{{ asset('storage/'.$sekolah->logo) }}" class="w-14 h-14 object-contain">
                @else
                <svg class="w-10 h-10 text-white/50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                @endif
            </div>
            <div>
                <h1 class="text-2xl md:text-3xl font-extrabold text-white leading-tight">{{ $sekolah->nama }}</h1>
                <div class="flex flex-wrap items-center gap-3 mt-2">
                    <span class="text-xs font-bold px-3 py-1 rounded-full {{ $sekolah->status_sekolah_str == 'Negeri' ? 'bg-green-500/20 text-green-300' : 'bg-blue-500/20 text-blue-300' }}">{{ $sekolah->status_sekolah_str ?? '-' }}</span>
                    <span class="text-blue-200/70 text-sm">NPSN: {{ $sekolah->npsn ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="py-12 bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Info Umum -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Informasi Umum
                </h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between"><dt class="text-gray-500">NPSN</dt><dd class="font-semibold text-gray-900">{{ $sekolah->npsn ?? '-' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">NSS</dt><dd class="font-semibold text-gray-900">{{ $sekolah->nss ?? '-' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Bentuk Pendidikan</dt><dd class="font-semibold text-gray-900">{{ $sekolah->bentuk_pendidikan_id_str ?? '-' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Status</dt><dd class="font-semibold text-gray-900">{{ $sekolah->status_sekolah_str ?? '-' }}</dd></div>
                </dl>
            </div>

            <!-- Kontak -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    Kontak
                </h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between"><dt class="text-gray-500">Telepon</dt><dd class="font-semibold text-gray-900">{{ $sekolah->nomor_telepon ?? '-' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Email</dt><dd class="font-semibold text-gray-900">{{ $sekolah->email ?? '-' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Website</dt><dd class="font-semibold text-blue-600">{{ $sekolah->website ?? '-' }}</dd></div>
                </dl>
            </div>

            <!-- Alamat -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:col-span-2">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Alamat
                </h3>
                <p class="text-gray-700 leading-relaxed">
                    {{ $sekolah->alamat_jalan ?? '' }}
                    @if($sekolah->desa_kelurahan), {{ $sekolah->desa_kelurahan }}@endif
                    @if($sekolah->kecamatan), Kec. {{ $sekolah->kecamatan }}@endif
                    @if($sekolah->kabupaten_kota), {{ $sekolah->kabupaten_kota }}@endif
                    @if($sekolah->provinsi), {{ $sekolah->provinsi }}@endif
                    @if($sekolah->kode_pos) {{ $sekolah->kode_pos }}@endif
                </p>
                @if($sekolah->lintang && $sekolah->bujur)
                <div class="mt-4 rounded-xl overflow-hidden border border-gray-200" style="height:300px;">
                    <iframe width="100%" height="100%" frameborder="0" style="border:0" 
                        src="https://maps.google.com/maps?q={{ $sekolah->lintang }},{{ $sekolah->bujur }}&z=15&output=embed" allowfullscreen></iframe>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
