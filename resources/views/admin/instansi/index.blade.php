@extends('layouts.admin')

@section('content')

{{-- HEADER --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold m-0 text-primary"><i class="bx bxs-business me-2"></i>Profil Instansi</h4>
        <p class="text-muted mb-0">Kelola identitas, branding, dan informasi publik.</p>
    </div>
</div>

{{-- @if(session('success'))
    <div class="alert alert-success alert-dismissible shadow-sm border-0 fade show" role="alert">
        <div class="d-flex align-items-center">
            <div class="avatar bg-success bg-opacity-10 rounded me-3 d-flex align-items-center justify-content-center text-success">
                <i class='bx bx-check fs-3'></i>
            </div>
            <div>
                <h6 class="mb-0 fw-bold">Berhasil Diperbarui!</h6>
                <div class="small text-muted">{{ session('success') }}</div>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif --}}

<form action="{{ route('admin.instansi.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row g-4">
        {{-- ================= KIRI: LOGO & TOMBOL UPDATE (STICKY) ================= --}}
        <div class="col-12 col-lg-4">
            <div class="card shadow border-0 rounded-4 text-center h-100 position-sticky" style="top: 20px; z-index: 10;">
                <div class="card-body p-4 d-flex flex-column align-items-center">
                    
                    {{-- Logo Wrapper --}}
                    <div class="position-relative mb-4 group-hover-effect">
                        <div class="avatar-wrapper rounded-circle overflow-hidden shadow-lg border border-4 border-white position-relative" 
                             style="width: 160px; height: 160px; background: #f8f9fa;">
                            @if($instansi->logo)
                                <img src="{{ Storage::url($instansi->logo) }}" alt="Logo" class="w-100 h-100 object-fit-cover" id="uploadedAvatarPreview">
                            @else
                                <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary fw-bold" style="font-size: 3rem;" id="uploadedAvatarPreview">
                                    {{ strtoupper(substr($instansi->nama_instansi, 0, 2)) }}
                                </div>
                            @endif
                            
                            {{-- Overlay saat hover --}}
                            <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-25 d-flex align-items-center justify-content-center opacity-0 hover-show transition-all">
                                <i class='bx bx-upload text-white fs-2'></i>
                            </div>
                        </div>
                        
                        {{-- Tombol Upload Tersembunyi tapi bisa diklik lewat label --}}
                        <label for="upload" class="stretched-link cursor-pointer" data-bs-toggle="tooltip" title="Klik untuk ganti logo">
                            <input type="file" id="upload" name="logo" hidden accept="image/png, image/jpeg, image/jpg" onchange="previewImage(this)"/>
                        </label>
                    </div>

                    <h5 class="fw-bold text-dark mb-1">{{ $instansi->nama_instansi }}</h5>
                    <span class="badge bg-label-primary px-3 py-2 rounded-pill mb-4">{{ $instansi->nama_brand ?? 'BRAND' }}</span>

                    {{-- TOMBOL UPDATE UTAMA --}}
                    <div class="d-grid w-100 gap-2">
                        <button type="submit" class="btn btn-primary btn-lg shadow-sm fw-bold">
                            <i class='bx bx-save me-2'></i> Simpan Perubahan
                        </button>
                        <button type="reset" class="btn btn-label-secondary">
                            <i class='bx bx-refresh me-2'></i> Reset Form
                        </button>
                    </div>

                    <div class="mt-4 text-start w-100 bg-lighter p-3 rounded-3">
                        <small class="text-muted d-block mb-1 fw-bold text-uppercase" style="font-size: 0.7rem;">Info File</small>
                        <div class="d-flex align-items-center text-muted small mb-1">
                            <i class='bx bx-image me-2'></i> Format: JPG, PNG
                        </div>
                        <div class="d-flex align-items-center text-muted small">
                            <i class='bx bx-ruler me-2'></i> Ukuran: Max 2MB (1:1)
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= KANAN: TAB DETAIL ================= --}}
        <div class="col-12 col-lg-8">
            <div class="card shadow border-0 rounded-4 overflow-hidden">
                
                {{-- TAB NAVIGATION --}}
                <div class="card-header bg-white border-bottom px-0 pt-0 pb-0">
                    <ul class="nav nav-pills nav-fill p-3 bg-lighter" role="tablist">
                        <li class="nav-item px-1">
                            <button class="nav-link active fw-bold py-2 shadow-sm-hover" id="tab-profil-btn" data-bs-toggle="pill" data-bs-target="#tab-profil" type="button">
                                <i class='bx bx-id-card me-1'></i> Profil & Pimpinan
                            </button>
                        </li>
                        <li class="nav-item px-1">
                            <button class="nav-link fw-bold py-2 shadow-sm-hover" id="tab-kontak-btn" data-bs-toggle="pill" data-bs-target="#tab-kontak" type="button">
                                <i class='bx bx-share-alt me-1'></i> Kontak & Sosmed
                            </button>
                        </li>
                        <li class="nav-item px-1">
                            <button class="nav-link fw-bold py-2 shadow-sm-hover" id="tab-lokasi-btn" data-bs-toggle="pill" data-bs-target="#tab-lokasi" type="button">
                                <i class='bx bx-map-pin me-1'></i> Lokasi & Peta
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="card-body p-4 p-lg-5">
                    <div class="tab-content m-0 p-0 shadow-none bg-transparent">
                        
                        {{-- TAB 1: PROFIL --}}
                        <div class="tab-pane fade show active" id="tab-profil">
                            <div class="row g-4">
                                <div class="col-12">
                                    <h6 class="fw-bold text-muted mb-3">Identitas Instansi</h6>
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label">Nama Instansi Lengkap <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class='bx bx-buildings'></i></span>
                                        <input type="text" class="form-control form-control-lg" name="nama_instansi" value="{{ old('nama_instansi', $instansi->nama_instansi) }}" required placeholder="Contoh: Kantor Cabang Dinas Wilayah VI">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Nama Brand (Singkatan)</label>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class='bx bx-purchase-tag'></i></span>
                                        <input type="text" class="form-control form-control-lg" name="nama_brand" value="{{ old('nama_brand', $instansi->nama_brand) }}" placeholder="Contoh: KCD ENAM">
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="divider text-start"><div class="divider-text text-primary fw-bold">Kepala Cabang Dinas</div></div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Nama Kepala</label>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class='bx bx-user-check'></i></span>
                                        <input type="text" class="form-control" name="nama_kepala" value="{{ old('nama_kepala', $instansi->nama_kepala) }}" placeholder="Nama & Gelar Lengkap">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">NIP Kepala</label>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class='bx bx-id-card'></i></span>
                                        <input type="text" class="form-control" name="nip_kepala" value="{{ old('nip_kepala', $instansi->nip_kepala) }}" placeholder="18 Digit Angka">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- TAB 2: KONTAK & SOSMED --}}
                        <div class="tab-pane fade" id="tab-kontak">
                            <div class="row g-4">
                                <div class="col-12">
                                    <h6 class="fw-bold text-muted mb-3">Kontak Resmi</h6>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class='bx bx-envelope'></i></span>
                                        <input type="email" class="form-control" name="email" value="{{ old('email', $instansi->email) }}" placeholder="kcd@jabarprov.go.id">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Telepon</label>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class='bx bx-phone'></i></span>
                                        <input type="text" class="form-control" name="telepon" value="{{ old('telepon', $instansi->telepon) }}" placeholder="(022) xxx-xxx">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Website</label>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class='bx bx-globe'></i></span>
                                        <input type="url" class="form-control" name="website" value="{{ old('website', $instansi->website) }}" placeholder="https://disdik.jabarprov.go.id">
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="divider text-start"><div class="divider-text text-primary fw-bold">Jejaring Sosial</div></div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label text-muted small text-uppercase fw-bold">Instagram</label>
                                    <div class="input-group">
                                        <span class="input-group-text text-white border-0" style="background: linear-gradient(45deg, #f09433 0%,#e6683c 25%,#dc2743 50%,#cc2366 75%,#bc1888 100%);"><i class='bx bxl-instagram'></i></span>
                                        <input type="text" class="form-control" name="social_media[instagram]" value="{{ $instansi->social_media['instagram'] ?? '' }}" placeholder="Username">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small text-uppercase fw-bold">Facebook</label>
                                    <div class="input-group">
                                        <span class="input-group-text text-white border-0" style="background: #1877F2;"><i class='bx bxl-facebook'></i></span>
                                        <input type="text" class="form-control" name="social_media[facebook]" value="{{ $instansi->social_media['facebook'] ?? '' }}" placeholder="Username / Link">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small text-uppercase fw-bold">Tiktok</label>
                                    <div class="input-group">
                                        <span class="input-group-text text-white border-0" style="background: #000000;"><i class='bx bxl-tiktok'></i></span>
                                        <input type="text" class="form-control" name="social_media[tiktok]" value="{{ $instansi->social_media['tiktok'] ?? '' }}" placeholder="@username">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small text-uppercase fw-bold">Youtube</label>
                                    <div class="input-group">
                                        <span class="input-group-text text-white border-0" style="background: #FF0000;"><i class='bx bxl-youtube'></i></span>
                                        <input type="text" class="form-control" name="social_media[youtube]" value="{{ $instansi->social_media['youtube'] ?? '' }}" placeholder="Link Channel">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-muted small text-uppercase fw-bold">Twitter / X</label>
                                    <div class="input-group">
                                        <span class="input-group-text text-white border-0" style="background: #1DA1F2;"><i class='bx bxl-twitter'></i></span>
                                        <input type="text" class="form-control" name="social_media[twitter]" value="{{ $instansi->social_media['twitter'] ?? '' }}" placeholder="Username">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- TAB 3: LOKASI --}}
                        <div class="tab-pane fade" id="tab-lokasi">
                            <div class="row g-4">
                                <div class="col-12">
                                    <h6 class="fw-bold text-muted mb-3">Alamat Kantor</h6>
                                    <div class="input-group">
                                        <span class="input-group-text align-items-start pt-2 bg-light"><i class='bx bx-map text-primary'></i></span>
                                        <textarea class="form-control form-control-lg" name="alamat" rows="3" placeholder="Jl. Raya...">{{ old('alamat', $instansi->alamat) }}</textarea>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="fw-bold text-muted mb-0">Peta Digital</h6>
                                        <a href="https://www.google.com/maps" target="_blank" class="btn btn-xs btn-outline-primary rounded-pill">
                                            <i class='bx bx-link-external me-1'></i> Buka Google Maps
                                        </a>
                                    </div>
                                    
                                    <label class="form-label small">Kode Embed HTML</label>
                                    <textarea class="form-control font-monospace text-muted mb-3" id="petaInput" name="peta" rows="3" 
                                        style="font-size: 0.8rem; background-color: #f8f9fa;"
                                        placeholder='Paste kode iframe di sini: <iframe src="...">'
                                        oninput="updateMapPreview(this.value)">{{ old('peta', $instansi->peta) }}</textarea>
                                    
                                    {{-- PREVIEW BOX --}}
                                    <div class="card bg-white border">
                                        <div class="card-body p-1">
                                            <div id="mapPreviewBox" class="rounded overflow-hidden bg-light d-flex align-items-center justify-content-center" style="height: 350px; width: 100%;">
                                                <div class="text-center text-muted">
                                                    <i class='bx bx-map-alt fs-1 mb-2'></i><br>
                                                    <span class="small">Preview peta akan muncul di sini otomatis.</span>
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
    </div>
</form>

@push('styles')
<style>
    /* Clean Inputs */
    .form-control, .form-select, .input-group-text {
        border-color: #eceef1;
        padding: 0.7rem 1rem;
    }
    .form-control:focus {
        border-color: #696cff;
        box-shadow: 0 0 0 0.2rem rgba(105, 108, 255, 0.1);
    }
    
    /* Nav Pills Style */
    .nav-pills .nav-link {
        color: #566a7f;
        border-radius: 0.375rem;
        transition: all 0.2s;
    }
    .nav-pills .nav-link.active {
        background-color: #696cff;
        color: #fff;
        box-shadow: 0 2px 4px rgba(105, 108, 255, 0.4);
    }
    .nav-pills .nav-link:hover:not(.active) {
        background-color: rgba(105, 108, 255, 0.1);
        color: #696cff;
    }

    /* Avatar Hover */
    .hover-show { transition: opacity 0.3s; }
    .group-hover-effect:hover .hover-show { opacity: 1 !important; cursor: pointer; }
    
    /* Map Iframe Reset */
    #mapPreviewBox iframe { width: 100% !important; height: 100% !important; border: none; }
</style>
@endpush

@push('scripts')
<script>
    // Preview Logo
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                var preview = document.getElementById('uploadedAvatarPreview');
                if (preview.tagName === 'IMG') {
                    preview.src = e.target.result;
                } else {
                    var parent = preview.parentNode;
                    parent.innerHTML = `<img src="${e.target.result}" class="w-100 h-100 object-fit-cover" id="uploadedAvatarPreview">`;
                }
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Auto Map Preview
    function updateMapPreview(htmlCode) {
        const previewBox = document.getElementById('mapPreviewBox');
        if (htmlCode.trim().startsWith('<iframe') && htmlCode.includes('src="')) {
            previewBox.innerHTML = htmlCode;
        } else if (htmlCode.trim() === '') {
            previewBox.innerHTML = `<div class="text-center text-muted"><i class='bx bx-map-alt fs-1 mb-2'></i><br><span class="small">Preview peta akan muncul di sini otomatis.</span></div>`;
        } else {
            previewBox.innerHTML = `<div class="text-center text-danger"><i class='bx bx-error-circle fs-1 mb-2'></i><br><span class="small">Kode Embed tidak valid. Pastikan dimulai dengan &lt;iframe&gt;</span></div>`;
        }
    }

    // Init Script
    document.addEventListener('DOMContentLoaded', function() {
        const existingMap = document.getElementById('petaInput').value;
        if(existingMap) updateMapPreview(existingMap);
    });
</script>
@endpush

@endsection