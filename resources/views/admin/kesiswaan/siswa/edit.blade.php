@extends('layouts.admin')

@section('content')
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Kesiswaan / Data Siswa /</span> Edit Siswa</h4>

<form action="{{ route('admin.kesiswaan.siswa.update', $siswa->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row">
        {{-- KOLOM FOTO --}}
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body text-center">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <h5 class="alert-heading">Terjadi Kesalahan!</h5>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <label for="foto" class="form-label">Foto Siswa</label>
                    <div class="mb-3">
                        @php
                            $namaParts = explode(' ', $siswa->nama);
                            $initials = count($namaParts) > 1 
                                ? strtoupper(substr($namaParts[0], 0, 1) . substr(end($namaParts), 0, 1))
                                : strtoupper(substr($siswa->nama, 0, 2));
                            
                            $photoUrl = $siswa->foto 
                                ? asset('storage/' . $siswa->foto) 
                                : 'https://placehold.co/150x150/696cff/FFFFFF?text=' . $initials;
                        @endphp
                        <img id="foto-preview" src="{{ $photoUrl }}" alt="Foto Siswa" class="img-fluid rounded" style="width: 150px; height: 150px; object-fit: cover;">
                    </div>
                    <input type="file" class="form-control" id="foto" name="foto" onchange="previewImage()">
                </div>
            </div>
        </div>

        {{-- KOLOM DATA DENGAN TAB --}}
        <div class="col-md-8">
            <div class="card">
                <div class="card-header p-0">
                    <div class="nav-align-top">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#data-pribadi" aria-controls="data-pribadi" aria-selected="true">
                                    <i class="tf-icons ti ti-user-circle ti-xs me-1"></i> Data Pribadi
                                </button>
                            </li>
                            <li class="nav-item">
                                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#data-kontak" aria-controls="data-kontak" aria-selected="false">
                                    <i class="tf-icons ti ti-device-mobile ti-xs me-1"></i> Kontak
                                </button>
                            </li>
                            <li class="nav-item">
                                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#data-keluarga" aria-controls="data-keluarga" aria-selected="false">
                                    <i class="tf-icons ti ti-users ti-xs me-1"></i> Data Keluarga
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="tab-content p-0">
                        {{-- Tab 1: Data Pribadi --}}
                        <div class="tab-pane fade show active" id="data-pribadi" role="tabpanel">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="nama" class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control" id="nama" name="nama" value="{{ old('nama', $siswa->nama) }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                                    <select name="jenis_kelamin" id="jenis_kelamin" class="form-select">
                                        <option value="L" {{ old('jenis_kelamin', $siswa->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="P" {{ old('jenis_kelamin', $siswa->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="nipd" class="form-label">NIS</label>
                                    <input type="text" class="form-control" id="nipd" name="nipd" value="{{ old('nipd', $siswa->nipd) }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="nisn" class="form-label">NISN</label>
                                    <input type="text" class="form-control" id="nisn" name="nisn" value="{{ old('nisn', $siswa->nisn) }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="nik" class="form-label">NIK</label>
                                    <input type="text" class="form-control" id="nik" name="nik" value="{{ old('nik', $siswa->nik) }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="tempat_lahir" class="form-label">Tempat Lahir</label>
                                    <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir" value="{{ old('tempat_lahir', $siswa->tempat_lahir) }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                                    <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir', $siswa->tanggal_lahir) }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="agama_id_str" class="form-label">Agama</label>
                                    <input type="text" class="form-control" id="agama_id_str" name="agama_id_str" value="{{ old('agama_id_str', $siswa->agama_id_str) }}">
                                </div>
                            </div>
                        </div>

                        {{-- Tab 2: Kontak --}}
                        <div class="tab-pane fade" id="data-kontak" role="tabpanel">
                             <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nomor_telepon_seluler" class="form-label">Nomor HP</label>
                                    <input type="text" class="form-control" id="nomor_telepon_seluler" name="nomor_telepon_seluler" value="{{ old('nomor_telepon_seluler', $siswa->nomor_telepon_seluler) }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $siswa->email) }}">
                                </div>
                            </div>
                        </div>

                        {{-- Tab 3: Data Keluarga --}}
                        <div class="tab-pane fade" id="data-keluarga" role="tabpanel">
                            <h6 class="mt-2">Data Ayah</h6><hr class="mt-0">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nama_ayah" class="form-label">Nama Ayah</label>
                                    <input type="text" class="form-control" id="nama_ayah" name="nama_ayah" value="{{ old('nama_ayah', $siswa->nama_ayah) }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="pekerjaan_ayah_id_str" class="form-label">Pekerjaan Ayah</label>
                                    <input type="text" class="form-control" id="pekerjaan_ayah_id_str" name="pekerjaan_ayah_id_str" value="{{ old('pekerjaan_ayah_id_str', $siswa->pekerjaan_ayah_id_str) }}">
                                </div>
                            </div>
                            
                            <h6 class="mt-3">Data Ibu</h6><hr class="mt-0">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nama_ibu" class="form-label">Nama Ibu</label>
                                    <input type="text" class="form-control" id="nama_ibu" name="nama_ibu" value="{{ old('nama_ibu', $siswa->nama_ibu) }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="pekerjaan_ibu_id_str" class="form-label">Pekerjaan Ibu</label>
                                    <input type="text" class="form-control" id="pekerjaan_ibu_id_str" name="pekerjaan_ibu_id_str" value="{{ old('pekerjaan_ibu_id_str', $siswa->pekerjaan_ibu_id_str) }}">
                                </div>
                            </div>

                            <h6 class="mt-3">Data Wali</h6><hr class="mt-0">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nama_wali" class="form-label">Nama Wali</label>
                                    <input type="text" class="form-control" id="nama_wali" name="nama_wali" value="{{ old('nama_wali', $siswa->nama_wali) }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="pekerjaan_wali_id_str" class="form-label">Pekerjaan Wali</label>
                                    <input type="text" class="form-control" id="pekerjaan_wali_id_str" name="pekerjaan_wali_id_str" value="{{ old('pekerjaan_wali_id_str', $siswa->pekerjaan_wali_id_str) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="my-4">
        <button type="submit" class="btn btn-primary">Simpan Semua Perubahan</button>
        <a href="{{ route('admin.kesiswaan.siswa.index') }}" class="btn btn-secondary">Batal</a>
    </div>
</form>
@endsection

@push('scripts')
<script>
    // Fungsi untuk menampilkan preview gambar sebelum di-upload
    function previewImage() {
        const fileInput = document.getElementById('foto');
        const preview = document.getElementById('foto-preview');
        
        if (fileInput.files && fileInput.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
            }
            reader.readAsDataURL(fileInput.files[0]);
        }
    }
</script>
@endpush