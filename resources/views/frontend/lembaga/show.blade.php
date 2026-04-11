@extends('layouts.frontend')

@section('title', $sekolah->nama . ' - Profil Lembaga')

@push('styles')
<style>
    .info-label {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        font-weight: 900;
        color: #94a3b8;
        margin-bottom: 0.35rem;
    }
    .info-value {
        font-weight: 700;
        color: #1e293b;
        font-size: 0.95rem;
    }
</style>
@endpush

@section('content')
<!-- Header Section - Version Hosting Stable -->
<div style="background-color: #0f172a; background-image: linear-gradient(to bottom right, #1e3a8a, #0f172a);" class="pt-20 pb-32 relative overflow-hidden">
    <!-- Manual Decorations -->
    <div class="absolute top-0 right-0 w-96 h-96 bg-blue-500 rounded-full blur-[100px] opacity-20 -mr-48 -mt-48"></div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <!-- Back Link -->
        <a href="{{ url('/lembaga') }}" class="inline-flex items-center gap-2 text-blue-300 hover:text-white text-[10px] font-black uppercase tracking-[0.2em] mb-10 transition-all">
            <i class='bx bx-left-arrow-alt text-lg'></i>
            Kembali ke Daftar
        </a>

        <div class="flex flex-col md:flex-row items-center md:items-start gap-8">
            <!-- Icon Box -->
            <div class="w-20 h-20 md:w-24 md:h-24 rounded-[2rem] bg-white/10 border border-white/20 flex items-center justify-center text-white shadow-2xl">
                <i class='bx bxs-institution text-4xl'></i>
            </div>

            <!-- Title & Badges -->
            <div class="text-center md:text-left flex-1">
                <div class="flex flex-wrap justify-center md:justify-start gap-2 mb-4">
                    <span class="px-3 py-1 rounded-lg bg-blue-500/20 text-blue-200 text-[10px] font-black uppercase tracking-widest border border-blue-400/20">
                        {{ $sekolah->bentuk_pendidikan_id_str }}
                    </span>
                    <span class="px-3 py-1 rounded-lg {{ strtolower($sekolah->status_sekolah_str) == 'negeri' ? 'bg-emerald-500/20 text-emerald-300 border-emerald-400/20' : 'bg-amber-500/20 text-amber-300 border-amber-400/20' }} text-[10px] font-black uppercase tracking-widest border">
                        {{ $sekolah->status_sekolah_str }}
                    </span>
                </div>
                <h1 class="text-3xl md:text-5xl font-black text-white leading-tight mb-2 uppercase tracking-tighter italic">
                    {{ $sekolah->nama }}
                </h1>
                <p class="text-blue-200/60 font-bold text-xs uppercase tracking-widest flex items-center justify-center md:justify-start gap-2">
                    <i class='bx bx-fingerprint text-blue-400'></i> NPSN : {{ $sekolah->npsn ?? '-' }}
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Area -->
<div class="bg-slate-50 min-h-screen pb-24 relative z-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8 pt-12">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            
            <!-- Left Side -->
            <div class="lg:col-span-8 space-y-12">
                <!-- Identitas Card -->
                <div class="bg-white rounded-[2.5rem] shadow-xl border border-slate-100 p-8 md:p-12">
                    <div class="flex items-center gap-4 mb-12">
                        <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shadow-inner">
                            <i class='bx bx-id-card text-2xl'></i>
                        </div>
                        <h2 class="text-xl font-black text-slate-800 uppercase tracking-tight">Informasi Identitas</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <div class="space-y-8">
                            <div class="flex items-start gap-4">
                                <div class="mt-1 text-blue-400"><i class='bx bx-buildings'></i></div>
                                <div>
                                    <div class="info-label">Nama Resmi</div>
                                    <div class="info-value text-slate-800">{{ $sekolah->nama }}</div>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="mt-1 text-blue-400"><i class='bx bx-barcode-reader'></i></div>
                                <div>
                                    <div class="info-label">NPSN / NSS</div>
                                    <div class="info-value text-blue-600 font-mono">{{ $sekolah->npsn ?? '-' }} / {{ $sekolah->nss ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="mt-1 text-blue-400"><i class='bx bx-time-five'></i></div>
                                <div>
                                    <div class="info-label">Waktu Belajar</div>
                                    <div class="info-value text-slate-700">Pagi / 5 Hari</div>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-8">
                            <div class="flex items-start gap-4">
                                <div class="mt-1 text-blue-400"><i class='bx bx-category'></i></div>
                                <div>
                                    <div class="info-label">Jenjang / Status</div>
                                    <div class="info-value">{{ $sekolah->bentuk_pendidikan_id_str }} / {{ $sekolah->status_sekolah_str }}</div>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="mt-1 text-blue-400"><i class='bx bx-map-alt'></i></div>
                                <div>
                                    <div class="info-label">Wilayah Pembinaan</div>
                                    <div class="info-value text-slate-600">{{ $sekolah->kabupaten_kota }}</div>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="mt-1 text-blue-400"><i class='bx bx-certification'></i></div>
                                <div>
                                    <div class="info-label">Program SKS</div>
                                    <div class="info-value text-slate-700">{{ $sekolah->is_sks == 1 ? 'Tersedia' : 'Tidak Ada' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Location Card -->
                <div class="bg-white rounded-[2.5rem] shadow-xl border border-slate-100 p-8 md:p-12">
                    <div class="flex items-center gap-4 mb-12">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shadow-inner">
                            <i class='bx bx-map-pin text-2xl'></i>
                        </div>
                        <h2 class="text-xl font-black text-slate-800 uppercase tracking-tight">Lokasi Lembaga</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                        <div>
                            <div class="info-label">Alamat Lengkap</div>
                            <div class="info-value leading-relaxed text-slate-600 mb-8">
                                {{ $sekolah->alamat_jalan ?? 'Alamat belum diatur' }}<br>
                                {{ $sekolah->desa_kelurahan ? 'Desa '.$sekolah->desa_kelurahan.',' : '' }} 
                                {{ $sekolah->kecamatan ? 'Kec. '.$sekolah->kecamatan : '' }}<br>
                                {{ $sekolah->kabupaten_kota }}, {{ $sekolah->provinsi }}
                            </div>
                            <div class="flex gap-4">
                                <div class="flex-1 p-4 bg-slate-50 rounded-2xl border border-slate-100 flex items-center gap-3">
                                    <div class="text-blue-400"><i class='bx bx-mail-send text-xl'></i></div>
                                    <div>
                                        <div class="text-[9px] font-black text-slate-400 uppercase mb-1">Kodepos</div>
                                        <div class="text-sm font-bold text-slate-700">{{ $sekolah->kode_pos ?? '-' }}</div>
                                    </div>
                                </div>
                                <div class="flex-1 p-4 bg-slate-50 rounded-2xl border border-slate-100 flex items-center gap-3">
                                    <div class="text-blue-400"><i class='bx bx-globe'></i></div>
                                    <div>
                                        <div class="text-[9px] font-black text-slate-400 uppercase mb-1">Wilayah</div>
                                        <div class="text-sm font-bold text-slate-700">{{ $sekolah->kode_wilayah ?? '-' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="rounded-[2rem] overflow-hidden border border-slate-100 shadow-inner h-64 bg-slate-100 relative">
                            @if($sekolah->lintang && $sekolah->bujur)
                                <iframe width="100%" height="100%" frameborder="0" style="border:0" 
                                    src="https://maps.google.com/maps?q={{ $sekolah->lintang }},{{ $sekolah->bujur }}&z=15&output=embed" allowfullscreen></iframe>
                            @else
                                <div class="w-full h-full flex flex-col items-center justify-center text-slate-300">
                                    <i class='bx bx-map-alt text-5xl mb-2 opacity-20'></i>
                                    <p class="text-[10px] font-black uppercase">Peta Belum Tersedia</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side -->
            <div class="lg:col-span-4 space-y-8">
                <!-- Contact Card -->
                <div class="bg-slate-900 rounded-[3rem] p-10 text-white shadow-2xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-blue-500/10 rounded-full -mr-16 -mt-16"></div>
                    <h3 class="text-lg font-black uppercase tracking-widest mb-10 flex items-center gap-3 text-blue-400">
                        <i class='bx bx-headphone text-2xl'></i> Hubungi Kami
                    </h3>
                    
                    <div class="space-y-8">
                        @if($sekolah->website)
                        <a href="{{ str_starts_with($sekolah->website, 'http') ? $sekolah->website : 'https://'.$sekolah->website }}" target="_blank" class="flex items-center gap-5 group">
                            <div class="w-12 h-12 rounded-2xl bg-white/5 flex items-center justify-center group-hover:bg-blue-600 transition-colors shadow-inner">
                                <i class='bx bx-globe text-xl'></i>
                            </div>
                            <div>
                                <div class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Situs Resmi</div>
                                <div class="text-sm font-bold truncate max-w-[180px]">{{ str_replace(['https://', 'http://'], '', $sekolah->website) }}</div>
                            </div>
                        </a>
                        @endif

                        <a href="mailto:{{ $sekolah->email ?? 'info@sekolah.sch.id' }}" class="flex items-center gap-5 group">
                            <div class="w-12 h-12 rounded-2xl bg-white/5 flex items-center justify-center group-hover:bg-blue-600 transition-colors shadow-inner">
                                <i class='bx bx-envelope text-xl'></i>
                            </div>
                            <div>
                                <div class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Email Sekolah</div>
                                <div class="text-sm font-bold truncate max-w-[180px]">{{ $sekolah->email ?? 'Belum tersedia' }}</div>
                            </div>
                        </a>

                        <a href="tel:{{ $sekolah->nomor_telepon }}" class="flex items-center gap-5 group">
                            <div class="w-12 h-12 rounded-2xl bg-white/5 flex items-center justify-center group-hover:bg-blue-600 transition-colors shadow-inner">
                                <i class='bx bx-phone-call text-xl'></i>
                            </div>
                            <div>
                                <div class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Telepon</div>
                                <div class="text-sm font-bold">{{ $sekolah->nomor_telepon ?? 'Belum tersedia' }}</div>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Verified Card -->
                <div class="p-8 bg-blue-600 rounded-[3rem] text-white shadow-xl relative overflow-hidden group">
                    <i class='bx bxs-badge-check absolute -right-4 -bottom-4 text-8xl text-white/10 group-hover:scale-110 transition-transform'></i>
                    <div class="relative z-10 text-center">
                        <div class="w-16 h-16 rounded-2xl bg-white/20 flex items-center justify-center mx-auto mb-6 text-3xl shadow-inner">
                            <i class='bx bx-shield-quarter'></i>
                        </div>
                        <h4 class="text-base font-black uppercase tracking-widest mb-2">Terverifikasi</h4>
                        <p class="text-[10px] text-blue-100 font-medium leading-relaxed opacity-80">
                            Informasi ini merupakan data resmi yang telah disinkronisasi melalui sistem Satu Data Pendidikan.
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
