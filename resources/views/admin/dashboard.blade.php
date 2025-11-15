@extends('layouts.admin')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- Header Dashboard --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card hero-header">
                    <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                        <div>
                            <h4 class="hero-title mb-1">Selamat Datang di Dashboard Sekolah 🎓</h4>
                            <p class="hero-subtitle mb-0">Sistem Informasi Akademik - Tahun Pelajaran 2025/2026</p>
                        </div>
                        <div class="d-flex align-items-center">
                            <img src="../assets/img/illustrations/man-with-laptop-light.png" alt="School" height="88"
                                class="hero-img" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Ringkasan Data --}}
        <div class="row g-3 mb-4">
            <div class="col-lg-3 col-md-6 col-12">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="icon-circle icon-blue"><i class="bx bx-user fs-4"></i></div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Jumlah Siswa</span>
                        <h3 class="stat-number mb-1">1,256</h3>
                        <small class="text-success fw-semibold">Aktif</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-12">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="icon-circle icon-teal"><i class="bx bx-chalkboard fs-4"></i></div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Jumlah Guru</span>
                        <h3 class="stat-number mb-1">78</h3>
                        <small class="text-muted">Tetap & Honorer</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-12">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="icon-circle icon-green"><i class="bx bx-building fs-4"></i></div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Jumlah Kelas</span>
                        <h3 class="stat-number mb-1">36</h3>
                        <small class="text-muted">X - XII</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-12">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="icon-circle icon-yellow"><i class="bx bx-book fs-4"></i></div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Mata Pelajaran</span>
                        <h3 class="stat-number mb-1">52</h3>
                        <small class="text-muted">Produktif & Umum</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    </div>
@endsection

@push('styles')
    <style>
        /* Typography */
        .hero-title {
            font-size: 1.6rem;
            font-weight: 600;
            margin-bottom: .25rem;
        }

        .hero-subtitle {
            opacity: .95;
        }

        /* Hero header gradient */
        .hero-header {
            border-radius: .85rem;
            overflow: hidden;
            background: linear-gradient(135deg, #2563eb 0%, #20c997 100%);
            color: #fff;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            position: relative;
        }

        .hero-header::after {
            content: "";
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at top right, rgba(255, 255, 255, 0.2), transparent 70%);
        }

        .hero-img {
            opacity: 1;
            filter: drop-shadow(0 6px 14px rgba(0, 0, 0, .08));
        }

        /* Stat cards */
        .stat-card {
            border-radius: .75rem;
            border: 1px solid rgba(229, 231, 235, 0.6);
            transition: all .25s ease;
            background: #fff;
        }

        .stat-card:hover {
            transform: translateY(-6px) scale(1.02);
            box-shadow: 0 12px 30px rgba(59, 130, 246, 0.12);
        }

        .icon-circle {
            width: 46px;
            height: 46px;
            border-radius: .6rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            color: #fff;
        }

        .icon-blue {
            background: rgba(13, 110, 253, 0.12);
            color: #2563eb;
        }

        .icon-teal {
            background: rgba(32, 201, 151, 0.12);
            color: #0d9488;
        }

        .icon-green {
            background: rgba(16, 185, 129, 0.12);
            color: #10b981;
        }

        .icon-yellow {
            background: rgba(255, 193, 7, 0.12);
            color: #f59e0b;
        }

        .stat-number {
            font-size: 1.75rem;
            font-weight: 700;
            color: #111827;
        }

        /* Agenda list */
        .agenda-list .date-badge {
            min-width: 88px;
            text-align: center;
            padding: .45rem .65rem;
            border-radius: .55rem;
            font-weight: 700;
            font-size: .8rem;
            letter-spacing: .5px;
            color: #fff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: all .25s ease;
        }

        .agenda-list .date-badge:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        }

        .bg-primary {
            background: linear-gradient(135deg, #60a5fa, #2563eb);
        }

        .bg-info {
            background: linear-gradient(135deg, #38bdf8, #0ea5e9);
        }

        .bg-success {
            background: linear-gradient(135deg, #34d399, #059669);
        }

        .bg-warning {
            background: linear-gradient(135deg, #fcd34d, #f59e0b);
            color: #1f2937;
        }

        /* Button refine */
        .btn-outline-primary {
            border-radius: .5rem;
            font-weight: 600;
            transition: all .2s ease;
        }

        .btn-outline-primary:hover {
            background: #2563eb;
            border-color: #2563eb;
            color: #fff;
        }

        /* small responsive adjustments */
        @media (max-width: 767.98px) {
            .hero-header {
                padding: 1rem;
            }

            .hero-img {
                height: 64px;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // ApexCharts: Statistik Siswa
            var options = {
                chart: {
                    type: 'area',
                    height: 320,
                    toolbar: {
                        show: false
                    },
                    zoom: {
                        enabled: false
                    }
                },
                series: [{
                    name: 'Jumlah Siswa',
                    data: [1050, 1120, 1180, 1256]
                }],
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                markers: {
                    size: 4
                },
                grid: {
                    borderColor: '#e6e9ef'
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.35,
                        opacityTo: 0.05,
                        stops: [0, 90, 100]
                    }
                },
                colors: ['#2563eb'],
                xaxis: {
                    categories: ['2022', '2023', '2024', '2025'],
                    labels: {
                        style: {
                            colors: '#6b7280'
                        }
                    }
                },
                tooltip: {
                    theme: 'light'
                }
            };
            var chart = new ApexCharts(document.querySelector("#chartSiswa"), options);
            chart.render();
        });
    </script>
@endpush
