@extends('layouts.admin')

@section('content')
    @php
        $user = Auth::user();

        // -------------------------------------------------------------
        // LOGIC ROLE CHECK
        // Admin = Role 'Admin' atau 'Operator KCD'
        // Pegawai = Semua user selain Admin/Operator (Kasubag, Staff, dll)
        // -------------------------------------------------------------
        $isAdmin = $user->role === 'Admin' || $user->role === 'Operator KCD';
        $isPegawai = !$isAdmin; // Kebalikan dari Admin
    @endphp
        <div class="row mb-4">
            <div class="col-12">
                <div class="card overflow-hidden border-0 shadow-sm" style="border-radius: 1rem;">
                    <div class="card-body p-4 text-white"
                        style="background: linear-gradient(135deg, #0f172a 0%, #334155 100%); position: relative;">

                        {{-- Dekorasi Background --}}
                        <div
                            style="position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; background: rgba(255,255,255,0.05); border-radius: 50%;">
                        </div>

                        <div
                            class="d-flex flex-column flex-xl-row justify-content-between align-items-center position-relative z-1">

                            {{-- BAGIAN KIRI: IDENTITAS INSTANSI --}}
                            <div class="d-flex align-items-center gap-4 mb-4 mb-xl-0">
                                <div class="bg-white p-1 rounded-circle d-flex align-items-center justify-content-center shadow-lg"
                                    style="width: 90px; height: 90px; flex-shrink: 0;">
                                    @if (!empty($instansi->logo) && \Storage::disk('public')->exists($instansi->logo))
                                        <img src="{{ \Storage::url($instansi->logo) }}"
                                            class="img-fluid rounded-circle w-100 h-100 object-fit-cover" alt="Logo">
                                    @else
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($instansi->nama_instansi ?? 'KCD') }}&background=0d6efd&color=fff&size=128"
                                            class="img-fluid rounded-circle" alt="Logo">
                                    @endif
                                </div>
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <h6 class="text-warning mb-0 text-uppercase fw-bold ls-1"
                                            style="letter-spacing: 1px; font-size: 0.75rem;">
                                            {{ $isPegawai ? 'PORTAL PEGAWAI' : 'SISTEM MONITORING WILAYAH' }}
                                        </h6>
                                        @if ($isAdmin)
                                            <a href="{{ route('admin.instansi.index') }}" class="text-white-50 hover-white"
                                                data-bs-toggle="tooltip" title="Edit Profil Instansi">
                                                <i class='bx bx-edit-alt fs-6'></i>
                                            </a>
                                        @endif
                                    </div>
                                    <h3 class="fw-bold mb-0 text-white text-uppercase">
                                        {{ $instansi->nama_instansi ?? 'DASHBOARD KCD' }}
                                    </h3>
                                    <p class="text-white-50 mb-0 mt-1">
                                        <i class='bx bx-calendar me-1'></i> <span id="greeting-text">Halo</span>,
                                        <strong>{{ $user->name }}</strong>
                                    </p>
                                </div>
                            </div>

                            {{-- BAGIAN KANAN: JAM REALTIME & STATISTIK WILAYAH --}}
                            <div class="d-flex flex-column flex-md-row align-items-center gap-4 text-center text-md-end">

                                {{-- 1. JAM REALTIME --}}
                                <div class="{{ $isAdmin ? 'pe-md-4 border-end-md border-white border-opacity-25' : '' }}">
                                    <h2 class="mb-0 fw-bold text-white" id="realtime-clock"
                                        style="font-family: monospace; letter-spacing: 1px;">--:--:--</h2>
                                    <small class="text-white-50" id="realtime-date">Loading...</small>
                                </div>

                                {{-- 2. STATISTIK WILAYAH (KHUSUS ADMIN) --}}
                                @if ($isAdmin)
                                    <div class="d-flex gap-4 animate__animated animate__fadeIn">
                                        <div class="px-2">
                                            <h2 class="mb-0 fw-bold text-warning">{{ $totalKabupaten ?? 0 }}</h2>
                                            <small class="text-white-50 text-uppercase fw-semibold"
                                                style="font-size: 0.7rem;">Kab/Kota</small>
                                        </div>
                                        <div class="ps-2">
                                            <h2 class="mb-0 fw-bold text-info">{{ $totalKecamatan ?? 0 }}</h2>
                                            <small class="text-white-50 text-uppercase fw-semibold"
                                                style="font-size: 0.7rem;">Kecamatan</small>
                                        </div>
                                    </div>
                                @endif

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- =================================================================== --}}
        {{-- SECTION 2: TAMPILAN KHUSUS ADMIN (DATA STATISTIK LENGKAP)           --}}
        {{-- =================================================================== --}}
        @if ($isAdmin)
            <div class="row g-4 mb-4 animate__animated animate__fadeIn">

                {{-- 1. SATUAN PENDIDIKAN --}}
                <div class="col-xl-3 col-md-6">
                    <div class="card h-100 border-0 shadow-sm hover-up">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <p class="text-muted mb-1 text-uppercase fw-bold small">Satuan Pendidikan</p>
                                    <h2 class="mb-0 fw-bold text-info">{{ number_format($totalSekolah ?? 0) }}</h2>
                                </div>
                                <div class="avatar avatar-md bg-label-info rounded p-2">
                                    <i class="bx bx-home-alt fs-4"></i>
                                </div>
                            </div>
                            <div class="mt-3 d-flex gap-2">
                                <span class="badge bg-label-success rounded-pill">Negeri: {{ $totalNegeri ?? 0 }}</span>
                                <span class="badge bg-label-warning rounded-pill">Swasta: {{ $totalSwasta ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 2. GURU --}}
                <div class="col-xl-3 col-md-6">
                    <div class="card h-100 border-0 shadow-sm hover-up">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <p class="text-muted mb-1 text-uppercase fw-bold small">Guru</p>
                                    <h2 class="mb-0 fw-bold text-success">{{ number_format($totalGuru ?? 0) }}</h2>
                                </div>
                                <div class="avatar avatar-md bg-label-success rounded p-2">
                                    <i class="bx bx-id-card fs-4"></i>
                                </div>
                            </div>
                            <div class="mt-3 d-flex gap-2">
                                <span class="badge bg-label-success rounded-pill">ASN: {{ $guruASN ?? 0 }}</span>
                                <span class="badge bg-label-warning rounded-pill">Non: {{ $guruNonASN ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 3. TENDIK --}}
                <div class="col-xl-3 col-md-6">
                    <div class="card h-100 border-0 shadow-sm hover-up">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <p class="text-muted mb-1 text-uppercase fw-bold small">Tendik</p>
                                    <h2 class="mb-0 fw-bold text-warning">{{ number_format($totalTendik ?? 0) }}</h2>
                                </div>
                                <div class="avatar avatar-md bg-label-warning rounded p-2">
                                    <i class="bx bx-support fs-4"></i>
                                </div>
                            </div>
                            <p class="text-muted small mt-3 mb-0">Tenaga Kependidikan & Staff</p>
                        </div>
                    </div>
                </div>

                {{-- 4. SISWA --}}
                <div class="col-xl-3 col-md-6">
                    <div class="card h-100 border-0 shadow-sm hover-up">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <p class="text-muted mb-1 text-uppercase fw-bold small">Peserta Didik</p>
                                    <h2 class="mb-0 fw-bold text-primary">{{ number_format($totalSiswa ?? 0) }}</h2>
                                </div>
                                <div class="avatar avatar-md bg-label-primary rounded p-2">
                                    <i class="bx bx-user fs-4"></i>
                                </div>
                            </div>
                            @php
                                $tSiswa = $totalSiswa ?? 0;
                                $tLaki = $siswaLaki ?? 0;
                                $tPerempuan = $siswaPerempuan ?? 0;
                                $persenL = $tSiswa > 0 ? ($tLaki / $tSiswa) * 100 : 0;
                            @endphp
                            <div class="progress mt-3" style="height: 6px;">
                                <div class="progress-bar bg-info" style="width: {{ $persenL }}%"></div>
                                <div class="progress-bar bg-danger" style="width: {{ 100 - $persenL }}%"></div>
                            </div>
                            <div class="d-flex justify-content-between text-muted small mt-2">
                                <span>L: {{ number_format($tLaki) }}</span>
                                <span>P: {{ number_format($tPerempuan) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- CHART & PINTASAN MENU --}}
            <div class="row g-4 animate__animated animate__fadeInUp">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-transparent border-bottom p-4">
                            <h5 class="card-title mb-0 fw-bold">Sebaran Sekolah per Kecamatan</h5>
                        </div>
                        <div class="card-body p-4">
                            <div id="chartWilayah" style="min-height: 350px;"></div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-transparent border-bottom p-4">
                            <h5 class="card-title mb-0 fw-bold">Pintasan Menu</h5>
                        </div>
                        <div class="list-group list-group-flush">
                            <a href="{{ route('admin.kesiswaan.siswa.index') }}"
                                class="list-group-item list-group-item-action p-4 border-bottom-0 d-flex gap-3 align-items-center hover-bg-light">
                                <div class="p-2 bg-label-primary rounded"><i class='bx bx-search'></i></div>
                                <div>
                                    <h6 class="mb-0">Cari Data Siswa</h6><small class="text-muted">Pencarian
                                        Detail</small>
                                </div>
                            </a>
                            <a href="{{ route('admin.sekolah.export-excel') }}" target="_blank"
                                class="list-group-item list-group-item-action p-4 border-bottom-0 d-flex gap-3 align-items-center hover-bg-light">
                                <div class="p-2 bg-label-success rounded"><i class='bx bx-download'></i></div>
                                <div>
                                    <h6 class="mb-0">Data Sekolah</h6><small class="text-muted">Unduh Excel</small>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- =================================================================== --}}
        {{-- SECTION 3: TAMPILAN KHUSUS PEGAWAI (WORKSPACE SIMPLE)               --}}
        {{-- =================================================================== --}}
        @if ($isPegawai)
            <div class="row g-4 animate__animated animate__fadeIn">

                {{-- KARTU UTAMA: WELCOME & ACTION --}}
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm h-100 bg-white">
                        <div
                            class="card-body p-5 text-center d-flex flex-column justify-content-center align-items-center">
                            <div class="mb-4 bg-label-primary rounded-circle p-4 animate__animated animate__pulse animate__infinite"
                                style="width: 120px; height: 120px; display: flex; align-items: center; justify-content: center;">
                                <i class='bx bx-file-find' style="font-size: 60px;"></i>
                            </div>
                            <h4 class="fw-bold text-dark mb-2">Selamat Bekerja, {{ Auth::user()->name }}!</h4>
                            <p class="text-muted" style="max-width: 500px;">
                                Anda memiliki akses sebagai <strong>Verifikator</strong>. Silakan periksa menu layanan di
                                sidebar untuk memulai verifikasi dokumen pengajuan atau gunakan tombol cepat di bawah.
                            </p>

                            <div class="d-flex justify-content-center gap-3 mt-4">
                                {{-- Tombol Pintas ke Layanan Verifikasi --}}
                                <a href="{{ $verifikasiLink }}"
                                    class="btn btn-primary px-4 shadow-sm hover-up">
                                    <i class='bx bx-task me-2'></i> Mulai Tugas
                                </a>
                                <a href="{{ route('admin.profil-saya.show') }}"
                                    class="btn btn-outline-secondary px-4 hover-up">
                                    <i class='bx bx-user me-2'></i> Profil Saya
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- KARTU STATUS --}}
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100 bg-primary text-white position-relative overflow-hidden">
                        {{-- Decoration --}}
                        <div style="position: absolute; right: -30px; top: -30px; opacity: 0.1;">
                            <i class='bx bx-check-shield' style="font-size: 200px;"></i>
                        </div>

                        <div class="card-body p-4 d-flex flex-column justify-content-between position-relative z-1">
                            <div>
                                <h5 class="fw-bold text-white mb-3 d-flex align-items-center">
                                    <i class='bx bx-info-circle me-2'></i> Status Verifikator
                                </h5>
                                <p class="text-white-50 mb-0 small">
                                    Pastikan Anda selalu memeriksa kelengkapan dokumen sesuai dengan Juknis terbaru sebelum
                                    memberikan persetujuan (ACC).
                                </p>
                            </div>

                            <div class="mt-4">
                                <div class="p-3 bg-white bg-opacity-10 rounded d-flex align-items-center gap-3">
                                    <div
                                        class="bg-white text-primary rounded p-2 d-flex align-items-center justify-content-center">
                                        <i class='bx bx-user-check fs-4'></i>
                                    </div>
                                    <div>
                                        <small class="d-block text-white-50">Akun Aktif</small>
                                        <h6 class="mb-0 fw-bold">{{ Auth::user()->name }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
@endsection

{{-- =================================================================== --}}
{{-- STYLES TAMBAHAN                                                     --}}
{{-- =================================================================== --}}
@push('styles')
    <style>
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

        .hover-up:hover {
            transform: translateY(-4px);
            transition: all 0.3s ease;
        }

        .hover-white:hover {
            color: white !important;
        }

        .hover-bg-light:hover {
            background-color: #f8f9fa;
        }

        /* Divider responsif di header */
        @media (min-width: 768px) {
            .border-end-md {
                border-right: 1px solid rgba(255, 255, 255, 0.25) !important;
            }
        }
    </style>
@endpush

{{-- =================================================================== --}}
{{-- SCRIPTS (CHART & JAM REALTIME)                                      --}}
{{-- =================================================================== --}}
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            // 1. JAM REALTIME
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

                const clockEl = document.getElementById('realtime-clock');
                const dateEl = document.getElementById('realtime-date');
                const greetEl = document.getElementById('greeting-text');

                if (clockEl) clockEl.innerText = timeString;
                if (dateEl) dateEl.innerText = dateString;
                if (greetEl) greetEl.innerText = greeting;
            }
            setInterval(updateClock, 1000);
            updateClock();

            // 2. CHART (Hanya dirender jika elemennya ada / Admin Only)
            if (document.querySelector("#chartWilayah")) {
                var categories = {!! json_encode($chartCategories ?? []) !!};
                var dataValues = {!! json_encode($chartData ?? []) !!};

                var options = {
                    chart: {
                        type: 'bar',
                        height: 350,
                        toolbar: {
                            show: false
                        },
                        fontFamily: 'inherit'
                    },
                    series: [{
                        name: 'Jumlah Sekolah',
                        data: dataValues
                    }],
                    colors: ['#696cff'],
                    plotOptions: {
                        bar: {
                            borderRadius: 5,
                            horizontal: true,
                            barHeight: '50%'
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        textAnchor: 'start',
                        style: {
                            colors: ['#fff']
                        },
                        offsetX: 0
                    },
                    xaxis: {
                        categories: categories
                    },
                    grid: {
                        borderColor: '#f1f1f1'
                    }
                };

                var chart = new ApexCharts(document.querySelector("#chartWilayah"), options);
                chart.render();
            }

            // 3. Tooltip Bootstrap
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        });
    </script>
@endpush