@extends('layouts.admin')

@use('App\Services\EncryptionService')

@section('content')
    {{-- 🔥 CSS PREMIUM & ANIMASI 🔥 --}}
    <style>
        /* Utility & Cards */
        .rounded-4 {
            border-radius: 1.25rem !important;
        }

        .shadow-soft {
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.04) !important;
        }

        .shadow-sm-custom {
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03) !important;
        }

        /* Animasi Keyframes */
        @keyframes fadeInUp {
            0% {
                opacity: 0;
                transform: translateY(30px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-up {
            animation: fadeInUp 0.7s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
            /* Mulai dari invisible */
        }

        .delay-1 {
            animation-delay: 0.1s;
        }

        .delay-2 {
            animation-delay: 0.2s;
        }

        .delay-3 {
            animation-delay: 0.3s;
        }

        /* Profile Header */
        .profile-cover {
            background: linear-gradient(135deg, #696cff 0%, #8592a3 100%);
            height: 180px;
            position: relative;
            overflow: hidden;
        }

        .profile-cover::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 50px;
            background: linear-gradient(to top, rgba(255, 255, 255, 1) 0%, rgba(255, 255, 255, 0) 100%);
        }

        .avatar-profile {
            width: 140px;
            height: 140px;
            border: 5px solid #fff;
            box-shadow: 0 10px 25px rgba(105, 108, 255, 0.2);
            background-color: #fff;
            margin-top: -70px;
            position: relative;
            z-index: 2;
            transition: transform 0.3s ease;
        }

        .avatar-profile:hover {
            transform: scale(1.05) rotate(-2deg);
        }

        /* Cards Hover Lift */
        .hover-lift {
            transition: all 0.3s ease;
        }

        .hover-lift:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08) !important;
        }

        /* Custom Tabs */
        .nav-custom .nav-link {
            border-radius: 0.5rem;
            color: #697a8d;
            font-weight: 600;
            padding: 0.7rem 1.5rem;
            transition: all 0.3s;
            border: none;
        }

        .nav-custom .nav-link:hover:not(.active) {
            background-color: rgba(105, 108, 255, 0.05);
            color: #696cff;
        }

        .nav-custom .nav-link.active {
            background-color: #696cff;
            color: #fff;
            box-shadow: 0 4px 12px rgba(105, 108, 255, 0.4);
            transform: translateY(-2px);
        }

        /* Table Data Styling */
        .table-profile td {
            padding: 1rem 0;
            border-bottom: 1px dashed #eef0f2;
            vertical-align: middle;
        }

        .table-profile tr:last-child td {
            border-bottom: none;
        }

        .label-td {
            width: 35%;
            font-size: 0.75rem;
            font-weight: 700;
            color: #a1acb8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .value-td {
            color: #32475c;
            font-weight: 600;
            font-size: 0.95rem;
        }

        /* Contact Icons */
        .icon-box {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
            transition: 0.3s;
        }

        .icon-box:hover {
            transform: scale(1.1);
        }

        .icon-primary {
            background: rgba(105, 108, 255, 0.1);
            color: #696cff;
        }

        .icon-success {
            background: rgba(113, 221, 55, 0.1);
            color: #71dd37;
        }

        .icon-dark {
            background: rgba(67, 89, 113, 0.1);
            color: #435971;
        }
    </style>

    @php
        $isGuru = str_contains($gtk->jenis_ptk_id_str, 'Guru');
        $backRoute = $isGuru ? route('admin.gtk.guru.index') : route('admin.gtk.tendik.index');
        $labelMenu = $isGuru ? 'Data Guru' : 'Data Tendik';
    @endphp

    {{-- Dihapus class 'container-xxl' agar layout sejajar penuh dengan navbar --}}
    <div class="flex-grow-1 container-p-y">

        {{-- BREADCRUMB --}}
        <h4 class="fw-bold py-3 mb-4 animate-fade-up">
            <span class="text-muted fw-light">GTK / <a href="{{ $backRoute }}"
                    class="text-muted text-decoration-none">{{ $labelMenu }}</a> /</span> Profil Detail
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
                                <div
                                    class="avatar-profile rounded-circle mx-auto mx-md-0 d-flex align-items-center justify-content-center p-1 bg-white">
                                    @php
                                        $cleanPath = str_replace('public/', '', $gtk->foto ?? '');
                                        $fileExists = !empty($cleanPath) && Storage::disk('public')->exists($cleanPath);
                                    @endphp
                                    @if ($fileExists)
                                        <img src="{{ asset('storage/' . $cleanPath) }}" alt="Avatar"
                                            class="rounded-circle w-100 h-100" style="object-fit: cover;">
                                    @else
                                        {{-- Ganti alfabet jadi ikon bx-user --}}
                                        <span
                                            class="rounded-circle bg-label-primary fs-1 fw-bold text-uppercase w-100 h-100 d-flex align-items-center justify-content-center"
                                            style="font-size: 3.5rem !important;">
                                            <i class='bx bx-user'></i>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- IDENTITAS --}}
                            <div class="col flex-grow-1 mt-3 mt-md-0 text-center text-md-start pt-md-3">
                                <div
                                    class="d-flex flex-column flex-md-row justify-content-between align-items-center align-items-md-start">
                                    <div>
                                        <h3 class="fw-bold mb-1 text-dark">{{ $gtk->nama }}</h3>
                                        <p class="mb-3 text-primary fw-semibold fs-6">
                                            {{ $gtk->jabatan_ptk_id_str ?? 'Tenaga Kependidikan' }}</p>

                                        <div class="d-flex flex-wrap justify-content-center justify-content-md-start gap-2">
                                            <span class="badge bg-label-primary px-3 py-2 rounded-pill shadow-sm-custom">
                                                <i class='bx bx-id-card me-1'></i> NIP. {{ $gtk->nip ?? '-' }}
                                            </span>
                                            <span class="badge bg-label-info px-3 py-2 rounded-pill shadow-sm-custom">
                                                <i class='bx bx-barcode me-1'></i> NUPTK. {{ $gtk->nuptk ?? '-' }}
                                            </span>
                                            <span
                                                class="badge bg-label-{{ $gtk->jenis_kelamin == 'L' || $gtk->jenis_kelamin == 'Laki-laki' ? 'dark' : 'danger' }} px-3 py-2 rounded-pill shadow-sm-custom">
                                                <i class='bx bx-user me-1'></i>
                                                {{ $gtk->jenis_kelamin == 'L' || $gtk->jenis_kelamin == 'Laki-laki' ? 'Laki-laki' : 'Perempuan' }}
                                            </span>
                                            <span class="badge bg-label-success px-3 py-2 rounded-pill shadow-sm-custom">
                                                <i class='bx bx-check-circle me-1'></i> {{ $gtk->status ?? 'Aktif' }}
                                            </span>
                                        </div>
                                    </div>

                                    {{-- TOMBOL KEMBALI --}}
                                    <div class="mt-4 mt-md-0">
                                        <a href="{{ $backRoute }}"
                                            class="btn btn-outline-secondary rounded-pill shadow-sm-custom px-4 fw-bold">
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

        {{-- STATISTIK SINGKAT --}}
        <div class="row g-4 mb-4 animate-fade-up delay-2">
            <div class="col-sm-6 col-xl-3">
                <div class="card h-100 border-0 shadow-soft rounded-4 hover-lift">
                    <div class="card-body d-flex justify-content-between align-items-center p-4">
                        <div class="overflow-hidden">
                            <span class="d-block text-uppercase fw-bold text-muted small mb-1"
                                style="letter-spacing: 0.5px;">Status Pegawai</span>
                            <h5 class="mb-0 fw-bold text-dark text-truncate" title="{{ $gtk->status_kepegawaian_id_str }}">
                                {{ $gtk->status_kepegawaian_id_str ?? '-' }}</h5>
                        </div>
                        <div class="icon-box icon-primary"><i class="bx bx-briefcase"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card h-100 border-0 shadow-soft rounded-4 hover-lift">
                    <div class="card-body d-flex justify-content-between align-items-center p-4">
                        <div class="overflow-hidden">
                            <span class="d-block text-uppercase fw-bold text-muted small mb-1"
                                style="letter-spacing: 0.5px;">Pendidikan</span>
                            <h5 class="mb-0 fw-bold text-dark text-truncate">{{ $gtk->pendidikan_terakhir ?? '-' }}</h5>
                        </div>
                        <div class="icon-box icon-success"><i class="bx bx-book-bookmark"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card h-100 border-0 shadow-soft rounded-4 hover-lift">
                    <div class="card-body d-flex justify-content-between align-items-center p-4">
                        <div class="overflow-hidden">
                            <span class="d-block text-uppercase fw-bold text-muted small mb-1"
                                style="letter-spacing: 0.5px;">Usia</span>
                            <h5 class="mb-0 fw-bold text-dark">
                                {{ $gtk->tanggal_lahir ? \Carbon\Carbon::parse($gtk->tanggal_lahir)->age . ' Tahun' : '-' }}
                            </h5>
                        </div>
                        <div class="icon-box" style="background: rgba(255, 171, 0, 0.1); color: #ffab00;"><i
                                class="bx bx-cake"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card h-100 border-0 shadow-soft rounded-4 hover-lift">
                    <div class="card-body d-flex justify-content-between align-items-center p-4">
                        <div class="overflow-hidden">
                            <span class="d-block text-uppercase fw-bold text-muted small mb-1"
                                style="letter-spacing: 0.5px;">Masa Kerja</span>
                            <h5 class="mb-0 fw-bold text-dark">
                                {{ $gtk->tmt_pengangkatan ? \Carbon\Carbon::parse($gtk->tmt_pengangkatan)->diffForHumans(null, true) : '-' }}
                            </h5>
                        </div>
                        <div class="icon-box" style="background: rgba(0, 204, 221, 0.1); color: #00ccdd;"><i
                                class="bx bx-time-five"></i></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- DETAIL CONTENT (KIRI: KONTAK, KANAN: TABS) --}}
        <div class="row animate-fade-up delay-3">

            {{-- INFO KIRI --}}
            <div class="col-xl-4 col-lg-5 col-md-5 mb-4">
                <div class="card h-100 shadow-soft border-0 rounded-4">
                    <div class="card-header pt-4 pb-3 border-bottom">
                        <h6 class="fw-bold mb-0 text-uppercase text-muted" style="letter-spacing: 0.5px;">Informasi Kontak
                        </h6>
                    </div>
                    <div class="card-body pt-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="icon-box icon-primary me-3"><i class='bx bx-envelope'></i></div>
                            <div class="overflow-hidden">
                                <span class="d-block small text-muted">Email Address</span>
                                <span
                                    class="fw-semibold text-dark text-truncate d-block">{{ $gtk->email ?? 'Tidak tersedia' }}</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-4">
                            <div class="icon-box icon-success me-3"><i class='bx bxl-whatsapp'></i></div>
                            <div>
                                <span class="d-block small text-muted">Nomor HP/WA</span>
                                <span
                                    class="fw-semibold text-dark">{{ EncryptionService::decrypt($gtk->no_hp) ?? (EncryptionService::decrypt($gtk->no_telepon_rumah) ?? 'Tidak tersedia') }}</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="icon-box icon-dark me-3"><i class='bx bx-buildings'></i></div>
                            <div class="overflow-hidden">
                                <span class="d-block small text-muted">Nama Sekolah</span>
                                <span class="fw-bold text-dark text-truncate d-block"
                                    title="{{ $gtk->sekolah->nama ?? '' }}">
                                    {{ $gtk->sekolah->nama ?? 'Belum Terhubung' }}
                                </span>
                            </div>
                        </div>

                        <hr class="my-4 text-light">

                        <h6 class="fw-bold mb-3 text-uppercase text-muted" style="letter-spacing: 0.5px;">Alamat Domisili
                        </h6>
                        <div class="bg-lighter rounded-3 p-3">
                            <p class="mb-1 text-dark fw-medium"><i
                                    class='bx bx-map text-primary me-2'></i>{{ $gtk->alamat_jalan ?? 'Alamat belum diisi' }}
                            </p>
                            @if ($gtk->rt || $gtk->rw)
                                <p class="mb-1 ms-4 text-muted small">RT {{ $gtk->rt ?? '-' }} / RW {{ $gtk->rw ?? '-' }}
                                </p>
                            @endif
                            @if ($gtk->desa_kelurahan || $gtk->kecamatan)
                                <p class="mb-1 ms-4 text-muted small">Ds. {{ $gtk->desa_kelurahan ?? '-' }}, Kec.
                                    {{ $gtk->kecamatan ?? '-' }}</p>
                            @endif
                            @if ($gtk->kabupaten_kota)
                                <p class="mb-0 ms-4 fw-semibold text-dark">{{ $gtk->kabupaten_kota ?? '' }} <span
                                        class="text-muted fw-normal">{{ $gtk->kode_pos ? '- ' . $gtk->kode_pos : '' }}</span>
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- INFO KANAN (TABS) --}}
            <div class="col-xl-8 col-lg-7 col-md-7 mb-4">
                <div class="card h-100 shadow-soft border-0 rounded-4">
                    <div class="card-header border-bottom pt-4 pb-3">
                        <ul class="nav nav-pills nav-custom" role="tablist">
                            <li class="nav-item">
                                <button type="button" class="nav-link active shadow-xs" role="tab"
                                    data-bs-toggle="tab" data-bs-target="#navs-pribadi">
                                    <i class="bx bx-user me-1"></i> Data Pribadi
                                </button>
                            </li>
                            <li class="nav-item ms-2">
                                <button type="button" class="nav-link shadow-xs" role="tab" data-bs-toggle="tab"
                                    data-bs-target="#navs-kepegawaian">
                                    <i class="bx bx-briefcase me-1"></i> Kepegawaian
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
                                            <tr>
                                                <td class="label-td">Nama Lengkap</td>
                                                <td class="value-td">{{ $gtk->nama }}</td>
                                            </tr>
                                            <tr>
                                                <td class="label-td">NIK KTP</td>
                                                <td class="value-td font-monospace text-primary">{{ EncryptionService::decrypt($gtk->nik) ?? '-' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="label-td">Tempat Lahir</td>
                                                <td class="value-td">{{ $gtk->tempat_lahir ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="label-td">Tanggal Lahir</td>
                                                <td class="value-td">
                                                    {{ $gtk->tanggal_lahir ? \Carbon\Carbon::parse($gtk->tanggal_lahir)->translatedFormat('d F Y') : '-' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="label-td">Nama Ibu Kandung</td>
                                                <td class="value-td">{{ $gtk->nama_ibu_kandung ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="label-td">Agama</td>
                                                <td class="value-td">{{ $gtk->agama_id_str ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="label-td">Kewarganegaraan</td>
                                                <td class="value-td">{{ $gtk->kewarganegaraan ?? 'Indonesia' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="label-td">Status Perkawinan</td>
                                                <td class="value-td">
                                                    @if ($gtk->status_perkawinan)
                                                        <span
                                                            class="badge bg-label-secondary px-3 py-1">{{ $gtk->status_perkawinan }}</span>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- TAB 2: KEPEGAWAIAN --}}
                            <div class="tab-pane fade" id="navs-kepegawaian" role="tabpanel">
                                <div class="table-responsive text-nowrap">
                                    <table class="table table-borderless table-profile w-100 m-0">
                                        <tbody>
                                            <tr>
                                                <td class="label-td">Jenis PTK</td>
                                                <td class="value-td"><span
                                                        class="badge bg-label-info px-3 py-1">{{ $gtk->jenis_ptk_id_str ?? '-' }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="label-td">Status Induk</td>
                                                <td class="value-td">
                                                    <span
                                                        class="badge bg-label-{{ $gtk->ptk_induk == 1 ? 'success' : 'secondary' }} px-3 py-1">
                                                        {{ $gtk->ptk_induk == 1 ? 'Induk' : 'Bukan Induk' }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="label-td">NIY / NIGK</td>
                                                <td class="value-td font-monospace">{{ $gtk->niy_nigk ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="label-td">SK Pengangkatan</td>
                                                <td class="value-td">{{ $gtk->sk_pengangkatan ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="label-td">TMT Pengangkatan</td>
                                                <td class="value-td">
                                                    {{ $gtk->tmt_pengangkatan ? \Carbon\Carbon::parse($gtk->tmt_pengangkatan)->translatedFormat('d F Y') : '-' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="label-td">Lembaga Pengangkat</td>
                                                <td class="value-td">{{ $gtk->lembaga_pengangkat ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="label-td">SK CPNS / TMT</td>
                                                <td class="value-td">
                                                    {{ $gtk->sk_cpns ?? '-' }}
                                                    @if ($gtk->tmt_cpns)
                                                        <br><small class="text-muted"><i
                                                                class="bx bx-calendar text-primary me-1"></i>{{ \Carbon\Carbon::parse($gtk->tmt_cpns)->translatedFormat('d M Y') }}</small>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="label-td">Sumber Gaji</td>
                                                <td class="value-td"><span
                                                        class="badge bg-label-success px-3 py-1">{{ $gtk->sumber_gaji ?? '-' }}</span>
                                                </td>
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
    </div>
@endsection
