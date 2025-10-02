@extends('layouts.admin')

@section('content')
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Kepegawaian / Data GTK /</span> Detail GTK</h4>

<div class="row">
    <!-- Kolom Kiri: Profil Utama -->
    <div class="col-xl-4 col-lg-5 col-md-5">
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex flex-column align-items-center text-center">
                    <div class="avatar avatar-xl mb-3">
                        <span class="avatar-initial rounded-circle bg-label-primary" style="font-size: 2rem;">{{ substr($gtk->nama, 0, 1) }}</span>
                    </div>
                    <h4 class="mb-1">{{ $gtk->nama }}</h4>
                    <p class="text-muted">NIP: {{ $gtk->nip ?? '-' }}</p>
                    <span class="badge bg-label-success mb-3">{{ $gtk->status_kepegawaian_id_str ?? 'Status Tidak Diketahui' }}</span>
                    <div class="d-flex w-100 justify-content-center">
                        <a href="{{ route('admin.kepegawaian.gtk.index') }}" class="btn btn-outline-secondary w-100">Kembali</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Informasi Detail dengan Tab -->
    <div class="col-xl-8 col-lg-7 col-md-7">
        <div class="card">
            <div class="card-body">
                <div class="nav-align-top">
                    <ul class="nav nav-pills mb-3" role="tablist">
                        <li class="nav-item">
                            <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#tab-pribadi" aria-selected="true">
                                <i class="tf-icons bx bx-user me-1"></i> Data Pribadi
                            </button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#tab-kepegawaian" aria-selected="false">
                                <i class="tf-icons bx bx-briefcase me-1"></i> Info Kepegawaian
                            </button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#tab-riwayat" aria-selected="false">
                                <i class="tf-icons bx bx-book-bookmark me-1"></i> Riwayat
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <!-- Tab Data Pribadi -->
                        <div class="tab-pane fade show active" id="tab-pribadi" role="tabpanel">
                            <dl class="row mt-3">
                                <dt class="col-sm-4">Nama Lengkap</dt>
                                <dd class="col-sm-8">: {{ $gtk->nama ?? '-' }}</dd>

                                <dt class="col-sm-4">Jenis Kelamin</dt>
                                <dd class="col-sm-8">: {{ $gtk->jenis_kelamin ?? '-' }}</dd>

                                <dt class="col-sm-4">Tempat, Tanggal Lahir</dt>
                                <dd class="col-sm-8">: {{ $gtk->tempat_lahir ?? '-' }}, {{ $gtk->tanggal_lahir ? \Carbon\Carbon::parse($gtk->tanggal_lahir)->translatedFormat('d F Y') : '-' }}</dd>

                                <dt class="col-sm-4">Agama</dt>
                                <dd class="col-sm-8">: {{ $gtk->agama_id_str ?? '-' }}</dd>

                                <dt class="col-sm-4">NIK</dt>
                                <dd class="col-sm-8">: {{ $gtk->nik ?? '-' }}</dd>
                            </dl>
                        </div>
                        <!-- Tab Info Kepegawaian -->
                        <div class="tab-pane fade" id="tab-kepegawaian" role="tabpanel">
                             <dl class="row mt-3">
                                <dt class="col-sm-4">Status Kepegawaian</dt>
                                <dd class="col-sm-8">: {{ $gtk->status_kepegawaian_id_str ?? '-' }}</dd>

                                <dt class="col-sm-4">NIP</dt>
                                <dd class="col-sm-8">: {{ $gtk->nip ?? '-' }}</dd>

                                <dt class="col-sm-4">NUPTK</dt>
                                <dd class="col-sm-8">: {{ $gtk->nuptk ?? '-' }}</dd>

                                <dt class="col-sm-4">Jenis PTK</dt>
                                <dd class="col-sm-8">: {{ $gtk->jenis_ptk_id_str ?? '-' }}</dd>

                                <dt class="col-sm-4">Jabatan</dt>
                                <dd class="col-sm-8">: {{ $gtk->jabatan_ptk_id_str ?? '-' }}</dd>

                                <dt class="col-sm-4">Tanggal Surat Tugas</dt>
                                <dd class="col-sm-8">: {{ $gtk->tanggal_surat_tugas ? \Carbon\Carbon::parse($gtk->tanggal_surat_tugas)->translatedFormat('d F Y') : '-' }}</dd>
                                
                                <dt class="col-sm-4">Status Induk</dt>
                                <dd class="col-sm-8">: {{ $gtk->ptk_induk == 1 ? 'Induk' : 'Non-Induk' }}</dd>
                            </dl>
                        </div>
                        <!-- Tab Riwayat -->
                        <div class="tab-pane fade" id="tab-riwayat" role="tabpanel">
                             <dl class="row mt-3">
                                <dt class="col-sm-4">Pendidikan Terakhir</dt>
                                <dd class="col-sm-8">: {{ $gtk->pendidikan_terakhir ?? '-' }}</dd>

                                <dt class="col-sm-4">Bidang Studi Terakhir</dt>
                                <dd class="col-sm-8">: {{ $gtk->bidang_studi_terakhir ?? '-' }}</dd>
                                
                                <dt class="col-sm-4">Pangkat/Golongan Terakhir</dt>
                                <dd class="col-sm-8">: {{ $gtk->pangkat_golongan_terakhir ?? '-' }}</dd>

                                <dt class="col-sm-4">Riwayat Pendidikan Formal</dt>
                                <dd class="col-sm-8">: {{ $gtk->rwy_pend_formal ?? '-' }}</dd>

                                <dt class="col-sm-4">Riwayat Kepangkatan</dt>
                                <dd class="col-sm-8">: {{ $gtk->rwy_kepangkatan ?? '-' }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
