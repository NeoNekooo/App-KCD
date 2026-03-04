@extends('layouts.admin')

@section('content')

    {{-- 🔥 CSS PREMIUM: ROUNDED, ANIMATED & MODERN 🔥 --}}
    <style>
        .rounded-4 { border-radius: 1rem !important; }
        .rounded-5 { border-radius: 1.25rem !important; }
        .shadow-xs { box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.05) !important; }
        .shadow-soft { box-shadow: 0 8px 25px rgba(105, 108, 255, 0.08) !important; }
        
        /* Nav Tabs Modern */
        .nav-tabs-custom .nav-link { border: none; border-bottom: 3px solid transparent; font-weight: 600; color: #8592a3; padding: 1rem 1.2rem; transition: all 0.3s ease; }
        .nav-tabs-custom .nav-link:hover { color: #696cff; }
        .nav-tabs-custom .nav-link.active { border-bottom-color: #696cff; color: #696cff; background: transparent; }

        /* Card Hover Effect */
        .stat-card {
            transition: transform 0.3s cubic-bezier(0.25, 0.8, 0.25, 1), box-shadow 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
        }

        /* Profile Avatar Styling */
        .avatar-profile-wrapper {
            border: 5px solid #fff;
            box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.15);
            transition: transform 0.3s ease;
            background: #fff;
        }
        .avatar-profile-wrapper:hover {
            transform: scale(1.02);
        }

        /* Animation Keyframes */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-fade-in-up {
            animation: fadeInUp 0.4s ease-out forwards;
        }

        /* Input Style for Edit Mode */
        .edit-element {
            animation: fadeInUp 0.3s ease-out forwards;
        }

        .input-modern {
            background-color: #f8f9fa;
            border: 1px solid transparent;
            border-bottom: 2px solid #d9dee3;
            border-radius: 0.5rem 0.5rem 0 0;
            padding: 0.5rem 1rem;
            transition: all 0.3s ease;
        }
        
        .input-modern:focus {
            background-color: #fff;
            border-color: transparent;
            border-bottom: 2px solid #696cff !important;
            box-shadow: 0 4px 10px rgba(105, 108, 255, 0.1) !important;
            outline: none;
        }

        .input-modern[readonly] {
            background-color: transparent;
            border-bottom: 2px dashed #d9dee3;
            pointer-events: none;
        }
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">
        {{-- BREADCRUMB --}}
        <h4 class="fw-bold py-3 mb-4 animate-fade-in-up">
            <span class="text-muted fw-light">Kepegawaian / <a href="{{ route('admin.kepegawaian.index') }}" class="text-muted text-decoration-none hover-primary">Data Pegawai</a> /</span> Detail Profil
        </h4>

        {{-- Display Validation Errors --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show animate-fade-in-up" role="alert">
                <h5 class="alert-heading fw-bold mb-2"><i class="bx bx-error-alt me-2"></i> Gagal Menyimpan Data!</h5>
                <p class="mb-2">Terdapat beberapa kesalahan pada input yang Anda masukkan. Silakan periksa kembali:</p>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- 🔥 FIX BUG DI SINI: Action Route dibikin dinamis menyesuaikan halaman 🔥 --}}
        <form action="{{ Request::routeIs('admin.profil-saya.*') ? route('admin.profil-saya.update') : route('admin.kepegawaian.update', $pegawai->id) }}" method="POST" enctype="multipart/form-data" id="formProfil">
            @csrf @method('PUT')

            {{-- 1. HEADER PROFIL --}}
            <div class="row mb-4 animate-fade-in-up" style="animation-delay: 0.1s;">
                <div class="col-12">
                    <div class="card border-0 shadow-soft overflow-hidden rounded-4">
                        {{-- Background Gradient Kekinian --}}
                        <div class="h-px-150 position-relative" style="background: linear-gradient(135deg, #696cff 0%, #4345eb 100%); overflow: hidden;">
                            {{-- Ornamen dekorasi background --}}
                            <div style="position: absolute; top: -20px; right: -20px; width: 150px; height: 150px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
                            <div style="position: absolute; bottom: -50px; left: 20%; width: 200px; height: 200px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
                            {{-- Potongan putih di bawah --}}
                            <div style="position: absolute; bottom: -20px; left: 0; width: 100%; height: 60px; background: #fff; clip-path: polygon(0 50%, 100% 0, 100% 100%, 0% 100%);"></div>
                        </div>

                        <div class="card-body position-relative pt-0">
                            <div class="row">
                                {{-- FOTO PROFIL --}}
                                <div class="col-sm-auto text-center text-sm-start mt-n5">
                                    <div class="d-flex justify-content-center justify-content-sm-start flex-column align-items-center">
                                        <div class="avatar-profile-wrapper rounded-4 d-flex align-items-center justify-content-center position-relative" style="width: 150px; height: 150px; z-index: 2;">
                                            {{-- Preview Foto --}}
                                            @if ($pegawai->foto && Storage::disk('public')->exists($pegawai->foto))
                                                <img src="{{ Storage::url($pegawai->foto) }}" id="previewFoto" class="rounded-4" style="width: 100%; height: 100%; object-fit: cover;">
                                            @else
                                                <div id="defaultAvatar" class="avatar-initial rounded-4 bg-label-primary fs-1 fw-bold text-uppercase w-100 h-100 d-flex align-items-center justify-content-center" style="font-size: 3rem;">
                                                    {{ substr($pegawai->nama, 0, 2) }}
                                                </div>
                                                <img src="" id="previewFoto" class="rounded-4 d-none" style="width: 100%; height: 100%; object-fit: cover;">
                                            @endif

                                            {{-- Tombol Upload Animasi (Muncul saat Edit Mode) --}}
                                            <label for="uploadFoto" class="btn btn-icon btn-primary rounded-circle shadow-lg position-absolute bottom-0 end-0 edit-element d-none" style="transform: translate(25%, 25%); transition: all 0.2s;" title="Ganti Foto">
                                                <i class="bx bx-camera fs-5"></i>
                                            </label>
                                            <input type="file" name="foto" id="uploadFoto" hidden accept="image/*" onchange="previewImage(this)">
                                        </div>
                                    </div>
                                </div>

                                {{-- IDENTITAS UTAMA --}}
                                <div class="col flex-grow-1 mt-3 mt-sm-4 text-center text-sm-start">
                                    {{-- VIEW MODE: NAMA --}}
                                    <h3 class="fw-bolder mb-1 text-dark view-element" style="letter-spacing: -0.5px;">{{ $pegawai->nama }}</h3>

                                    {{-- EDIT MODE: NAMA --}}
                                    <div class="edit-element d-none mb-3" style="max-width: 400px;">
                                        <label class="form-label small fw-bold text-primary mb-1">Nama Lengkap <span class="text-danger">*</span></label>
                                        <input type="text" name="nama" class="form-control form-control-lg fw-bold input-modern" value="{{ $pegawai->nama }}" required>
                                    </div>

                                    <div class="view-element mt-2 d-flex flex-wrap justify-content-center justify-content-sm-start gap-2">
                                        <span class="badge bg-label-primary px-3 py-2 rounded-pill"><i class='bx bx-briefcase-alt-2 me-1'></i> {{ $pegawai->jabatan }}</span>
                                        <span class="badge bg-label-info px-3 py-2 rounded-pill"><i class='bx bx-id-card me-1'></i> NIP: {{ $pegawai->nip ?? '-' }}</span>
                                        
                                        @if ($pegawai->user)
                                            <span class="badge bg-label-success px-3 py-2 rounded-pill"><i class='bx bx-check-shield me-1'></i> Aktif</span>
                                        @else
                                            <span class="badge bg-label-warning px-3 py-2 rounded-pill"><i class='bx bx-x-circle me-1'></i> Belum Ada Akun</span>
                                        @endif
                                    </div>
                                </div>

                                {{-- TOMBOL AKSI (TOGGLE) --}}
                                <div class="col-12 col-md-auto mt-4 mt-sm-0 text-center text-sm-end d-flex align-items-end flex-column justify-content-center gap-2">
                                    {{-- State 1: View Buttons --}}
                                    <div id="viewButtons" class="d-flex flex-wrap gap-2 justify-content-center">
                                        <button type="button" class="btn btn-primary rounded-pill shadow-sm px-4" onclick="enableEditMode()">
                                            <i class="bx bx-edit-alt me-2"></i> Edit Profil
                                        </button>
                                        @if (Auth::user()->role === 'Admin')
                                            <a href="{{ route('admin.kepegawaian.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                                                <i class="bx bx-arrow-back me-1"></i> Kembali
                                            </a>
                                        @endif
                                    </div>

                                    {{-- State 2: Edit Buttons --}}
                                    <div id="editButtons" class="d-none d-flex flex-wrap gap-2 justify-content-center edit-element">
                                        <button type="submit" class="btn btn-success rounded-pill shadow-sm px-4">
                                            <i class="bx bx-save me-2"></i> Simpan
                                        </button>
                                        {{-- 🔥 TOMBOL BATAL PAKE MODAL 🔥 --}}
                                        <button type="button" class="btn btn-label-danger rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalBatalEdit">
                                            <i class="bx bx-x me-1"></i> Batal
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. STATISTIK SINGKAT --}}
            <div class="row g-4 mb-4 animate-fade-in-up" style="animation-delay: 0.2s;">
                <div class="col-sm-6 col-xl-3">
                    <div class="card h-100 border-0 stat-card shadow-sm rounded-4">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div class="overflow-hidden">
                                <span class="d-block fw-bold text-primary small text-uppercase mb-1">Username Login</span>
                                <h5 class="mb-0 fw-bold text-truncate text-dark">{{ $pegawai->user->username ?? '-' }}</h5>
                            </div>
                            <div class="avatar bg-label-primary rounded-circle p-2 shadow-xs"><i class="bx bx-key fs-4"></i></div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="card h-100 border-0 stat-card shadow-sm rounded-4">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div class="overflow-hidden">
                                <span class="d-block fw-bold text-warning small text-uppercase mb-1">Usia</span>
                                <h5 class="mb-0 fw-bold text-dark">
                                    {{ $pegawai->tanggal_lahir ? \Carbon\Carbon::parse($pegawai->tanggal_lahir)->age . ' Tahun' : '-' }}
                                </h5>
                            </div>
                            <div class="avatar bg-label-warning rounded-circle p-2 shadow-xs"><i class="bx bx-cake fs-4"></i></div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="card h-100 border-0 stat-card shadow-sm rounded-4">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div class="overflow-hidden">
                                <span class="d-block fw-bold text-info small text-uppercase mb-1">NIK KTP</span>
                                <h5 class="mb-0 fw-bold text-truncate text-dark">{{ $pegawai->nik ?? '-' }}</h5>
                            </div>
                            <div class="avatar bg-label-info rounded-circle p-2 shadow-xs"><i class="bx bx-id-card fs-4"></i></div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="card h-100 border-0 stat-card shadow-sm rounded-4">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div class="overflow-hidden">
                                <span class="d-block fw-bold text-danger small text-uppercase mb-1">Role Sistem</span>
                                <h5 class="mb-0 fw-bold text-uppercase text-dark">{{ $pegawai->user->role ?? 'User' }}</h5>
                            </div>
                            <div class="avatar bg-label-danger rounded-circle p-2 shadow-xs"><i class="bx bx-shield-quarter fs-4"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. KONTEN DETAIL UTAMA --}}
            <div class="row animate-fade-in-up" style="animation-delay: 0.3s;">
                {{-- INFO KIRI (KONTAK) --}}
                <div class="col-xl-4 col-lg-5 col-md-5 mb-4">
                    <div class="card h-100 shadow-soft border-0 rounded-4">
                        <div class="card-header bg-transparent border-bottom py-3">
                            <h5 class="card-title fw-bold m-0 text-dark"><i class='bx bx-phone-call me-2 text-primary'></i>Kontak & Domisili</h5>
                        </div>
                        <div class="card-body py-4">

                            {{-- EMAIL PRIBADI --}}
                            <div class="mb-4">
                                <label class="form-label fw-bold text-muted small d-block mb-2 text-uppercase">Email Pribadi</label>
                                {{-- View --}}
                                <div class="d-flex align-items-center view-element">
                                    <i class="bx bx-envelope text-primary me-2 fs-5"></i>
                                    <span class="text-dark fw-semibold">{{ $pegawai->email_pribadi ?? '-' }}</span>
                                </div>
                                {{-- Edit --}}
                                <div class="input-group input-group-merge edit-element d-none">
                                    <span class="input-group-text bg-light border-0 border-bottom border-secondary"><i class="bx bx-envelope"></i></span>
                                    <input type="email" name="email_pribadi" class="form-control input-modern ps-0" value="{{ $pegawai->email_pribadi }}" placeholder="email@contoh.com">
                                </div>
                            </div>

                            {{-- NO HP --}}
                            <div class="mb-4">
                                <label class="form-label fw-bold text-muted small d-block mb-2 text-uppercase">WhatsApp / No. HP</label>
                                {{-- View --}}
                                <div class="d-flex align-items-center view-element">
                                    <i class="bx bxl-whatsapp text-success me-2 fs-5"></i>
                                    <span class="text-dark fw-semibold">{{ $pegawai->no_hp ?? '-' }}</span>
                                </div>
                                {{-- Edit --}}
                                <div class="input-group input-group-merge edit-element d-none">
                                    <span class="input-group-text bg-light border-0 border-bottom border-secondary"><i class="bx bx-phone"></i></span>
                                    <input type="text" name="no_hp" class="form-control input-modern ps-0" value="{{ $pegawai->no_hp }}" placeholder="08xxxx">
                                </div>
                            </div>

                            {{-- ALAMAT --}}
                            <div class="mb-4">
                                <label class="form-label fw-bold text-muted small d-block mb-2 text-uppercase">Alamat Lengkap</label>
                                {{-- View --}}
                                <div class="d-flex align-items-start view-element">
                                    <i class="bx bx-map text-danger me-2 fs-5 mt-1"></i>
                                    <p class="text-dark fw-medium mb-0 lh-base">{{ $pegawai->alamat ?? '-' }}</p>
                                </div>
                                {{-- Edit --}}
                                <textarea name="alamat" class="form-control input-modern edit-element d-none" rows="3" placeholder="Isi alamat lengkap...">{{ $pegawai->alamat }}</textarea>
                            </div>

                            {{-- PASSWORD (Hanya muncul saat Edit) --}}
                            <div class="edit-element d-none mt-4 p-3 bg-label-danger rounded-4 border border-danger border-opacity-25">
                                <label class="form-label fw-bold text-danger small mb-2 d-flex align-items-center"><i class='bx bx-lock-alt me-1 fs-5'></i> Ganti Password (Opsional)</label>
                                <input type="password" name="password" class="form-control bg-white border-danger shadow-none" placeholder="Isi jika ingin ubah password...">
                                <small class="text-muted mt-1 d-block" style="font-size: 0.7rem;">Biarkan kosong jika tidak ingin mengubah sandi.</small>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- INFO KANAN (TABS DATA) --}}
                <div class="col-xl-8 col-lg-7 col-md-7 mb-4">
                    <div class="card h-100 shadow-soft border-0 rounded-4">
                        <div class="card-header p-0 border-bottom bg-transparent">
                            <ul class="nav nav-tabs nav-tabs-custom" role="tablist">
                                <li class="nav-item">
                                    <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-pribadi">
                                        <i class="bx bx-user me-1"></i> Data Pribadi
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-kepegawaian">
                                        <i class="bx bx-briefcase me-1"></i> Data Kepegawaian
                                    </button>
                                </li>
                            </ul>
                        </div>
                        
                        <div class="card-body py-4">
                            <div class="tab-content p-0 shadow-none bg-transparent">
                                
                                {{-- TAB 1: PRIBADI --}}
                                <div class="tab-pane fade show active" id="navs-pribadi" role="tabpanel">
                                    <div class="row g-4">
                                        {{-- NIK --}}
                                        <div class="col-md-6">
                                            <div class="p-3 border rounded-4 bg-light-subtle h-100">
                                                <label class="form-label fw-bold text-muted small d-block mb-2 text-uppercase">NIK (Sesuai KTP)</label>
                                                <span class="text-dark fw-bold view-element fs-6">{{ $pegawai->nik ?? '-' }}</span>
                                                <input type="number" name="nik" class="form-control input-modern edit-element d-none" value="{{ $pegawai->nik }}" placeholder="Masukkan 16 digit NIK">
                                            </div>
                                        </div>

                                        {{-- JENIS KELAMIN --}}
                                        <div class="col-md-6">
                                            <div class="p-3 border rounded-4 bg-light-subtle h-100">
                                                <label class="form-label fw-bold text-muted small d-block mb-2 text-uppercase">Jenis Kelamin</label>
                                                <span class="text-dark fw-bold view-element fs-6">
                                                    @if($pegawai->jenis_kelamin == 'L') <i class='bx bx-male text-primary'></i> Laki-laki
                                                    @elseif($pegawai->jenis_kelamin == 'P') <i class='bx bx-female text-danger'></i> Perempuan
                                                    @else - @endif
                                                </span>
                                                <select name="jenis_kelamin" class="form-select input-modern edit-element d-none">
                                                    <option value="L" {{ $pegawai->jenis_kelamin == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                                    <option value="P" {{ $pegawai->jenis_kelamin == 'P' ? 'selected' : '' }}>Perempuan</option>
                                                </select>
                                            </div>
                                        </div>

                                        {{-- TEMPAT LAHIR --}}
                                        <div class="col-md-6">
                                            <div class="p-3 border rounded-4 bg-light-subtle h-100">
                                                <label class="form-label fw-bold text-muted small d-block mb-2 text-uppercase">Tempat Lahir</label>
                                                <span class="text-dark fw-bold view-element fs-6">{{ $pegawai->tempat_lahir ?? '-' }}</span>
                                                <input type="text" name="tempat_lahir" class="form-control input-modern edit-element d-none" value="{{ $pegawai->tempat_lahir }}" placeholder="Kota Lahir">
                                            </div>
                                        </div>

                                        {{-- TANGGAL LAHIR --}}
                                        <div class="col-md-6">
                                            <div class="p-3 border rounded-4 bg-light-subtle h-100">
                                                <label class="form-label fw-bold text-muted small d-block mb-2 text-uppercase">Tanggal Lahir</label>
                                                <span class="text-dark fw-bold view-element fs-6">
                                                    {{ $pegawai->tanggal_lahir ? $pegawai->tanggal_lahir->translatedFormat('d F Y') : '-' }}
                                                </span>
                                                <input type="date" name="tanggal_lahir" class="form-control input-modern edit-element d-none" value="{{ $pegawai->tanggal_lahir ? $pegawai->tanggal_lahir->format('Y-m-d') : '' }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- TAB 2: KEPEGAWAIAN --}}
                                <div class="tab-pane fade" id="navs-kepegawaian" role="tabpanel">
                                    <div class="row g-4">
                                        {{-- JABATAN (PROTECTED LOGIC) --}}
                                        <div class="col-md-6">
                                            <div class="p-3 border border-primary border-opacity-25 rounded-4 bg-label-primary h-100">
                                                <label class="form-label fw-bold text-primary small d-block mb-2 text-uppercase">Posisi / Jabatan</label>
                                                <span class="text-dark fw-bold view-element fs-6">{{ $pegawai->jabatan }}</span>

                                                @if (Auth::user()->role === 'Admin')
                                                    <select name="jabatan_kcd_id" class="form-select input-modern edit-element d-none border-primary">
                                                        @foreach ($jabatans as $jab)
                                                            <option value="{{ $jab->id }}" {{ $pegawai->jabatan_kcd_id == $jab->id ? 'selected' : '' }}>{{ $jab->nama }}</option>
                                                        @endforeach
                                                    </select>
                                                @else
                                                    <input type="hidden" name="jabatan" value="{{ $pegawai->jabatan }}">
                                                    <input type="text" class="form-control input-modern edit-element d-none fw-bold text-muted" value="{{ $pegawai->jabatan }}" readonly>
                                                    <small class="text-muted edit-element d-none mt-1 d-block" style="font-size: 0.7rem;">Hubungi Admin untuk ubah jabatan.</small>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- NIP (PROTECTED LOGIC) --}}
                                        <div class="col-md-6">
                                            <div class="p-3 border border-info border-opacity-25 rounded-4 bg-label-info h-100">
                                                <label class="form-label fw-bold text-info small d-block mb-2 text-uppercase">NIP (Username Sistem)</label>
                                                <span class="text-dark fw-bold view-element fs-6">{{ $pegawai->nip ?? '-' }}</span>

                                                @if (Auth::user()->role === 'Admin')
                                                    <input type="text" name="nip" class="form-control input-modern edit-element d-none border-info" value="{{ $pegawai->nip }}" placeholder="-">
                                                @else
                                                    <input type="hidden" name="nip" value="{{ $pegawai->nip }}">
                                                    <input type="text" class="form-control input-modern edit-element d-none fw-bold text-muted" value="{{ $pegawai->nip }}" readonly>
                                                    <small class="text-muted edit-element d-none mt-1 d-block" style="font-size: 0.7rem;">Hubungi Admin untuk ubah NIP.</small>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- INFO TAMBAHAN --}}
                                        <div class="col-12 mt-4">
                                            <div class="alert alert-secondary d-flex align-items-center m-0 rounded-4 border-0" role="alert">
                                                <i class='bx bx-shield-quarter me-3 fs-3 text-secondary'></i>
                                                <div>
                                                    <h6 class="mb-0 fw-bold">Role Akses Sistem saat ini: <span class="text-primary text-uppercase">{{ $pegawai->user->role ?? 'User' }}</span></h6>
                                                    <small>Hak akses ini menentukan menu dan data yang bisa Anda kelola di dalam aplikasi.</small>
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
    </div>

    {{-- 🔥 MODAL KONFIRMASI BATAL EDIT 🔥 --}}
    <div class="modal fade" id="modalBatalEdit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-body text-center p-4">
                    <div class="mb-3">
                        <i class="bx bx-error-circle text-danger" style="font-size: 5rem;"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Batalkan Edit?</h5>
                    <p class="text-muted small mb-4">Semua perubahan yang belum disimpan akan hilang dan tidak bisa dikembalikan.</p>
                    <div class="d-flex flex-column gap-2">
                        <button type="button" class="btn btn-danger rounded-pill fw-bold w-100" onclick="confirmCancel()">Ya, Batalkan</button>
                        <button type="button" class="btn btn-label-secondary rounded-pill fw-bold w-100" data-bs-dismiss="modal">Kembali Edit</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        // --- MODE TOGGLE LOGIC ---
        function enableEditMode() {
            // 1. Sembunyikan elemen View
            document.querySelectorAll('.view-element').forEach(el => el.classList.add('d-none'));
            document.getElementById('viewButtons').classList.add('d-none');

            // 2. Munculkan elemen Edit (Input Form)
            document.querySelectorAll('.edit-element').forEach(el => {
                el.classList.remove('d-none');
            });
            document.getElementById('editButtons').classList.remove('d-none');

            // 3. Fokus ke input pertama (Nama)
            setTimeout(() => {
                document.querySelector('input[name="nama"]').focus();
            }, 100);
        }

        // Fungsi yang dipanggil dari Modal Batal Edit
        function confirmCancel() {
            // Tutup modal secara halus lewat script
            var myModalEl = document.getElementById('modalBatalEdit');
            var modal = bootstrap.Modal.getInstance(myModalEl);
            if (modal) { modal.hide(); }

            // Beri animasi fade out sebelum halamannya kerefresh
            document.querySelectorAll('.edit-element').forEach(el => {
                el.style.transition = 'opacity 0.2s';
                el.style.opacity = '0';
            });
            
            setTimeout(() => { location.reload(); }, 200);
        }

        // --- IMAGE PREVIEW LOGIC ---
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    const defaultAvatar = document.getElementById('defaultAvatar');
                    if (defaultAvatar) defaultAvatar.classList.add('d-none'); // Hide inisial

                    const img = document.getElementById('previewFoto');
                    img.src = e.target.result;
                    img.classList.remove('d-none'); // Show img tag
                    
                    // Efek jreng pas ganti foto
                    img.style.transform = 'scale(0.9)';
                    setTimeout(() => { img.style.transform = 'scale(1)'; img.style.transition = 'transform 0.3s ease'; }, 50);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endpush