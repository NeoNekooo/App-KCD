@extends('layouts.frontend')

@section('title', $sekolah->nama . ' - Profil Lembaga')

@push('styles')
<style>
    .glass-profile {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    .stat-card {
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-5px);
    }
    .info-label {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        font-weight: 900;
        color: #94a3b8;
        margin-bottom: 0.25rem;
    }
    .info-value {
        font-weight: 700;
        color: #1e293b;
        font-size: 0.95rem;
    }
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
<!-- Hero Header -->
<div class="bg-blue-950 pt-16 pb-32 relative overflow-hidden">
    <div class="absolute inset-0 opacity-20">
        <div class="absolute top-0 -right-20 w-96 h-96 bg-blue-500 rounded-full blur-[100px]"></div>
        <div class="absolute bottom-0 -left-20 w-96 h-96 bg-indigo-500 rounded-full blur-[100px]"></div>
    </div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <a href="{{ url('/lembaga') }}" class="inline-flex items-center gap-2 text-blue-300 hover:text-white text-xs font-black uppercase tracking-widest mb-8 transition-all group">
            <i class='bx bx-left-arrow-alt fs-4 group-hover:-translate-x-1 transition-transform'></i>
            Kembali ke Daftar
        </a>

        <div class="flex flex-col md:flex-row items-center md:items-end gap-8">
            <!-- Logo Sekolah -->
            <div class="w-32 h-32 md:w-40 md:h-40 rounded-[2.5rem] bg-white p-6 shadow-2xl flex items-center justify-center flex-shrink-0 animate-in">
                @if($sekolah->logo)
                    <img src="{{ Storage::url($sekolah->logo) }}" class="w-full h-full object-contain" alt="Logo {{ $sekolah->nama }}">
                @else
                    <i class='bx bxs-school text-blue-100' style="font-size: 5rem;"></i>
                @endif
            </div>

            <!-- Judul & Info Utama -->
            <div class="text-center md:text-left flex-grow animate-in" style="animation-delay: 100ms;">
                <div class="flex flex-wrap justify-center md:justify-start gap-2 mb-4">
                    <span class="px-3 py-1 rounded-lg bg-blue-500/20 text-blue-300 text-[10px] font-black uppercase tracking-widest border border-blue-400/30">
                        {{ $sekolah->bentuk_pendidikan_id_str }}
                    </span>
                    <span class="px-3 py-1 rounded-lg {{ strtolower($sekolah->status_sekolah_str) == 'negeri' ? 'bg-emerald-500/20 text-emerald-300 border-emerald-400/30' : 'bg-amber-500/20 text-amber-300 border-amber-400/30' }} text-[10px] font-black uppercase tracking-widest border">
                        {{ $sekolah->status_sekolah_str }}
                    </span>
                </div>
                <h1 class="text-3xl md:text-5xl font-black text-white leading-tight mb-2 uppercase tracking-tight">
                    {{ $sekolah->nama }}
                </h1>
                <p class="text-blue-200/70 font-medium flex items-center justify-center md:justify-start gap-2">
                    <i class='bx bx-fingerprint'></i> NPSN: {{ $sekolah->npsn ?? '-' }}
                </p>
            </div>

            <!-- Quick Action -->
            <div class="animate-in" style="animation-delay: 200ms;">
                @if($sekolah->website)
                <a href="{{ str_starts_with($sekolah->website, 'http') ? $sekolah->website : 'https://'.$sekolah->website }}" target="_blank" 
                   class="inline-flex items-center gap-2 px-6 py-3 bg-white text-blue-900 font-black uppercase tracking-widest text-xs rounded-2xl hover:bg-blue-50 transition-all shadow-xl">
                    Kunjungi Website <i class='bx bx-link-external'></i>
                </a>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Content Grid -->
<div class="bg-slate-50 min-h-screen -mt-12 pb-24 relative z-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- Left Column: Details -->
            <div class="lg:col-span-8 space-y-8">
                <!-- Data Identitas -->
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 p-8 md:p-10 animate-in" style="animation-delay: 300ms;">
                    <div class="flex items-center gap-3 mb-8">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                            <i class='bx bx-info-circle fs-4'></i>
                        </div>
                        <h2 class="text-xl font-black text-slate-800 uppercase tracking-tight">Identitas Lembaga</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-6">
                            <div>
                                <div class="info-label">Nama Satuan Pendidikan</div>
                                <div class="info-value">{{ $sekolah->nama }}</div>
                            </div>
                            <div>
                                <div class="info-label">NPSN / NSS</div>
                                <div class="info-value">{{ $sekolah->npsn ?? '-' }} / {{ $sekolah->nss ?? '-' }}</div>
                            </div>
                            <div>
                                <div class="info-label">Status Sekolah</div>
                                <div class="info-value">{{ $sekolah->status_sekolah_str ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="space-y-6">
                            <div>
                                <div class="info-label">Bentuk Pendidikan</div>
                                <div class="info-value">{{ $sekolah->bentuk_pendidikan_id_str ?? '-' }}</div>
                            </div>
                            <div>
                                <div class="info-label">Penyelenggaraan SKS</div>
                                <div class="info-value">{{ $sekolah->is_sks == 1 ? 'Ya' : 'Tidak' }}</div>
                            </div>
                            <div>
                                <div class="info-label">Kode Wilayah</div>
                                <div class="info-value">{{ $sekolah->kode_wilayah ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kontak & Alamat -->
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 p-8 md:p-10 animate-in" style="animation-delay: 400ms;">
                    <div class="flex items-center gap-3 mb-8">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <i class='bx bx-map-pin fs-4'></i>
                        </div>
                        <h2 class="text-xl font-black text-slate-800 uppercase tracking-tight">Lokasi & Kontak</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <div class="space-y-6">
                            <div>
                                <div class="info-label">Alamat Lengkap</div>
                                <div class="info-value leading-relaxed">
                                    {{ $sekolah->alamat_jalan ?? 'Alamat belum diatur' }}<br>
                                    @if($sekolah->desa_kelurahan) Desa/Kel. {{ $sekolah->desa_kelurahan }}, @endif
                                    @if($sekolah->kecamatan) Kec. {{ $sekolah->kecamatan }} @endif<br>
                                    {{ $sekolah->kabupaten_kota }}, {{ $sekolah->provinsi }} {{ $sekolah->kode_pos }}
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-4 pt-4">
                                @if($sekolah->email)
                                <a href="mailto:{{ $sekolah->email }}" class="flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-50 text-slate-600 hover:bg-blue-50 hover:text-blue-600 transition-colors text-xs font-bold">
                                    <i class='bx bx-envelope'></i> Email
                                </a>
                                @endif
                                @if($sekolah->nomor_telepon)
                                <a href="tel:{{ $sekolah->nomor_telepon }}" class="flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-50 text-slate-600 hover:bg-blue-50 hover:text-blue-600 transition-colors text-xs font-bold">
                                    <i class='bx bx-phone'></i> Telepon
                                </a>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Mini Map -->
                        <div class="rounded-3xl overflow-hidden border border-slate-100 h-48 bg-slate-50">
                            @if($sekolah->lintang && $sekolah->bujur)
                                <iframe width="100%" height="100%" frameborder="0" style="border:0" 
                                    src="https://maps.google.com/maps?q={{ $sekolah->lintang }},{{ $sekolah->bujur }}&z=15&output=embed" allowfullscreen></iframe>
                            @else
                                <div class="w-full h-full flex flex-col items-center justify-center text-slate-300">
                                    <i class='bx bx-map-alt fs-1'></i>
                                    <span class="text-[10px] font-black uppercase mt-2">Koordinat belum tersedia</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Sidebar -->
            <div class="lg:col-span-4 space-y-6">
                <!-- Statistik/Sekilas (Simulasi) -->
                <div class="bg-blue-600 rounded-[2.5rem] p-8 text-white animate-in" style="animation-delay: 500ms;">
                    <h3 class="text-lg font-black uppercase tracking-widest mb-6">Sekilas Data</h3>
                    <div class="space-y-6">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center text-2xl">
                                <i class='bx bx-user-voice'></i>
                            </div>
                            <div>
                                <div class="text-blue-100 text-[10px] font-black uppercase tracking-widest">Akreditasi</div>
                                <div class="text-xl font-black">A (Unggul)</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center text-2xl">
                                <i class='bx bx-group'></i>
                            </div>
                            <div>
                                <div class="text-blue-100 text-[10px] font-black uppercase tracking-widest">Kurikulum</div>
                                <div class="text-xl font-black">Merdeka</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-8 pt-8 border-t border-white/10">
                        <p class="text-blue-100 text-xs leading-relaxed opacity-80">
                            Data ini disinkronisasi secara berkala dari sistem database Dapodik pusat untuk menjamin akurasi informasi pendidikan.
                        </p>
                    </div>
                </div>

                <!-- Banner/Info -->
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 p-8 animate-in" style="animation-delay: 600ms;">
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest mb-4">Butuh Bantuan?</h3>
                    <p class="text-slate-500 text-xs leading-relaxed mb-6">Jika terdapat kesalahan data atau ingin menanyakan informasi lebih lanjut mengenai sekolah ini, silakan hubungi layanan pengaduan kami.</p>
                    <a href="/kontak" class="block w-full py-3 bg-slate-50 text-slate-600 text-center rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-100 transition">
                        Hubungi KCD Wilayah
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
