@extends('layouts.admin')

@section('content')

    {{-- ============================================================== --}}
    {{-- HEADER HALAMAN --}}
    {{-- ============================================================== --}}
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Pengaturan /</span> Profil Sekolah
    </h4>

    {{-- Alert Error Global --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm border-0" role="alert">
            <ul class="mb-0 small ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- ============================================================== --}}
    {{-- MAIN WRAPPER --}}
    {{-- ============================================================== --}}
    <div class="card shadow-sm overflow-hidden fade-in-animation">
        <div class="card-body p-0">
            <div class="row g-0">

                {{-- ========================================================== --}}
                {{-- 1. SIDEBAR KIRI (LOGO & IDENTITAS) --}}
                {{-- ========================================================== --}}
                <div class="col-lg-3 border-end d-flex flex-column align-items-center text-center py-4 px-3 bg-light-gray">
                    
                    {{-- Form Upload Logo --}}
                    <form action="{{ route('admin.pengaturan.sekolah.update') }}" method="POST" enctype="multipart/form-data" class="w-100">
                        @csrf
                        @method('PUT')

                        <div class="position-relative d-inline-block mb-2">
                            {{-- Logo Preview --}}
                            <div class="avatar-wrapper rounded p-2 d-flex align-items-center justify-content-center logo-container">
                                @if($sekolah->logo)
                                    <img src="{{ asset('storage/' . $sekolah->logo) }}" class="d-block object-fit-contain w-100 h-100">
                                @else
                                    <i class="bx bxs-school text-muted logo-placeholder"></i>
                                @endif
                            </div>

                            {{-- Tombol Kamera Floating --}}
                            <label for="upload-logo" class="btn-upload-float shadow-sm" title="Ubah Logo">
                                <i class="bx bx-camera small-icon"></i>
                                <input type="file" id="upload-logo" name="logo" class="d-none" accept="image/png,image/jpeg,image/jpg" onchange="this.form.submit()">
                            </label>
                        </div>
                    </form>

                    {{-- Nama Sekolah --}}
                    <h5 class="fw-bold text-dark mb-3 px-2 text-break lh-sm">
                        {{ $sekolah->nama ?? 'Nama Sekolah' }}
                    </h5>

                    {{-- Badges (Bentuk, Status, NPSN) --}}
                    <div class="d-flex flex-column align-items-center gap-2 mb-4 w-100 px-3">
                        <span class="badge bg-label-primary px-3 py-2 rounded-pill shadow-sm ls-wide">
                            {{ strtoupper($sekolah->bentuk_pendidikan_id_str ?? 'SEKOLAH') }}
                        </span>
                        <div class="d-flex gap-2 w-100 justify-content-center">
                            <span class="badge bg-label-success flex-fill py-2 rounded-pill small-text">
                                {{ strtoupper($sekolah->status_sekolah_str ?? '-') }}
                            </span>
                            <span class="badge bg-label-info flex-fill py-2 rounded-pill small-text">
                                NPSN: {{ $sekolah->npsn ?? '-' }}
                            </span>
                        </div>
                    </div>

                    {{-- Area Peta --}}
                    <div class="text-start px-2 w-100 mt-auto">
                        <label class="small text-muted text-uppercase fw-bold mb-1 small-text">Lokasi Sekolah</label>
                        <div class="signature-box border rounded bg-white position-relative overflow-hidden hover-lift map-wrapper">
                            @if($sekolah->peta)
                                <div class="w-100 h-100 map-iframe-box">
                                    {!! $sekolah->peta !!}
                                </div>
                            @else
                                <div class="text-muted small fst-italic text-center">
                                    <i class='bx bx-map-alt fs-1 mb-1'></i><br>Belum ada peta
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- ========================================================== --}}
                {{-- 2. KONTEN KANAN (FORM DETAIL & TABS) --}}
                {{-- ========================================================== --}}
                <div class="col-lg-9 p-4 d-flex flex-column bg-white">

                    {{-- Header Kanan & Tombol Aksi --}}
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <h6 class="mb-0 text-primary fw-bold">
                            <i class="bx bx-list-ul me-2"></i>Informasi Lengkap
                        </h6>

                        <div class="action-button-container">
                            {{-- Tombol Edit --}}
                            <button type="button" class="btn btn-sm btn-label-primary btn-edit-toggle" id="btn-edit-data">
                                <i class="bx bx-edit-alt me-1"></i> Edit Data
                            </button>
                            
                            {{-- Group Tombol Simpan/Batal --}}
                            <div class="d-none align-items-center gap-2" id="action-buttons-group">
                                <button type="button" class="btn btn-sm btn-label-secondary" id="btn-batal-edit">Batal</button>
                                <button type="submit" form="form-data-sekolah" class="btn btn-sm btn-primary shadow-sm">
                                    <i class="bx bx-save me-1"></i> Simpan
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Form Utama --}}
                    <form action="{{ route('admin.pengaturan.sekolah.update') }}" method="POST" id="form-data-sekolah">
                        @csrf
                        @method('PUT')

                        {{-- Navigasi Tab --}}
                        <ul class="nav nav-pills nav-fill mb-3 custom-pills" role="tablist">
                            <li class="nav-item"><button type="button" class="nav-link active btn-sm" data-bs-toggle="tab" data-bs-target="#tab-umum">Informasi Umum</button></li>
                            <li class="nav-item"><button type="button" class="nav-link btn-sm" data-bs-toggle="tab" data-bs-target="#tab-alamat">Alamat</button></li>
                            <li class="nav-item"><button type="button" class="nav-link btn-sm" data-bs-toggle="tab" data-bs-target="#tab-kontak">Kontak & Sosmed</button></li>
                            <li class="nav-item"><button type="button" class="nav-link btn-sm" data-bs-toggle="tab" data-bs-target="#tab-peta-edit">Peta</button></li>
                        </ul>

                        <div class="tab-content p-0 mt-2">
                            
                            {{-- TAB 1: INFORMASI UMUM --}}
                            <div class="tab-pane fade show active" id="tab-umum">
                                
                                <div class="row-clean">
                                    <label>Nama Sekolah <i class="bx bx-lock-alt text-muted small ms-1" title="Data Dapodik"></i></label>
                                    <div class="sep">:</div>
                                    <div class="inp"><input type="text" name="nama" class="clean-input locked-dapodik" value="{{ $sekolah->nama }}" readonly></div>
                                </div>
                                <div class="row-clean">
                                    <label>NPSN <i class="bx bx-lock-alt text-muted small ms-1" title="Data Dapodik"></i></label>
                                    <div class="sep">:</div>
                                    <div class="inp"><input type="text" name="npsn" class="clean-input locked-dapodik" value="{{ $sekolah->npsn }}" readonly></div>
                                </div>
                                <div class="row-clean">
                                    <label>NSS <i class="bx bx-lock-alt text-muted small ms-1" title="Data Dapodik"></i></label>
                                    <div class="sep">:</div>
                                    <div class="inp"><input type="text" name="nss" class="clean-input locked-dapodik" value="{{ $sekolah->nss }}" readonly></div>
                                </div>
                                <div class="row-clean">
                                    <label>Status <i class="bx bx-lock-alt text-muted small ms-1" title="Data Dapodik"></i></label>
                                    <div class="sep">:</div>
                                    <div class="inp"><input type="text" class="clean-input locked-dapodik" value="{{ $sekolah->status_sekolah_str ?? '-' }}" readonly></div>
                                </div>
                                <div class="row-clean">
                                    <label>Bentuk Pendidikan <i class="bx bx-lock-alt text-muted small ms-1" title="Data Dapodik"></i></label>
                                    <div class="sep">:</div>
                                    <div class="inp"><input type="text" class="clean-input locked-dapodik" value="{{ $sekolah->bentuk_pendidikan_id_str }}" readonly></div>
                                </div>

                                {{-- Kode Sekolah (Editable) --}}
                                <div class="row-clean">
                                    <label>Kode Sekolah <i class="bx bx-pencil text-muted small ms-1" title="Dapat diedit"></i></label>
                                    <div class="sep">:</div>
                                    <div class="inp">
                                        <input type="text" name="kode_sekolah" class="clean-input editable-field" 
                                               value="{{ old('kode_sekolah', $sekolah->kode_sekolah) }}" 
                                               readonly placeholder="Klik Edit Data...">
                                    </div>
                                </div>
                            </div>

                            {{-- TAB 2: ALAMAT --}}
                            <div class="tab-pane fade" id="tab-alamat">
                                <div class="row-clean">
                                    <label>Alamat Jalan <i class="bx bx-lock-alt text-muted small ms-1" title="Data Dapodik"></i></label>
                                    <div class="sep">:</div>
                                    <div class="inp"><input type="text" class="clean-input locked-dapodik" value="{{ $sekolah->alamat_jalan }}" readonly></div>
                                </div>
                                <div class="row-clean">
                                    <label>RT / RW <i class="bx bx-lock-alt text-muted small ms-1" title="Data Dapodik"></i></label>
                                    <div class="sep">:</div>
                                    <div class="inp"><input type="text" class="clean-input locked-dapodik" value="{{ $sekolah->rt }} / {{ $sekolah->rw }}" readonly></div>
                                </div>
                                <div class="row-clean">
                                    <label>Dusun <i class="bx bx-lock-alt text-muted small ms-1" title="Data Dapodik"></i></label>
                                    <div class="sep">:</div>
                                    <div class="inp"><input type="text" class="clean-input locked-dapodik" value="{{ $sekolah->dusun }}" readonly></div>
                                </div>
                                <div class="row-clean">
                                    <label>Desa / Kelurahan <i class="bx bx-lock-alt text-muted small ms-1" title="Data Dapodik"></i></label>
                                    <div class="sep">:</div>
                                    <div class="inp"><input type="text" class="clean-input locked-dapodik" value="{{ $sekolah->desa_kelurahan }}" readonly></div>
                                </div>
                                <div class="row-clean">
                                    <label>Kecamatan <i class="bx bx-lock-alt text-muted small ms-1" title="Data Dapodik"></i></label>
                                    <div class="sep">:</div>
                                    <div class="inp"><input type="text" class="clean-input locked-dapodik" value="{{ $sekolah->kecamatan }}" readonly></div>
                                </div>
                                <div class="row-clean">
                                    <label>Kabupaten / Kota <i class="bx bx-lock-alt text-muted small ms-1" title="Data Dapodik"></i></label>
                                    <div class="sep">:</div>
                                    <div class="inp"><input type="text" class="clean-input locked-dapodik" value="{{ $sekolah->kabupaten_kota }}" readonly></div>
                                </div>
                                <div class="row-clean">
                                    <label>Provinsi <i class="bx bx-lock-alt text-muted small ms-1" title="Data Dapodik"></i></label>
                                    <div class="sep">:</div>
                                    <div class="inp"><input type="text" class="clean-input locked-dapodik" value="{{ $sekolah->provinsi }}" readonly></div>
                                </div>
                                <div class="row-clean">
                                    <label>Kode Pos <i class="bx bx-lock-alt text-muted small ms-1" title="Data Dapodik"></i></label>
                                    <div class="sep">:</div>
                                    <div class="inp"><input type="text" class="clean-input locked-dapodik" value="{{ $sekolah->kode_pos }}" readonly></div>
                                </div>
                            </div>

                            {{-- TAB 3: KONTAK & SOSMED (Dynamic) --}}
                            <div class="tab-pane fade" id="tab-kontak">
                                
                                {{-- Kontak Dasar --}}
                                <div class="row-clean">
                                    <label>No. Telepon <i class="bx bx-lock-alt text-muted small ms-1"></i></label>
                                    <div class="sep">:</div>
                                    <div class="inp"><input type="text" class="clean-input locked-dapodik" value="{{ $sekolah->nomor_telepon }}" readonly></div>
                                </div>
                                <div class="row-clean">
                                    <label>Fax <i class="bx bx-lock-alt text-muted small ms-1"></i></label>
                                    <div class="sep">:</div>
                                    <div class="inp"><input type="text" class="clean-input locked-dapodik" value="{{ $sekolah->nomor_fax }}" readonly></div>
                                </div>
                                <div class="row-clean">
                                    <label>Email <i class="bx bx-lock-alt text-muted small ms-1"></i></label>
                                    <div class="sep">:</div>
                                    <div class="inp"><input type="email" class="clean-input locked-dapodik" value="{{ $sekolah->email }}" readonly></div>
                                </div>
                                <div class="row-clean">
                                    <label>Website <i class="bx bx-lock-alt text-muted small ms-1"></i></label>
                                    <div class="sep">:</div>
                                    <div class="inp"><input type="url" class="clean-input locked-dapodik" value="{{ $sekolah->website }}" readonly></div>
                                </div>

                                <hr class="my-3 border-dashed">

                                {{-- Header Sosmed --}}
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="fw-bold text-dark small text-uppercase mb-0">
                                        <i class='bx bx-share-alt me-1'></i> Media Sosial
                                    </h6>
                                    <button type="button" class="btn btn-xs btn-primary rounded-pill d-none" id="btn-add-sosmed">
                                        <i class="bx bx-plus"></i> Tambah
                                    </button>
                                </div>

                                {{-- Container Sosmed --}}
                                <div id="social-media-container">
                                    @php
                                        $sosmeds = $sekolah->social_media ?? [];
                                        
                                        // Definisi Map Icon
                                        $iconMap = [
                                            'facebook'  => ['icon' => 'bxl-facebook-circle', 'color' => 'text-primary'],
                                            'instagram' => ['icon' => 'bxl-instagram-alt',   'color' => 'text-danger'],
                                            'youtube'   => ['icon' => 'bxl-youtube',         'color' => 'text-danger'],
                                            'tiktok'    => ['icon' => 'bxl-tiktok',          'color' => 'text-dark'],
                                            'twitter'   => ['icon' => 'bxl-twitter',         'color' => 'text-info'],
                                            'linkedin'  => ['icon' => 'bxl-linkedin-square', 'color' => 'text-primary'],
                                            'whatsapp'  => ['icon' => 'bxl-whatsapp',        'color' => 'text-success'],
                                        ];
                                        $defaultIcon = ['icon' => 'bx-globe', 'color' => 'text-secondary'];
                                    @endphp

                                    @forelse($sosmeds as $index => $sosmed)
                                        @php
                                            $platform = strtolower($sosmed['platform'] ?? '');
                                            $style = $iconMap[$platform] ?? $defaultIcon;
                                        @endphp

                                        <div class="row-clean social-row align-items-center" id="row-{{ $index }}">
                                            <label class="d-flex align-items-center" style="width: 35%;">
                                                <i class='bx {{ $style['icon'] }} fs-4 me-2 social-icon {{ $style['color'] }}'></i>
                                                <span class="social-label d-none d-sm-block">{{ ucfirst($sosmed['platform'] ?? 'Website') }}</span>
                                            </label>
                                            
                                            <div class="sep">:</div>
                                            
                                            <div class="inp d-flex align-items-center gap-2" style="width: 62%;">
                                                <input type="text" 
                                                       name="social_media[{{ $index }}][url]" 
                                                       class="clean-input editable-field social-url-input" 
                                                       value="{{ $sosmed['url'] ?? '' }}" 
                                                       readonly 
                                                       placeholder="https://..."
                                                       oninput="detectSocialMedia(this)">

                                                <input type="hidden" name="social_media[{{ $index }}][platform]" class="social-platform-input" value="{{ $sosmed['platform'] ?? 'website' }}">
                                                <input type="hidden" name="social_media[{{ $index }}][username]" class="social-username-input" value="{{ $sosmed['username'] ?? '' }}">

                                                <button type="button" class="btn btn-icon btn-xs btn-label-danger btn-remove-sosmed d-none" onclick="removeSocialRow(this)">
                                                    <i class="bx bx-x"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-muted small fst-italic empty-sosmed-msg">Belum ada media sosial.</div>
                                    @endforelse
                                </div>
                            </div>

                            {{-- TAB 4: PETA --}}
                            <div class="tab-pane fade" id="tab-peta-edit">
                                <label class="form-label fw-bold text-dark small text-uppercase mb-2">
                                    <i class='bx bx-code-alt me-1'></i> Embed Google Maps (Editable)
                                </label>
                                <textarea name="peta" 
                                          class="form-control text-muted mb-3 editable-field" 
                                          rows="4" 
                                          placeholder='Paste kode <iframe src="..."></iframe> dari Google Maps di sini...' 
                                          style="font-size: 13px; background-color: #fdfdfd; border-color: #eee;" 
                                          readonly>{{ old('peta', $sekolah->peta) }}</textarea>
                                
                                <div class="alert alert-info py-2 px-3 small">
                                    <i class="bx bx-info-circle me-1"></i> Preview peta akan muncul di sidebar kiri setelah disimpan.
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================== --}}
    {{-- CSS CUSTOMIZATION --}}
    {{-- ============================================================== --}}
    <style>
        /* --- 1. Layout & Typography --- */
        .bg-light-gray { background-color: #f5f5f9; }
        .ls-wide { letter-spacing: 0.5px; }
        .small-text { font-size: 0.75rem; }
        
        /* --- 2. Row Clean System --- */
        .row-clean { display: flex; align-items: flex-start; padding: 4px 0; border-bottom: 1px solid transparent; line-height: 1.5; }
        .row-clean label { width: 35%; font-weight: 600; color: #566a7f; margin: 0; font-size: 0.9375rem; }
        .row-clean .sep { width: 3%; text-align: center; font-weight: 600; color: #566a7f; }
        .row-clean .inp { width: 62%; }

        /* --- 3. Clean Input Styling --- */
        .clean-input { width: 100%; background: transparent !important; border: none !important; padding: 0; margin: 0; outline: none !important; box-shadow: none !important; font-size: 0.9375rem; color: #697a8d; pointer-events: none; font-family: inherit; }
        .clean-input.locked-dapodik { cursor: not-allowed; color: #697a8d; }
        
        /* --- 4. Edit Mode Styling --- */
        .clean-input.editing { pointer-events: auto; color: #333; cursor: text; border-bottom: 1px dashed #d9dee3 !important; }
        .clean-input.editing:focus { border-bottom: 1px solid #696cff !important; }
        .clean-input::placeholder { color: #b4bdc6; font-style: italic; opacity: 0; }
        .clean-input.editing::placeholder { opacity: 1; }

        /* --- 5. Components: Logo, Pills, Map --- */
        .logo-container { width: 120px; height: 120px; }
        .logo-placeholder { font-size: 4rem; opacity: 0.5; }
        .btn-upload-float { position: absolute; bottom: 0; right: 0; width: 28px; height: 28px; background: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #696cff; cursor: pointer; transition: 0.2s; }
        .btn-upload-float:hover { transform: scale(1.1); }
        .small-icon { font-size: 0.9rem; }
        
        .custom-pills .nav-link { border-radius: 50rem; padding: 0.4rem 1rem; color: #697a8d; font-weight: 500; font-size: 0.85rem; border: 1px solid transparent; margin-right: 4px; margin-bottom: 4px; transition: all 0.2s; }
        .custom-pills .nav-link:hover { background-color: rgba(67, 89, 113, 0.05); color: #696cff; }
        .custom-pills .nav-link.active { background-color: #696cff; color: #fff; box-shadow: 0 2px 6px rgba(105, 108, 255, 0.4); }

        .signature-box { background-image: radial-gradient(#e2e2e2 1px, transparent 1px); background-size: 8px 8px; }
        .map-wrapper { height: 130px; display: flex; align-items: center; justify-content: center; }
        .map-iframe-box iframe { width: 100% !important; height: 100% !important; border: none; }
        
        .fade-in-animation { animation: fadeIn 0.5s ease-in-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>

@endsection

@push('scripts')
<script>
    // =========================================================================
    // 1. CONFIGURATION
    // =========================================================================
    const socialPlatforms = [
        { name: 'facebook', regex: /facebook\.com/, icon: 'bxl-facebook-circle', color: 'text-primary' },
        { name: 'instagram', regex: /instagram\.com/, icon: 'bxl-instagram-alt', color: 'text-danger' },
        { name: 'youtube', regex: /youtube\.com|youtu\.be/, icon: 'bxl-youtube', color: 'text-danger' },
        { name: 'tiktok', regex: /tiktok\.com/, icon: 'bxl-tiktok', color: 'text-dark' },
        { name: 'twitter', regex: /twitter\.com|x\.com/, icon: 'bxl-twitter', color: 'text-info' },
        { name: 'linkedin', regex: /linkedin\.com/, icon: 'bxl-linkedin-square', color: 'text-primary' },
        { name: 'whatsapp', regex: /wa\.me|whatsapp\.com/, icon: 'bxl-whatsapp', color: 'text-success' },
    ];
    let rowCount = 100; // Mulai dari 100

    // =========================================================================
    // 2. MAIN LOGIC (DOCUMENT READY)
    // =========================================================================
    document.addEventListener('DOMContentLoaded', function() {
        const btnEdit = document.getElementById('btn-edit-data');
        const btnBatal = document.getElementById('btn-batal-edit');
        const actionGroup = document.getElementById('action-buttons-group');
        const editableFields = document.querySelectorAll('.editable-field');
        const btnAddSosmed = document.getElementById('btn-add-sosmed');

        // A. Toggle Edit Mode
        btnEdit.addEventListener('click', function() {
            btnEdit.classList.add('d-none');
            actionGroup.classList.remove('d-none');
            actionGroup.classList.add('d-flex');

            editableFields.forEach(field => {
                field.removeAttribute('readonly');
                field.classList.add('editing');
            });

            if(btnAddSosmed) btnAddSosmed.classList.remove('d-none');
            document.querySelectorAll('.btn-remove-sosmed').forEach(btn => btn.classList.remove('d-none'));
        });

        // B. Tombol Batal
        btnBatal.addEventListener('click', function() {
            location.reload(); 
        });

        // C. Tombol Tambah Sosmed
        if(btnAddSosmed) {
            btnAddSosmed.addEventListener('click', function() {
                addSocialRow();
            });
        }
    });

    // =========================================================================
    // 3. HELPER FUNCTIONS
    // =========================================================================
    
    // Auto Detect Platform by URL
    window.detectSocialMedia = function(input) {
        const url = input.value.toLowerCase();
        const row = input.closest('.social-row');
        const iconElement = row.querySelector('.social-icon');
        const labelElement = row.querySelector('.social-label');
        const platformInput = row.querySelector('.social-platform-input');

        let matched = false;

        for (const platform of socialPlatforms) {
            if (platform.regex.test(url)) {
                iconElement.className = `bx ${platform.icon} fs-4 me-2 social-icon ${platform.color}`;
                labelElement.textContent = platform.name.charAt(0).toUpperCase() + platform.name.slice(1);
                platformInput.value = platform.name;
                matched = true;
                break;
            }
        }

        if (!matched) {
            iconElement.className = `bx bx-globe fs-4 me-2 social-icon text-secondary`;
            labelElement.textContent = 'Website';
            platformInput.value = 'website';
        }
    };

    // Add New Social Media Row
    function addSocialRow() {
        const container = document.getElementById('social-media-container');
        const emptyMsg = container.querySelector('.empty-sosmed-msg');
        if(emptyMsg) emptyMsg.remove(); 

        rowCount++;
        
        const newRow = `
            <div class="row-clean social-row align-items-center fade-in-animation mb-2">
                <label class="d-flex align-items-center" style="width: 35%;">
                    <i class='bx bx-globe fs-4 me-2 social-icon text-secondary'></i>
                    <span class="social-label d-none d-sm-block">Website</span>
                </label>
                <div class="sep">:</div>
                <div class="inp d-flex align-items-center gap-2" style="width: 62%;">
                    <input type="text" 
                           name="social_media[${rowCount}][url]" 
                           class="clean-input editing social-url-input" 
                           placeholder="Paste link di sini..." 
                           oninput="detectSocialMedia(this)"
                           autofocus>
                    <input type="hidden" name="social_media[${rowCount}][platform]" class="social-platform-input" value="website">
                    <input type="hidden" name="social_media[${rowCount}][username]" class="social-username-input" value="">
                    
                    <button type="button" class="btn btn-icon btn-xs btn-label-danger btn-remove-sosmed" onclick="removeSocialRow(this)">
                        <i class="bx bx-x"></i>
                    </button>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', newRow);
    }

    // Remove Social Row
    window.removeSocialRow = function(btn) {
        btn.closest('.social-row').remove();
    };
</script>
@endpush