@extends('layouts.admin')

@section('content')

@use('App\Services\EncryptionService')

{{-- 🔥 CSS PREMIUM & ANIMASI 🔥 --}}
<style>
    /* Utility & Cards */
    .rounded-4 { border-radius: 1.25rem !important; }
    .shadow-soft { box-shadow: 0 8px 30px rgba(0, 0, 0, 0.04) !important; }
    .shadow-sm-custom { box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03) !important; }
    
    /* Animasi Keyframes */
    @keyframes fadeInUp {
        0% { opacity: 0; transform: translateY(30px); }
        100% { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-up {
        animation: fadeInUp 0.7s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        opacity: 0;
    }
    .delay-1 { animation-delay: 0.1s; }
    .delay-2 { animation-delay: 0.2s; }
    
    /* Profile Header */
    .profile-cover {
        background: linear-gradient(135deg, #20c997 0%, #2563eb 100%);
        height: 180px;
        position: relative;
        overflow: hidden;
    }
    .profile-cover::after {
        content: '';
        position: absolute;
        bottom: 0; left: 0; right: 0;
        height: 50px;
        background: linear-gradient(to top, rgba(255,255,255,1) 0%, rgba(255,255,255,0) 100%);
    }
    .avatar-profile {
        width: 140px; height: 140px;
        border: 5px solid #fff;
        box-shadow: 0 10px 25px rgba(37, 99, 235, 0.2);
        background-color: #fff;
        margin-top: -70px;
        position: relative;
        z-index: 2;
        transition: transform 0.3s ease;
    }
    .avatar-profile:hover { transform: scale(1.05) rotate(-2deg); }

    /* Custom Tabs */
    .nav-custom .nav-link {
        border-radius: 0.5rem; color: #697a8d; font-weight: 600; padding: 0.7rem 1.5rem; transition: all 0.3s; border: none;
    }
    .nav-custom .nav-link:hover:not(.active) { background-color: rgba(37, 99, 235, 0.05); color: #2563eb; }
    .nav-custom .nav-link.active { 
        background-color: #2563eb; color: #fff; 
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4); 
        transform: translateY(-2px); 
    }

    /* Table Data Styling */
    .table-profile td { padding: 1rem 0; border-bottom: 1px dashed #eef0f2; vertical-align: middle; }
    .table-profile tr:last-child td { border-bottom: none; }
    .label-td { width: 35%; font-size: 0.75rem; font-weight: 700; color: #a1acb8; text-transform: uppercase; letter-spacing: 0.5px; }
    .value-td { color: #32475c; font-weight: 600; font-size: 0.95rem; }

    /* Contact Icons */
    .icon-box { 
        width: 42px; height: 42px; border-radius: 12px; 
        display: inline-flex; align-items: center; justify-content: center; 
        font-size: 1.5rem; flex-shrink: 0; transition: 0.3s;
    }
    .icon-box:hover { transform: scale(1.1); }
    .icon-primary { background: rgba(37, 99, 235, 0.1); color: #2563eb; }
    .icon-success { background: rgba(32, 201, 151, 0.1); color: #20c997; }
    .icon-dark { background: rgba(67, 89, 113, 0.1); color: #435971; }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    {{-- BREADCRUMB --}}
    <h4 class="fw-bold py-3 mb-4 animate-fade-up">
        <span class="text-muted fw-light">Kesiswaan / <a href="{{ route('admin.kesiswaan.siswa.index') }}" class="text-muted text-decoration-none">Data Siswa</a> /</span> Profil Detail
    </h4>

    {{-- HEADER PROFIL --}}
    <div class="row mb-4 animate-fade-up delay-1">
        <div class="col-12">
            <div class="card border-0 shadow-soft rounded-4 overflow-hidden">
                <div class="profile-cover"></div>
                
                <div class="card-body position-relative pt-0 pb-4">
                    <div class="row">
                        {{-- FOTO PROFIL --}}
                        <div class="col-12 col-md-auto text-center text-md-start">
                            <div class="avatar-profile rounded-circle mx-auto mx-md-0 d-flex align-items-center justify-content-center p-1">
                                @php
                                    $cleanPath = str_replace('public/', '', $siswa->foto ?? '');
                                    $fileExists = !empty($cleanPath) && \Illuminate\Support\Facades\Storage::disk('public')->exists($cleanPath);
                                @endphp
                                @if($fileExists)
                                    <img src="{{ asset('storage/' . $cleanPath) }}" alt="Avatar" class="rounded-circle w-100 h-100" style="object-fit: cover;">
                                @else
                                    <span class="rounded-circle bg-label-info fs-1 fw-bold text-uppercase w-100 h-100 d-flex align-items-center justify-content-center" style="font-size: 2.5rem !important;">
                                        <i class='bx bx-user'></i>
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- IDENTITAS --}}
                        <div class="col flex-grow-1 mt-3 mt-md-0 text-center text-md-start pt-md-3">
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center align-items-md-start">
                                <div>
                                    <h3 class="fw-bold mb-1 text-dark">{{ $siswa->nama }}</h3>
                                    <p class="mb-3 text-muted fw-medium fs-6">Siswa Kelas <span class="fw-bold text-primary">{{ $siswa->rombel->nama ?? 'Belum Terdaftar' }}</span></p>
                                    
                                    <div class="d-flex flex-wrap justify-content-center justify-content-md-start gap-2">
                                        <span class="badge bg-label-primary px-3 py-2 rounded-pill shadow-sm-custom">
                                            <i class='bx bx-id-card me-1'></i> NISN. {{ EncryptionService::decrypt($siswa->nisn) ?? '-' }}
                                        </span>
                                        <span class="badge bg-label-{{ ($siswa->jenis_kelamin == 'L' || $siswa->jenis_kelamin == 'Laki-laki') ? 'info' : 'danger' }} px-3 py-2 rounded-pill shadow-sm-custom">
                                            <i class='bx bx-user me-1'></i> {{ ($siswa->jenis_kelamin == 'L' || $siswa->jenis_kelamin == 'Laki-laki') ? 'Laki-laki' : 'Perempuan' }}
                                        </span>
                                        <span class="badge bg-label-success px-3 py-2 rounded-pill shadow-sm-custom">
                                            <i class='bx bx-check-circle me-1'></i> {{ $siswa->status ?? 'Aktif' }}
                                        </span>
                                    </div>
                                </div>

                                {{-- TOMBOL KEMBALI --}}
                                <div class="mt-4 mt-md-0">
                                    <a href="{{ route('admin.kesiswaan.siswa.index') }}" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm-custom fw-bold">
                                        <i class="bx bx-arrow-back me-1"></i> Kembali
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- KONTEN UTAMA --}}
    <div class="row animate-fade-up delay-2">
        {{-- KIRI: INFO AKADEMIK & KONTAK --}}
        <div class="col-xl-4 col-lg-5 col-md-5 mb-4">
            <div class="card h-100 shadow-soft border-0 rounded-4">
                <div class="card-header pt-4 pb-3 border-bottom">
                    <h6 class="fw-bold mb-0 text-uppercase text-muted" style="letter-spacing: 0.5px;">Akademik & Kontak</h6>
                </div>
                <div class="card-body pt-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="icon-box icon-dark me-3"><i class='bx bx-buildings'></i></div>
                        <div class="overflow-hidden">
                            <span class="d-block small text-muted">Sekolah Induk</span>
                            <span class="fw-bold text-dark text-truncate d-block" title="{{ $siswa->sekolah->nama ?? '' }}">
                                {{ $siswa->sekolah->nama ?? 'Belum Terhubung' }}
                            </span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-4">
                        <div class="icon-box icon-primary me-3"><i class='bx bx-barcode'></i></div>
                        <div>
                            <span class="d-block small text-muted">NIPD / NIK</span>
                            <span class="fw-semibold text-dark font-monospace">{{ $siswa->nipd ?? '-' }} / {{ EncryptionService::decrypt($siswa->nik) ?? '-' }}</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="icon-box icon-success me-3"><i class='bx bxl-whatsapp'></i></div>
                        <div>
                            <span class="d-block small text-muted">Email / HP</span>
                            <span class="fw-semibold text-dark">{{ $siswa->email ?? '-' }} <br> {{ EncryptionService::decrypt($siswa->nomor_telepon_seluler) ?? '-' }}</span>
                        </div>
                    </div>

                    <hr class="my-4 text-light">

                    <h6 class="fw-bold mb-3 text-uppercase text-muted" style="letter-spacing: 0.5px;">Alamat Domisili</h6>
                    <div class="bg-lighter rounded-3 p-3">
                        <p class="mb-1 text-dark fw-medium"><i class='bx bx-map text-primary me-2'></i>{{ $siswa->alamat_jalan ?? 'Alamat belum diisi' }}</p>
                        @if($siswa->rt || $siswa->rw)
                            <p class="mb-1 ms-4 text-muted small">RT {{ $siswa->rt ?? '-' }} / RW {{ $siswa->rw ?? '-' }}</p>
                        @endif
                        @if($siswa->desa_kelurahan || $siswa->kecamatan)
                            <p class="mb-1 ms-4 text-muted small">Ds. {{ $siswa->desa_kelurahan ?? '-' }}, Kec. {{ $siswa->kecamatan ?? '-' }}</p>
                        @endif
                        @if($siswa->kabupaten_kota)
                            <p class="mb-0 ms-4 fw-semibold text-dark">{{ $siswa->kabupaten_kota ?? '' }} <span class="text-muted fw-normal">{{ $siswa->kode_pos ? '- '.$siswa->kode_pos : '' }}</span></p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- KANAN: TAB DETAIL --}}
        <div class="col-xl-8 col-lg-7 col-md-7 mb-4">
            <div class="card h-100 shadow-soft border-0 rounded-4">
                <div class="card-header border-bottom pt-4 pb-3">
                    <ul class="nav nav-pills nav-custom" role="tablist">
                        <li class="nav-item">
                            <button type="button" class="nav-link active shadow-xs" role="tab" data-bs-toggle="tab" data-bs-target="#navs-pribadi">
                                <i class="bx bx-user me-1"></i> Data Pribadi
                            </button>
                        </li>
                        <li class="nav-item ms-2">
                            <button type="button" class="nav-link shadow-xs" role="tab" data-bs-toggle="tab" data-bs-target="#navs-akademik">
                                <i class="bx bx-book-open me-1"></i> Akademik
                            </button>
                        </li>
                        <li class="nav-item ms-2">
                            <button type="button" class="nav-link shadow-xs" role="tab" data-bs-toggle="tab" data-bs-target="#navs-wali">
                                <i class="bx bx-group me-1"></i> Orang Tua
                            </button>
                        </li>
                    </ul>
                </div>
                
                <div class="card-body p-4">
                    <div class="tab-content p-0">
                        
                        {{-- TAB 1: DATA PRIBADI --}}
                        <div class="tab-pane fade show active" id="navs-pribadi" role="tabpanel">
                            <div class="table-responsive text-nowrap">
                                <table class="table table-borderless table-profile w-100 m-0">
                                    <tbody>
                                        <tr><td class="label-td">Nama Lengkap</td><td class="value-td">{{ $siswa->nama }}</td></tr>
                                        <tr><td class="label-td">Tempat, Tgl Lahir</td><td class="value-td">{{ $siswa->tempat_lahir ?? '-' }}, {{ $siswa->tanggal_lahir ? \Carbon\Carbon::parse(EncryptionService::decrypt($siswa->tanggal_lahir))->translatedFormat('d F Y') : '-' }}</td></tr>
                                        <tr><td class="label-td">Jenis Kelamin</td><td class="value-td">{{ ($siswa->jenis_kelamin == 'L' || $siswa->jenis_kelamin == 'Laki-laki') ? 'Laki-laki' : 'Perempuan' }}</td></tr>
                                        <tr><td class="label-td">Agama</td><td class="value-td">{{ $siswa->agama_id_str ?? '-' }}</td></tr>
                                        <tr><td class="label-td">Kewarganegaraan</td><td class="value-td">{{ $siswa->kewarganegaraan ?? 'Indonesia' }}</td></tr>
                                        <tr><td class="label-td">Anak Ke</td><td class="value-td">{{ $siswa->anak_keberapa ?? '-' }} <span class="text-muted small ms-1">(Jml Saudara: {{ $siswa->jumlah_saudara_kandung ?? '-' }})</span></td></tr>
                                        <tr><td class="label-td">Fisik</td><td class="value-td">{{ $siswa->tinggi_badan ?? '-' }} cm / {{ $siswa->berat_badan ?? '-' }} kg</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- TAB 2: AKADEMIK --}}
                        <div class="tab-pane fade" id="navs-akademik" role="tabpanel">
                            <div class="table-responsive text-nowrap">
                                <table class="table table-borderless table-profile w-100 m-0">
                                    <tbody>
                                        <tr><td class="label-td">NISN</td><td class="value-td font-monospace text-primary">{{ EncryptionService::decrypt($siswa->nisn) ?? '-' }}</td></tr>
                                        <tr><td class="label-td">NIPD / Nomor Induk</td><td class="value-td font-monospace">{{ $siswa->nipd ?? '-' }}</td></tr>
                                        <tr><td class="label-td">Kelas Saat Ini</td><td class="value-td"><span class="badge bg-label-info px-3 py-1">{{ $siswa->rombel->nama ?? 'Belum Ditentukan' }}</span></td></tr>
                                        <tr><td class="label-td">Tingkat</td><td class="value-td">{{ $siswa->tingkat_pendidikan_id ?? '-' }}</td></tr>
                                        <tr><td class="label-td">Tanggal Masuk</td><td class="value-td">{{ $siswa->tanggal_masuk_sekolah ? \Carbon\Carbon::parse($siswa->tanggal_masuk_sekolah)->translatedFormat('d F Y') : '-' }}</td></tr>
                                        <tr><td class="label-td">Asal Sekolah Sebelumnya</td><td class="value-td">{{ $siswa->sekolah_asal ?? '-' }}</td></tr>
                                        <tr><td class="label-td">No. Ijazah (SMP/MTs)</td><td class="value-td font-monospace">{{ $siswa->no_seri_ijazah ?? '-' }}</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- TAB 3: ORANG TUA / WALI --}}
                        <div class="tab-pane fade" id="navs-wali" role="tabpanel">
                            <h6 class="fw-bold mb-3 text-primary"><i class="bx bx-male me-2"></i>Data Ayah</h6>
                            <div class="table-responsive text-nowrap mb-4">
                                <table class="table table-borderless table-profile w-100 m-0">
                                    <tbody>
                                        <tr><td class="label-td">Nama Ayah</td><td class="value-td">{{ $siswa->nama_ayah ?? '-' }}</td></tr>
                                        <tr><td class="label-td">Pekerjaan</td><td class="value-td">{{ $siswa->pekerjaan_ayah_id_str ?? '-' }}</td></tr>
                                        <tr><td class="label-td">Penghasilan</td><td class="value-td">{{ $siswa->penghasilan_ayah_id_str ?? '-' }}</td></tr>
                                    </tbody>
                                </table>
                            </div>

                            <hr class="text-light">

                            <h6 class="fw-bold my-3 text-info"><i class="bx bx-female me-2"></i>Data Ibu</h6>
                            <div class="table-responsive text-nowrap">
                                <table class="table table-borderless table-profile w-100 m-0">
                                    <tbody>
                                        <tr><td class="label-td">Nama Ibu</td><td class="value-td">{{ $siswa->nama_ibu ?? '-' }}</td></tr>
                                        <tr><td class="label-td">Pekerjaan</td><td class="value-td">{{ $siswa->pekerjaan_ibu_id_str ?? '-' }}</td></tr>
                                        <tr><td class="label-td">Penghasilan</td><td class="value-td">{{ $siswa->penghasilan_ibu_id_str ?? '-' }}</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection