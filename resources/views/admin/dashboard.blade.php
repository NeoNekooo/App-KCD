@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
    @php
        $user = Auth::user();
    @endphp

    @push('styles')
        <style>
            .rounded-4 {
                border-radius: 1rem !important;
            }

            .rounded-5 {
                border-radius: 1.25rem !important;
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

            .hover-white:hover {
                color: white !important;
            }

            .list-group-item-action:hover {
                background-color: #f8f9fa;
                color: #696cff;
            }

            .hover-bg-light:hover {
                background-color: #f8f9fa;
                cursor: default;
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

            /* Style Table Sync Terbaru */
            .table-sync th {
                font-size: 0.75rem;
                text-transform: uppercase;
                font-weight: 700;
                color: #a1acb8;
                border-bottom: 2px solid #eef0f2;
            }

            .table-sync td {
                vertical-align: middle;
                border-bottom: 1px solid #f0f2f5;
                padding: 1rem 1.25rem;
            }

            .table-sync tbody tr:hover {
                background-color: #fcfdfe;
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
                                <div class="bg-white p-2 rounded-4 d-flex align-items-center justify-content-center shadow-lg"
                                    style="width: 130px; height: 130px; flex-shrink: 0;">
                                    @if (strtolower($user->role) === 'administrator' && is_null($user->instansi_id) && isset($appSettings['site_logo']))
                                        <img src="{{ asset('storage/' . $appSettings['site_logo']) }}"
                                            class="img-fluid rounded-3 w-100 h-100 object-fit-contain" alt="App Logo">
                                    @elseif (!empty($instansi->logo) && \Storage::disk('public')->exists($instansi->logo))
                                        <img src="{{ \Storage::url($instansi->logo) }}"
                                            class="img-fluid rounded-3 w-100 h-100 object-fit-contain" alt="Logo">
                                    @else
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($instansi->nama_instansi ?? ($appSettings['site_name'] ?? 'KCD')) }}&background=696cff&color=fff&size=128&font-size=0.33"
                                            class="img-fluid rounded-3" alt="Logo">
                                    @endif
                                </div>
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="badge bg-label-warning text-uppercase fw-bold"
                                            style="letter-spacing: 1px; font-size: 0.7rem;">
                                            {{ $appSettings['site_name_badge'] ?? 'SISTEM MONITORING WILAYAH' }}
                                        </span>
                                        <a href="{{ route('admin.instansi.index') }}" class="text-white-50 hover-white"
                                            data-bs-toggle="tooltip" title="Edit Profil Instansi">
                                            <i class='bx bx-edit-alt fs-5'></i>
                                        </a>
                                    </div>
                                    <h3 class="fw-bolder mb-1 text-white text-uppercase" style="letter-spacing: -0.5px;">
                                        @if (strtolower($user->role) === 'administrator' && is_null($user->instansi_id))
                                            {{ $appSettings['site_name_hero'] ?? 'MANAGEMENT KANTOR CABANG DINAS' }}
                                        @else
                                            {{ $instansi->nama_instansi ?? 'DASHBOARD KCD' }}
                                        @endif
                                    </h3>
                                    <p class="text-white-50 mb-0 fs-6">
                                        <span id="greeting-text">Halo</span>, <strong>{{ $user->name }}</strong> 👋
                                    </p>
                                </div>
                            </div>

                            <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center gap-3">
                                <div class="d-flex gap-3 glass-widget px-4 py-3 align-items-center">
                                    <div class="text-center border-end border-white border-opacity-25 pe-3">
                                        <h3 class="mb-0 fw-bolder text-warning lh-1">{{ $totalKabupaten ?? 0 }}</h3>
                                        <span class="text-white-50 text-uppercase fw-semibold"
                                            style="font-size: 0.65rem; letter-spacing: 0.5px;">Kab/Kota</span>
                                    </div>
                                    <div class="text-center">
                                        <h3 class="mb-0 fw-bolder text-info lh-1">{{ $totalKecamatan ?? 0 }}</h3>
                                        <span class="text-white-50 text-uppercase fw-semibold"
                                            style="font-size: 0.65rem; letter-spacing: 0.5px;">Kecamatan</span>
                                    </div>
                                </div>
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

            {{-- =================================================================== --}}
            {{-- SECTION 2: TAMPILAN KHUSUS ADMIN (DATA STATISTIK LENGKAP)           --}}
            {{-- =================================================================== --}}
            <div class="row g-2 mb-2 animate-fade-in-up" style="animation-delay: 0.1s;">

                {{-- 1. SATUAN PENDIDIKAN --}}
                <div class="col-xl-3 col-md-6">
                    <div class="card h-100 border-0 shadow-sm stat-card rounded-4">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <p class="text-muted mb-1 text-uppercase fw-bold small" style="letter-spacing: 0.5px;">
                                        Satuan Pendidikan</p>
                                    <h2 class="mb-0 fw-bolder text-info">{{ number_format($totalSekolah ?? 0) }}</h2>
                                </div>
                                <div
                                    class="avatar avatar-md bg-label-info rounded-circle d-flex align-items-center justify-content-center shadow-xs">
                                    <i class="bx bx-buildings fs-4"></i>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <span class="badge bg-label-success rounded-pill px-3 py-2"><i
                                        class="bx bx-check-shield me-1"></i> Negeri: {{ $totalNegeri ?? 0 }}</span>
                                <span class="badge bg-label-warning rounded-pill px-3 py-2"><i
                                        class="bx bx-home-heart me-1"></i> Swasta: {{ $totalSwasta ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 2. GURU --}}
                <div class="col-xl-3 col-md-6">
                    <div class="card h-100 border-0 shadow-sm stat-card rounded-4">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <p class="text-muted mb-1 text-uppercase fw-bold small" style="letter-spacing: 0.5px;">
                                        Tenaga Pendidik</p>
                                    <h2 class="mb-0 fw-bolder text-success">{{ number_format($totalGuru ?? 0) }}</h2>
                                </div>
                                <div
                                    class="avatar avatar-md bg-label-success rounded-circle d-flex align-items-center justify-content-center shadow-xs">
                                    <i class="bx bx-chalkboard fs-4"></i>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <span class="badge bg-label-success rounded-pill px-3 py-2">ASN: {{ $guruASN ?? 0 }}</span>
                                <span class="badge bg-label-secondary rounded-pill px-3 py-2">Non-ASN:
                                    {{ $guruNonASN ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 3. TENDIK --}}
                <div class="col-xl-3 col-md-6">
                    <div class="card h-100 border-0 shadow-sm stat-card rounded-4">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <p class="text-muted mb-1 text-uppercase fw-bold small" style="letter-spacing: 0.5px;">
                                        Tenaga Kependidikan</p>
                                    <h2 class="mb-0 fw-bolder text-warning">{{ number_format($totalTendik ?? 0) }}</h2>
                                </div>
                                <div
                                    class="avatar avatar-md bg-label-warning rounded-circle d-flex align-items-center justify-content-center shadow-xs">
                                    <i class="bx bx-support fs-4"></i>
                                </div>
                            </div>
                            <div class="d-flex gap-2 mt-2">
                                <span class="badge bg-label-warning rounded-pill px-3 py-2">ASN:
                                    {{ $tendikASN ?? 0 }}</span>
                                <span class="badge bg-label-secondary rounded-pill px-3 py-2">Non-ASN:
                                    {{ $tendikNonASN ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 4. SISWA --}}
                <div class="col-xl-3 col-md-6">
                    <div class="card h-100 border-0 shadow-sm stat-card rounded-4">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <div>
                                    <p class="text-muted mb-1 text-uppercase fw-bold small" style="letter-spacing: 0.5px;">
                                        Peserta Didik</p>
                                    <h2 class="mb-0 fw-bolder text-primary">{{ number_format($totalSiswa ?? 0) }}</h2>
                                </div>
                                <div
                                    class="avatar avatar-md bg-label-primary rounded-circle d-flex align-items-center justify-content-center shadow-xs">
                                    <i class="bx bx-group fs-4"></i>
                                </div>
                            </div>
                            @php
                                $tSiswa = $totalSiswa ?? 0;
                                $tLaki = $siswaLaki ?? 0;
                                $tPerempuan = $siswaPerempuan ?? 0;
                                $persenL = $tSiswa > 0 ? ($tLaki / $tSiswa) * 100 : 0;
                            @endphp
                            <div class="progress mt-3 rounded-pill" style="height: 8px;">
                                <div class="progress-bar bg-info" style="width: {{ $persenL }}%"
                                    data-bs-toggle="tooltip" title="Laki-laki: {{ number_format($tLaki) }}"></div>
                                <div class="progress-bar bg-danger" style="width: {{ 100 - $persenL }}%"
                                    data-bs-toggle="tooltip" title="Perempuan: {{ number_format($tPerempuan) }}"></div>
                            </div>
                            <div class="d-flex justify-content-between text-muted mt-2 fw-semibold"
                                style="font-size: 0.7rem;">
                                <span class="text-info"><i class="bx bx-male"></i> {{ number_format($tLaki) }}</span>
                                <span class="text-danger"><i class="bx bx-female"></i>
                                    {{ number_format($tPerempuan) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- CHART & PINTASAN --}}
            <div class="row g-2 mb-2 animate-fade-in-up" style="animation-delay: 0.2s;">

                {{-- KIRI: CHART BAR (FILTER ENABLED) --}}
                <div class="col-lg-8 d-flex flex-column">
                    <div class="card border-0 shadow-soft rounded-4 flex-grow-1">
                        <div
                            class="card-header bg-transparent border-bottom p-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <h5 class="card-title mb-0 fw-bold text-dark"><i
                                    class="bx bx-bar-chart-alt-2 text-primary me-2"></i>Sebaran Sekolah</h5>
                            <form action="{{ url()->current() }}" method="GET" class="m-0" id="formFilterChart">
                                <select name="filter_kabupaten" class="form-select form-select-sm border-1 shadow-sm"
                                    onchange="document.getElementById('formFilterChart').submit()"
                                    style="border-radius: 20px; font-weight: 600; color: #566a7f;">
                                    <option value="">- Semua Kab/Kota -</option>
                                    @foreach ($listKabupaten as $kab)
                                        <option value="{{ $kab }}"
                                            {{ request('filter_kabupaten') == $kab ? 'selected' : '' }}>
                                            {{ str_replace(['Kab. ', 'Kota '], '', $kab) }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                        <div class="card-body p-4 d-flex flex-column justify-content-center">
                            <div id="chartWilayah" style="min-height: 400px; width: 100%;"></div>
                        </div>
                    </div>
                </div>

                {{-- KANAN: QUICK LINKS + SINKRONISASI TERBARU --}}
                <div class="col-lg-4 d-flex flex-column gap-4">

                    {{-- 1. QUICK LINKS --}}
                    <div class="card border-0 shadow-soft rounded-4">
                        <div class="card-header bg-transparent border-bottom p-3">
                            <h6 class="card-title mb-0 fw-bold text-dark"><i
                                    class="bx bx-link text-primary me-2"></i>Pintasan Cepat</h6>
                        </div>
                        <div class="list-group list-group-flush rounded-bottom-4">
                            <a href="{{ route('admin.kesiswaan.siswa.index') }}"
                                class="list-group-item list-group-item-action p-3 border-bottom-0 d-flex gap-3 align-items-center transition-all">
                                <div
                                    class="avatar avatar-sm bg-label-primary rounded-circle d-flex justify-content-center align-items-center flex-shrink-0">
                                    <i class='bx bx-search'></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.9rem;">Pencarian Siswa</h6>
                                </div>
                                <i class="bx bx-chevron-right text-muted"></i>
                            </a>
                            <a href="{{ route('admin.sekolah.export-excel') }}" target="_blank"
                                class="list-group-item list-group-item-action p-3 border-bottom-0 d-flex gap-3 align-items-center border-top transition-all">
                                <div
                                    class="avatar avatar-sm bg-label-success rounded-circle d-flex justify-content-center align-items-center flex-shrink-0">
                                    <i class='bx bx-file text-success'></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.9rem;">Unduh Data Sekolah</h6>
                                </div>
                                <i class="bx bx-chevron-right text-muted"></i>
                            </a>
                        </div>
                    </div>

                    {{-- 2. SINKRONISASI TERBARU (HIGHLIGHTED) --}}
                    <div class="card border-0 shadow-soft rounded-4 flex-grow-1">
                        <div
                            class="card-header bg-transparent border-bottom p-3 d-flex justify-content-between align-items-center">
                            <h6 class="card-title mb-0 fw-bold text-dark"><i
                                    class="bx bx-time-five text-info me-2"></i>Data Sinkronisasi Terbaru</h6>
                            <span class="badge bg-label-info rounded-pill" style="font-size: 0.65rem;">5 Data
                                Terbaru</span>
                        </div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush rounded-bottom-4">
                                @forelse($sekolahTerbaru as $sekolah)
                                    <li
                                        class="list-group-item px-3 py-3 border-bottom-0 border-top hover-bg-light transition-all">
                                        <div class="d-flex justify-content-between align-items-start mb-1">
                                            <h6 class="mb-0 fw-bold text-dark text-truncate pe-2"
                                                style="font-size: 0.85rem;" title="{{ $sekolah->nama }}">
                                                {{ $sekolah->nama }}</h6>
                                            {{-- 🔥 HIGHLIGHT WAKTU SINKRONISASI 🔥 --}}
                                            <span
                                                class="badge bg-label-info fw-bold rounded-pill flex-shrink-0 animate-pulse"
                                                style="font-size: 0.65rem; border: 1px solid rgba(3, 195, 236, 0.2);">
                                                <i class="bx bx-sync bx-spin-hover me-1" style="font-size: 0.7rem;"></i>
                                                {{ $sekolah->terakhir_sinkron ? \Carbon\Carbon::parse($sekolah->terakhir_sinkron)->diffForHumans(null, true, true) : 'Baru' }}
                                            </span>
                                        </div>
                                        <div class="d-flex align-items-center mt-1">
                                            <i class="bx bx-map text-muted me-1" style="font-size: 0.75rem;"></i>
                                            <small class="text-muted text-truncate" style="font-size: 0.7rem;">
                                                {{ str_replace('Kec. ', '', $sekolah->kecamatan ?? '-') }},
                                                {{ str_replace(['Kab. ', 'Kota '], '', $sekolah->kabupaten_kota ?? '-') }}
                                            </small>
                                        </div>
                                    </li>
                                @empty
                                    <li
                                        class="list-group-item p-4 text-center text-muted small border-top border-bottom-0">
                                        Belum ada sinkronisasi.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    @endsection

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl)
                });

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

                if (document.querySelector("#chartWilayah")) {
                    var categories = {!! json_encode($chartCategories ?? []) !!};
                    var dataValues = {!! json_encode($chartData ?? []) !!};
                    var options = {
                        chart: {
                            type: 'bar',
                            height: 400,
                            toolbar: {
                                show: false
                            },
                            fontFamily: 'inherit',
                            animations: {
                                enabled: true,
                                easing: 'easeinout',
                                speed: 800
                            }
                        },
                        series: [{
                            name: 'Jumlah Sekolah',
                            data: dataValues
                        }],
                        colors: ['#696cff'],
                        plotOptions: {
                            bar: {
                                borderRadius: 6,
                                horizontal: true,
                                barHeight: '50%',
                                dataLabels: {
                                    position: 'bottom'
                                }
                            }
                        },
                        dataLabels: {
                            enabled: true,
                            textAnchor: 'start',
                            style: {
                                colors: ['#fff'],
                                fontSize: '12px',
                                fontWeight: 600
                            },
                            formatter: function(val) {
                                return val + " Sekolah"
                            },
                            offsetX: 10
                        },
                        xaxis: {
                            categories: categories,
                            labels: {
                                style: {
                                    colors: '#a1acb8',
                                    fontSize: '12px'
                                }
                            }
                        },
                        yaxis: {
                            labels: {
                                style: {
                                    colors: '#566a7f',
                                    fontSize: '13px',
                                    fontWeight: 500
                                }
                            }
                        },
                        grid: {
                            borderColor: '#f1f5f9',
                            strokeDashArray: 4,
                            xaxis: {
                                lines: {
                                    show: true
                                }
                            },
                            yaxis: {
                                lines: {
                                    show: false
                                }
                            }
                        },
                        tooltip: {
                            theme: 'light',
                            y: {
                                formatter: function(val) {
                                    return val + " Sekolah"
                                }
                            }
                        }
                    };
                    new ApexCharts(document.querySelector("#chartWilayah"), options).render();
                }
            });
        </script>
    @endpush
