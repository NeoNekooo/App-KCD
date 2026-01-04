@extends('layouts.admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    
    {{-- BREADCRUMB --}}
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Kepegawaian / <a href="{{ route('admin.kepegawaian.guru.index') }}">Data Guru</a> /</span> Detail Profil
    </h4>

    {{-- HEADER PROFIL --}}
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 border-0 shadow-sm overflow-hidden">
                {{-- Background Gradient --}}
                <div class="h-px-150 bg-primary position-relative" style="background: linear-gradient(45deg, #696cff, #8592a3);">
                    <div style="position: absolute; bottom: -20px; left: 0; width: 100%; height: 60px; background: #f5f5f9; clip-path: polygon(0 50%, 100% 0, 100% 100%, 0% 100%);"></div>
                </div>
                
                <div class="card-body position-relative pt-0">
                    <div class="row">
                        <div class="col-sm-auto text-center text-sm-start mt-n5">
                            <div class="d-flex justify-content-center justify-content-sm-start">
                                <div class="bg-white p-1 rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 140px; height: 140px;">
                                    @php
                                        // Logika Foto Aman
                                        $cleanPath = str_replace('public/', '', $gtk->foto ?? '');
                                        $fileExists = !empty($cleanPath) && Storage::disk('public')->exists($cleanPath);
                                    @endphp

                                    @if($fileExists)
                                        <img src="{{ asset('storage/' . $cleanPath) }}" alt="Avatar" class="rounded-circle p-1" style="width: 100%; height: 100%; object-fit: cover;">
                                    @else
                                        <span class="avatar-initial rounded-circle bg-label-primary fs-1 fw-bold text-uppercase w-100 h-100 d-flex align-items-center justify-content-center">
                                            {{ substr($gtk->nama, 0, 2) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col flex-grow-1 mt-3 mt-sm-4 text-center text-sm-start">
                            <h3 class="fw-bold mb-1 text-primary">{{ $gtk->nama }}</h3>
                            <p class="mb-2 text-muted fw-medium">{{ $gtk->jabatan_ptk_id_str ?? 'Guru Mapel' }}</p>
                            
                            <div class="d-flex flex-wrap justify-content-center justify-content-sm-start gap-2">
                                <span class="badge bg-label-primary">
                                    <i class='bx bx-id-card me-1'></i> NIP: {{ $gtk->nip ?? '-' }}
                                </span>
                                <span class="badge bg-label-info">
                                    <i class='bx bx-barcode me-1'></i> NUPTK: {{ $gtk->nuptk ?? '-' }}
                                </span>
                                <span class="badge bg-label-{{ ($gtk->jenis_kelamin == 'L' || $gtk->jenis_kelamin == 'Laki-laki') ? 'info' : 'danger' }}">
                                    <i class='bx bx-user me-1'></i> {{ ($gtk->jenis_kelamin == 'L' || $gtk->jenis_kelamin == 'Laki-laki') ? 'Laki-laki' : 'Perempuan' }}
                                </span>
                                <span class="badge bg-label-success">
                                    <i class='bx bx-check-circle me-1'></i> {{ $gtk->status ?? 'Aktif' }}
                                </span>
                            </div>
                        </div>
                        <div class="col-12 col-md-auto mt-4 mt-sm-0 text-center text-sm-end d-flex align-items-end flex-column justify-content-center">
                            <a href="{{ route('admin.kepegawaian.guru.index') }}" class="btn btn-outline-secondary mb-2">
                                <i class="bx bx-arrow-back me-1"></i> Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- STATISTIK SINGKAT CARD --}}
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 border-start border-4 border-primary">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div class="overflow-hidden">
                        <span class="d-block fw-semibold text-muted mb-1">Status Pegawai</span>
                        <h5 class="mb-0 fw-bold text-truncate" title="{{ $gtk->status_kepegawaian_id_str }}">{{ $gtk->status_kepegawaian_id_str ?? '-' }}</h5>
                    </div>
                    <div class="avatar bg-label-primary rounded p-2"><i class="bx bx-briefcase fs-4"></i></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 border-start border-4 border-success">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div class="overflow-hidden">
                        <span class="d-block fw-semibold text-muted mb-1">Pendidikan</span>
                        <h5 class="mb-0 fw-bold text-truncate">{{ $gtk->pendidikan_terakhir ?? '-' }}</h5>
                    </div>
                    <div class="avatar bg-label-success rounded p-2"><i class="bx bx-book-bookmark fs-4"></i></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 border-start border-4 border-warning">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div class="overflow-hidden">
                        <span class="d-block fw-semibold text-muted mb-1">Usia</span>
                        <h5 class="mb-0 fw-bold">
                            {{ $gtk->tanggal_lahir ? \Carbon\Carbon::parse($gtk->tanggal_lahir)->age . ' Tahun' : '-' }}
                        </h5>
                    </div>
                    <div class="avatar bg-label-warning rounded p-2"><i class="bx bx-cake fs-4"></i></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 border-start border-4 border-info">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div class="overflow-hidden">
                        <span class="d-block fw-semibold text-muted mb-1">Masa Kerja</span>
                        <h5 class="mb-0 fw-bold">
                            {{ $gtk->tmt_pengangkatan ? \Carbon\Carbon::parse($gtk->tmt_pengangkatan)->diffForHumans(null, true) : '-' }}
                        </h5>
                    </div>
                    <div class="avatar bg-label-info rounded p-2"><i class="bx bx-time-five fs-4"></i></div>
                </div>
            </div>
        </div>
    </div>

    {{-- DETAIL CONTENT (KIRI: KONTAK, KANAN: TABS) --}}
    <div class="row">
        {{-- INFO KIRI --}}
        <div class="col-xl-4 col-lg-5 col-md-5 mb-4">
            <div class="card h-100">
                <div class="card-header border-bottom">
                    <h5 class="card-title mb-0"><i class='bx bx-phone-call me-2 text-primary'></i>Kontak & Akun</h5>
                </div>
                <div class="card-body py-4">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item px-0 pb-3 pt-0">
                            <span class="fw-semibold d-block mb-1">Email:</span>
                            <span class="text-muted">{{ $gtk->email ?? '-' }}</span>
                        </li>
                        <li class="list-group-item px-0 py-3">
                            <span class="fw-semibold d-block mb-1">Nomor HP/WA:</span>
                            <span class="text-muted">{{ $gtk->no_hp ?? $gtk->no_telepon_rumah ?? '-' }}</span>
                        </li>
                        <li class="list-group-item px-0 py-3">
                            <span class="fw-semibold d-block mb-1">Sekolah Induk:</span>
                            @if($gtk->pengguna && $gtk->pengguna->sekolah)
                                <span class="badge bg-label-dark text-wrap lh-sm">{{ $gtk->pengguna->sekolah->nama }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </li>
                    </ul>
                    
                    <div class="mt-4 pt-3 border-top">
                        <h6 class="fw-bold mb-3">Alamat Domisili</h6>
                        <p class="mb-1 text-muted"><i class='bx bx-map me-2'></i>{{ $gtk->alamat_jalan ?? '-' }}</p>
                        <p class="mb-1 ms-4 text-muted small">RT {{ $gtk->rt ?? '-' }} / RW {{ $gtk->rw ?? '-' }}</p>
                        <p class="mb-1 ms-4 text-muted small">Ds. {{ $gtk->desa_kelurahan ?? '-' }}, Kec. {{ $gtk->kecamatan ?? '-' }}</p>
                        <p class="ms-4 fw-semibold text-dark">{{ $gtk->kabupaten_kota ?? '' }} - {{ $gtk->kode_pos ?? '' }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- INFO KANAN (TABS) --}}
        <div class="col-xl-8 col-lg-7 col-md-7 mb-4">
            <div class="nav-align-top mb-4 h-100">
                <ul class="nav nav-tabs nav-fill" role="tablist">
                    <li class="nav-item">
                        <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-pribadi">
                            <i class="bx bx-user me-1"></i> Data Pribadi
                        </button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-kepegawaian">
                            <i class="bx bx-briefcase me-1"></i> Kepegawaian
                        </button>
                    </li>
                </ul>
                <div class="tab-content h-100">
                    
                    {{-- TAB 1: DATA PRIBADI --}}
                    <div class="tab-pane fade show active" id="navs-pribadi" role="tabpanel">
                        <div class="table-responsive text-nowrap">
                            <table class="table table-borderless table-sm">
                                <tbody>
                                    <tr>
                                        <td width="35%" class="fw-semibold text-muted">Nama Lengkap</td>
                                        <td>: <span class="fw-bold text-dark">{{ $gtk->nama }}</span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">NIK</td>
                                        <td>: {{ $gtk->nik ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">Tempat Lahir</td>
                                        <td>: {{ $gtk->tempat_lahir ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">Tanggal Lahir</td>
                                        <td>: {{ $gtk->tanggal_lahir ? \Carbon\Carbon::parse($gtk->tanggal_lahir)->translatedFormat('d F Y') : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">Nama Ibu Kandung</td>
                                        <td>: {{ $gtk->nama_ibu_kandung ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">Agama</td>
                                        <td>: {{ $gtk->agama_id_str ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">Kewarganegaraan</td>
                                        <td>: {{ $gtk->kewarganegaraan ?? 'Indonesia' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">Status Perkawinan</td>
                                        <td>: {{ $gtk->status_perkawinan ?? '-' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- TAB 2: KEPEGAWAIAN --}}
                    <div class="tab-pane fade" id="navs-kepegawaian" role="tabpanel">
                        <div class="table-responsive text-nowrap">
                            <table class="table table-borderless table-sm">
                                <tbody>
                                    <tr>
                                        <td width="35%" class="fw-semibold text-muted">Jenis PTK</td>
                                        <td>: <span class="badge bg-label-info">{{ $gtk->jenis_ptk_id_str ?? '-' }}</span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">NIY / NIGK</td>
                                        <td>: {{ $gtk->niy_nigk ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">SK Pengangkatan</td>
                                        <td>: {{ $gtk->sk_pengangkatan ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">TMT Pengangkatan</td>
                                        <td>: {{ $gtk->tmt_pengangkatan ? \Carbon\Carbon::parse($gtk->tmt_pengangkatan)->translatedFormat('d F Y') : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">Lembaga Pengangkat</td>
                                        <td>: {{ $gtk->lembaga_pengangkat ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">SK CPNS / TMT</td>
                                        <td>: {{ $gtk->sk_cpns ?? '-' }} <small class="text-muted">({{ $gtk->tmt_cpns ?? '-' }})</small></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">Sumber Gaji</td>
                                        <td>: {{ $gtk->sumber_gaji ?? '-' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection