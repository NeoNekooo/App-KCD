@extends('layouts.admin')

@section('title', 'Instrumen Tidak Tersedia')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y d-flex align-items-center justify-content-center" style="min-height: 80vh;">
    <div class="row justify-content-center w-100">
        <div class="col-md-8 col-lg-6 text-center animate__animated animate__fadeIn">
            {{-- Decorative Background Circles --}}
            <div class="position-absolute opacity-10 d-none d-md-block" style="top: 10%; left: 20%; z-index: -1;">
                <div class="rounded-circle bg-primary" style="width: 200px; height: 200px; filter: blur(80px);"></div>
            </div>
            <div class="position-absolute opacity-10 d-none d-md-block" style="bottom: 10%; right: 20%; z-index: -1;">
                <div class="rounded-circle bg-info" style="width: 150px; height: 150px; filter: blur(60px);"></div>
            </div>

            <div class="card border-0 shadow-lg overflow-hidden glass-card">
                <div class="card-body p-5">
                    {{-- Animated Icon Container --}}
                    <div class="icon-container mb-4">
                        <div class="pulse-ring"></div>
                        <div class="avatar avatar-xl mx-auto shadow-sm floating-anim rounded-circle" style="width: 120px; height: 120px; background: rgba(105, 108, 255, 0.1);">
                            <span class="avatar-initial rounded-circle bg-gradient-primary">
                                <i class="bx bx-file-find text-white" style="font-size: 4.5rem !important;"></i>
                            </span>
                        </div>
                    </div>

                    <h2 class="fw-black text-dark mb-3 tracking-tight">Instrumen Belum Rilis</h2>
                    
                    <div class="px-md-4">
                        <p class="text-secondary fs-5 mb-4 line-height-base">
                            Maaf, tim admin belum merilis instrumen penilaian PKKS untuk jenjang 
                            <span class="badge bg-label-primary px-3 py-2 fs-6 rounded-pill">{{ $sekolah->bentuk_pendidikan_id_str }}</span> 
                            pada periode tahun <span class="fw-bold text-primary">{{ date('Y') }}</span>.
                        </p>
                    </div>

                    <div class="alert alert-label-info border-0 mb-4 py-3 d-inline-flex align-items-center rounded-3">
                        <i class="bx bx-info-circle me-2 fs-4"></i>
                        <span>Silakan cek kembali secara berkala atau hubungi Pengawas Pembina.</span>
                    </div>

                    <div class="d-grid gap-2 col-md-8 mx-auto">
                        <a href="{{ route('admin.dashboard.sekolah') }}" class="btn btn-primary btn-lg shadow-md hover-scale py-3">
                            <i class="bx bx-left-arrow-alt me-2 fs-4"></i> Kembali ke Dashboard
                        </a>
                    </div>
                </div>
            </div>

            <p class="mt-4 text-muted small opacity-75">
                &copy; {{ date('Y') }} {{ $appSettings['site_name'] ?? 'MANDALA' }} - KCD Pendidikan Wilayah
            </p>
        </div>
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;700;900&display=swap');

    .fw-black { font-family: 'Outfit', sans-serif; font-weight: 900; }
    .tracking-tight { letter-spacing: -1px; }
    .line-height-base { line-height: 1.6; }

    .glass-card {
        background: rgba(255, 255, 255, 0.8) !important;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
        border-radius: 2rem !important;
    }

    .bg-gradient-primary {
        background: linear-gradient(135deg, #696cff 0%, #3f42ff 100%) !important;
    }

    /* Floating Animation */
    .floating-anim {
        animation: floating 3s ease-in-out infinite;
    }

    @keyframes floating {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-15px); }
        100% { transform: translateY(0px); }
    }

    /* Pulse Effect */
    .icon-container {
        position: relative;
        display: inline-block;
    }

    .pulse-ring {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 140px;
        height: 140px;
        background-color: rgba(105, 108, 255, 0.2);
        border-radius: 50%;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { transform: translate(-50%, -50%) scale(0.8); opacity: 0.8; }
        100% { transform: translate(-50%, -50%) scale(1.5); opacity: 0; }
    }

    .hover-scale {
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .hover-scale:hover {
        transform: scale(1.05);
        box-shadow: 0 10px 20px rgba(105, 108, 255, 0.3) !important;
    }

    .btn-lg {
        border-radius: 1rem !important;
        font-weight: 700;
        letter-spacing: 0.5px;
    }
</style>

{{-- Add Animate.css for entrance --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
@endsection
