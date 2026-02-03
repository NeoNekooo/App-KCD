@extends('layouts.admin')

@section('content')

    {{-- BREADCRUMB --}}
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Kepegawaian / <a href="{{ route('admin.kepegawaian.index') }}">Data Pegawai</a>
            /</span> Detail Profil
    </h4>

    {{-- FORM WRAPPER (Membungkus seluruh halaman agar bisa di-submit) --}}
    
    {{-- Display Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h4 class="alert-heading"><i class="bx bx-error-alt me-2"></i> Gagal Menyimpan Data!</h4>
            <p>Terdapat beberapa kesalahan pada input yang Anda masukkan. Silakan periksa kembali di bawah:</p>
            <hr>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('admin.profil-saya.update') }}" method="POST" enctype="multipart/form-data"
        id="formProfil">
        @csrf @method('PUT')

        {{-- 1. HEADER PROFIL --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 1rem;">
                    {{-- Background Gradient --}}
                    <div class="h-px-150 bg-primary position-relative"
                        style="background: linear-gradient(45deg, #696cff, #8592a3);">
                        <div
                            style="position: absolute; bottom: -20px; left: 0; width: 100%; height: 60px; background: #fff; clip-path: polygon(0 50%, 100% 0, 100% 100%, 0% 100%);">
                        </div>
                    </div>

                    <div class="card-body position-relative pt-0">
                        <div class="row">
                            {{-- FOTO PROFIL --}}
                            <div class="col-sm-auto text-center text-sm-start mt-n5">
                                <div
                                    class="d-flex justify-content-center justify-content-sm-start flex-column align-items-center">
                                    {{-- UPDATE: rounded-circle jadi rounded-3 --}}
                                    <div class="bg-white p-1 rounded-3 shadow-sm d-flex align-items-center justify-content-center position-relative"
                                        style="width: 140px; height: 140px;">

                                        {{-- Preview Foto --}}
                                        @if ($pegawai->foto && Storage::disk('public')->exists($pegawai->foto))
                                            {{-- UPDATE: rounded-circle jadi rounded-3 --}}
                                            <img src="{{ Storage::url($pegawai->foto) }}" id="previewFoto"
                                                class="rounded-3 p-1" style="width: 100%; height: 100%; object-fit: cover;">
                                        @else
                                            {{-- UPDATE: rounded-circle jadi rounded-3 --}}
                                            <div id="defaultAvatar"
                                                class="avatar-initial rounded-3 bg-label-primary fs-1 fw-bold text-uppercase w-100 h-100 d-flex align-items-center justify-content-center"
                                                style="font-size: 3rem;">
                                                {{ substr($pegawai->nama, 0, 2) }}
                                            </div>
                                            {{-- UPDATE: rounded-circle jadi rounded-3 --}}
                                            <img src="" id="previewFoto" class="rounded-3 p-1 d-none"
                                                style="width: 100%; height: 100%; object-fit: cover;">
                                        @endif

                                        {{-- Tombol Upload (Muncul saat Edit Mode) --}}
                                        <label for="uploadFoto"
                                            class="btn btn-icon btn-primary rounded-circle shadow-sm position-absolute bottom-0 end-0 edit-element d-none"
                                            style="transform: translate(30%, 30%);" title="Ganti Foto">
                                            <i class="bx bx-camera"></i>
                                        </label>
                                        <input type="file" name="foto" id="uploadFoto" hidden accept="image/*"
                                            onchange="previewImage(this)">
                                    </div>
                                </div>
                            </div>

                            {{-- IDENTITAS UTAMA --}}
                            <div class="col flex-grow-1 mt-3 mt-sm-4 text-center text-sm-start">

                                {{-- VIEW MODE: NAMA --}}
                                <h3 class="fw-bold mb-1 text-primary view-element">{{ $pegawai->nama }}</h3>

                                {{-- EDIT MODE: NAMA --}}
                                <div class="edit-element d-none mb-2" style="max-width: 400px;">
                                    <label class="form-label small fw-bold text-muted">Nama Lengkap</label>
                                    <input type="text" name="nama"
                                        class="form-control form-control-lg fw-bold border-0 border-bottom rounded-0 bg-transparent ps-0"
                                        value="{{ $pegawai->nama }}" required
                                        style="border-bottom: 2px solid #696cff !important;">
                                </div>

                                <p class="mb-2 text-muted fw-medium badge bg-label-secondary view-element">
                                    {{ $pegawai->jabatan }}</p>

                                <div
                                    class="d-flex flex-wrap justify-content-center justify-content-sm-start gap-2 view-element">
                                    <span class="badge bg-label-primary"><i class='bx bx-id-card me-1'></i> NIP:
                                        {{ $pegawai->nip ?? '-' }}</span>
                                    {{-- Status Akun --}}
                                    @if ($pegawai->user)
                                        <span class="badge bg-label-success"><i class='bx bx-check-circle me-1'></i> Akun:
                                            {{ $pegawai->user->username }}</span>
                                    @else
                                        <span class="badge bg-label-warning"><i class='bx bx-x-circle me-1'></i> Belum Ada
                                            Akun</span>
                                    @endif
                                </div>
                            </div>

                            {{-- TOMBOL AKSI (TOGGLE) --}}
                            <div
                                class="col-12 col-md-auto mt-4 mt-sm-0 text-center text-sm-end d-flex align-items-end flex-column justify-content-center gap-2">

                                {{-- State 1: View Buttons --}}
                                <div id="viewButtons">
                                    <button type="button" class="btn btn-primary shadow-sm" onclick="enableEditMode()">
                                        <i class="bx bx-edit-alt me-1"></i> Edit Profil
                                    </button>
                                    @if (Auth::user()->role === 'Admin')
                                        <a href="{{ route('admin.kepegawaian.index') }}" class="btn btn-outline-secondary">
                                            <i class="bx bx-arrow-back me-1"></i> Kembali
                                        </a>
                                    @endif
                                </div>

                                {{-- State 2: Edit Buttons --}}
                                <div id="editButtons" class="d-none">
                                    <button type="submit" class="btn btn-success shadow-sm">
                                        <i class="bx bx-save me-1"></i> Simpan
                                    </button>
                                    <button type="button" class="btn btn-label-secondary" onclick="cancelEditMode()">
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
        <div class="row g-4 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="card h-100 border-start border-4 border-primary shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div class="overflow-hidden">
                            <span class="d-block fw-semibold text-muted mb-1">Username Login</span>
                            <h5 class="mb-0 fw-bold text-truncate">{{ $pegawai->user->username ?? '-' }}</h5>
                        </div>
                        <div class="avatar bg-label-primary rounded p-2"><i class="bx bx-key fs-4"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card h-100 border-start border-4 border-warning shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div class="overflow-hidden">
                            <span class="d-block fw-semibold text-muted mb-1">Usia</span>
                            <h5 class="mb-0 fw-bold">
                                {{ $pegawai->tanggal_lahir ? \Carbon\Carbon::parse($pegawai->tanggal_lahir)->age . ' Thn' : '-' }}
                            </h5>
                        </div>
                        <div class="avatar bg-label-warning rounded p-2"><i class="bx bx-cake fs-4"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card h-100 border-start border-4 border-info shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div class="overflow-hidden">
                            <span class="d-block fw-semibold text-muted mb-1">NIK</span>
                            <h5 class="mb-0 fw-bold text-truncate">{{ $pegawai->nik ?? '-' }}</h5>
                        </div>
                        <div class="avatar bg-label-info rounded p-2"><i class="bx bx-id-card fs-4"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card h-100 border-start border-4 border-danger shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div class="overflow-hidden">
                            <span class="d-block fw-semibold text-muted mb-1">Role</span>
                            <h5 class="mb-0 fw-bold text-uppercase">{{ $pegawai->user->role ?? 'User' }}</h5>
                        </div>
                        <div class="avatar bg-label-danger rounded p-2"><i class="bx bx-shield-quarter fs-4"></i></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. KONTEN DETAIL UTAMA --}}
        <div class="row">

            {{-- INFO KIRI (KONTAK) --}}
            <div class="col-xl-4 col-lg-5 col-md-5 mb-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header border-bottom">
                        <h5 class="card-title mb-0"><i class='bx bx-phone-call me-2 text-primary'></i>Kontak & Domisili
                        </h5>
                    </div>
                    <div class="card-body py-4">

                        {{-- EMAIL PRIBADI --}}
                        <div class="mb-3 border-bottom pb-2">
                            <label class="form-label fw-bold text-muted small d-block mb-1">Email Pribadi</label>
                            {{-- View --}}
                            <span class="text-dark fw-semibold view-element">{{ $pegawai->email_pribadi ?? '-' }}</span>
                            {{-- Edit --}}
                            <input type="email" name="email_pribadi"
                                class="form-control border-0 p-0 edit-element d-none text-dark fw-semibold"
                                value="{{ $pegawai->email_pribadi }}" placeholder="email@contoh.com">
                        </div>

                        {{-- NO HP --}}
                        <div class="mb-3 border-bottom pb-2">
                            <label class="form-label fw-bold text-muted small d-block mb-1">Nomor HP / WhatsApp</label>
                            {{-- View --}}
                            <span class="text-dark fw-semibold view-element">{{ $pegawai->no_hp ?? '-' }}</span>
                            {{-- Edit --}}
                            <input type="text" name="no_hp"
                                class="form-control border-0 p-0 edit-element d-none text-dark fw-semibold"
                                value="{{ $pegawai->no_hp }}" placeholder="08...">
                        </div>

                        {{-- ALAMAT --}}
                        <div class="mb-3 border-bottom pb-2">
                            <label class="form-label fw-bold text-muted small d-block mb-1">Alamat Lengkap</label>
                            {{-- View --}}
                            <p class="text-dark view-element mb-0">{{ $pegawai->alamat ?? '-' }}</p>
                            {{-- Edit --}}
                            <textarea name="alamat" class="form-control border-0 p-0 edit-element d-none text-dark" rows="3"
                                placeholder="Isi alamat...">{{ $pegawai->alamat }}</textarea>
                        </div>

                        {{-- PASSWORD (Hanya muncul saat Edit) --}}
                        <div class="edit-element d-none mt-4 p-3 bg-label-warning rounded border border-warning">
                            <label class="form-label fw-bold text-dark small mb-1"><i class='bx bx-lock-alt'></i> Ganti
                                Password (Opsional)</label>
                            <input type="password" name="password" class="form-control form-control-sm border-warning"
                                placeholder="Isi jika ingin ubah password...">
                        </div>

                    </div>
                </div>
            </div>

            {{-- INFO KANAN (TABS DATA) --}}
            <div class="col-xl-8 col-lg-7 col-md-7 mb-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header border-bottom">
                        <ul class="nav nav-pills card-header-pills" role="tablist">
                            <li class="nav-item">
                                <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab"
                                    data-bs-target="#navs-pribadi">
                                    <i class="bx bx-user me-1"></i> Data Pribadi
                                </button>
                            </li>
                            <li class="nav-item">
                                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                    data-bs-target="#navs-kepegawaian">
                                    <i class="bx bx-briefcase me-1"></i> Kepegawaian
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body py-4">
                        <div class="tab-content p-0">

                            {{-- TAB 1: PRIBADI --}}
                            <div class="tab-pane fade show active" id="navs-pribadi" role="tabpanel">
                                <div class="row g-4">

                                    {{-- NIK --}}
                                    <div class="col-md-6 border-bottom pb-2">
                                        <label class="form-label fw-bold text-muted small d-block mb-1">NIK (KTP)</label>
                                        <span class="text-dark fw-semibold view-element">{{ $pegawai->nik ?? '-' }}</span>
                                        <input type="number" name="nik"
                                            class="form-control border-0 p-0 edit-element d-none fw-semibold"
                                            value="{{ $pegawai->nik }}" placeholder="-">
                                    </div>

                                    {{-- JENIS KELAMIN --}}
                                    <div class="col-md-6 border-bottom pb-2">
                                        <label class="form-label fw-bold text-muted small d-block mb-1">Jenis
                                            Kelamin</label>
                                        <span class="text-dark fw-semibold view-element">
                                            {{ $pegawai->jenis_kelamin == 'L' ? 'Laki-laki' : ($pegawai->jenis_kelamin == 'P' ? 'Perempuan' : '-') }}
                                        </span>
                                        <select name="jenis_kelamin"
                                            class="form-select border-0 p-0 edit-element d-none fw-semibold">
                                            <option value="L" {{ $pegawai->jenis_kelamin == 'L' ? 'selected' : '' }}>
                                                Laki-laki</option>
                                            <option value="P" {{ $pegawai->jenis_kelamin == 'P' ? 'selected' : '' }}>
                                                Perempuan</option>
                                        </select>
                                    </div>

                                    {{-- TEMPAT LAHIR --}}
                                    <div class="col-md-6 border-bottom pb-2">
                                        <label class="form-label fw-bold text-muted small d-block mb-1">Tempat
                                            Lahir</label>
                                        <span
                                            class="text-dark fw-semibold view-element">{{ $pegawai->tempat_lahir ?? '-' }}</span>
                                        <input type="text" name="tempat_lahir"
                                            class="form-control border-0 p-0 edit-element d-none fw-semibold"
                                            value="{{ $pegawai->tempat_lahir }}" placeholder="-">
                                    </div>

                                    {{-- TANGGAL LAHIR --}}
                                    <div class="col-md-6 border-bottom pb-2">
                                        <label class="form-label fw-bold text-muted small d-block mb-1">Tanggal
                                            Lahir</label>
                                        <span class="text-dark fw-semibold view-element">
                                            {{ $pegawai->tanggal_lahir ? $pegawai->tanggal_lahir->translatedFormat('d F Y') : '-' }}
                                        </span>
                                        <input type="date" name="tanggal_lahir"
                                            class="form-control border-0 p-0 edit-element d-none fw-semibold"
                                            value="{{ $pegawai->tanggal_lahir ? $pegawai->tanggal_lahir->format('Y-m-d') : '' }}">
                                    </div>

                                </div>
                            </div>

                            {{-- TAB 2: KEPEGAWAIAN --}}
                            <div class="tab-pane fade" id="navs-kepegawaian" role="tabpanel">
                                <div class="row g-4">

                                    {{-- JABATAN (PROTECTED LOGIC) --}}
                                    <div class="col-md-6 border-bottom pb-2">
                                        <label class="form-label fw-bold text-muted small d-block mb-1">Jabatan</label>

                                        {{-- TAMPILAN VIEW MODE --}}
                                        <span class="badge bg-label-info view-element">{{ $pegawai->jabatan }}</span>

                                        {{-- TAMPILAN EDIT MODE --}}
                                        @if (Auth::user()->role === 'Admin')
                                            {{-- Jika Admin: Bisa Edit (Select Option) --}}
                                            <select name="jabatan_kcd_id"
                                                class="form-select border-0 p-0 edit-element d-none fw-semibold">
                                                @foreach ($jabatans as $jab)
                                                    <option value="{{ $jab->id }}"
                                                        {{ $pegawai->jabatan_kcd_id == $jab->id ? 'selected' : '' }}>
                                                        {{ $jab->nama }}</option>
                                                @endforeach
                                            </select>
                                        @else
                                            {{-- Jika Bukan Admin: Readonly (Input Text Biasa, Gak ada border edit) --}}
                                            <input type="hidden" name="jabatan" value="{{ $pegawai->jabatan }}">
                                            <input type="text"
                                                class="form-control border-0 p-0 bg-transparent edit-element d-none fw-semibold text-muted"
                                                value="{{ $pegawai->jabatan }}" readonly style="pointer-events: none;">
                                        @endif
                                    </div>

                                    {{-- NIP (PROTECTED LOGIC) --}}
                                    <div class="col-md-6 border-bottom pb-2">
                                        <label class="form-label fw-bold text-muted small d-block mb-1">NIP
                                            (Username)</label>
                                        <span class="text-dark fw-semibold view-element">{{ $pegawai->nip ?? '-' }}</span>

                                        @if (Auth::user()->role === 'Admin')
                                            <input type="text" name="nip"
                                                class="form-control border-0 p-0 edit-element d-none fw-semibold"
                                                value="{{ $pegawai->nip }}" placeholder="-">
                                        @else
                                            <input type="hidden" name="nip" value="{{ $pegawai->nip }}">
                                            <input type="text"
                                                class="form-control border-0 p-0 bg-transparent edit-element d-none fw-semibold text-muted"
                                                value="{{ $pegawai->nip }}" readonly style="pointer-events: none;">
                                        @endif
                                    </div>

                                    {{-- INFO TAMBAHAN --}}
                                    <div class="col-12 mt-4 view-element">
                                        <div class="alert alert-primary d-flex align-items-center" role="alert">
                                            <i class='bx bx-info-circle me-2'></i>
                                            <div>
                                                Role akses sistem saat ini:
                                                <strong>{{ $pegawai->user->role ?? 'User' }}</strong>
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

@endsection

@push('scripts')
    <script>
        // --- MODE TOGGLE LOGIC ---
        function enableEditMode() {
            // 1. Sembunyikan elemen View
            document.querySelectorAll('.view-element').forEach(el => el.classList.add('d-none'));
            document.getElementById('viewButtons').classList.add('d-none');

            // 2. Munculkan elemen Edit (Input Form)
            document.querySelectorAll('.edit-element').forEach(el => el.classList.remove('d-none'));
            document.getElementById('editButtons').classList.remove('d-none');

            // 3. Fokus ke input pertama (Nama)
            document.querySelector('input[name="nama"]').focus();
        }

        function cancelEditMode() {
            if (confirm('Batalkan edit? Perubahan belum disimpan.')) {
                location.reload();
            }
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
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>

    <style>
        /* Style saat mode edit: Garis bawah biru saat fokus */
        /* KECUALI yang readonly (Jabatan/NIP buat pegawai) */
        .edit-element:not([readonly]):focus {
            outline: none;
            border-bottom: 2px solid #696cff !important;
            background: #f8f9fa;
        }

        /* Style default input di mode edit: bersih tanpa border kotak */
        input.edit-element,
        textarea.edit-element,
        select.edit-element {
            box-shadow: none !important;
            border-radius: 0;
            padding-left: 0;
        }
    </style>
@endpush
