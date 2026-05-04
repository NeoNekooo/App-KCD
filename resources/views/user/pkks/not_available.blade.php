@extends('layouts.admin')

@section('title', 'Status Penilaian PKKS')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y d-flex align-items-center justify-content-center" style="min-height: 80vh;">
    <div class="row justify-content-center w-100">
        <div class="col-md-8 col-lg-6 text-center animate__animated animate__fadeIn">
            
            @php
                $config = [
                    'not_found' => [
                        'title' => 'Instrumen Belum Rilis',
                        'desc' => "Maaf, instrumen penilaian PKKS untuk jenjang <strong>$sekolah->bentuk_pendidikan_id_str</strong> belum tersedia.",
                        'icon' => 'bx-file-find',
                        'color' => 'primary',
                        'label' => 'Belum Tersedia'
                    ],
                    'not_started' => [
                        'title' => 'Belum Waktunya',
                        'desc' => "Sabar ya! Penilaian PKKS baru akan dibuka pada tanggal <br><strong>" . ($instrumen ? ($instrumen->start_at ? $instrumen->start_at->format('d M Y, H:i') : '-') : '-') . " WIB</strong>.",
                        'icon' => 'bx-alarm',
                        'color' => 'warning',
                        'label' => 'Segera Hadir'
                    ],
                    'expired' => [
                        'title' => 'Waktu Berakhir',
                        'desc' => "Mohon maaf, masa pengisian penilaian PKKS untuk periode ini telah ditutup pada <strong>" . ($instrumen ? ($instrumen->end_at ? $instrumen->end_at->format('d M Y, H:i') : '-') : '-') . "</strong>.",
                        'icon' => 'bx-timer',
                        'color' => 'danger',
                        'label' => 'Selesai'
                    ]
                ];
                $current = $config[$status] ?? $config['not_found'];
            @endphp

            {{-- Decorative Background --}}
            <div class="position-absolute opacity-10 d-none d-md-block" style="top: 10%; left: 20%; z-index: -1;">
                <div class="rounded-circle bg-{{ $current['color'] }}" style="width: 200px; height: 200px; filter: blur(80px);"></div>
            </div>

            <div class="card border-0 shadow-lg overflow-hidden glass-card">
                <div class="card-body p-5">
                    <div class="icon-container mb-4">
                        <div class="pulse-ring bg-{{ $current['color'] }}" style="opacity: 0.2;"></div>
                        <div class="avatar avatar-xl bg-label-{{ $current['color'] }} mx-auto shadow-sm floating-anim rounded-circle" style="width: 120px; height: 120px;">
                            <span class="avatar-initial rounded-circle bg-gradient-{{ $current['color'] }}">
                                <i class="bx {{ $current['icon'] }} text-white" style="font-size: 4.5rem !important;"></i>
                            </span>
                        </div>
                    </div>

                    <h2 class="fw-black text-dark mb-3 tracking-tight">{{ $current['title'] }}</h2>
                    
                    <div class="px-md-4">
                        <p class="text-secondary fs-5 mb-4 line-height-base">
                            {!! $current['desc'] !!}
                        </p>
                    </div>

                    <div class="alert alert-label-{{ $current['color'] }} border-0 mb-4 py-3 d-inline-flex align-items-center rounded-3">
                        <i class="bx bx-info-circle me-2 fs-4"></i>
                        <span>Status: <strong>{{ $current['label'] }}</strong></span>
                    </div>

                    <div class="d-grid gap-2 col-md-8 mx-auto">
                        <a href="{{ route('admin.dashboard.sekolah') }}" class="btn btn-{{ $current['color'] }} btn-lg shadow-md hover-scale py-3">
                            <i class="bx bx-left-arrow-alt me-2 fs-4"></i> Kembali ke Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;700;900&display=swap');
    .fw-black { font-family: 'Outfit', sans-serif; font-weight: 900; }
    .tracking-tight { letter-spacing: -1px; }
    .glass-card { background: rgba(255, 255, 255, 0.8) !important; backdrop-filter: blur(10px); border-radius: 2rem !important; }
    .bg-gradient-primary { background: linear-gradient(135deg, #696cff 0%, #3f42ff 100%) !important; }
    .bg-gradient-warning { background: linear-gradient(135deg, #ffab00 0%, #ffcf5c 100%) !important; }
    .bg-gradient-danger { background: linear-gradient(135deg, #ff3e1d 0%, #ff6e52 100%) !important; }
    .floating-anim { animation: floating 3s ease-in-out infinite; }
    @keyframes floating { 0%, 100% { transform: translateY(0px); } 50% { transform: translateY(-15px); } }
    .icon-container { position: relative; display: inline-block; }
    .pulse-ring { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 140px; height: 140px; border-radius: 50%; animation: pulse 2s infinite; }
    @keyframes pulse { 0% { transform: translate(-50%, -50%) scale(0.8); opacity: 0.8; } 100% { transform: translate(-50%, -50%) scale(1.5); opacity: 0; } }
    .hover-scale { transition: all 0.3s; }
    .hover-scale:hover { transform: scale(1.05); }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
@endsection
