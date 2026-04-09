@extends('layouts.admin')

@push('styles')
    {{-- 🔥 CSS PREMIUM: ROUNDED, ANIMATED & MODERN 🔥 --}}
    <style>
        :root {
            --primary: #696cff;
            --primary-soft: rgba(105, 108, 255, 0.1);
            --dark: #233446;
            --border: #eceef1;
        }

        .rounded-4 { border-radius: 1rem !important; }
        .rounded-5 { border-radius: 1.25rem !important; }
        .shadow-xs { box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.05) !important; }
        .shadow-soft { box-shadow: 0 8px 25px rgba(105, 108, 255, 0.08) !important; }

        /* --- ANIMATIONS --- */
        @keyframes slideInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-entry {
            animation: slideInUp 0.6s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
            opacity: 0;
        }
        .delay-1 { animation-delay: 0.1s; }
        .delay-2 { animation-delay: 0.2s; }

        /* --- CARDS --- */
        .card-premium {
            background: #fff;
            border: none;
            border-radius: 1rem;
            box-shadow: 0 8px 25px rgba(105, 108, 255, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
        }

        /* --- AVATAR --- */
        .avatar-wrapper {
            margin-top: -85px; /* Sesuaikan karena box makin gede */
            position: relative;
            display: inline-block;
            z-index: 5;
        }
        .avatar-box {
            width: 170px; /* Gedein dari 140px */
            height: 170px; /* Gedein dari 140px */
            border-radius: 1.5rem; /* Sudut lebih manis */
            border: 5px solid #fff;
            background: #fff;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            position: relative;
        }
        .avatar-overlay {
            background: rgba(35, 52, 70, 0.6);
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: 0.3s;
            cursor: pointer;
            color: #fff;
        }
        .avatar-box:hover .avatar-overlay { opacity: 1; }

        /* --- INPUTS & TABS MODERN --- */
        .input-modern {
            background-color: #f8f9fa;
            border: 1px solid transparent;
            border-bottom: 2px solid #d9dee3;
            border-radius: 0.5rem 0.5rem 0 0;
            padding: 0.7rem 1rem;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        .input-modern:focus {
            background-color: #fff;
            border-color: transparent;
            border-bottom: 2px solid #696cff !important;
            box-shadow: 0 4px 10px rgba(105, 108, 255, 0.1) !important;
            outline: none;
        }

        /* Mac/iOS Style Segmented Tabs */
        .nav-pills-custom {
            background: #f1f5f9;
            padding: 0.4rem;
            border-radius: 1rem;
            display: inline-flex;
            gap: 0.2rem;
        }
        .nav-pills-custom .nav-link {
            color: #64748b;
            font-weight: 600;
            padding: 0.6rem 1.5rem;
            border-radius: 0.75rem;
            transition: all 0.3s ease;
        }
        .nav-pills-custom .nav-link.active {
            background: #fff;
            color: #696cff;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        }

        /* --- CUSTOM DROPDOWN --- */
        .custom-dropdown-btn {
            width: 100%;
            text-align: left;
            background: #f8f9fa;
            border: 1px solid transparent;
            border-bottom: 2px solid #d9dee3;
            padding: 0.7rem 1rem;
            border-radius: 0.5rem 0.5rem 0 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: #566a7f;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .custom-dropdown-btn:focus {
            background-color: #fff;
            border-bottom: 2px solid #696cff;
            box-shadow: 0 4px 10px rgba(105, 108, 255, 0.1);
        }
        .custom-dropdown-menu {
            width: 100%;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border-radius: 1rem;
            padding: 0.5rem;
            max-height: 250px;
            overflow-y: auto;
        }
        .platform-option {
            padding: 10px;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: background 0.2s;
            font-weight: 600;
        }
        .platform-option:hover { background: #f8f9fa; color: #696cff; }
        .platform-icon-box {
            width: 32px; height: 32px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 50%; color: #fff; font-size: 1.2rem;
        }

        /* --- SIGNATURE PAD --- */
        .signature-pad {
            background-color: #f8f9fa;
            background-image: radial-gradient(#cbd5e1 1px, transparent 1px);
            background-size: 15px 15px;
            border: 2px dashed #cbd5e1;
            border-radius: 1rem;
            min-height: 140px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: 0.3s;
        }
        .signature-pad:hover {
            border-color: #696cff;
            background-color: #f1f5f9;
        }
        .signature-img { max-height: 100px; }

        /* --- SOSMED ITEMS --- */
        .sosmed-item {
            background: #fff;
            border: 1px solid #f1f5f9;
            border-radius: 1rem;
            padding: 1rem;
            margin-bottom: 0.8rem;
            transition: all 0.3s;
            box-shadow: 0 2px 6px rgba(0,0,0,0.02);
        }
        .sosmed-item:hover {
            transform: translateX(5px);
            border-color: transparent;
            box-shadow: 0 8px 20px rgba(105,108,255,0.1);
        }
        .btn-icon-circle {
            width: 42px; height: 42px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            transition: 0.3s;
        }
        
        .hover-primary:hover { color: #696cff !important; }
    </style>
@endpush

@section('content')
    {{-- 2. HEADER --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 animate-entry gap-3">
        <div>
            <h4 class="fw-bolder m-0 text-dark">Profil Instansi</h4>
            <span class="text-muted small">Kelola identitas, branding, dan informasi publik.</span>
        </div>
        <button type="submit" form="form-instansi" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold d-flex align-items-center">
            <i class='bx bx-save me-2 fs-5'></i> Simpan Perubahan
        </button>
    </div>

    {{-- Alert Messages --}}
    {{-- @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show animate-entry" role="alert">
            <i class='bx bx-check-circle me-1'></i> {!! session('success') !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif --}}

    <form id="form-instansi" action="{{ route('admin.instansi.update') }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div id="hidden-sosmed-inputs"></div>

        <div class="row g-4">

            {{-- 3. LEFT COLUMN: PREVIEW --}}
            <div class="col-12 col-lg-4 animate-entry delay-1">
                <div class="card-premium h-100 position-sticky" style="top: 20px; z-index: 1;">
                    {{-- Banner Background Kekinian --}}
                    <div class="h-px-150 position-relative" style="background: linear-gradient(135deg, #696cff 0%, #4345eb 100%);">
                        <div style="position: absolute; top: -20px; right: -20px; width: 100px; height: 100px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
                        <div style="position: absolute; bottom: -30px; left: 10%; width: 80px; height: 80px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
                    </div>

                    <div class="card-body text-center pt-0 px-4 pb-4">
                        {{-- Logo Avatar --}}
                        <div class="avatar-wrapper">
                            <div class="avatar-box">
                                @if ($instansi->logo)
                                    <img src="{{ Storage::url($instansi->logo) }}" id="previewLogo" class="w-100 h-100 object-fit-cover">
                                @else
                                    <div id="previewLogoContainer" class="w-100 h-100 d-flex align-items-center justify-content-center bg-light text-primary fw-bold" style="font-size: 3rem;">
                                        {{ strtoupper(substr($instansi->nama_instansi, 0, 2)) }}
                                    </div>
                                    <img src="" id="previewLogo" class="d-none w-100 h-100 object-fit-cover">
                                @endif
                                <label for="uploadLogo" class="avatar-overlay">
                                    <i class='bx bx-camera fs-2'></i>
                                </label>
                            </div>
                            <input type="file" id="uploadLogo" name="logo" hidden accept="image/*" onchange="previewImage(this, 'previewLogo')" />
                        </div>

                        <div class="mt-3 mb-4 border-bottom pb-4">
                            <h4 class="fw-bolder text-dark mb-2" style="letter-spacing: -0.5px;">{{ $instansi->nama_instansi }}</h4>
                            <span class="badge bg-label-primary rounded-pill px-3 py-2 text-uppercase fw-bold shadow-xs">{{ $instansi->nama_brand ?? 'BRAND' }}</span>
                        </div>

                        {{-- Icon Preview List --}}
                        <div id="preview-sosmed-buttons" class="d-flex justify-content-center gap-2 flex-wrap mb-4"></div>

                        {{-- Map Preview --}}
                        <div class="text-start">
                            <label class="small fw-bold text-muted text-uppercase mb-2 d-flex align-items-center"><i class="bx bx-map-pin me-1 text-danger"></i> Lokasi Kantor</label>
                            <div id="mapPreviewBox" class="rounded-4 overflow-hidden border-0 shadow-xs bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                <div class="text-center text-muted">
                                    <i class='bx bx-map-alt fs-1 mb-2 opacity-25'></i>
                                    <div class="small fw-medium">Peta akan tampil di sini</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 4. RIGHT COLUMN: TABS FORM --}}
            <div class="col-12 col-lg-8 animate-entry delay-2">
                <div class="card-premium">
                    <div class="card-body p-4 p-md-5">

                        {{-- Navigation Tabs --}}
                        <div class="d-flex justify-content-center justify-content-sm-start mb-4 pb-2 border-bottom">
                            <ul class="nav nav-pills nav-pills-custom" role="tablist">
                                <li class="nav-item">
                                    <button class="nav-link active d-flex align-items-center gap-2" data-bs-toggle="pill" data-bs-target="#tab-utama" type="button">
                                        <i class='bx bx-home-smile fs-5'></i> <span class="d-none d-sm-block">Utama</span>
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link d-flex align-items-center gap-2" data-bs-toggle="pill" data-bs-target="#tab-kontak" type="button">
                                        <i class='bx bx-share-alt fs-5'></i> <span class="d-none d-sm-block">Kontak & Sosmed</span>
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link d-flex align-items-center gap-2" data-bs-toggle="pill" data-bs-target="#tab-lokasi" type="button">
                                        <i class='bx bx-map-alt fs-5'></i> <span class="d-none d-sm-block">Lokasi</span>
                                    </button>
                                </li>
                            </ul>
                        </div>

                        <div class="tab-content shadow-none bg-transparent p-0">

                            {{-- TAB 1: UTAMA --}}
                            <div class="tab-pane fade show active" id="tab-utama">
                                <div class="row g-4">
                                    <div class="col-12">
                                        <label class="form-label small fw-bold text-muted text-uppercase mb-1">Nama Instansi Resmi</label>
                                        <input type="text" class="form-control input-modern fs-5 text-dark" name="nama_instansi" value="{{ old('nama_instansi', $instansi->nama_instansi) }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted text-uppercase mb-1">Brand / Singkatan</label>
                                        <div class="input-group input-group-merge rounded-top-2 border-0 shadow-none">
                                            <span class="input-group-text bg-light border-0 border-bottom border-secondary"><i class='bx bx-purchase-tag-alt text-muted'></i></span>
                                            <input type="text" class="form-control input-modern ps-0" name="nama_brand" value="{{ old('nama_brand', $instansi->nama_brand) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted text-uppercase mb-1">Website Resmi</label>
                                        <div class="input-group input-group-merge rounded-top-2 border-0 shadow-none">
                                            <span class="input-group-text bg-light border-0 border-bottom border-secondary"><i class='bx bx-globe text-muted'></i></span>
                                            <input type="url" class="form-control input-modern ps-0" name="website" value="{{ old('website', $instansi->website) }}" placeholder="https://...">
                                        </div>
                                    </div>

                                    {{-- Identitas Kepala & TTD --}}
                                    <div class="col-12 mt-5">
                                        <div class="p-4 rounded-4" style="background: rgba(105, 108, 255, 0.03); border: 1px solid rgba(105, 108, 255, 0.1);">
                                            <div class="d-flex align-items-center mb-4">
                                                <div class="btn-icon-circle bg-label-primary shadow-xs me-3"><i class='bx bx-user-pin fs-4'></i></div>
                                                <div>
                                                    <h6 class="fw-bold m-0 text-dark">Pimpinan Instansi & Aset Surat</h6>
                                                    <small class="text-muted">Data ini akan digunakan otomatis pada cetak Surat.</small>
                                                </div>
                                            </div>
                                            <div class="row g-4">
                                                <div class="col-md-7">
                                                    @if($kepala)
                                                        <div class="mb-4">
                                                            <label class="small text-uppercase fw-bold text-primary mb-1">Nama & Gelar</label>
                                                            <input type="text" class="form-control input-modern bg-light" name="nama_kepala" value="{{ $kepala->nama }}" readonly data-bs-toggle="tooltip" title="Data diambil otomatis dari Kepegawaian">
                                                        </div>
                                                        <div class="mb-0">
                                                            <label class="small text-uppercase fw-bold text-primary mb-1">NIP Pimpinan</label>
                                                            <input type="text" class="form-control input-modern bg-light font-monospace" name="nip_kepala" value="{{ $kepala->nip }}" readonly data-bs-toggle="tooltip" title="Data diambil otomatis dari Kepegawaian">
                                                        </div>
                                                        <div class="mt-3">
                                                            <a href="{{ route('admin.kepegawaian.index') }}" class="btn btn-sm btn-label-primary rounded-pill">
                                                                <i class='bx bx-edit-alt me-1'></i> Kelola Kepala di Kepegawaian
                                                            </a>
                                                        </div>
                                                    @else
                                                        <div class="alert alert-warning rounded-4 border-0 mb-0 shadow-xs">
                                                            <div class="d-flex align-items-center mb-2">
                                                                <i class='bx bx-error-circle fs-4 me-2'></i>
                                                                <h6 class="fw-bold m-0 text-dark">Kepala Belum Terdaftar</h6>
                                                            </div>
                                                            <p class="small mb-2">Tidak ditemukan pegawai dengan jabatan <b>"Kepala"</b> di data kepegawaian.</p>
                                                            <a href="{{ route('admin.kepegawaian.index') }}" class="btn btn-sm btn-warning rounded-pill fw-bold">
                                                                <i class='bx bx-user-plus me-1'></i> Atur Kepala Sekarang
                                                            </a>
                                                        </div>
                                                        {{-- Hidden inputs to prevent empty update errors if any --}}
                                                        <input type="hidden" name="nama_kepala" value="{{ $instansi->nama_kepala }}">
                                                        <input type="hidden" name="nip_kepala" value="{{ $instansi->nip_kepala }}">
                                                    @endif
                                                </div>
                                                <div class="col-md-5">
                                                    <label class="small text-uppercase fw-bold text-primary mb-1 d-block">Tanda Tangan Digital</label>
                                                    <label for="uploadTtd" class="signature-pad w-100 shadow-xs">
                                                        @if ($instansi->tanda_tangan)
                                                            <img src="{{ Storage::url($instansi->tanda_tangan) }}" id="previewTtd" class="signature-img">
                                                        @else
                                                            <img src="" id="previewTtd" class="signature-img d-none">
                                                            <div id="ttdPlaceholder" class="text-center text-muted">
                                                                <i class='bx bx-cloud-upload fs-1 opacity-50 mb-1 text-primary'></i>
                                                                <div class="small fw-medium">Upload TTD</div>
                                                            </div>
                                                        @endif
                                                    </label>
                                                    <input type="file" id="uploadTtd" name="tanda_tangan" hidden accept="image/png, image/jpeg, image/jpg" onchange="previewSignature(this)" />
                                                    <div class="text-center mt-2">
                                                        <small class="text-muted" style="font-size: 0.7rem;">Wajib format PNG (Transparan)</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- TAB 2: KONTAK & SOSMED --}}
                            <div class="tab-pane fade" id="tab-kontak">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted text-uppercase mb-1">Alamat Email</label>
                                        <div class="input-group input-group-merge rounded-top-2 border-0 shadow-none">
                                            <span class="input-group-text bg-light border-0 border-bottom border-secondary"><i class='bx bx-envelope text-muted'></i></span>
                                            <input type="email" class="form-control input-modern ps-0" name="email" value="{{ old('email', $instansi->email) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted text-uppercase mb-1">WhatsApp / Telepon</label>
                                        <div class="input-group input-group-merge rounded-top-2 border-0 shadow-none">
                                            <span class="input-group-text bg-light border-0 border-bottom border-secondary"><i class='bx bx-phone-call text-muted'></i></span>
                                            <input type="text" class="form-control input-modern ps-0" name="telepon" value="{{ old('telepon', $instansi->telepon) }}">
                                        </div>
                                    </div>

                                    <div class="col-12 mt-5">
                                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4 pb-2 border-bottom">
                                            <div>
                                                <h6 class="fw-bold m-0 text-dark"><i class="bx bx-share-alt text-primary me-2"></i>Media Sosial Instansi</h6>
                                                <span class="small text-muted">Hubungkan akun resmi untuk ditampilkan di portal.</span>
                                            </div>
                                            <button type="button" class="btn btn-primary btn-sm rounded-pill shadow-sm mt-3 mt-sm-0 fw-bold px-3" data-bs-toggle="modal" data-bs-target="#modalAddSosmed">
                                                <i class='bx bx-plus me-1'></i> Tambah Akun
                                            </button>
                                        </div>

                                        <div id="sosmed-list-container"></div>
                                        
                                        <div id="empty-sosmed-msg" class="text-center py-5 bg-light rounded-4 border-0 d-none">
                                            <i class='bx bx-ghost fs-1 text-muted opacity-50 mb-2'></i>
                                            <div class="text-muted fw-medium small">Belum ada akun sosial media yang terhubung.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- TAB 3: LOKASI --}}
                            <div class="tab-pane fade" id="tab-lokasi">
                                <div class="row g-4">
                                    <div class="col-12">
                                        <label class="form-label small fw-bold text-muted text-uppercase mb-1">Alamat Lengkap</label>
                                        <div class="input-group input-group-merge rounded-top-2 border-0 shadow-none">
                                            <span class="input-group-text align-items-start pt-3 bg-light border-0 border-bottom border-secondary"><i class='bx bx-map text-muted'></i></span>
                                            <textarea class="form-control input-modern ps-0" name="alamat" rows="3" placeholder="Ketik alamat lengkap...">{{ old('alamat', $instansi->alamat) }}</textarea>
                                        </div>
                                    </div>
                                    
                                    <div class="col-12 mt-4">
                                        <div class="p-4 rounded-4" style="background: rgba(3, 195, 236, 0.05); border: 1px solid rgba(3, 195, 236, 0.2);">
                                            <h6 class="fw-bold text-dark mb-3"><i class="bx bx-map-alt text-info me-2"></i>Koordinat & Peta</h6>
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold text-info text-uppercase mb-1">Lintang (Latitude)</label>
                                                    <input type="text" class="form-control input-modern bg-white font-monospace" name="lintang" value="{{ old('lintang', $instansi->lintang) }}" placeholder="-6.12345">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold text-info text-uppercase mb-1">Bujur (Longitude)</label>
                                                    <input type="text" class="form-control input-modern bg-white font-monospace" name="bujur" value="{{ old('bujur', $instansi->bujur) }}" placeholder="106.12345">
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label small fw-bold text-info text-uppercase mb-1">Embed Code Google Maps</label>
                                                    <textarea class="form-control input-modern font-monospace small bg-white" id="petaInput" name="peta" rows="3" placeholder='<iframe src="..."></iframe>' oninput="updateMapPreview(this.value)">{{ old('peta', $instansi->peta) }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- 5. MODAL TAMBAH SOSMED --}}
    <div class="modal fade" id="modalAddSosmed" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                
                <div class="modal-header bg-light-subtle border-bottom p-4">
                    <h5 class="modal-title fw-bold text-dark m-0"><i class='bx bx-layer-plus text-primary me-2 fs-4' style="vertical-align: middle;"></i>Tambah Akun Sosial Media</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    {{-- Custom Dropdown for Icons --}}
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-1">Pilih Platform</label>
                        <div class="dropdown">
                            <button class="custom-dropdown-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="selectedPlatformBtn">
                                <span class="d-flex align-items-center gap-3">
                                    <span class="platform-icon-box shadow-xs" style="background: #E1306C"><i class='bx bxl-instagram'></i></span>
                                    <span id="selectedPlatformText" class="fw-bold">Instagram</span>
                                </span>
                                <i class='bx bx-chevron-down fs-4'></i>
                            </button>
                            <ul class="dropdown-menu custom-dropdown-menu">
                                <li><a class="dropdown-item platform-option" onclick="selectPlatform('Instagram', 'bx bxl-instagram', '#E1306C')">
                                        <span class="platform-icon-box" style="background: #E1306C"><i class='bx bxl-instagram'></i></span> Instagram
                                    </a></li>
                                <li><a class="dropdown-item platform-option" onclick="selectPlatform('Facebook', 'bx bxl-facebook', '#1877F2')">
                                        <span class="platform-icon-box" style="background: #1877F2"><i class='bx bxl-facebook'></i></span> Facebook
                                    </a></li>
                                <li><a class="dropdown-item platform-option" onclick="selectPlatform('Twitter / X', 'bx bxl-twitter', '#000000')">
                                        <span class="platform-icon-box" style="background: #000000"><i class='bx bxl-twitter'></i></span> Twitter / X
                                    </a></li>
                                <li><a class="dropdown-item platform-option" onclick="selectPlatform('Youtube', 'bx bxl-youtube', '#FF0000')">
                                        <span class="platform-icon-box" style="background: #FF0000"><i class='bx bxl-youtube'></i></span> Youtube
                                    </a></li>
                                <li><a class="dropdown-item platform-option" onclick="selectPlatform('Tiktok', 'bx bxl-tiktok', '#000000')">
                                        <span class="platform-icon-box" style="background: #000000"><i class='bx bxl-tiktok'></i></span> Tiktok
                                    </a></li>
                                <li><a class="dropdown-item platform-option" onclick="selectPlatform('WhatsApp', 'bx bxl-whatsapp', '#25D366')">
                                        <span class="platform-icon-box" style="background: #25D366"><i class='bx bxl-whatsapp'></i></span> WhatsApp
                                    </a></li>
                                <li><a class="dropdown-item platform-option" onclick="selectPlatform('Website Lain', 'bx bx-globe', '#696cff')">
                                        <span class="platform-icon-box" style="background: #696cff"><i class='bx bx-globe'></i></span> Website Lain
                                    </a></li>
                            </ul>
                        </div>
                        {{-- Hidden inputs to store selection --}}
                        <input type="hidden" id="modal-input-icon" value="bx bxl-instagram">
                        <input type="hidden" id="modal-input-color" value="#E1306C">
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-1">Nama Akun / Label</label>
                        <input type="text" id="modal-input-name" class="form-control input-modern fs-6" placeholder="Contoh: @humas_kcd">
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-1">Link URL</label>
                        <input type="url" id="modal-input-url" class="form-control input-modern font-monospace fs-6" placeholder="https://...">
                    </div>
                </div>
                <div class="modal-footer border-top bg-light-subtle py-3 px-4">
                    <button type="button" class="btn btn-label-secondary rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm" onclick="saveFromModal()">Simpan Akun</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // --- DATA HANDLING ---
            let sosmedData = @json($instansi->social_media ?? []);
            if (!Array.isArray(sosmedData) && typeof sosmedData === 'object') {
                sosmedData = Object.entries(sosmedData).map(([k, v]) => ({
                    name: k,
                    url: v
                }));
            }

            // --- HELPER METADATA ---
            const getMeta = (name) => {
                const map = {
                    'instagram': { icon: 'bx bxl-instagram', color: '#E1306C' },
                    'facebook': { icon: 'bx bxl-facebook', color: '#1877F2' },
                    'twitter': { icon: 'bx bxl-twitter', color: '#000000' },
                    'x': { icon: 'bx bxl-twitter', color: '#000000' },
                    'youtube': { icon: 'bx bxl-youtube', color: '#FF0000' },
                    'tiktok': { icon: 'bx bxl-tiktok', color: '#000000' },
                    'whatsapp': { icon: 'bx bxl-whatsapp', color: '#25D366' },
                    'website': { icon: 'bx bx-globe', color: '#696cff' }
                };
                for (const k in map) {
                    if (name.toLowerCase().includes(k)) return map[k];
                }
                return { icon: 'bx bx-link', color: '#8592a3' };
            }

            // --- INIT ---
            document.addEventListener('DOMContentLoaded', () => {
                renderSosmedList();
                const mapVal = document.getElementById('petaInput').value;
                if (mapVal) updateMapPreview(mapVal);
            });

            // --- RENDER LIST ---
            function renderSosmedList() {
                const list = document.getElementById('sosmed-list-container');
                const hidden = document.getElementById('hidden-sosmed-inputs');
                const preview = document.getElementById('preview-sosmed-buttons');
                const empty = document.getElementById('empty-sosmed-msg');

                list.innerHTML = '';
                hidden.innerHTML = '';
                preview.innerHTML = '';

                if (sosmedData.length === 0) empty.classList.remove('d-none');
                else {
                    empty.classList.add('d-none');
                    sosmedData.forEach((item, idx) => {
                        let meta = getMeta(item.name);
                        let iconClass = item.icon || meta.icon;

                        if (item.icon) {
                            const foundKey = Object.keys(getMeta('')).find(k => getMeta(k).icon === item.icon); 
                            if (!foundKey) meta.color = '#696cff'; 
                        }

                        list.innerHTML += `
                    <div class="sosmed-item d-flex align-items-center justify-content-between animate-entry" style="border-left: 4px solid ${meta.color}; animation-delay: ${idx * 0.1}s">
                        <div class="d-flex align-items-center gap-3">
                            <div class="btn-icon-circle text-white flex-shrink-0 shadow-sm" style="background-color: ${meta.color}">
                                <i class="${iconClass} fs-4"></i>
                            </div>
                            <div class="overflow-hidden">
                                <h6 class="mb-0 fw-bold text-truncate text-dark">${item.name}</h6>
                                <small class="text-muted text-truncate d-block font-monospace" style="max-width: 250px;">${item.url}</small>
                            </div>
                        </div>
                        <button type="button" class="btn btn-icon btn-sm btn-label-danger rounded-circle border-0 shadow-none" onclick="removeSosmed(${idx})">
                            <i class='bx bx-trash fs-5'></i>
                        </button>
                    </div>`;

                        hidden.innerHTML += `
                    <input type="hidden" name="social_media[${idx}][name]" value="${item.name}">
                    <input type="hidden" name="social_media[${idx}][url]" value="${item.url}">
                    <input type="hidden" name="social_media[${idx}][icon]" value="${iconClass}">`;

                        preview.innerHTML += `
                    <a href="${item.url}" target="_blank" class="btn-icon-circle shadow-xs text-white hover-up" style="background: ${meta.color}" data-bs-toggle="tooltip" title="${item.name}">
                        <i class="${iconClass} fs-5"></i>
                    </a>`;
                    });
                    
                    // Re-init tooltips for new dynamic elements
                    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                    tooltipTriggerList.map(function (tooltipTriggerEl) { return new bootstrap.Tooltip(tooltipTriggerEl) });
                }
            }

            // --- MODAL LOGIC (FIXED ICON SELECTION) ---
            function selectPlatform(name, icon, color) {
                // Update Button Display
                document.getElementById('selectedPlatformText').innerText = name;
                const iconBox = document.querySelector('#selectedPlatformBtn .platform-icon-box');
                iconBox.style.background = color;
                iconBox.innerHTML = `<i class='${icon}'></i>`;

                // Update Hidden Value
                document.getElementById('modal-input-name').value = name;
                document.getElementById('modal-input-icon').value = icon;
                document.getElementById('modal-input-color').value = color;
            }

            function saveFromModal() {
                const name = document.getElementById('modal-input-name').value;
                const url = document.getElementById('modal-input-url').value;
                const icon = document.getElementById('modal-input-icon').value;

                if (!name || !url) {
                    alert('Harap lengkapi Nama Akun dan URL!');
                    return;
                }

                sosmedData.push({
                    name: name,
                    url: url,
                    icon: icon
                });
                renderSosmedList();

                // Reset
                document.getElementById('modal-input-name').value = '';
                document.getElementById('modal-input-url').value = '';
                bootstrap.Modal.getInstance(document.getElementById('modalAddSosmed')).hide();
            }

            function removeSosmed(idx) {
                if (confirm('Hapus akun ini?')) {
                    sosmedData.splice(idx, 1);
                    renderSosmedList();
                }
            }

            // --- PREVIEWS ---
            function previewImage(input, imgId) {
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const img = document.getElementById(imgId);
                        const ph = document.getElementById('previewLogoContainer');
                        img.src = e.target.result;
                        img.classList.remove('d-none');
                        if (ph) ph.classList.add('d-none');
                        
                        img.style.transform = 'scale(0.9)';
                        setTimeout(() => { img.style.transform = 'scale(1)'; img.style.transition = 'transform 0.3s ease'; }, 50);
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }

            function previewSignature(input) {
                if (input.files && input.files[0]) {
                    const file = input.files[0];
                    const validTypes = ['image/png', 'image/jpeg', 'image/jpg'];

                    if (!validTypes.includes(file.type)) {
                        alert('Format file harus PNG, JPG, atau JPEG!');
                        input.value = '';
                        return;
                    }

                    if (file.type !== 'image/png') {
                        alert('Catatan: Sangat disarankan menggunakan format PNG berlatar transparan agar tidak ada blok putih pada cetakan SK.');
                    }

                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const img = document.getElementById('previewTtd');
                        img.src = e.target.result;
                        img.classList.remove('d-none');
                        document.getElementById('ttdPlaceholder').classList.add('d-none');
                        
                        img.style.transform = 'scale(0.9)';
                        setTimeout(() => { img.style.transform = 'scale(1)'; img.style.transition = 'transform 0.3s ease'; }, 50);
                    }
                    reader.readAsDataURL(file);
                }
            }

            function updateMapPreview(val) {
                const box = document.getElementById('mapPreviewBox');
                if (val.includes('<iframe') && val.includes('src="')) {
                    box.innerHTML = val.replace('width="600"', 'width="100%"').replace('height="450"', 'height="100%"');
                    const iframe = box.querySelector('iframe');
                    if(iframe) {
                        iframe.style.width = '100%';
                        iframe.style.height = '100%';
                        iframe.style.border = 'none';
                    }
                } else {
                    box.innerHTML = `<div class="text-center text-muted"><i class='bx bx-map-pin fs-1 mb-2 opacity-25'></i><div class="small fw-medium">Peta akan tampil di sini</div></div>`;
                }
            }
        </script>
    @endpush
@endsection