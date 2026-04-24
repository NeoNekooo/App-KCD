@extends('layouts.admin')

@section('content')

    {{-- 🔥 CSS PREMIUM: ROUNDED, ANIMATED & MODERN 🔥 --}}
    <style>
        .rounded-4 { border-radius: 1rem !important; }
        .rounded-5 { border-radius: 1.25rem !important; }
        .shadow-xs { box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.05) !important; }
        .shadow-soft { box-shadow: 0 8px 25px rgba(105, 108, 255, 0.08) !important; }
        
        /* Profile Avatar Pop-out */
        .avatar-profile-wrapper {
            border: 5px solid #fff;
            box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.15);
            transition: transform 0.3s ease;
            background: #fff;
            width: 140px; 
            height: 140px;
            z-index: 2;
        }
        .avatar-profile-wrapper:hover {
            transform: scale(1.05);
        }

        /* Card Hover Effect */
        .stat-card { transition: transform 0.3s cubic-bezier(0.25, 0.8, 0.25, 1), box-shadow 0.3s ease; }
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }

        /* Custom List Group */
        .list-group-custom .list-group-item {
            border: none;
            padding: 1rem 0;
            border-bottom: 1px dashed #e4e6e8;
            background: transparent;
        }
        .list-group-custom .list-group-item:last-child { border-bottom: none; }

        /* Animation Keyframes */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up { animation: fadeInUp 0.5s ease-out forwards; }
        
        .bx-spin-hover:hover { animation: spin 2s linear infinite; }
        @keyframes spin { 100% { transform: rotate(360deg); } }
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">
        {{-- BREADCRUMB --}}
        <h4 class="fw-bold py-3 mb-4 animate-fade-in-up">
            <span class="text-muted fw-light">Monitoring / <a href="{{ route('admin.sekolah.index') }}" class="text-muted text-decoration-none hover-primary">Satuan Pendidikan</a> /</span> Detail Profil
        </h4>

        {{-- HEADER PROFIL --}}
        <div class="row mb-4 animate-fade-in-up" style="animation-delay: 0.1s;">
            <div class="col-12">
                <div class="card border-0 shadow-soft overflow-hidden rounded-4">
                    {{-- Banner Background Kekinian --}}
                    <div class="h-px-150 position-relative" style="background: linear-gradient(135deg, #2563eb 0%, #20c997 100%);">
                        <div style="position: absolute; top: -20px; right: -20px; width: 150px; height: 150px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
                        <div style="position: absolute; bottom: -50px; left: 20%; width: 200px; height: 200px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
                        <div style="position: absolute; bottom: -20px; left: 0; width: 100%; height: 60px; background: #fff; clip-path: polygon(0 50%, 100% 0, 100% 100%, 0% 100%);"></div>
                    </div>
                    
                    <div class="card-body position-relative pt-0 pb-4">
                        <div class="row">
                            {{-- Logo Sekolah --}}
                            <div class="col-sm-auto text-center text-sm-start mt-n5">
                                <div class="d-flex justify-content-center justify-content-sm-start flex-column align-items-center">
                                    <div class="avatar-profile-wrapper rounded-circle d-flex align-items-center justify-content-center">
                                        <img src="{{ $sekolah->logo_url }}" alt="Logo" class="img-fluid rounded-circle" style="object-fit: cover; width: 100%; height: 100%;">
                                    </div>
                                </div>
                            </div>

                            {{-- Nama & Alamat Utama --}}
                            <div class="col flex-grow-1 mt-3 mt-sm-4 text-center text-sm-start">
                                <div class="d-flex flex-column flex-md-row align-items-center justify-content-between mb-2 gap-2">
                                    <h3 class="fw-bolder mb-0 text-dark" style="letter-spacing: -0.5px;">{{ $sekolah->nama }}</h3>
                                    
                                    {{-- 🔥 INFO TERAKHIR SINKRON (DI HEADER) 🔥 --}}
                                    @php 
                                        // Pakai update_at sebagai fallback jika terakhir_sinkron null
                                        $lastSync = $sekolah->terakhir_sinkron ?? $sekolah->updated_at; 
                                    @endphp
                                    <div class="badge bg-label-success rounded-pill px-3 py-2 d-flex align-items-center shadow-xs" data-bs-toggle="tooltip" title="{{ $lastSync ? \Carbon\Carbon::parse($lastSync)->format('d F Y, H:i:s') : 'Belum Sinkron' }}">
                                        <i class="bx bx-refresh bx-spin-hover fs-5 me-2"></i>
                                        <div class="d-flex flex-column text-start">
                                            <span class="fw-bold" style="font-size: 0.7rem; line-height: 1;">Terakhir Sinkron</span>
                                            <span style="font-size: 0.8rem;">{{ $lastSync ? \Carbon\Carbon::parse($lastSync)->diffForHumans() : 'Belum Pernah' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <p class="mb-3 text-muted fw-medium"><i class="bx bx-map text-danger me-1"></i> {{ $sekolah->alamat_jalan ?? 'Alamat belum diisi' }}</p>
                                
                                <div class="d-flex flex-wrap justify-content-center justify-content-sm-start gap-2">
                                    <span class="badge bg-label-primary px-3 py-2 rounded-pill"><i class='bx bx-id-card me-1'></i> NPSN: {{ $sekolah->npsn ?? '-' }}</span>
                                    <span class="badge bg-label-{{ str_contains($sekolah->status_sekolah_str, 'Negeri') ? 'success' : 'warning' }} px-3 py-2 rounded-pill">
                                        <i class='bx bx-building-house me-1'></i> {{ $sekolah->status_sekolah_str ?? '-' }}
                                    </span>
                                    <span class="badge bg-label-secondary px-3 py-2 rounded-pill"><i class='bx bx-book-reader me-1'></i> {{ $sekolah->bentuk_pendidikan_id_str ?? '-' }}</span>
                                </div>
                            </div>

                            {{-- Tombol Kembali --}}
                            <div class="col-12 col-md-auto mt-4 mt-sm-0 text-center text-sm-end d-flex align-items-start justify-content-center pt-sm-4">
                                <a href="{{ route('admin.sekolah.index') }}" class="btn btn-outline-secondary rounded-pill fw-bold shadow-xs px-4">
                                    <i class="bx bx-arrow-back me-2"></i> Kembali
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- STATISTIK CARD --}}
        <div class="row g-4 mb-4 animate-fade-in-up" style="animation-delay: 0.2s;">
            <div class="col-sm-6 col-xl-3">
                <div class="card h-100 border-0 stat-card shadow-sm rounded-4">
                    <div class="card-body d-flex justify-content-between align-items-center p-4">
                        <div>
                            <span class="d-block fw-bold text-primary small text-uppercase mb-1" style="letter-spacing: 0.5px;">Total Siswa</span>
                            <h3 class="mb-0 fw-bolder text-dark">{{ number_format($totalSiswa) }}</h3>
                        </div>
                        <div class="avatar avatar-lg bg-label-primary rounded-circle shadow-xs d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="bx bx-user fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card h-100 border-0 stat-card shadow-sm rounded-4">
                    <div class="card-body d-flex justify-content-between align-items-center p-4">
                        <div>
                            <span class="d-block fw-bold text-success small text-uppercase mb-1" style="letter-spacing: 0.5px;">Total Guru</span>
                            <h3 class="mb-0 fw-bolder text-dark">{{ number_format($totalGuru) }}</h3>
                        </div>
                        <div class="avatar avatar-lg bg-label-success rounded-circle shadow-xs d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="bx bx-chalkboard fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card h-100 border-0 stat-card shadow-sm rounded-4">
                    <div class="card-body d-flex justify-content-between align-items-center p-4">
                        <div>
                            <span class="d-block fw-bold text-warning small text-uppercase mb-1" style="letter-spacing: 0.5px;">Total Tendik</span>
                            <h3 class="mb-0 fw-bolder text-dark">{{ number_format($totalTendik) }}</h3>
                        </div>
                        <div class="avatar avatar-lg bg-label-warning rounded-circle shadow-xs d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="bx bx-briefcase fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card h-100 border-0 stat-card shadow-sm rounded-4">
                    <div class="card-body d-flex justify-content-between align-items-center p-4">
                        <div>
                            <span class="d-block fw-bold text-info small text-uppercase mb-1" style="letter-spacing: 0.5px;">Total Rombel</span>
                            <h3 class="mb-0 fw-bolder text-dark">{{ number_format($totalRombel) }}</h3>
                        </div>
                        <div class="avatar avatar-lg bg-label-info rounded-circle shadow-xs d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="bx bx-grid-alt fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- DETAIL CONTENT --}}
        <div class="row animate-fade-in-up" style="animation-delay: 0.3s;">
            {{-- INFO KIRI --}}
            <div class="col-xl-4 col-lg-5 col-md-5 mb-4">
                <div class="card h-100 shadow-soft border-0 rounded-4">
                    <div class="card-header bg-transparent border-bottom py-3">
                        <h5 class="card-title mb-0 fw-bold text-dark"><i class='bx bx-info-circle me-2 text-primary'></i>Informasi Utama</h5>
                    </div>
                    <div class="card-body py-4">
                        <ul class="list-group list-group-flush list-group-custom">
                            
                            {{-- MENAMPILKAN KEPALA SEKOLAH --}}
                            <li class="list-group-item px-0 pb-3 pt-0">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-bold text-muted small text-uppercase">Kepala Sekolah</span>
                                    @if($kepalaSekolah)
                                        <span class="badge bg-label-success rounded-pill px-2" style="font-size: 0.65rem;">Aktif Menjabat</span>
                                    @else
                                        <span class="badge bg-label-secondary rounded-pill px-2" style="font-size: 0.65rem;">Kosong</span>
                                    @endif
                                </div>
                                @if($kepalaSekolah)
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-3">
                                            <span class="avatar-initial rounded-circle bg-label-primary fw-bold">{{ substr($kepalaSekolah->nama, 0, 1) }}</span>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-0">{{ $kepalaSekolah->nama }}</h6>
                                            @if(!empty($kepalaSekolah->nip))
                                                <small class="text-muted font-monospace">NIP: {{ $kepalaSekolah->nip }}</small>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-secondary py-2 mb-0 border-0 d-flex align-items-center">
                                        <i class="bx bx-error-circle me-2"></i><span class="small">Data belum tersedia di sistem.</span>
                                    </div>
                                @endif
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span class="fw-semibold text-muted">NSS / NPSN</span>
                                <span class="fw-bold text-dark">{{ $sekolah->nss ?? '-' }} / {{ $sekolah->npsn }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span class="fw-semibold text-muted">Jenjang</span>
                                <span class="fw-bold text-dark">{{ $sekolah->bentuk_pendidikan_id_str ?? '-' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span class="fw-semibold text-muted">Status</span>
                                <span class="badge bg-label-dark rounded-pill">{{ $sekolah->status_sekolah_str ?? '-' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span class="fw-semibold text-muted">Kode Wilayah</span>
                                <span class="fw-bold text-dark font-monospace">{{ $sekolah->kode_wilayah ?? '-' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span class="fw-semibold text-muted">Penyelenggara SKS</span>
                                <span class="fw-bold">{!! $sekolah->is_sks == '1' ? '<span class="text-success"><i class="bx bx-check-circle fs-5 align-middle me-1"></i> Ya</span>' : '<span class="text-danger"><i class="bx bx-x-circle fs-5 align-middle me-1"></i> Tidak</span>' !!}</span>
                            </li>
                        </ul>

                        {{-- SOCIAL MEDIA --}}
                        @php $sosmed = is_string($sekolah->social_media) ? json_decode($sekolah->social_media, true) : $sekolah->social_media; @endphp
                        @if(!empty($sosmed))
                        <div class="mt-4 pt-3 border-top">
                            <h6 class="fw-bold small text-uppercase text-muted mb-3">Media Sosial Official</h6>
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
                                        <a href="{{ $sm['url'] }}" target="_blank" class="btn btn-icon btn-{{ $color }} rounded-circle shadow-xs" data-bs-toggle="tooltip" title="{{ ucfirst($platform) }}">
                                            <i class='bx {{ $icon }} fs-5'></i>
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- INFO KANAN (LOKASI & KONTAK) --}}
            <div class="col-xl-8 col-lg-7 col-md-7 mb-4">
                <div class="card h-100 shadow-soft border-0 rounded-4">
                    <div class="card-header bg-transparent border-bottom py-3">
                        <h5 class="card-title mb-0 fw-bold text-dark"><i class='bx bx-map-pin me-2 text-danger'></i>Lokasi & Kontak Pendaftaran</h5>
                    </div>
                    <div class="card-body py-4">
                        <div class="row g-4">
                            <div class="col-md-6 border-end border-light">
                                <h6 class="fw-bold text-muted text-uppercase small mb-3">Alamat Lengkap</h6>
                                <div class="d-flex mb-3">
                                    <div class="avatar avatar-sm bg-label-danger rounded flex-shrink-0 me-3 d-flex align-items-center justify-content-center">
                                        <i class="bx bx-map fs-5"></i>
                                    </div>
                                    <div>
                                        <p class="mb-1 fw-bold text-dark">{{ $sekolah->alamat_jalan }}</p>
                                        <p class="mb-1 text-muted small">RT {{ $sekolah->rt ?? '-' }} / RW {{ $sekolah->rw ?? '-' }}, Dusun {{ $sekolah->dusun ?? '-' }}</p>
                                        <p class="mb-1 text-muted small">Kel. {{ $sekolah->desa_kelurahan ?? '-' }}</p>
                                        <p class="mb-1 fw-bold text-primary">{{ $sekolah->kecamatan }}, {{ $sekolah->kabupaten_kota }}</p>
                                        <span class="badge bg-label-secondary mt-1">{{ $sekolah->provinsi }} - {{ $sekolah->kode_pos }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 ps-md-4">
                                <h6 class="fw-bold text-muted text-uppercase small mb-3">Kontak & Website</h6>
                                <ul class="list-unstyled mb-0 d-flex flex-column gap-3">
                                    <li class="d-flex align-items-center">
                                        <div class="avatar avatar-sm bg-label-info rounded flex-shrink-0 me-3 d-flex align-items-center justify-content-center">
                                            <i class="bx bx-phone-call fs-5"></i>
                                        </div>
                                        <div>
                                            <small class="d-block text-muted">Telepon / Fax</small>
                                            <span class="fw-bold text-dark">{{ $sekolah->nomor_telepon ?? '-' }} / {{ $sekolah->nomor_fax ?? '-' }}</span>
                                        </div>
                                    </li>
                                    <li class="d-flex align-items-center">
                                        <div class="avatar avatar-sm bg-label-warning rounded flex-shrink-0 me-3 d-flex align-items-center justify-content-center">
                                            <i class="bx bx-envelope fs-5"></i>
                                        </div>
                                        <div>
                                            <small class="d-block text-muted">Email Sekolah</small>
                                            <span class="fw-bold text-dark">{{ $sekolah->email ?? '-' }}</span>
                                        </div>
                                    </li>
                                    <li class="d-flex align-items-center">
                                        <div class="avatar avatar-sm bg-label-primary rounded flex-shrink-0 me-3 d-flex align-items-center justify-content-center">
                                            <i class="bx bx-globe fs-5"></i>
                                        </div>
                                        <div class="text-truncate">
                                            <small class="d-block text-muted">Website Utama</small>
                                            @if(!empty($sekolah->website))
                                                <a href="{{ str_starts_with($sekolah->website, 'http') ? $sekolah->website : 'http://'.$sekolah->website }}" target="_blank" class="fw-bold text-primary text-decoration-none hover-primary">{{ $sekolah->website }}</a>
                                            @else 
                                                <span class="fw-bold text-dark">-</span> 
                                            @endif
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        {{-- MAPS --}}
                        <div class="mt-4 pt-4 border-top">
                            <h6 class="fw-bold text-muted text-uppercase small mb-3">Peta Lokasi Satuan Pendidikan</h6>
                            @if(!empty($sekolah->peta))
                                <div class="rounded-4 overflow-hidden shadow-sm" style="height: 350px;">{!! $sekolah->peta !!}</div>
                            @elseif(!empty($sekolah->lintang) && !empty($sekolah->bujur))
                                <div class="rounded-4 overflow-hidden shadow-sm" style="height: 350px; background: #eee;">
                                    <iframe width="100%" height="100%" frameborder="0" scrolling="no" src="https://maps.google.com/maps?q={{ $sekolah->lintang }},{{ $sekolah->bujur }}&hl=id&z=15&output=embed"></iframe>
                                </div>
                            @else
                                <div class="alert alert-secondary border-0 bg-light d-flex align-items-center p-4 rounded-4">
                                    <i class='bx bx-map-alt fs-1 text-muted me-3'></i> 
                                    <div>
                                        <h6 class="fw-bold mb-1">Koordinat Belum Tersedia</h6>
                                        <span class="small text-muted">Data Lintang & Bujur (Peta) sekolah ini belum diisi dari sistem Dapodik.</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        // Aktifkan Tooltip bawaan Bootstrap untuk badge terakhir sinkron & medsos
        document.addEventListener("DOMContentLoaded", function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        });
    </script>
@endpush