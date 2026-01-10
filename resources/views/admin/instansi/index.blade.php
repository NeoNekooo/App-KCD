@extends('layouts.admin')

@section('content')

{{-- CUSTOM CSS --}}
@push('styles')
<style>
    :root {
        --primary-color: #696cff;
        --primary-light: #e7e7ff;
        --border-color: #eceef1;
    }

    /* Animasi & Card */
    .fade-in-up { animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; transform: translateY(20px); }
    @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }

    .card-modern {
        border: none;
        border-radius: 16px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.03);
        background: #fff;
    }
    
    /* Header Background */
    .profile-header-bg {
        height: 110px;
        background: linear-gradient(135deg, #696cff 0%, #8587ff 100%);
        border-radius: 16px 16px 0 0;
        position: relative;
    }

    /* Avatar Upload */
    .avatar-upload {
        width: 130px; height: 130px;
        margin-top: -65px;
        border: 4px solid #fff;
        border-radius: 50%;
        background: #fff;
        position: relative;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        transition: transform 0.3s;
        z-index: 2;
    }
    .avatar-upload:hover { transform: scale(1.05); }
    .avatar-upload .overlay {
        position: absolute; top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.5); border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        opacity: 0; transition: opacity 0.3s; color: #fff; cursor: pointer;
    }
    .avatar-upload:hover .overlay { opacity: 1; }

    /* Inputs */
    .form-control, .form-select {
        border: 1px solid var(--border-color);
        border-radius: 10px;
        padding: 0.75rem 1rem;
        background-color: #fcfdfd;
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 4px rgba(105, 108, 255, 0.1);
        background-color: #fff;
    }
    
    /* Tabs */
    .nav-tabs-modern { border-bottom: 1px solid #f0f0f0; }
    .nav-tabs-modern .nav-link {
        border: none; color: #8e9bb4; font-weight: 600; padding: 1rem 1.5rem;
        position: relative; transition: color 0.3s;
    }
    .nav-tabs-modern .nav-link.active { color: var(--primary-color); background: transparent; }
    .nav-tabs-modern .nav-link.active::after {
        content: ''; position: absolute; bottom: -1px; left: 0; width: 100%; height: 2px;
        background: var(--primary-color);
    }

    /* Map Preview */
    .sidebar-map-wrapper {
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid var(--border-color);
        height: 200px;
        background: #f8f9fa;
        position: relative;
    }
    .sidebar-map-wrapper iframe { width: 100% !important; height: 100% !important; border: none; }

    /* Sosmed List Item (Tampilan Data) */
    .sosmed-list-item {
        display: flex; align-items: center; justify-content: space-between;
        padding: 12px 16px; margin-bottom: 10px;
        background: #fff; border: 1px solid var(--border-color); border-radius: 12px;
        transition: all 0.2s;
    }
    .sosmed-list-item:hover { transform: translateX(5px); border-color: var(--primary-color); }
    
    /* Icons Dropdown */
    .icon-dropdown-btn {
        width: 100%; height: 45px; border-radius: 10px; border: 1px solid var(--border-color);
        display: flex; align-items: center; justify-content: space-between; padding: 0 15px; background: #fff;
    }
    .icon-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 8px; padding: 8px; }
    .icon-option {
        width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center;
        cursor: pointer; transition: all 0.2s; font-size: 1.4rem; border: 1px solid #eee;
    }
    .icon-option:hover { background: #f0f0f0; transform: scale(1.1); border-color: var(--primary-color); }

    /* Sosmed Buttons (Preview Sidebar) */
    .btn-sosmed {
        width: 38px; height: 38px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
        color: #fff; transition: transform 0.2s; border: none; text-decoration: none;
    }
    .btn-sosmed:hover { transform: translateY(-3px); color: #fff; opacity: 0.9; }
</style>
@endpush

{{-- HEADER --}}
<div class="d-flex justify-content-between align-items-center mb-4 fade-in-up">
    <div>
        <h4 class="fw-bold m-0 text-primary">Profil Instansi</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-style1 m-0">
                <li class="breadcrumb-item"><a href="javascript:void(0);">Pengaturan</a></li>
                <li class="breadcrumb-item active">Identitas & Branding</li>
            </ol>
        </nav>
    </div>
    <button type="submit" form="form-instansi" class="btn btn-primary d-flex align-items-center shadow-sm">
        <i class='bx bx-save me-2'></i> Simpan Perubahan
    </button>
</div>
{{-- 
@if(session('success'))
    <div class="alert alert-primary d-flex align-items-center role="alert" class="fade-in-up" style="animation-delay: 0.1s;">
        <i class='bx bx-check-circle fs-4 me-2'></i>
        <div>{{ session('success') }}</div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif --}}

<form id="form-instansi" action="{{ route('admin.instansi.update') }}" method="POST" enctype="multipart/form-data">
    @csrf @method('PUT')

    {{-- CONTAINER UNTUK INPUT SOSMED HIDDEN (Diisi oleh JS) --}}
    <div id="hidden-sosmed-inputs"></div>

    <div class="row g-4">
        
        {{-- KIRI: PREVIEW KARTU --}}
        <div class="col-12 col-lg-4 fade-in-up" style="animation-delay: 0.1s;">
            <div class="card card-modern h-100 position-sticky" style="top: 20px;">
                <div class="profile-header-bg"></div>
                <div class="card-body text-center pt-0">
                    {{-- Logo --}}
                    <div class="d-flex justify-content-center">
                        <div class="avatar-upload group-hover-effect">
                            <label for="upload" class="w-100 h-100 d-block overflow-hidden rounded-circle m-0 position-relative">
                                @if($instansi->logo)
                                    <img src="{{ Storage::url($instansi->logo) }}" alt="Logo" class="w-100 h-100 object-fit-cover" id="uploadedAvatarPreview">
                                @else
                                    <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-light text-primary fw-bold fs-1" id="uploadedAvatarPreview">
                                        {{ strtoupper(substr($instansi->nama_instansi, 0, 2)) }}
                                    </div>
                                @endif
                                <div class="overlay"><i class='bx bx-camera fs-2'></i></div>
                            </label>
                            <input type="file" id="upload" name="logo" hidden accept="image/png, image/jpeg, image/jpg" onchange="previewImage(this)"/>
                        </div>
                    </div>

                    <h5 class="fw-bold text-dark mt-3 mb-1">{{ $instansi->nama_instansi }}</h5>
                    <span class="badge bg-label-primary rounded-pill px-3 mb-3">{{ $instansi->nama_brand ?? 'BRAND' }}</span>

                    {{-- PREVIEW TOMBOL SOSMED --}}
                    <div id="preview-sosmed-buttons" class="d-flex justify-content-center gap-2 mb-4 flex-wrap">
                        {{-- Diisi oleh JS --}}
                    </div>

                    {{-- Map Preview --}}
                    <div class="text-start border-top pt-3">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-2">Lokasi Kantor</label>
                        <div id="mapPreviewBox" class="sidebar-map-wrapper d-flex align-items-center justify-content-center">
                            <div class="text-center text-muted p-3">
                                <i class='bx bx-map-alt fs-1 mb-2 opacity-50'></i><br>
                                <span class="small" style="font-size: 0.75rem;">Preview peta akan muncul di sini.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- KANAN: FORM EDITOR --}}
        <div class="col-12 col-lg-8 fade-in-up" style="animation-delay: 0.2s;">
            <div class="card card-modern">
                <div class="card-header p-0 mx-4 mt-2">
                    <ul class="nav nav-tabs nav-tabs-modern" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-utama" type="button"><i class='bx bx-info-circle me-1'></i> Utama</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-kontak" type="button"><i class='bx bx-share-alt me-1'></i> Kontak</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-lokasi" type="button"><i class='bx bx-map-pin me-1'></i> Lokasi</button>
                        </li>
                    </ul>
                </div>

                <div class="card-body p-4">
                    <div class="tab-content m-0 p-0 shadow-none bg-transparent">
                        
                        {{-- TAB 1: UTAMA --}}
                        <div class="tab-pane fade show active" id="tab-utama">
                            <div class="row g-4">
                                <div class="col-12">
                                    <label class="form-label fw-bold">Nama Instansi Resmi <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-lg" name="nama_instansi" value="{{ old('nama_instansi', $instansi->nama_instansi) }}" required placeholder="Contoh: Kantor Cabang Dinas Wilayah VI">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Singkatan / Brand</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class='bx bx-purchase-tag'></i></span>
                                        <input type="text" class="form-control" name="nama_brand" value="{{ old('nama_brand', $instansi->nama_brand) }}" placeholder="Contoh: KCD VI">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Website Utama</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class='bx bx-globe'></i></span>
                                        <input type="url" class="form-control" name="website" value="{{ old('website', $instansi->website) }}" placeholder="https://...">
                                    </div>
                                </div>

                                <div class="col-12 mt-4"><div class="small text-uppercase text-muted fw-bold mb-1">Kepala Cabang Dinas</div><hr class="mt-0 mb-3"></div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Nama Lengkap</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class='bx bx-user'></i></span>
                                        <input type="text" class="form-control" name="nama_kepala" value="{{ old('nama_kepala', $instansi->nama_kepala) }}" placeholder="Nama & Gelar">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">NIP Kepala</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class='bx bx-id-card'></i></span>
                                        <input type="text" class="form-control" name="nip_kepala" value="{{ old('nip_kepala', $instansi->nip_kepala) }}" placeholder="1980xxxx...">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- TAB 2: KONTAK & SOSMED --}}
                        <div class="tab-pane fade" id="tab-kontak">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Email Resmi</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class='bx bx-envelope'></i></span>
                                        <input type="email" class="form-control" name="email" value="{{ old('email', $instansi->email) }}" placeholder="email@instansi.go.id">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Telepon / WhatsApp</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class='bx bx-phone'></i></span>
                                        <input type="text" class="form-control" name="telepon" value="{{ old('telepon', $instansi->telepon) }}" placeholder="0812...">
                                    </div>
                                </div>

                                {{-- LIST SOSMED (TAMPILAN DATA) --}}
                                <div class="col-12 mt-4">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div><h6 class="fw-bold mb-0">Social Media</h6><small class="text-muted">Kelola akun sosial media instansi.</small></div>
                                        
                                        {{-- TOMBOL TAMBAH (BUKA MODAL) --}}
                                        <button type="button" class="btn btn-primary btn-sm rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAddSosmed">
                                            <i class='bx bx-plus me-1'></i> Tambah Akun
                                        </button>
                                    </div>
                                    
                                    {{-- CONTAINER LIST --}}
                                    <div id="sosmed-list-container">
                                        {{-- Data List akan di-render di sini oleh JS --}}
                                    </div>
                                    
                                    <div id="empty-sosmed-msg" class="text-center py-4 bg-light rounded-3 border border-dashed d-none">
                                        <div class="text-muted opacity-75 mb-1"><i class='bx bx-share-alt fs-2'></i></div>
                                        <small class="text-muted">Belum ada sosial media.</small>
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
                                        <span class="input-group-text align-items-start pt-2"><i class='bx bx-map'></i></span>
                                        <textarea class="form-control" name="alamat" rows="3" placeholder="Jl. Raya...">{{ old('alamat', $instansi->alamat) }}</textarea>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold mb-1">Kode Embed Google Maps</label>
                                    <textarea class="form-control font-monospace small bg-light mb-1" id="petaInput" name="peta" rows="4" 
                                        placeholder='<iframe src="...">' oninput="updateMapPreview(this.value)">{{ old('peta', $instansi->peta) }}</textarea>
                                    <div class="form-text">Paste kode HTML embed map di sini. Preview akan muncul di sidebar kiri.</div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

{{-- MODAL TAMBAH SOSMED --}}
<div class="modal fade" id="modalAddSosmed" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold"><i class='bx bx-plus-circle me-2 text-primary'></i>Tambah Sosial Media</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-bold">Pilih Platform</label>
                    <div class="dropdown w-100">
                        <button class="icon-dropdown-btn shadow-sm w-100 text-start" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="modal-icon-btn">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bx bxl-instagram fs-4" style="color: #E1306C"></i> <span>Instagram</span>
                            </div>
                            <i class='bx bx-chevron-down text-muted'></i>
                        </button>
                        <ul class="dropdown-menu w-100 icon-dropdown-menu p-2" id="modal-icon-list">
                            {{-- Generated by JS --}}
                        </ul>
                    </div>
                    <input type="hidden" id="modal-input-icon" value="bx bxl-instagram">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Label / Nama Akun</label>
                    <input type="text" id="modal-input-name" class="form-control" placeholder="Contoh: IG Resmi KCD">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Link / URL</label>
                    <input type="text" id="modal-input-url" class="form-control" placeholder="https://instagram.com/...">
                </div>
            </div>
            <div class="modal-footer border-top bg-light">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="saveFromModal()">Simpan ke List</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // 1. DATA MASTER
    const socialIcons = [
        { icon: 'bx bxl-instagram', label: 'Instagram', color: '#E1306C' },
        { icon: 'bx bxl-facebook', label: 'Facebook', color: '#1877F2' },
        { icon: 'bx bxl-twitter', label: 'Twitter/X', color: '#000000' },
        { icon: 'bx bxl-youtube', label: 'Youtube', color: '#FF0000' },
        { icon: 'bx bxl-tiktok', label: 'Tiktok', color: '#000000' },
        { icon: 'bx bxl-whatsapp', label: 'WhatsApp', color: '#25D366' },
        { icon: 'bx bx-globe', label: 'Website', color: '#696cff' },
        { icon: 'bx bx-envelope', label: 'Email', color: '#EA4335' },
    ];

    let sosmedData = []; // Array penampung data

    // 2. INIT FUNCTION
    document.addEventListener('DOMContentLoaded', function() {
        const existingMap = document.getElementById('petaInput').value;
        if(existingMap) updateMapPreview(existingMap);

        // Load Data dari Backend
        const rawData = @json($instansi->social_media ?? []);
        
        // Convert to standard format array
        if (Array.isArray(rawData)) {
            sosmedData = rawData;
        } else if (typeof rawData === 'object' && rawData !== null) {
            Object.entries(rawData).forEach(([key, value]) => {
                if(value) {
                    let matchedIcon = socialIcons.find(i => i.label.toLowerCase().includes(key.toLowerCase()));
                    let iconClass = matchedIcon ? matchedIcon.icon : 'bx bx-link';
                    sosmedData.push({ name: key.charAt(0).toUpperCase() + key.slice(1), url: value, icon: iconClass });
                }
            });
        }
        
        renderSosmedList();
        generateModalIcons();
    });

    // 3. RENDER FUNCTION (Update Tampilan List & Input Hidden)
    function renderSosmedList() {
        const listContainer = document.getElementById('sosmed-list-container');
        const hiddenContainer = document.getElementById('hidden-sosmed-inputs');
        const previewContainer = document.getElementById('preview-sosmed-buttons');
        const emptyMsg = document.getElementById('empty-sosmed-msg');

        listContainer.innerHTML = '';
        hiddenContainer.innerHTML = '';
        previewContainer.innerHTML = '';

        if (sosmedData.length === 0) {
            emptyMsg.classList.remove('d-none');
        } else {
            emptyMsg.classList.add('d-none');
            
            sosmedData.forEach((item, index) => {
                // Find Color
                const foundIcon = socialIcons.find(i => i.icon === item.icon);
                const colorVal = foundIcon ? foundIcon.color : '#ccc';

                // A. Render List Item (Di Form Kanan)
                const itemHtml = `
                <div class="sosmed-list-item fade-in-up" style="border-left: 4px solid ${colorVal}; animation-delay: ${index * 0.05}s;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar avatar-sm flex-shrink-0">
                            <span class="avatar-initial rounded-circle bg-label-secondary" style="color: ${colorVal}"><i class='${item.icon} fs-4'></i></span>
                        </div>
                        <div>
                            <h6 class="mb-0 text-dark">${item.name}</h6>
                            <small class="text-muted text-truncate d-block" style="max-width: 250px;">${item.url}</small>
                        </div>
                    </div>
                    <button type="button" class="btn btn-icon btn-sm btn-outline-danger" onclick="removeSosmed(${index})">
                        <i class='bx bx-trash'></i>
                    </button>
                </div>`;
                listContainer.insertAdjacentHTML('beforeend', itemHtml);

                // B. Render Hidden Inputs (Buat dikirim ke Server)
                const inputHtml = `
                    <input type="hidden" name="social_media[${index}][name]" value="${item.name}">
                    <input type="hidden" name="social_media[${index}][url]" value="${item.url}">
                    <input type="hidden" name="social_media[${index}][icon]" value="${item.icon}">
                `;
                hiddenContainer.insertAdjacentHTML('beforeend', inputHtml);

                // C. Render Preview Buttons (Di Sidebar Kiri)
                const btnHtml = `
                    <a href="${item.url}" target="_blank" class="btn-sosmed shadow-sm" style="background-color: ${colorVal};" title="${item.name}">
                        <i class="${item.icon} fs-5"></i>
                    </a>
                `;
                previewContainer.insertAdjacentHTML('beforeend', btnHtml);
            });
        }
    }

    // 4. MODAL LOGIC
    function generateModalIcons() {
        const list = document.getElementById('modal-icon-list');
        let html = '';
        socialIcons.forEach(item => {
            html += `
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="javascript:void(0)" onclick="selectModalIcon('${item.icon}', '${item.label}', '${item.color}')">
                        <i class="${item.icon} fs-4" style="color: ${item.color}"></i> ${item.label}
                    </a>
                </li>`;
        });
        list.innerHTML = html;
    }

    function selectModalIcon(icon, label, color) {
        document.getElementById('modal-input-icon').value = icon;
        document.getElementById('modal-icon-btn').innerHTML = `
            <div class="d-flex align-items-center gap-2">
                <i class="${icon} fs-4" style="color: ${color}"></i> <span>${label}</span>
            </div>
            <i class='bx bx-chevron-down text-muted'></i>
        `;
    }

    function saveFromModal() {
        const name = document.getElementById('modal-input-name').value;
        const url = document.getElementById('modal-input-url').value;
        const icon = document.getElementById('modal-input-icon').value;

        if(!name || !url) {
            alert('Harap isi Label dan URL!');
            return;
        }

        // Add to Array
        sosmedData.push({ name, url, icon });
        
        // Render Ulang
        renderSosmedList();

        // Reset & Close Modal
        document.getElementById('modal-input-name').value = '';
        document.getElementById('modal-input-url').value = '';
        
        const modalEl = document.getElementById('modalAddSosmed');
        const modal = bootstrap.Modal.getInstance(modalEl);
        modal.hide();
    }

    function removeSosmed(index) {
        if(confirm('Hapus akun ini?')) {
            sosmedData.splice(index, 1);
            renderSosmedList();
        }
    }

    // 5. HELPER
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

    function updateMapPreview(htmlCode) {
        const previewBox = document.getElementById('mapPreviewBox');
        if (htmlCode.trim().startsWith('<iframe') && htmlCode.includes('src="')) {
            previewBox.innerHTML = htmlCode;
        } else {
            previewBox.innerHTML = `<div class="text-center text-muted p-3"><i class='bx bx-map-alt fs-1 mb-2 opacity-50'></i><br><span class="small" style="font-size: 0.75rem;">Preview peta akan muncul di sini.</span></div>`;
        }
    }
</script>
@endpush

@endsection