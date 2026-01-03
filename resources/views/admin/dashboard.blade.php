@extends('layouts.admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- SECTION 1: HERO HEADER (KCD IDENTITY) --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card overflow-hidden" style="border-radius: 1rem; border:none; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
                <div class="card-body p-4 text-white" style="background: linear-gradient(135deg, #0f172a 0%, #334155 100%); position: relative;">
                    
                    <div style="position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
                    <div style="position: absolute; bottom: -30px; left: 10%; width: 100px; height: 100px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>

                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center position-relative z-1">
                        <div class="d-flex align-items-center gap-4">
                            {{-- Logo Wrapper --}}
                            <div class="bg-white p-2 rounded-circle d-flex align-items-center justify-content-center shadow-lg" style="width: 72px; height: 72px;">
                                @if(!empty($instansi->logo) && Storage::disk('public')->exists($instansi->logo))
                                    <img src="{{ Storage::url($instansi->logo) }}" class="img-fluid rounded-circle" alt="Logo">
                                @else
                                    <img src="https://ui-avatars.com/api/?name=KCD&background=0d6efd&color=fff&size=64" class="img-fluid rounded-circle" alt="Logo">
                                @endif
                            </div>
                            
                            <div>
                                <h6 class="text-warning mb-1 text-uppercase fw-bold ls-1" style="letter-spacing: 1px; font-size: 0.75rem;">Sistem Monitoring Wilayah</h6>
                                <h3 class="fw-bold mb-0 text-white">KANTOR CABANG DINAS (KCD)</h3>
                                <p class="text-white-50 mb-0 mt-1"><i class='bx bx-calendar me-1'></i> Tahun Pelajaran {{ $tahunAjaran }}</p>
                            </div>
                        </div>

                        {{-- Statistik Header --}}
                        <div class="d-flex gap-4 mt-3 mt-md-0 text-end border-start border-secondary ps-4 border-opacity-50">
                            <div>
                                <h2 class="mb-0 fw-bold text-white">{{ $totalSekolah }}</h2>
                                <small class="text-white-50">Satuan Pendidikan</small>
                            </div>
                            <div>
                                <h2 class="mb-0 fw-bold text-white">{{ $totalKecamatan }}</h2>
                                <small class="text-white-50">Kecamatan</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SECTION 2: STATISTIK UTAMA (CARDS) --}}
    <div class="row g-4 mb-4">
        
        {{-- CARD 1: SISWA --}}
        <div class="col-lg-4 col-md-6">
            <div class="card h-100 border-0 shadow-sm hover-up">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <p class="text-muted mb-1 text-uppercase fw-semibold" style="font-size: 0.75rem;">Total Peserta Didik</p>
                            <h2 class="mb-0 fw-bold text-primary">{{ number_format($totalSiswa) }}</h2>
                        </div>
                        <div class="avatar avatar-md bg-label-primary rounded p-2 d-flex align-items-center justify-content-center" style="background-color: rgba(105, 108, 255, 0.16); color: #696cff;">
                            <i class="bx bx-user fs-3"></i>
                        </div>
                    </div>
                    
                    {{-- Progress Bar Gender --}}
                    @php 
                        $persenL = $totalSiswa > 0 ? ($siswaLaki / $totalSiswa) * 100 : 0; 
                    @endphp
                    <div class="d-flex justify-content-between text-muted small mb-1">
                        <span><i class='bx bx-male-sign text-info'></i> L: <strong>{{ number_format($siswaLaki) }}</strong></span>
                        <span><i class='bx bx-female-sign text-danger'></i> P: <strong>{{ number_format($siswaPerempuan) }}</strong></span>
                    </div>
                    <div class="progress" style="height: 8px; border-radius: 4px;">
                        <div class="progress-bar bg-info" role="progressbar" style="width: {{ $persenL }}%" aria-valuenow="{{ $persenL }}" aria-valuemin="0" aria-valuemax="100"></div>
                        <div class="progress-bar bg-danger" role="progressbar" style="width: {{ 100 - $persenL }}%" aria-valuenow="{{ 100 - $persenL }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- CARD 2: GTK --}}
        <div class="col-lg-4 col-md-6">
            <div class="card h-100 border-0 shadow-sm hover-up">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <p class="text-muted mb-1 text-uppercase fw-semibold" style="font-size: 0.75rem;">Total Guru & Tendik</p>
                            <h2 class="mb-0 fw-bold text-success">{{ number_format($totalGuru) }}</h2>
                        </div>
                        <div class="avatar avatar-md bg-label-success rounded p-2 d-flex align-items-center justify-content-center" style="background-color: rgba(113, 221, 55, 0.16); color: #71dd37;">
                            <i class="bx bx-id-card fs-3"></i>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-4">
                        <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">ASN: {{ $guruASN }}</span>
                        <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill">Non-ASN: {{ $guruNonASN }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- CARD 3: WILAYAH --}}
        <div class="col-lg-4 col-md-12">
            <div class="card h-100 border-0 shadow-sm hover-up">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <p class="text-muted mb-1 text-uppercase fw-semibold" style="font-size: 0.75rem;">Cakupan Wilayah</p>
                            <h2 class="mb-0 fw-bold text-warning">{{ $totalKecamatan }} <span class="fs-6 text-muted fw-normal">Kecamatan</span></h2>
                        </div>
                        <div class="avatar avatar-md bg-label-warning rounded p-2 d-flex align-items-center justify-content-center" style="background-color: rgba(255, 171, 0, 0.16); color: #ffab00;">
                            <i class="bx bx-map-alt fs-3"></i>
                        </div>
                    </div>
                    <p class="text-muted small mb-0 mt-3">
                        <i class='bx bx-info-circle me-1'></i> Jumlah kecamatan berdasarkan domisili siswa aktif saat ini.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- SECTION 3: GRAFIK & MENU --}}
    <div class="row g-4">
        {{-- GRAFIK SEBARAN --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center p-4">
                    <div>
                        <h5 class="card-title mb-0 fw-bold">Sebaran Siswa per Kecamatan</h5>
                        <small class="text-muted">Top 5 Wilayah dengan jumlah siswa terbanyak (Data Dummy)</small>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown"><i class='bx bx-filter-alt me-1'></i> Filter</button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Tahun Ini</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div id="chartWilayah" style="min-height: 350px;"></div>
                </div>
            </div>
        </div>

        {{-- MENU CEPAT --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-bottom p-4">
                    <h5 class="card-title mb-0 fw-bold">Menu Monitoring</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('admin.kesiswaan.siswa.index') }}" class="list-group-item list-group-item-action d-flex align-items-center gap-3 p-4 border-bottom-0 hover-bg-light">
                            <div class="p-2 rounded bg-primary bg-opacity-10 text-primary">
                                <i class="bx bx-search fs-4"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0 fw-semibold">Cari Data Siswa</h6>
                                <small class="text-muted">Filter detail per sekolah/wilayah</small>
                            </div>
                            <i class='bx bx-chevron-right text-muted'></i>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action d-flex align-items-center gap-3 p-4 border-bottom-0 hover-bg-light">
                            <div class="p-2 rounded bg-info bg-opacity-10 text-info">
                                <i class="bx bx-bar-chart-alt-2 fs-4"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0 fw-semibold">Analisis Data</h6>
                                <small class="text-muted">Statistik & Grafik mendalam</small>
                            </div>
                            <i class='bx bx-chevron-right text-muted'></i>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action d-flex align-items-center gap-3 p-4 hover-bg-light">
                            <div class="p-2 rounded bg-success bg-opacity-10 text-success">
                                <i class="bx bx-download fs-4"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0 fw-semibold">Unduh Laporan</h6>
                                <small class="text-muted">Export Excel/PDF untuk Dinas</small>
                            </div>
                            <i class='bx bx-chevron-right text-muted'></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Hover Animation */
    .hover-up {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .hover-up:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important;
    }
    
    /* Background Colors for Icons */
    .bg-label-primary { background-color: #e7e7ff !important; color: #696cff !important; }
    .bg-label-success { background-color: #e8fadf !important; color: #71dd37 !important; }
    .bg-label-warning { background-color: #fff2d6 !important; color: #ffab00 !important; }
    .bg-label-info    { background-color: #d7f5fc !important; color: #03c3ec !important; }

    .hover-bg-light:hover {
        background-color: #f8f9fa;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // --- DUMMY DATA UNTUK TAMPILAN ---
        // Anda bisa mengganti ini nanti dengan @json($chartCategories) saat data sudah banyak
        var categories = ['Kec. Cibinong', 'Kec. Citeureup', 'Kec. Bojonggede', 'Kec. Sukaraja', 'Kec. Babakan Madang'];
        
        // Data Dummy Jumlah Siswa
        var data = [1250, 980, 850, 740, 620];

        // CONFIG CHART
        var options = {
            chart: {
                type: 'bar',
                height: 350,
                fontFamily: 'inherit',
                toolbar: { show: false }
            },
            series: [{
                name: 'Jumlah Siswa',
                data: data
            }],
            colors: ['#696cff'], // Warna Ungu Khas Template
            plotOptions: {
                bar: {
                    borderRadius: 6,       
                    horizontal: true,      
                    barHeight: '45%',      // PENTING: Mengatur ketebalan bar agar estetik
                    distributed: false
                }
            },
            dataLabels: {
                enabled: true,
                textAnchor: 'start',
                style: { colors: ['#fff'] },
                formatter: function (val, opt) {
                    return val
                },
                offsetX: 0,
            },
            xaxis: {
                categories: categories,
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: {
                    style: { colors: '#a1acb8', fontSize: '12px' }
                }
            },
            yaxis: {
                labels: {
                    style: { colors: '#697a8d', fontSize: '13px', fontWeight: 600 }
                }
            },
            grid: {
                borderColor: '#f1f1f1',
                padding: { top: 0, right: 0, bottom: 0, left: 10 }
            },
            tooltip: {
                theme: 'light',
                y: {
                    formatter: function (val) { return val + " Siswa" }
                }
            }
        };
        
        var chart = new ApexCharts(document.querySelector("#chartWilayah"), options);
        chart.render();
    });
</script>
@endpush