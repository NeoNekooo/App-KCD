@extends('layouts.admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    
    {{-- BREADCRUMB --}}
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Monitoring / <a href="{{ route('admin.sekolah.index') }}">Satuan Pendidikan</a> /</span> Detail
    </h4>

    {{-- HEADER PROFIL --}}
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 border-0 shadow-sm overflow-hidden">
                <div class="h-px-150 bg-primary position-relative" style="background: linear-gradient(45deg, #2563eb, #20c997);">
                    <div style="position: absolute; bottom: -20px; left: 0; width: 100%; height: 60px; background: #fff; clip-path: polygon(0 50%, 100% 0, 100% 100%, 0% 100%);"></div>
                </div>
                <div class="card-body position-relative pt-0">
                    <div class="row">
                        <div class="col-sm-auto text-center text-sm-start mt-n5">
                            <div class="d-flex justify-content-center justify-content-sm-start">
                                <div class="bg-white p-2 rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 130px; height: 130px;">
                                    @if(!empty($sekolah->logo) && Storage::disk('public')->exists($sekolah->logo))
                                        <img src="{{ asset('storage/' . $sekolah->logo) }}" alt="Logo" class="img-fluid rounded-circle p-1" style="object-fit: cover; width: 100%; height: 100%;">
                                    @else
                                        <span class="avatar-initial rounded-circle bg-label-primary fs-1 fw-bold">{{ substr($sekolah->nama, 0, 2) }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col flex-grow-1 mt-3 mt-sm-4 text-center text-sm-start">
                            <h3 class="fw-bold mb-1 text-primary">{{ $sekolah->nama }}</h3>
                            <p class="mb-2 text-muted fw-medium">{{ $sekolah->alamat_jalan ?? 'Alamat belum diisi' }}</p>
                            <div class="d-flex flex-wrap justify-content-center justify-content-sm-start gap-2">
                                <span class="badge bg-label-primary"><i class='bx bx-id-card me-1'></i> NPSN: {{ $sekolah->npsn ?? '-' }}</span>
                                <span class="badge bg-label-{{ str_contains($sekolah->status_sekolah_str, 'Negeri') ? 'success' : 'warning' }}">
                                    <i class='bx bx-building me-1'></i> {{ $sekolah->status_sekolah_str ?? '-' }}
                                </span>
                                <span class="badge bg-label-secondary"><i class='bx bx-book-reader me-1'></i> {{ $sekolah->bentuk_pendidikan_id_str ?? '-' }}</span>
                            </div>
                        </div>
                        <div class="col-12 col-md-auto mt-4 mt-sm-0 text-center text-sm-end d-flex align-items-end flex-column justify-content-center">
                            <a href="{{ route('admin.sekolah.index') }}" class="btn btn-outline-secondary"><i class="bx bx-arrow-back me-1"></i> Kembali</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- STATISTIK CARD --}}
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 border-start border-4 border-primary">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><span class="d-block fw-semibold text-muted mb-1">Total Siswa</span><h3 class="mb-0 fw-bold">{{ number_format($totalSiswa) }}</h3></div>
                    <div class="avatar bg-label-primary rounded p-2"><i class="bx bx-user fs-4"></i></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 border-start border-4 border-success">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><span class="d-block fw-semibold text-muted mb-1">Total Guru</span><h3 class="mb-0 fw-bold">{{ number_format($totalGuru) }}</h3></div>
                    <div class="avatar bg-label-success rounded p-2"><i class="bx bx-chalkboard fs-4"></i></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 border-start border-4 border-warning">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><span class="d-block fw-semibold text-muted mb-1">Total Tendik</span><h3 class="mb-0 fw-bold">{{ number_format($totalTendik) }}</h3></div>
                    <div class="avatar bg-label-warning rounded p-2"><i class="bx bx-briefcase fs-4"></i></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 border-start border-4 border-info">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><span class="d-block fw-semibold text-muted mb-1">Total Rombel</span><h3 class="mb-0 fw-bold">{{ number_format($totalRombel) }}</h3></div>
                    <div class="avatar bg-label-info rounded p-2"><i class="bx bx-grid-alt fs-4"></i></div>
                </div>
            </div>
        </div>
    </div>

    {{-- DETAIL CONTENT --}}
    <div class="row">
        {{-- INFO KIRI --}}
        <div class="col-xl-4 col-lg-5 col-md-5 mb-4">
            <div class="card h-100">
                <div class="card-header border-bottom"><h5 class="card-title mb-0"><i class='bx bx-info-circle me-2 text-primary'></i>Informasi Utama</h5></div>
                <div class="card-body py-4">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between px-0 pb-3 pt-0"><span class="fw-semibold">NSS / NPSN:</span><span>{{ $sekolah->nss ?? '-' }} / {{ $sekolah->npsn }}</span></li>
                        <li class="list-group-item d-flex justify-content-between px-0 py-3"><span class="fw-semibold">Jenjang:</span><span>{{ $sekolah->bentuk_pendidikan_id_str ?? '-' }}</span></li>
                        <li class="list-group-item d-flex justify-content-between px-0 py-3"><span class="fw-semibold">Status:</span><span class="badge bg-label-dark">{{ $sekolah->status_sekolah_str ?? '-' }}</span></li>
                        <li class="list-group-item d-flex justify-content-between px-0 py-3"><span class="fw-semibold">Kode Wilayah:</span><span>{{ $sekolah->kode_wilayah ?? '-' }}</span></li>
                        <li class="list-group-item d-flex justify-content-between px-0 py-3"><span class="fw-semibold">SKS:</span><span>{!! $sekolah->is_sks == '1' ? '<i class="bx bx-check text-success"></i> Ya' : '<i class="bx bx-x text-danger"></i> Tidak' !!}</span></li>
                    </ul>

                    {{-- SOCIAL MEDIA --}}
                    @php $sosmed = is_string($sekolah->social_media) ? json_decode($sekolah->social_media, true) : $sekolah->social_media; @endphp
                    @if(!empty($sosmed))
                    <div class="mt-4">
                        <h6 class="fw-semibold small text-uppercase text-muted mb-3">Media Sosial</h6>
                        <div class="d-flex gap-2 flex-wrap">
                            @foreach($sosmed as $sm)
                                @if(isset($sm['url']) && !empty($sm['url']))
                                    @php
                                        $icon = 'bx-link'; $color = 'secondary'; $platform = strtolower($sm['platform'] ?? '');
                                        if(str_contains($platform, 'facebook')) { $icon = 'bxl-facebook'; $color = 'primary'; }
                                        elseif(str_contains($platform, 'instagram')) { $icon = 'bxl-instagram'; $color = 'danger'; }
                                        elseif(str_contains($platform, 'youtube')) { $icon = 'bxl-youtube'; $color = 'danger'; }
                                        elseif(str_contains($platform, 'tiktok')) { $icon = 'bxl-tiktok'; $color = 'dark'; }
                                    @endphp
                                    <a href="{{ $sm['url'] }}" target="_blank" class="btn btn-icon btn-outline-{{ $color }}"><i class='bx {{ $icon }}'></i></a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- INFO KANAN (LOKASI) --}}
        <div class="col-xl-8 col-lg-7 col-md-7 mb-4">
            <div class="card h-100">
                <div class="card-header border-bottom"><h5 class="card-title mb-0"><i class='bx bx-map-pin me-2 text-danger'></i>Lokasi & Kontak</h5></div>
                <div class="card-body py-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3">Alamat Lengkap</h6>
                            <p class="mb-1 text-muted"><i class='bx bx-map me-2'></i>{{ $sekolah->alamat_jalan }}</p>
                            <p class="mb-1 ms-4 text-muted">RT {{ $sekolah->rt ?? '-' }} / RW {{ $sekolah->rw ?? '-' }}</p>
                            <p class="mb-1 ms-4 text-muted">Dusun {{ $sekolah->dusun ?? '-' }}, Kel. {{ $sekolah->desa_kelurahan ?? '-' }}</p>
                            <p class="mb-0 ms-4 fw-semibold text-dark">{{ $sekolah->kecamatan }}, {{ $sekolah->kabupaten_kota }}</p>
                            <p class="ms-4 text-muted small">{{ $sekolah->provinsi }} - {{ $sekolah->kode_pos }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3">Kontak Resmi</h6>
                            <ul class="list-unstyled mb-0">
                                <li class="mb-3"><small class="d-block text-muted">Telepon/Fax</small><span class="fw-medium">{{ $sekolah->nomor_telepon ?? '-' }} / {{ $sekolah->nomor_fax ?? '-' }}</span></li>
                                <li class="mb-3"><small class="d-block text-muted">Email</small><span class="fw-medium">{{ $sekolah->email ?? '-' }}</span></li>
                                <li><small class="d-block text-muted">Website</small>
                                    @if(!empty($sekolah->website))
                                        <a href="{{ str_starts_with($sekolah->website, 'http') ? $sekolah->website : 'http://'.$sekolah->website }}" target="_blank" class="fw-medium">{{ $sekolah->website }}</a>
                                    @else - @endif
                                </li>
                            </ul>
                        </div>
                    </div>
                    {{-- MAPS --}}
                    <div class="mt-4 pt-3 border-top">
                        <h6 class="fw-bold mb-3">Peta Lokasi</h6>
                        @if(!empty($sekolah->peta))
                            <div class="rounded overflow-hidden shadow-sm" style="height: 350px;">{!! $sekolah->peta !!}</div>
                        @elseif(!empty($sekolah->lintang) && !empty($sekolah->bujur))
                            <div class="rounded overflow-hidden" style="height: 350px; background: #eee;">
                                <iframe width="100%" height="100%" frameborder="0" scrolling="no" src="https://maps.google.com/maps?q={{ $sekolah->lintang }},{{ $sekolah->bujur }}&hl=id&z=15&output=embed"></iframe>
                            </div>
                        @else
                            <div class="alert alert-secondary"><i class='bx bx-map-alt me-2'></i> Data peta belum tersedia.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection