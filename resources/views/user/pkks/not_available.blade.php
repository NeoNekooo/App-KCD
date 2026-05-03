@extends('layouts.admin')

@section('title', 'Instrumen Tidak Tersedia')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center py-5">
            <div class="card shadow-none border-0 bg-transparent">
                <div class="card-body">
                    <div class="avatar avatar-xl bg-label-secondary mx-auto mb-4" style="width: 100px; height: 100px;">
                        <span class="avatar-initial rounded-circle"><i class="bx bx-file-blank fs-1" style="font-size: 4rem !important;"></i></span>
                    </div>
                    <h3 class="fw-bold text-dark">Instrumen Belum Tersedia</h3>
                    <p class="text-muted fs-5">Maaf, saat ini belum ada instrumen penilaian PKKS yang dirilis untuk jenjang <strong>{{ $sekolah->bentuk_pendidikan_id_str }}</strong> pada tahun {{ date('Y') }}.</p>
                    <hr class="my-4 mx-auto w-25">
                    <p class="small text-muted">Silakan hubungi Admin atau Pengawas Pembina untuk informasi lebih lanjut.</p>
                    <a href="{{ route('admin.dashboard.sekolah') }}" class="btn btn-primary mt-3">
                        <i class="bx bx-home-alt me-1"></i> Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
