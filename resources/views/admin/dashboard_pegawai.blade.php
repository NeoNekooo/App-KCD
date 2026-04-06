@extends('layouts.admin')

@section('title', 'Dashboard Pegawai')

@section('content')
    @php
        $user = Auth::user();
    @endphp

    @push('styles')
        <style>
            .rounded-4 {
                border-radius: 1rem !important;
            }

            .shadow-xs {
                box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.05) !important;
            }

            .shadow-soft {
                box-shadow: 0 8px 25px rgba(105, 108, 255, 0.08) !important;
            }

            .stat-card {
                transition: transform 0.3s cubic-bezier(0.25, 0.8, 0.25, 1), box-shadow 0.3s ease;
            }

            .stat-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
            }

            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(15px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .animate-fade-in-up {
                animation: fadeInUp 0.5s ease-out forwards;
            }

            .glass-widget {
                background: rgba(255, 255, 255, 0.05);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.1);
                border-radius: 1rem;
            }

            .bg-label-primary {
                background-color: rgba(105, 108, 255, 0.1) !important;
                color: #696cff !important;
            }

            .bg-label-success {
                background-color: rgba(113, 221, 55, 0.1) !important;
                color: #71dd37 !important;
            }

            .bg-label-info {
                background-color: rgba(3, 195, 236, 0.1) !important;
                color: #03c3ec !important;
            }

            .bg-label-warning {
                background-color: rgba(255, 171, 0, 0.1) !important;
                color: #ffab00 !important;
            }

            .bg-label-danger {
                background-color: rgba(255, 62, 29, 0.1) !important;
                color: #ff3e1d !important;
            }

            .hover-up {
                transition: transform 0.3s ease;
            }

            .hover-up:hover {
                transform: translateY(-3px);
            }

            @keyframes float {
                0% {
                    transform: translateY(0px);
                }

                50% {
                    transform: translateY(-10px);
                }

                100% {
                    transform: translateY(0px);
                }
            }

            .animate-float {
                animation: float 4s ease-in-out infinite;
            }
        </style>
    @endpush

    <div class="flex-grow-1 container-p-y">

        {{-- =================================================================== --}}
        {{-- SECTION 1: HERO BANNER UTAMA                                        --}}
        {{-- =================================================================== --}}
        <div class="row mb-4 animate-fade-in-up">
            <div class="col-12">
                <div class="card overflow-hidden border-0 shadow-soft rounded-4">
                    <div class="card-body p-4 p-md-5 text-white position-relative"
                        style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">

                        <div
                            style="position: absolute; top: -50px; right: -50px; width: 250px; height: 250px; background: radial-gradient(circle, rgba(105,108,255,0.15) 0%, rgba(0,0,0,0) 70%); border-radius: 50%;">
                        </div>
                        <div
                            style="position: absolute; bottom: -50px; left: 20%; width: 200px; height: 200px; background: radial-gradient(circle, rgba(3,195,236,0.1) 0%, rgba(0,0,0,0) 70%); border-radius: 50%;">
                        </div>

                        <div
                            class="d-flex flex-column flex-xl-row justify-content-between align-items-xl-center position-relative z-1 gap-4">
                            <div class="d-flex align-items-center gap-4">
                                <div class="bg-white p-1 rounded-circle d-flex align-items-center justify-content-center shadow-lg"
                                    style="width: 100px; height: 100px; flex-shrink: 0;">
                                    @if (!empty($instansi->logo) && \Storage::disk('public')->exists($instansi->logo))
                                        <img src="{{ \Storage::url($instansi->logo) }}"
                                            class="img-fluid rounded-circle w-100 h-100 object-fit-cover" alt="Logo">
                                    @else
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($instansi->nama_instansi ?? 'KCD') }}&background=696cff&color=fff&size=128&font-size=0.33"
                                            class="img-fluid rounded-circle" alt="Logo">
                                    @endif
                                </div>
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="badge bg-label-warning text-uppercase fw-bold"
                                            style="letter-spacing: 1px; font-size: 0.7rem;">
                                            PORTAL PEGAWAI
                                        </span>
                                    </div>
                                    <h3 class="fw-bolder mb-1 text-white text-uppercase" style="letter-spacing: -0.5px;">
                                        {{ $instansi->nama_instansi ?? 'DASHBOARD KCD' }}
                                    </h3>
                                    <p class="text-white-50 mb-0 fs-6">
                                        <span id="greeting-text">Halo</span>, <strong>{{ $user->name }}</strong> 👋
                                    </p>
                                </div>
                            </div>

                            <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center gap-3">
                                <div
                                    class="glass-widget px-4 py-3 text-center text-sm-end d-flex flex-column justify-content-center">
                                    <h3 class="mb-0 fw-bolder text-white lh-1" id="realtime-clock"
                                        style="font-family: monospace; letter-spacing: 2px;">--:--:--</h3>
                                    <span class="text-info fw-medium mt-1" id="realtime-date"
                                        style="font-size: 0.75rem;">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- =================================================================== --}}
        {{-- SECTION 2: TAMPILAN KHUSUS PEGAWAI                                  --}}
        {{-- =================================================================== --}}
        <div class="row g-4 animate-fade-in-up" style="animation-delay: 0.1s;">
            <div class="col-md-8">
                <div class="card border-0 shadow-soft h-100 bg-white rounded-4 overflow-hidden position-relative">
                    <div style="position: absolute; right: -50px; top: -50px; opacity: 0.03;"><i class='bx bx-check-shield'
                            style="font-size: 300px;"></i></div>
                    <div
                        class="card-body p-5 text-center d-flex flex-column justify-content-center align-items-center position-relative z-1">
                        <div class="mb-4 bg-label-primary rounded-circle p-4 animate-float shadow-xs"
                            style="width: 130px; height: 130px; display: flex; align-items: center; justify-content: center;">
                            <i class='bx bx-file-find text-primary' style="font-size: 60px;"></i>
                        </div>
                        <h3 class="fw-bolder text-dark mb-2">Selamat Bertugas!</h3>
                        <p class="text-muted mb-4 fs-6" style="max-width: 550px; line-height: 1.6;">Anda login sebagai
                            <strong>Verifikator Cabang Dinas</strong>. Silakan periksa menu layanan untuk memproses
                            pengajuan.</p>
                        <div class="d-flex flex-wrap justify-content-center gap-3">
                            <a href="{{ $verifikasiLink ?? '#' }}"
                                class="btn btn-primary rounded-pill px-5 py-2 fw-bold shadow-sm hover-up"><i
                                    class='bx bx-task me-2 fs-5'></i> Buka Antrean Layanan</a>
                            <a href="{{ route('admin.profil-saya.show') }}"
                                class="btn btn-outline-secondary rounded-pill px-4 py-2 fw-bold shadow-xs hover-up"><i
                                    class='bx bx-user-circle me-2 fs-5'></i> Profil & Sandi</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div
                    class="card border-0 shadow-soft h-100 bg-primary text-white position-relative overflow-hidden rounded-4">
                    <div
                        style="position: absolute; bottom: 0; right: 0; width: 100%; height: 100%; background: linear-gradient(to top right, rgba(255,255,255,0.2), transparent);">
                    </div>
                    <div class="card-body p-4 p-xl-5 d-flex flex-column justify-content-between position-relative z-1">
                        <div>
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-white rounded-circle p-2 text-primary me-3 shadow-sm"><i
                                        class="bx bx-shield-quarter fs-3"></i></div>
                                <h5 class="fw-bold text-white mb-0">Status Sistem</h5>
                            </div>
                            <p class="text-white opacity-75 mb-0" style="font-size: 0.9rem; line-height: 1.5;">Data yang
                                Anda verifikasi akan tercatat dalam log aktivitas sistem.</p>
                        </div>
                        <div class="mt-4 pt-4 border-top border-white border-opacity-25">
                            <div class="d-flex align-items-center justify-content-between">
                                <div><small class="d-block text-white opacity-75 text-uppercase fw-bold"
                                        style="font-size: 0.7rem;">Sesi Pengguna</small>
                                    <h6 class="mb-0 fw-bold text-white mt-1">{{ Auth::user()->username }}</h6>
                                </div>
                                <div
                                    class="badge bg-success bg-opacity-25 text-white border border-success border-opacity-50 rounded-pill px-3 py-2 d-flex align-items-center gap-2 shadow-xs">
                                    <div class="rounded-circle bg-success"
                                        style="width: 8px; height: 8px; box-shadow: 0 0 10px #71dd37;"></div><span
                                        class="fw-bold small">Online</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            function updateClock() {
                const now = new Date();
                const timeString = now.toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: false
                }).replace(/\./g, ':');
                const dateString = now.toLocaleDateString('id-ID', {
                    weekday: 'long',
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric'
                });
                const hour = now.getHours();
                let greeting = 'Halo';
                if (hour >= 4 && hour < 11) greeting = 'Selamat Pagi';
                else if (hour >= 11 && hour < 15) greeting = 'Selamat Siang';
                else if (hour >= 15 && hour < 18) greeting = 'Selamat Sore';
                else greeting = 'Selamat Malam';
                if (document.getElementById('realtime-clock')) document.getElementById('realtime-clock').innerText =
                    timeString;
                if (document.getElementById('realtime-date')) document.getElementById('realtime-date').innerText =
                    dateString;
                if (document.getElementById('greeting-text')) document.getElementById('greeting-text').innerText =
                    greeting;
            }
            setInterval(updateClock, 1000);
            updateClock();
        });
    </script>
@endpush
