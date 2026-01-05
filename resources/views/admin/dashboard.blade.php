@extends('layouts.admin')

@section('content')

{{-- SECTION 1: HERO HEADER --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card overflow-hidden border-0 shadow-sm" style="border-radius: 1rem;">
            <div class="card-body p-4 text-white" style="background: linear-gradient(135deg, #0f172a 0%, #334155 100%); position: relative;">
                <div style="position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
                
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center position-relative z-1">
                    <div class="d-flex align-items-center gap-4">
                        <div class="bg-white p-1 rounded-circle d-flex align-items-center justify-content-center shadow-lg" style="width: 90px; height: 90px; flex-shrink: 0;">
                            @if(!empty($instansi->logo) && \Storage::disk('public')->exists($instansi->logo))
                                <img src="{{ \Storage::url($instansi->logo) }}" class="img-fluid rounded-circle w-100 h-100 object-fit-cover" alt="Logo">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($instansi->nama_instansi ?? 'KCD') }}&background=0d6efd&color=fff&size=128" class="img-fluid rounded-circle" alt="Logo">
                            @endif
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <h6 class="text-warning mb-0 text-uppercase fw-bold ls-1" style="letter-spacing: 1px; font-size: 0.75rem;">Sistem Monitoring Wilayah</h6>
                                <a href="{{ route('admin.instansi.index') }}" class="text-white-50 hover-white" data-bs-toggle="tooltip" title="Edit Profil Instansi">
                                    <i class='bx bx-edit-alt fs-6'></i>
                                </a>
                            </div>
                            <h3 class="fw-bold mb-0 text-white text-uppercase">
                                {{ $instansi->nama_instansi ?? 'DASHBOARD KCD' }}
                            </h3>
                            <p class="text-white-50 mb-0 mt-1"><i class='bx bx-calendar me-1'></i> Tahun Pelajaran {{ $tahunAjaran }}</p>
                        </div>
                    </div>

                    {{-- STATISTIK WILAYAH DI HEADER (Yang Dibulatin Merah) --}}
                    <div class="d-flex gap-4 mt-4 mt-md-0 text-end">
                        <div class="px-md-3 border-end border-secondary border-opacity-50">
                            <h2 class="mb-0 fw-bold text-warning">{{ $totalKabupaten }}</h2>
                            <small class="text-white-50 text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Kab/Kota</small>
                        </div>
                        <div class="ps-md-1">
                            <h2 class="mb-0 fw-bold text-info">{{ $totalKecamatan }}</h2>
                            <small class="text-white-50 text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Kecamatan</small>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

{{-- SECTION 2: STATISTIK 4 KOTAK --}}
<div class="row g-4 mb-4">
    
    {{-- 1. SATUAN PENDIDIKAN (Negeri vs Swasta) --}}
    <div class="col-xl-3 col-md-6">
        <div class="card h-100 border-0 shadow-sm hover-up">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <p class="text-muted mb-1 text-uppercase fw-bold small">Satuan Pendidikan</p>
                        <h2 class="mb-0 fw-bold text-info">{{ number_format($totalSekolah) }}</h2>
                    </div>
                    <div class="avatar avatar-md bg-label-info rounded p-2">
                        <i class="bx bx-home-alt fs-4"></i>
                    </div>
                </div>
                
                {{-- STATUS NEGERI / SWASTA (Pengganti Wilayah) --}}
                <div class="mt-3 d-flex gap-2">
                    <span class="badge bg-label-success rounded-pill">Negeri: {{ $totalNegeri }}</span>
                    <span class="badge bg-label-warning rounded-pill">Swasta: {{ $totalSwasta }}</span>
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
                        <h2 class="mb-0 fw-bold text-success">{{ number_format($totalGuru) }}</h2>
                    </div>
                    <div class="avatar avatar-md bg-label-success rounded p-2">
                        <i class="bx bx-id-card fs-4"></i>
                    </div>
                </div>
                <div class="mt-3 d-flex gap-2">
                    <span class="badge bg-label-success rounded-pill">ASN: {{ $guruASN }}</span>
                    <span class="badge bg-label-warning rounded-pill">Non: {{ $guruNonASN }}</span>
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
                        <h2 class="mb-0 fw-bold text-warning">{{ number_format($totalTendik) }}</h2>
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
                        <h2 class="mb-0 fw-bold text-primary">{{ number_format($totalSiswa) }}</h2>
                    </div>
                    <div class="avatar avatar-md bg-label-primary rounded p-2">
                        <i class="bx bx-user fs-4"></i>
                    </div>
                </div>
                @php $persenL = $totalSiswa > 0 ? ($siswaLaki / $totalSiswa) * 100 : 0; @endphp
                <div class="progress mt-3" style="height: 6px;">
                    <div class="progress-bar bg-info" style="width: {{ $persenL }}%"></div>
                    <div class="progress-bar bg-danger" style="width: {{ 100 - $persenL }}%"></div>
                </div>
                <div class="d-flex justify-content-between text-muted small mt-2">
                    <span>L: {{ number_format($siswaLaki) }}</span>
                    <span>P: {{ number_format($siswaPerempuan) }}</span>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- SECTION 3: GRAFIK & PINTASAN --}}
<div class="row g-4">
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
                <a href="{{ route('admin.kesiswaan.siswa.index') }}" class="list-group-item list-group-item-action p-4 border-bottom-0 d-flex gap-3 align-items-center hover-bg-light">
                    <div class="p-2 bg-label-primary rounded"><i class='bx bx-search'></i></div>
                    <div><h6 class="mb-0">Cari Data Siswa</h6><small class="text-muted">Pencarian Detail</small></div>
                </a>
                <a href="{{ route('admin.sekolah.export-excel') }}" target="_blank" class="list-group-item list-group-item-action p-4 border-bottom-0 d-flex gap-3 align-items-center hover-bg-light">
                    <div class="p-2 bg-label-success rounded"><i class='bx bx-download'></i></div>
                    <div><h6 class="mb-0">Data Sekolah</h6><small class="text-muted">Unduh Excel</small></div>
                </a>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .bg-label-primary { background-color: rgba(105, 108, 255, 0.1) !important; color: #696cff !important; }
    .bg-label-success { background-color: rgba(113, 221, 55, 0.1) !important; color: #71dd37 !important; }
    .bg-label-info    { background-color: rgba(3, 195, 236, 0.1) !important; color: #03c3ec !important; }
    .bg-label-warning { background-color: rgba(255, 171, 0, 0.1) !important; color: #ffab00 !important; }
    
    .hover-up:hover { transform: translateY(-4px); transition: all 0.3s ease; }
    .hover-white:hover { color: white !important; }
    .hover-bg-light:hover { background-color: #f8f9fa; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var categories = {!! json_encode($chartCategories) !!}; 
        var dataValues = {!! json_encode($chartData) !!}; 

        var options = {
            chart: { 
                type: 'bar', 
                height: 350, 
                toolbar: { show: false }, 
                fontFamily: 'inherit' 
            },
            series: [{ name: 'Jumlah Sekolah', data: dataValues }],
            colors: ['#696cff'],
            plotOptions: { bar: { borderRadius: 5, horizontal: true, barHeight: '50%' } },
            dataLabels: { enabled: true, textAnchor: 'start', style: { colors: ['#fff'] }, offsetX: 0 },
            xaxis: { categories: categories },
            grid: { borderColor: '#f1f1f1' }
        };
        
        var chart = new ApexCharts(document.querySelector("#chartWilayah"), options);
        chart.render();

        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
          return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>
@endpush