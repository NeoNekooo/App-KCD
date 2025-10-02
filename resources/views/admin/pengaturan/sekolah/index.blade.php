@extends('layouts.admin')

@section('content')
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Pengaturan /</span> Profil Sekolah</h4>

<div class="row">
    <!-- Kolom Kiri: Info Utama & Peta -->
    <div class="col-md-5 col-lg-4">
        <div class="card mb-4">
            <div class="card-body text-center">
                <i class="bx bxs-school bx-lg text-primary mb-3"></i>
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
                {{-- Di sini bisa diletakkan peta interaktif jika ada Lintang & Bujur --}}
                <div class="p-3 bg-light rounded text-center">
                     <i class="bx bx-map bx-md text-secondary"></i>
                     <p class="mb-0 mt-2 text-muted">Pratinjau Peta Lokasi</p>
                     <small>Lintang: {{ $sekolah->lintang ?? '-' }} | Bujur: {{ $sekolah->bujur ?? '-' }}</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Detail Informasi -->
    <div class="col-md-7 col-lg-8">
        <div class="card">
            <div class="card-body">
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
        </div>
    </div>
</div>
@endsection

