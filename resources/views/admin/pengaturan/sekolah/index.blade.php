@extends('layouts.admin')

@section('content')
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Pengaturan /</span> Profil Sekolah</h4>

@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

<div class="row">
    <!-- Kolom Kiri: Info Utama & Peta -->
    <div class="col-md-5 col-lg-4">
        <div class="card mb-4">
            <div class="card-body text-center">
                {{-- Tampilkan Logo jika ada, jika tidak, tampilkan ikon default --}}
                @if($sekolah->logo)
                    <img src="{{ asset('storage/' . $sekolah->logo) }}" alt="Logo Sekolah" class="img-fluid rounded-circle mb-3" style="width: 100px; height: 100px; object-fit: cover;">
                @else
                    <i class="bx bxs-school bx-lg text-primary mb-3"></i>
                @endif
                <h5 class="card-title">{{ $sekolah->nama ?? 'Nama Sekolah Belum Diisi' }}</h5>
                <p class="card-text">{{ $sekolah->bentuk_pendidikan_id_str ?? 'Bentuk Pendidikan' }}</p>
                <div class="d-flex justify-content-center gap-2">
                    <span class="badge bg-label-success">{{ $sekolah->status_sekolah_str ?? 'Status' }}</span>
                    <span class="badge bg-label-info">NPSN: {{ $sekolah->npsn ?? '-' }}</span>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title m-0 me-2">Lokasi Sekolah</h5>
            </div>
            <div class="card-body">
                {{-- Tampilkan Peta jika ada, jika tidak, tampilkan placeholder --}}
                @if($sekolah->peta)
                    <div class="map-container" style="height: 300px; width: 100%;">
                        {!! $sekolah->peta !!}
                    </div>
                @else
                <div class="p-3 bg-light rounded text-center">
                    <i class="bx bx-map bx-md text-secondary"></i>
                    <p class="mb-0 mt-2 text-muted">Peta Lokasi Belum Diatur</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Detail Informasi dengan Tab -->
    <div class="col-md-7 col-lg-8">
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-pills card-header-pills" role="tablist">
                    <li class="nav-item">
                        <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#tab-detail" aria-controls="tab-detail" aria-selected="true">
                            <i class="bx bx-list-ul me-1"></i> Detail
                        </button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#tab-logo" aria-controls="tab-logo" aria-selected="false">
                            <i class="bx bx-image-alt me-1"></i> Logo
                        </button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#tab-peta" aria-controls="tab-peta" aria-selected="false">
                           <i class="bx bx-map-pin me-1"></i> Peta
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.pengaturan.sekolah.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="tab-content">
                        <!-- Tab Detail Informasi (Display Only) -->
                        <div class="tab-pane fade show active" id="tab-detail" role="tabpanel">
                            <h5 class="card-title mb-4">Detail Informasi Sekolah</h5>
                            
                            <h6 class="fw-semibold mb-3 text-primary">INFORMASI UMUM</h6>
                            <dl class="row mb-4">
                                <dt class="col-sm-4">Nama Sekolah</dt>
                                <dd class="col-sm-8">: {{ $sekolah->nama ?? '-' }}</dd>

                                <dt class="col-sm-4">NPSN / NSS</dt>
                                <dd class="col-sm-8">: {{ $sekolah->npsn ?? '-' }} / {{ $sekolah->nss ?? '-' }}</dd>

                                <dt class="col-sm-4">Status</dt>
                                <dd class="col-sm-8">: {{ $sekolah->status_sekolah_str ?? '-' }}</dd>
                            </dl>

                            <hr class="my-4">

                            <h6 class="fw-semibold mb-3 text-primary">DETAIL ALAMAT</h6>
                            <dl class="row mb-4">
                                <dt class="col-sm-4">Alamat Jalan</dt>
                                <dd class="col-sm-8">: {{ $sekolah->alamat_jalan ?? '-' }}</dd>

                                <dt class="col-sm-4">RT / RW</dt>
                                <dd class="col-sm-8">: {{ $sekolah->rt ?? '-' }} / {{ $sekolah->rw ?? '-' }}</dd>

                                <dt class="col-sm-4">Dusun</dt>
                                <dd class="col-sm-8">: {{ $sekolah->dusun ?? '-' }}</dd>

                                <dt class="col-sm-4">Desa / Kelurahan</dt>
                                <dd class="col-sm-8">: {{ $sekolah->desa_kelurahan ?? '-' }}</dd>

                                <dt class="col-sm-4">Kecamatan</dt>
                                <dd class="col-sm-8">: {{ $sekolah->kecamatan ?? '-' }}</dd>

                                <dt class="col-sm-4">Kabupaten / Kota</dt>
                                <dd class="col-sm-8">: {{ $sekolah->kabupaten_kota ?? '-' }}</dd>

                                <dt class="col-sm-4">Provinsi</dt>
                                <dd class="col-sm-8">: {{ $sekolah->provinsi ?? '-' }}</dd>

                                <dt class="col-sm-4">Kode Pos</dt>
                                <dd class="col-sm-8">: {{ $sekolah->kode_pos ?? '-' }}</dd>
                            </dl>
                            
                            <hr class="my-4">

                            <h6 class="fw-semibold mb-3 text-primary">KONTAK & MEDIA</h6>
                             <dl class="row">
                                <dt class="col-sm-4">Nomor Telepon</dt>
                                <dd class="col-sm-8">: {{ $sekolah->nomor_telepon ?? '-' }}</dd>

                                <dt class="col-sm-4">Nomor Fax</dt>
                                <dd class="col-sm-8">: {{ $sekolah->nomor_fax ?? '-' }}</dd>

                                <dt class="col-sm-4">Email</dt>
                                <dd class="col-sm-8">: {{ $sekolah->email ?? '-' }}</dd>

                                <dt class="col-sm-4">Website</dt>
                                <dd class="col-sm-8">: <a href="{{ $sekolah->website ?? '#' }}" target="_blank">{{ $sekolah->website ?? '-' }}</a></dd>
                            </dl>
                        </div>

                        <!-- Tab Logo -->
                        <div class="tab-pane fade" id="tab-logo" role="tabpanel">
                            <h5 class="mb-4">Upload Logo Sekolah</h5>
                            <div class="mb-3">
                                <label for="logo" class="form-label">Pilih File Logo</label>
                                <input class="form-control" type="file" id="logo" name="logo">
                                <div class="form-text">Tipe file: jpeg, jpg, png. Maksimal ukuran 2MB.</div>
                            </div>
                        </div>

                        <!-- Tab Peta -->
                        <div class="tab-pane fade" id="tab-peta" role="tabpanel">
                             <h5 class="mb-4">Sematkan Peta Lokasi</h5>
                            <div class="mb-3">
                                <label for="peta" class="form-label">Kode Semat (Embed) Google Maps</label>
                                <textarea class="form-control" id="peta" name="peta" rows="5">{{ $sekolah->peta }}</textarea>
                                <div class="form-text">
                                    Salin kode iframe dari Google Maps dan tempel di sini. Contoh: <code>&lt;iframe src="..."&gt;&lt;/iframe&gt;</code>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="pt-4">
                        <button type="submit" class="btn btn-primary me-sm-3 me-1">Simpan Perubahan</button>
                        <button type="reset" class="btn btn-label-secondary">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    /* Menyesuaikan ukuran iframe agar responsif */
    .map-container iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100% !important;
        height: 100% !important;
    }
    .map-container {
        position: relative;
        overflow: hidden;
        padding-top: 75%; /* Rasio aspek 4:3, sesuaikan jika perlu */
    }
</style>
@endsection

