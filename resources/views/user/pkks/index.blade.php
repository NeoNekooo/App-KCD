@extends('layouts.admin')

@section('title', 'Penilaian Kinerja Kepala Sekolah')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h3 class="fw-bold">Instrumen Penilaian (PKKS)</h3>
            <p class="text-muted">Silakan pilih instrumen yang tersedia untuk melakukan penilaian terhadap Kepala Sekolah.</p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            {{-- Info Sekolah & Kepsek --}}
            <div class="card mb-4 border-0 shadow-sm overflow-hidden">
                <div class="card-body p-4 bg-primary bg-opacity-10 position-relative">
                    <i class="bx bx-buildings position-absolute text-primary opacity-10" style="font-size: 8rem; right: -20px; top: -20px;"></i>
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-xl bg-white rounded p-1 me-4 shadow-sm">
                            <img src="{{ $sekolah->logo_url }}" alt="Logo" class="rounded">
                        </div>
                        <div>
                            <h4 class="mb-1 fw-bold text-primary">{{ $sekolah->nama }}</h4>
                            <div class="d-flex flex-wrap gap-3 mt-2">
                                <span class="badge bg-white text-dark shadow-sm"><i class="bx bx-user me-1 text-primary"></i> 
                                    Kepsek: {{ $kepsek->nama ?? 'Belum Ditentukan' }}
                                </span>
                                <span class="badge bg-white text-dark shadow-sm"><i class="bx bx-label me-1 text-primary"></i> 
                                    Jenjang: {{ $sekolah->bentuk_pendidikan_id_str }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @forelse($instrumens as $item)
            @php
                $now = \Carbon\Carbon::now();
                $isOpen = ($item->start_at && $now->between($item->start_at, $item->end_at));
                $isUpcoming = ($item->start_at && $now->lt($item->start_at));
                $isClosed = ($item->end_at && $now->gt($item->end_at));
            @endphp
            <div class="card mb-4 border-0 shadow-sm instrument-item">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="fw-bold mb-1 text-dark">{{ $item->nama }}</h5>
                            <span class="badge bg-label-primary">Tahun {{ $item->tahun }}</span>
                        </div>
                        @if($isOpen)
                            <span class="badge bg-success shadow-sm">SEDANG BERLANGSUNG</span>
                        @elseif($isUpcoming)
                            <span class="badge bg-warning shadow-sm">BELUM DIMULAI</span>
                        @else
                            <span class="badge bg-danger shadow-sm">SUDAH DITUTUP</span>
                        @endif
                    </div>

                    <div class="bg-light rounded p-3 mb-4 border">
                        <div class="row text-center">
                            <div class="col-md-6 border-end">
                                <small class="text-muted d-block mb-1">WAKTU BUKA</small>
                                <div class="fw-bold small">{{ $item->start_at ? $item->start_at->format('d M Y, H:i') : '-' }}</div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block mb-1">WAKTU TUTUP</small>
                                <div class="fw-bold small">{{ $item->end_at ? $item->end_at->format('d M Y, H:i') : '-' }}</div>
                            </div>
                        </div>
                    </div>

                    @if($isOpen)
                        @if($kepsek)
                            <div class="d-grid">
                                <a href="{{ route('pkks.penilaian.show', $item->id) }}" class="btn btn-primary btn-lg shadow-sm">
                                    <i class="bx bx-edit-alt me-2"></i> Mulai Penilaian Sekarang
                                </a>
                            </div>
                        @else
                            <div class="alert alert-warning mb-0 text-center">
                                <i class="bx bx-error-circle me-1"></i> Kepala Sekolah belum terdata di sistem. Silakan hubungi operator.
                            </div>
                        @endif
                    @elseif($isUpcoming)
                        <div class="alert alert-label-warning mb-0 text-center border-0">
                            <i class="bx bx-time me-1"></i> Penilaian akan dibuka pada <strong>{{ $item->start_at->format('d M Y, H:i') }}</strong>
                        </div>
                    @else
                        <div class="alert alert-label-danger mb-0 text-center border-0">
                            <i class="bx bx-lock-alt me-1"></i> Maaf, waktu pengisian untuk paket ini sudah berakhir.
                        </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="card shadow-none border-dashed text-center py-5 bg-transparent" style="border: 2px dashed #d9dee3 !important;">
                <div class="card-body">
                    <i class="bx bx-file-blank text-muted mb-3" style="font-size: 4rem; opacity: 0.2;"></i>
                    <h5>Belum Ada Instrumen Penilaian</h5>
                    <p class="text-muted">Admin belum merilis instrumen penilaian untuk jenjang {{ $sekolah->bentuk_pendidikan_id_str }} tahun ini.</p>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</div>

<style>
    .instrument-item { transition: all 0.3s ease; }
    .instrument-item:hover { transform: scale(1.01); box-shadow: 0 8px 25px rgba(0,0,0,0.05) !important; }
</style>
@endsection
