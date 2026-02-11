@extends('layouts.admin')

@section('content')
    {{-- 1. CUSTOM CSS MODERN (Fixed & Polished) --}}
    @push('styles')
        <style>
            :root {
                --primary: #696cff;
                --primary-soft: rgba(105, 108, 255, 0.1);
                --dark: #233446;
                --border: #eceef1;
            }

            /* --- ANIMATIONS --- */
            @keyframes slideInUp {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .animate-entry {
                animation: slideInUp 0.6s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
                opacity: 0;
            }

            .delay-1 {
                animation-delay: 0.1s;
            }

            .delay-2 {
                animation-delay: 0.2s;
            }

            /* --- CARDS --- */
            .card-premium {
                background: #fff;
                border: none;
                border-radius: 20px;
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.04);
                transition: transform 0.3s ease, box-shadow 0.3s ease;
                overflow: hidden;
            }

            .card-premium:hover {
                transform: translateY(-5px);
                box-shadow: 0 15px 50px rgba(105, 108, 255, 0.1);
            }

            /* --- PROFILE HEADER --- */
            .profile-bg-mesh {
                height: 140px;
                background-color: #696cff;
                background-image:
                    radial-gradient(at 90% 10%, hsla(280, 80%, 70%, 1) 0px, transparent 50%),
                    radial-gradient(at 10% 90%, hsla(240, 80%, 65%, 1) 0px, transparent 50%),
                    radial-gradient(at 50% 50%, hsla(260, 90%, 75%, 1) 0px, transparent 50%);
            }

            /* --- AVATAR --- */
            .avatar-wrapper {
                margin-top: -70px;
                position: relative;
                display: inline-block;
                z-index: 5;
            }

            .avatar-box {
                width: 140px;
                height: 140px;
                border-radius: 50%;
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

            .avatar-box:hover .avatar-overlay {
                opacity: 1;
            }

            /* --- INPUTS & TABS --- */
            .form-control,
            .form-select {
                border: 2px solid #f3f4f6;
                border-radius: 12px;
                padding: 0.8rem 1rem;
                transition: all 0.3s;
            }

            .form-control:focus,
            .form-select:focus {
                border-color: var(--primary);
                box-shadow: 0 0 0 4px var(--primary-soft);
            }

            .nav-pills-custom .nav-link {
                color: #8592a3;
                font-weight: 600;
                padding: 0.8rem 1.5rem;
                border-radius: 12px;
            }

            .nav-pills-custom .nav-link.active {
                background: var(--primary-soft);
                color: var(--primary);
            }

            /* --- FIX TOMBOL CLOSE MENDELEP --- */
            .modal-header-fix {
                padding: 1.5rem;
                background: #fff;
                border-bottom: 1px solid #eceef1;
                display: flex;
                align-items: center;
                justify-content: space-between;
                position: relative;
            }

            .btn-close-fix {
                position: absolute;
                right: 1.5rem;
                top: 50%;
                transform: translateY(-50%);
                margin: 0;
                padding: 0.5rem;
                opacity: 0.5;
                transition: 0.3s;
                background-color: transparent;
                border: none;
            }

            .btn-close-fix:hover {
                opacity: 1;
                transform: translateY(-50%) rotate(90deg);
            }

            /* --- CUSTOM DROPDOWN --- */
            .custom-dropdown-btn {
                width: 100%;
                text-align: left;
                background: #fff;
                border: 2px solid #f3f4f6;
                padding: 0.8rem 1rem;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                color: #566a7f;
            }

            .custom-dropdown-menu {
                width: 100%;
                border: none;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
                border-radius: 12px;
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
            }

            .platform-option:hover {
                background: #f8f9fa;
            }

            .platform-icon-box {
                width: 32px;
                height: 32px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 50%;
                color: #fff;
                font-size: 1.2rem;
            }

            /* --- SIGNATURE & MAP --- */
            .signature-pad {
                background-color: #fff;
                background-image: radial-gradient(#dbe1e8 1px, transparent 1px);
                background-size: 10px 10px;
                border: 2px dashed #d9dee3;
                border-radius: 15px;
                min-height: 120px;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: 0.3s;
            }

            .signature-pad:hover {
                border-color: var(--primary);
                background-color: #fcfcfc;
            }

            .signature-img {
                max-height: 90px;
            }

            /* --- SOSMED ITEMS --- */
            .sosmed-item {
                background: #fff;
                border: 1px solid #f0f0f0;
                border-radius: 12px;
                padding: 1rem;
                margin-bottom: 0.8rem;
                transition: all 0.3s;
            }

            .sosmed-item:hover {
                transform: translateX(5px);
                border-color: var(--primary);
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            }

            .btn-icon-circle {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: 0.3s;
            }
        </style>
    @endpush

    {{-- 2. HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4 animate-entry">
        <div>
            <h4 class="fw-bolder m-0" style="color: #566a7f;">Profil Instansi</h4>
            <span class="text-muted small">Kelola identitas, branding, dan informasi publik.</span>
        </div>
        <button type="submit" form="form-instansi"
            class="btn btn-primary btn-lg rounded-pill px-4 shadow-sm d-flex align-items-center">
            <i class='bx bx-save me-2 fs-5'></i> Simpan Perubahan
        </button>
    </div>

    <form id="form-instansi" action="{{ route('admin.instansi.update') }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div id="hidden-sosmed-inputs"></div>

        <div class="row g-4">

            {{-- 3. LEFT COLUMN: PREVIEW --}}
            <div class="col-12 col-lg-4 animate-entry delay-1">
                <div class="card-premium h-100 position-sticky" style="top: 20px; z-index: 1;">
                    <div class="profile-bg-mesh"></div>

                    <div class="card-body text-center pt-0">
                        {{-- Logo Avatar --}}
                        <div class="avatar-wrapper">
                            <div class="avatar-box">
                                @if ($instansi->logo)
                                    <img src="{{ Storage::url($instansi->logo) }}" id="previewLogo"
                                        class="w-100 h-100 object-fit-cover">
                                @else
                                    <div id="previewLogoContainer"
                                        class="w-100 h-100 d-flex align-items-center justify-content-center bg-light text-primary fw-bold fs-1">
                                        {{ strtoupper(substr($instansi->nama_instansi, 0, 2)) }}
                                    </div>
                                    <img src="" id="previewLogo" class="d-none w-100 h-100 object-fit-cover">
                                @endif
                                <label for="uploadLogo" class="avatar-overlay">
                                    <i class='bx bx-camera fs-2'></i>
                                </label>
                            </div>
                            <input type="file" id="uploadLogo" name="logo" hidden accept="image/*"
                                onchange="previewImage(this, 'previewLogo')" />
                        </div>

                        <div class="mt-3">
                            <h4 class="fw-bold text-dark mb-1">{{ $instansi->nama_instansi }}</h4>
                            <span
                                class="badge bg-label-primary rounded-pill px-3 py-2 text-uppercase fw-bold">{{ $instansi->nama_brand ?? 'BRAND' }}</span>
                        </div>

                        {{-- Icon Preview List --}}
                        <div id="preview-sosmed-buttons" class="d-flex justify-content-center gap-2 mt-4 flex-wrap"></div>

                        {{-- Map Preview --}}
                        <div class="mt-4 pt-4 border-top">
                            <label class="small fw-bold text-muted text-uppercase mb-2 d-block text-start">Lokasi
                                Kantor</label>
                            <div id="mapPreviewBox"
                                class="rounded-3 overflow-hidden border bg-light d-flex align-items-center justify-content-center"
                                style="height: 180px;">
                                <div class="text-center text-muted">
                                    <i class='bx bx-map-pin fs-1 mb-2 opacity-25'></i>
                                    <div class="small">Peta akan tampil di sini</div>
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
                        <ul class="nav nav-pills nav-pills-custom mb-4" role="tablist">
                            <li class="nav-item">
                                <button class="nav-link active d-flex align-items-center gap-2" data-bs-toggle="pill"
                                    data-bs-target="#tab-utama" type="button">
                                    <i class='bx bx-home-smile'></i> Utama
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link d-flex align-items-center gap-2" data-bs-toggle="pill"
                                    data-bs-target="#tab-kontak" type="button">
                                    <i class='bx bx-share-alt'></i> Kontak
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link d-flex align-items-center gap-2" data-bs-toggle="pill"
                                    data-bs-target="#tab-lokasi" type="button">
                                    <i class='bx bx-map-alt'></i> Lokasi
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content shadow-none bg-transparent p-0">

                            {{-- TAB 1: UTAMA --}}
                            <div class="tab-pane fade show active" id="tab-utama">
                                <div class="row g-4">
                                    <div class="col-12">
                                        <label class="form-label fw-bold">Nama Instansi Resmi</label>
                                        <input type="text" class="form-control form-control-lg" name="nama_instansi"
                                            value="{{ old('nama_instansi', $instansi->nama_instansi) }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Brand / Singkatan</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class='bx bx-purchase-tag-alt'></i></span>
                                            <input type="text" class="form-control" name="nama_brand"
                                                value="{{ old('nama_brand', $instansi->nama_brand) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Website</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class='bx bx-globe'></i></span>
                                            <input type="url" class="form-control" name="website"
                                                value="{{ old('website', $instansi->website) }}">
                                        </div>
                                    </div>

                                    {{-- Identitas Kepala & TTD --}}
                                    <div class="col-12 mt-4">
                                        <div class="p-4 rounded-4"
                                            style="background: #fcfcfc; border: 2px dashed #eceef1;">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="btn-icon-circle bg-primary text-white shadow-sm me-3"><i
                                                        class='bx bx-user-pin fs-4'></i></div>
                                                <h6 class="fw-bold m-0 text-dark">Kepala Instansi & Aset Surat</h6>
                                            </div>
                                            <div class="row g-4">
                                                <div class="col-md-7">
                                                    <div class="mb-3">
                                                        <label class="small text-uppercase fw-bold text-muted">Nama &
                                                            Gelar</label>
                                                        <input type="text" class="form-control" name="nama_kepala"
                                                            value="{{ old('nama_kepala', $instansi->nama_kepala) }}"
                                                            placeholder="Contoh: Dr. H. Fulan, M.Pd">
                                                    </div>
                                                    <div class="mb-0">
                                                        <label class="small text-uppercase fw-bold text-muted">NIP</label>
                                                        <input type="text" class="form-control" name="nip_kepala"
                                                            value="{{ old('nip_kepala', $instansi->nip_kepala) }}"
                                                            placeholder="1980xxxx...">
                                                    </div>
                                                </div>
                                                <div class="col-md-5">
                                                    <label class="small text-uppercase fw-bold text-muted mb-2">Upload
                                                        TTD</label>
                                                    <label for="uploadTtd" class="signature-pad w-100">
                                                        @if ($instansi->tanda_tangan)
                                                            <img src="{{ Storage::url($instansi->tanda_tangan) }}"
                                                                id="previewTtd" class="signature-img">
                                                        @else
                                                            <img src="" id="previewTtd"
                                                                class="signature-img d-none">
                                                            <div id="ttdPlaceholder" class="text-center text-muted">
                                                                <i class='bx bx-cloud-upload fs-1 opacity-50 mb-1'></i>
                                                                <div class="small">Klik untuk upload</div>
                                                            </div>
                                                        @endif
                                                    </label>
                                                    {{-- UPDATE: Accept JPG/JPEG --}}
                                                    <input type="file" id="uploadTtd" name="tanda_tangan" hidden
                                                        accept="image/png, image/jpeg, image/jpg"
                                                        onchange="previewSignature(this)" />
                                                    <div class="text-end mt-1">
                                                        <small class="text-muted" style="font-size: 10px;">Format: PNG
                                                            (Transparan) atau JPG</small>
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
                                        <label class="form-label fw-bold">Email</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class='bx bx-envelope'></i></span>
                                            <input type="email" class="form-control" name="email"
                                                value="{{ old('email', $instansi->email) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">WhatsApp / Telp</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class='bx bx-phone-call'></i></span>
                                            <input type="text" class="form-control" name="telepon"
                                                value="{{ old('telepon', $instansi->telepon) }}">
                                        </div>
                                    </div>

                                    <div class="col-12 mt-4">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div>
                                                <h6 class="fw-bold m-0">Sosial Media</h6>
                                                <span class="small text-muted">Hubungkan akun resmi instansi.</span>
                                            </div>
                                            <button type="button" class="btn btn-primary btn-sm rounded-pill shadow-sm"
                                                data-bs-toggle="modal" data-bs-target="#modalAddSosmed">
                                                <i class='bx bx-plus'></i> Tambah Baru
                                            </button>
                                        </div>

                                        <div id="sosmed-list-container"></div>
                                        <div id="empty-sosmed-msg"
                                            class="text-center py-5 bg-light rounded-3 border-0 d-none">
                                            <i class='bx bx-share-alt fs-1 opacity-25 mb-2'></i>
                                            <div class="text-muted">Belum ada akun terhubung.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- TAB 3: LOKASI --}}
                            <div class="tab-pane fade" id="tab-lokasi">
                                <div class="row g-4">
                                    <div class="col-12">
                                        <label class="form-label fw-bold">Alamat Lengkap</label>
                                        <div class="input-group">
                                            <span class="input-group-text align-items-start pt-3"><i
                                                    class='bx bx-map'></i></span>
                                            <textarea class="form-control" name="alamat" rows="3">{{ old('alamat', $instansi->alamat) }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Lintang (Latitude)</label>
                                        <input type="text" class="form-control" name="lintang"
                                            value="{{ old('lintang', $instansi->lintang) }}" placeholder="-6.12345">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Bujur (Longitude)</label>
                                        <input type="text" class="form-control" name="bujur"
                                            value="{{ old('bujur', $instansi->bujur) }}" placeholder="106.12345">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold">Embed Code Google Maps</label>
                                        <textarea class="form-control font-monospace small bg-light" id="petaInput" name="peta" rows="5"
                                            placeholder='<iframe src="..."></iframe>' oninput="updateMapPreview(this.value)">{{ old('peta', $instansi->peta) }}</textarea>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- 5. MODAL TAMBAH SOSMED (FIXED CLOSE BUTTON & WITH ICONS!) --}}
    <div class="modal fade" id="modalAddSosmed" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                {{-- HEADER MODAL YANG SUDAH DIPERBAIKI --}}
                <div class="modal-header-fix">
                    <h5 class="modal-title fw-bold m-0"><i class='bx bx-layer-plus text-primary me-2'></i>Tambah Akun</h5>
                    {{-- Tombol Close Pakai Class Bawaan BS tapi di-override posisinya --}}
                    <button type="button" class="btn-close btn-close-fix" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    {{-- Custom Dropdown for Icons --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Pilih Platform</label>
                        <div class="dropdown">
                            <button class="custom-dropdown-btn" type="button" data-bs-toggle="dropdown"
                                aria-expanded="false" id="selectedPlatformBtn">
                                <span class="d-flex align-items-center gap-2">
                                    <span class="platform-icon-box" style="background: #E1306C"><i
                                            class='bx bxl-instagram'></i></span>
                                    <span id="selectedPlatformText">Instagram</span>
                                </span>
                                <i class='bx bx-chevron-down text-muted'></i>
                            </button>
                            <ul class="dropdown-menu custom-dropdown-menu">
                                <li><a class="dropdown-item platform-option"
                                        onclick="selectPlatform('Instagram', 'bx bxl-instagram', '#E1306C')">
                                        <span class="platform-icon-box" style="background: #E1306C"><i
                                                class='bx bxl-instagram'></i></span> Instagram
                                    </a></li>
                                <li><a class="dropdown-item platform-option"
                                        onclick="selectPlatform('Facebook', 'bx bxl-facebook', '#1877F2')">
                                        <span class="platform-icon-box" style="background: #1877F2"><i
                                                class='bx bxl-facebook'></i></span> Facebook
                                    </a></li>
                                <li><a class="dropdown-item platform-option"
                                        onclick="selectPlatform('Twitter', 'bx bxl-twitter', '#000000')">
                                        <span class="platform-icon-box" style="background: #000000"><i
                                                class='bx bxl-twitter'></i></span> Twitter/X
                                    </a></li>
                                <li><a class="dropdown-item platform-option"
                                        onclick="selectPlatform('Youtube', 'bx bxl-youtube', '#FF0000')">
                                        <span class="platform-icon-box" style="background: #FF0000"><i
                                                class='bx bxl-youtube'></i></span> Youtube
                                    </a></li>
                                <li><a class="dropdown-item platform-option"
                                        onclick="selectPlatform('Tiktok', 'bx bxl-tiktok', '#000000')">
                                        <span class="platform-icon-box" style="background: #000000"><i
                                                class='bx bxl-tiktok'></i></span> Tiktok
                                    </a></li>
                                <li><a class="dropdown-item platform-option"
                                        onclick="selectPlatform('WhatsApp', 'bx bxl-whatsapp', '#25D366')">
                                        <span class="platform-icon-box" style="background: #25D366"><i
                                                class='bx bxl-whatsapp'></i></span> WhatsApp
                                    </a></li>
                                <li><a class="dropdown-item platform-option"
                                        onclick="selectPlatform('Website', 'bx bx-globe', '#696cff')">
                                        <span class="platform-icon-box" style="background: #696cff"><i
                                                class='bx bx-globe'></i></span> Website
                                    </a></li>
                            </ul>
                        </div>
                        {{-- Hidden inputs to store selection --}}
                        <input type="hidden" id="modal-input-icon" value="bx bxl-instagram">
                        <input type="hidden" id="modal-input-color" value="#E1306C">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Akun / Label</label>
                        <input type="text" id="modal-input-name" class="form-control form-control-lg"
                            placeholder="@username">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Link URL</label>
                        <input type="text" id="modal-input-url" class="form-control form-control-lg"
                            placeholder="https://...">
                    </div>
                </div>
                <div class="modal-footer border-top p-3 bg-white">
                    <button type="button" class="btn btn-label-secondary rounded-pill px-4"
                        data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary rounded-pill px-4" onclick="saveFromModal()">Simpan
                        Akun</button>
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
                    'instagram': {
                        icon: 'bx bxl-instagram',
                        color: '#E1306C'
                    },
                    'facebook': {
                        icon: 'bx bxl-facebook',
                        color: '#1877F2'
                    },
                    'twitter': {
                        icon: 'bx bxl-twitter',
                        color: '#000000'
                    },
                    'youtube': {
                        icon: 'bx bxl-youtube',
                        color: '#FF0000'
                    },
                    'tiktok': {
                        icon: 'bx bxl-tiktok',
                        color: '#000000'
                    },
                    'whatsapp': {
                        icon: 'bx bxl-whatsapp',
                        color: '#25D366'
                    },
                    'website': {
                        icon: 'bx bx-globe',
                        color: '#696cff'
                    }
                };
                for (const k in map)
                    if (name.toLowerCase().includes(k)) return map[k];
                return {
                    icon: 'bx bx-link',
                    color: '#8592a3'
                };
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
                        // Gunakan icon dari data atau auto-detect
                        let meta = getMeta(item.name);
                        let iconClass = item.icon || meta.icon;

                        // Override warna jika icon tersimpan manual (opsional, disini kita auto-detect warna biar rapi)
                        // Tapi kita pastikan icon sesuai pilihan user saat add
                        if (item.icon) {
                            // Cek jika icon ada di map kita untuk ambil warnanya
                            const foundKey = Object.keys(getMeta('')).find(k => getMeta(k).icon === item
                                .icon); // logic simple
                            if (!foundKey) meta.color = '#696cff'; // Default color
                        }

                        list.innerHTML += `
                    <div class="sosmed-item d-flex align-items-center justify-content-between animate-entry" style="border-left: 4px solid ${meta.color}; animation-delay: ${idx * 0.1}s">
                        <div class="d-flex align-items-center gap-3">
                            <div class="btn-icon-circle text-white flex-shrink-0" style="background-color: ${meta.color}">
                                <i class="${iconClass} fs-4"></i>
                            </div>
                            <div class="overflow-hidden">
                                <h6 class="mb-0 fw-bold text-truncate text-dark">${item.name}</h6>
                                <small class="text-muted text-truncate d-block" style="max-width: 200px;">${item.url}</small>
                            </div>
                        </div>
                        <button type="button" class="btn btn-icon btn-sm btn-outline-danger border-0" onclick="removeSosmed(${idx})">
                            <i class='bx bx-trash fs-5'></i>
                        </button>
                    </div>`;

                        hidden.innerHTML += `
                    <input type="hidden" name="social_media[${idx}][name]" value="${item.name}">
                    <input type="hidden" name="social_media[${idx}][url]" value="${item.url}">
                    <input type="hidden" name="social_media[${idx}][icon]" value="${iconClass}">`;

                        preview.innerHTML += `
                    <a href="${item.url}" target="_blank" class="btn-icon-circle shadow-sm text-white" style="background: ${meta.color}" title="${item.name}">
                        <i class="${iconClass} fs-5"></i>
                    </a>`;
                    });
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
                document.getElementById('modal-input-name').value = name; // Auto fill label
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
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }

            function previewSignature(input) {
                if (input.files && input.files[0]) {
                    const file = input.files[0];
                    // UPDATE: Bolehkan PNG, JPG, dan JPEG
                    const validTypes = ['image/png', 'image/jpeg', 'image/jpg'];

                    if (!validTypes.includes(file.type)) {
                        alert('Format file harus PNG, JPG, atau JPEG!');
                        input.value = '';
                        return;
                    }

                    // Peringatan jika bukan PNG (Optional, biar user sadar)
                    if (file.type !== 'image/png') {
                        alert('Warning: File JPG tidak transparan. Akan ada latar putih di hasil cetak.');
                    }

                    const reader = new FileReader();
                    reader.onload = (e) => {
                        document.getElementById('previewTtd').src = e.target.result;
                        document.getElementById('previewTtd').classList.remove('d-none');
                        document.getElementById('ttdPlaceholder').classList.add('d-none');
                    }
                    reader.readAsDataURL(file);
                }
            }

            function updateMapPreview(val) {
                const box = document.getElementById('mapPreviewBox');
                if (val.includes('<iframe') && val.includes('src="')) {
                    box.innerHTML = val.replace('width="600"', 'width="100%"').replace('height="450"', 'height="100%"');
                    box.querySelector('iframe').style.width = '100%';
                    box.querySelector('iframe').style.height = '100%';
                    box.querySelector('iframe').style.border = 'none';
                } else {
                    box.innerHTML =
                        `<div class="text-center text-muted"><i class='bx bx-map-pin fs-1 mb-2 opacity-25'></i><div class="small">Peta akan tampil di sini</div></div>`;
                }
            }
        </script>
    @endpush
@endsection
