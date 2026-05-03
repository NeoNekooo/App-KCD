@extends('layouts.admin')

@section('title', 'Status Penilaian PKKS')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm border-0 overflow-hidden">
                @php
                    $isUpcoming = $instrumen->start_at && $now->lt($instrumen->start_at);
                    $isClosed = $instrumen->end_at && $now->gt($instrumen->end_at);
                @endphp

                <div class="card-header {{ $isUpcoming ? 'bg-warning' : 'bg-danger' }} py-4 text-center">
                    <h4 class="mb-0 text-white fw-bold">
                        <i class="bx {{ $isUpcoming ? 'bx-time' : 'bx-lock-alt' }} me-2"></i>
                        Akses Penilaian Terkunci
                    </h4>
                </div>

                <div class="card-body p-4 text-center">
                    <div class="mb-4">
                        <h5 class="fw-bold mb-1">{{ $instrumen->nama }}</h5>
                        <div class="badge bg-label-primary">Tahun {{ $instrumen->tahun }}</div>
                    </div>

                    <div class="alert {{ $isUpcoming ? 'alert-label-warning' : 'alert-label-danger' }} border-0 py-3 px-4 mb-4">
                        @if($isUpcoming)
                            <p class="mb-2 fw-medium">Penilaian belum dimulai.</p>
                            <h5 class="mb-0 text-warning fw-bold">Dibuka pada: {{ $instrumen->start_at->format('d M Y, H:i') }} WIB</h5>
                        @else
                            <p class="mb-2 fw-medium">Waktu pengisian sudah berakhir.</p>
                            <h5 class="mb-0 text-danger fw-bold">Ditutup pada: {{ $instrumen->end_at->format('d M Y, H:i') }} WIB</h5>
                        @endif
                    </div>

                    <div class="bg-light rounded p-3 mb-4 text-start border">
                        <div class="d-flex align-items-center mb-2">
                            <i class="bx bx-buildings me-2 text-primary"></i>
                            <span class="text-muted small">Sekolah:</span>
                            <span class="ms-auto fw-bold small">{{ $sekolah->nama }}</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="bx bx-user me-2 text-primary"></i>
                            <span class="text-muted small">Kepala Sekolah:</span>
                            <span class="ms-auto fw-bold small">{{ $kepsek->nama ?? '-' }}</span>
                        </div>
                    </div>

                    <a href="{{ route('admin.dashboard.sekolah') }}" class="btn btn-outline-secondary w-100">
                        <i class="bx bx-home-alt me-1"></i> Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
