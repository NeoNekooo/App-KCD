@extends('layouts.admin')

@section('title', 'Dashboard Sekolah')

@section('content')
    <div class="flex-grow-1 container-p-y">
        <div class="row mb-4 animate-fade-in-up">
            <div class="col-12">
                <div class="card overflow-hidden border-0 shadow-soft rounded-4">
                    <div class="card-body p-4 p-md-5 text-white position-relative"
                        style="background: linear-gradient(135deg, #0f172a 0%, #008493 100%);">

                        <div
                            style="position: absolute; top: -50px; right: -50px; width: 250px; height: 250px; background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(0,0,0,0) 70%); border-radius: 50%;">
                        </div>

                        <div class="d-flex flex-column flex-xl-row justify-content-between align-items-xl-center position-relative z-1 gap-4">
                            <div class="d-flex align-items-center gap-4">
                                <div class="bg-white p-1 rounded-circle d-flex align-items-center justify-content-center shadow-lg"
                                    style="width: 100px; height: 100px; flex-shrink: 0;">
                                    @if (!empty($sekolah->logo) && \Storage::disk('public')->exists($sekolah->logo))
                                        <img src="{{ \Storage::url($sekolah->logo) }}"
                                            class="img-fluid rounded-circle w-100 h-100 object-fit-cover" alt="Logo">
                                    @else
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($sekolah->nama ?? 'Sekolah') }}&background=008493&color=fff&size=128&font-size=0.33"
                                            class="img-fluid rounded-circle" alt="Logo">
                                    @endif
                                </div>
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="badge bg-label-warning text-uppercase fw-bold"
                                            style="letter-spacing: 1px; font-size: 0.7rem;">
                                            PORTAL {{ strtoupper($role) }}
                                        </span>
                                    </div>
                                    <h3 class="fw-bolder mb-1 text-white text-uppercase" style="letter-spacing: -0.5px;">
                                        {{ $sekolah->nama ?? 'DASHBOARD SEKOLAH' }}
                                    </h3>
                                    <p class="text-white-50 mb-0 fs-6">
                                        <span id="greeting-text">Halo</span>, <strong>{{ $user->username }}</strong> 👋
                                    </p>
                                </div>
                            </div>

                            <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center gap-3">
                                <div class="glass-widget px-4 py-3 text-center text-sm-end d-flex flex-column justify-content-center">
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

        <div class="row g-4 animate-fade-in-up" style="animation-delay: 0.1s;">
            <div class="col-md-12">
                <div class="card border-0 shadow-soft h-100 bg-white rounded-4 overflow-hidden position-relative">
                    <div class="card-body p-5 text-center d-flex flex-column justify-content-center align-items-center position-relative z-1">
                        <div class="mb-4 bg-label-primary rounded-circle p-4 animate-float shadow-xs"
                            style="width: 130px; height: 130px; display: flex; align-items: center; justify-content: center;">
                            <i class='bx bx-school text-primary' style="font-size: 60px;"></i>
                        </div>
                        <h3 class="fw-bolder text-dark mb-2">Selamat Datang di Portal {{ ucfirst($role) }}!</h3>
                        <p class="text-muted mb-4 fs-6" style="max-width: 550px; line-height: 1.6;">
                            Anda terhubung dengan sistem KCD melalui data sekolah <strong>{{ $sekolah->nama ?? 'Sekolah' }}</strong>. 
                            Gunakan portal ini untuk mengakses layanan pendidikan yang tersedia.
                        </p>
                        <div class="d-flex flex-wrap justify-content-center gap-3">
                            <a href="#" class="btn btn-primary rounded-pill px-5 py-2 fw-bold shadow-sm hover-up">
                                <i class='bx bx-book-open me-2 fs-5'></i> Materi & Pembelajaran
                            </a>
                            <a href="#" class="btn btn-outline-secondary rounded-pill px-4 py-2 fw-bold shadow-xs hover-up">
                                <i class='bx bx-user-circle me-2 fs-5'></i> Profil Akun
                            </a>
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
                
                if (document.getElementById('realtime-clock')) document.getElementById('realtime-clock').innerText = timeString;
                if (document.getElementById('realtime-date')) document.getElementById('realtime-date').innerText = dateString;
                if (document.getElementById('greeting-text')) document.getElementById('greeting-text').innerText = greeting;
            }
            setInterval(updateClock, 1000);
            updateClock();
        });
    </script>
@endpush
