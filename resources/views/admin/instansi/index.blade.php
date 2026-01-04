@extends('layouts.admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    
    {{-- Header Halaman --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0">Profil Instansi</h4>
            <p class="text-muted mb-0">Kelola informasi Kantor Cabang Dinas (KCD)</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible shadow-sm border-0 fade show" role="alert">
            <i class='bx bx-check-circle me-2'></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('admin.instansi.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-4">
            {{-- KOLOM KIRI: KARTU IDENTITAS & LOGO --}}
            <div class="col-12 col-lg-4">
                <div class="card shadow-sm border-0 rounded-4 text-center h-100">
                    <div class="card-body p-4 p-lg-5">
                        {{-- Avatar Wrapper dengan tombol upload tersembunyi --}}
                        <div class="position-relative d-inline-block mb-4">
                            <div class="avatar-wrapper rounded-circle overflow-hidden shadow-sm" style="width: 150px; height: 150px; border: 4px solid #f8f9fa;">
                                @if($instansi->logo)
                                    <img src="{{ Storage::url($instansi->logo) }}" alt="Logo Instansi" class="w-100 h-100 object-fit-cover" id="uploadedAvatarPreview">
                                @else
                                    <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-primary bg-opacity-10 fw-bold fs-1 text-primary" id="uploadedAvatarPreview">
                                        {{ strtoupper(substr($instansi->nama_instansi, 0, 2)) }}
                                    </div>
                                @endif
                            </div>
                            {{-- Tombol Upload Icon Floating --}}
                            <label for="upload" class="btn btn-primary btn-icon rounded-circle position-absolute bottom-0 end-0 shadow-sm" style="transform: translate(10%, 10%);" data-bs-toggle="tooltip" title="Ubah Logo">
                                <i class='bx bx-camera fs-5'></i>
                                <input type="file" id="upload" name="logo" class="account-file-input" hidden accept="image/png, image/jpeg, image/gif" onchange="previewImage(this)"/>
                            </label>
                        </div>

                        <h5 class="fw-bold mb-1 text-truncate">{{ $instansi->nama_instansi }}</h5>
                        <p class="text-muted mb-3">{{ $instansi->kabupaten_kota ?? 'Kantor Cabang Dinas' }}</p>

                        <div class="d-flex justify-content-center gap-2 mb-4">
                             <span class="badge bg-label-primary rounded-pill px-3">TP {{ $tahunAjaran ?? date('Y') }}</span>
                        </div>
                        
                        <p class="text-muted small mb-0">
                            Allowed JPG, GIF or PNG. Max size of 2MB. <br>
                            Disarankan gambar persegi (rasio 1:1).
                        </p>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: FORMULIR DETAIL --}}
            <div class="col-12 col-lg-8">
                <div class="card shadow-sm border-0 rounded-4 h-100">
                    <div class="card-header bg-transparent border-bottom px-4 py-3 d-flex align-items-center">
                         <h5 class="mb-0 fw-bold"><i class='bx bx-building-house me-2 text-primary'></i>Detail Informasi Kantor</h5>
                    </div>
                    
                    <div class="card-body p-4">
                        <h6 class="text-muted text-uppercase ls-1 mb-3" style="font-size: 0.75rem; letter-spacing: 1px;">Identitas Kantor</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-12">
                                <label for="nama_instansi" class="form-label fw-semibold">Nama Instansi / KCD <span class="text-danger">*</span></label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class='bx bx-buildings'></i></span>
                                    <input class="form-control form-control-lg px-3" type="text" id="nama_instansi" name="nama_instansi" value="{{ old('nama_instansi', $instansi->nama_instansi) }}" placeholder="Contoh: KCD Wilayah X" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold">E-mail Resmi</label>
                                 <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class='bx bx-envelope'></i></span>
                                    <input class="form-control px-3" type="email" id="email" name="email" value="{{ old('email', $instansi->email) }}" placeholder="kcd@jabarprov.go.id" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="telepon" class="form-label fw-semibold">Nomor Telepon</label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class='bx bx-phone'></i></span>
                                    <input type="text" class="form-control px-3" id="telepon" name="telepon" value="{{ old('telepon', $instansi->telepon) }}" placeholder="022-xxxxxxx" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label for="website" class="form-label fw-semibold">Website</label>
                                 <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class='bx bx-globe'></i></span>
                                    <input type="url" class="form-control px-3" id="website" name="website" value="{{ old('website', $instansi->website) }}" placeholder="https://..." />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label for="alamat" class="form-label fw-semibold">Alamat Lengkap</label>
                                 <div class="input-group input-group-merge">
                                    <span class="input-group-text align-items-start pt-2"><i class='bx bx-map'></i></span>
                                    <textarea class="form-control px-3 py-2" id="alamat" name="alamat" rows="3" placeholder="Jl. Nama Jalan No. X, Kota...">{{ old('alamat', $instansi->alamat) }}</textarea>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4 text-muted opacity-25">

                        <h6 class="text-muted text-uppercase ls-1 mb-3" style="font-size: 0.75rem; letter-spacing: 1px;">Kepala Cabang Dinas</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="nama_kepala" class="form-label fw-semibold">Nama Kepala KCD</label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class='bx bx-user-check'></i></span>
                                    <input type="text" class="form-control px-3" id="nama_kepala" name="nama_kepala" value="{{ old('nama_kepala', $instansi->nama_kepala) }}" placeholder="Nama Lengkap & Gelar" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="nip_kepala" class="form-label fw-semibold">NIP Kepala</label>
                                 <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class='bx bx-id-card'></i></span>
                                    <input type="text" class="form-control px-3" id="nip_kepala" name="nip_kepala" value="{{ old('nip_kepala', $instansi->nip_kepala) }}" placeholder="NIP 18 digit" />
                                </div>
                            </div>
                        </div>

                        <div class="mt-5 d-flex justify-content-end gap-2">
                            <button type="reset" class="btn btn-label-secondary px-4">Batal</button>
                            <button type="submit" class="btn btn-primary px-4 fw-semibold"><i class='bx bx-save me-1'></i> Simpan Perubahan</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('styles')
<style>
    /* Tambahan CSS untuk mempercantik */
    .form-label {
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #566a7f;
    }
    .form-control, .input-group-text {
        border-color: #d9dee3; /* Warna border lebih halus */
    }
    .form-control:focus, .input-group-text:focus-within {
        border-color: #696cff; /* Warna primary saat fokus */
        box-shadow: 0 0 0 0.25rem rgba(105, 108, 255, 0.1);
    }
    .input-group-text {
        background-color: #f5f7f9;
        color: #697a8d;
    }
    /* Agar text area tidak ada resize handle di pojok */
    textarea { resize: none; }
</style>
@endpush

@push('scripts')
<script>
    // Script sederhana untuk preview gambar saat dipilih
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                // Cek apakah yang ditampilkan gambar atau inisial
                var preview = document.getElementById('uploadedAvatarPreview');
                if (preview.tagName === 'IMG') {
                    preview.src = e.target.result;
                } else {
                    // Jika sebelumnya inisial, ganti jadi tag IMG
                    var img = document.createElement('img');
                    img.src = e.target.result;
                    img.alt = "Logo Instansi";
                    img.className = "w-100 h-100 object-fit-cover";
                    img.id = "uploadedAvatarPreview";
                    preview.parentNode.replaceChild(img, preview);
                }
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Aktifkan Bootstrap Tooltip
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>
@endpush
@endsection