@extends('layouts.admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- SECTION 1: HERO HEADER (Dinamis dari Profil Instansi) --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card overflow-hidden" style="border-radius: 1rem; border:none; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
                <div class="card-body p-4 text-white" style="background: linear-gradient(135deg, #0f172a 0%, #334155 100%); position: relative;">
                    
                    <div style="position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
                    
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center position-relative z-1">
                        <div class="d-flex align-items-center gap-4">
                            {{-- Logo Dinamis - Ukuran Diperbesar ke 90px --}}
                            <div class="bg-white p-2 rounded-circle d-flex align-items-center justify-content-center shadow-lg" style="width: 90px; height: 90px; flex-shrink: 0;">
                                @if(!empty($instansi->logo) && \Storage::disk('public')->exists($instansi->logo))
                                    <img src="{{ \Storage::url($instansi->logo) }}" class="img-fluid rounded-circle w-100 h-100 object-fit-cover" alt="Logo">
                                @else
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($instansi->nama_instansi ?? 'KCD') }}&background=0d6efd&color=fff&size=128" class="img-fluid rounded-circle" alt="Logo">
                                @endif
                            </div>

                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <h6 class="text-warning mb-0 text-uppercase fw-bold ls-1" style="letter-spacing: 1px; font-size: 0.75rem;">Sistem Monitoring Wilayah</h6>
                                    {{-- Tombol Edit Profil Akses Cepat --}}
                                    <a href="{{ route('admin.instansi.index') }}" class="text-white-50 hover-white" data-bs-toggle="tooltip" title="Edit Profil Instansi">
                                        <i class='bx bx-edit-alt fs-6'></i>
                                    </a>
                                </div>
                                {{-- Nama Instansi Dinamis --}}
                                <h3 class="fw-bold mb-0 text-white text-uppercase">
                                    {{ $instansi->nama_instansi ?? 'DASHBOARD KCD' }}
                                </h3>
                                <p class="text-white-50 mb-0 mt-1"><i class='bx bx-calendar me-1'></i> Tahun Pelajaran {{ $tahunAjaran }}</p>
                            </div>
                        </div>
                        <div class="text-end mt-3 mt-md-0 border-start border-secondary ps-4 border-opacity-50">
                            <h2 class="mb-0 fw-bold text-white">{{ $totalSekolah }}</h2>
                            <small class="text-white-50">Total Satuan Pendidikan</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SECTION 2: STATISTIK UTAMA (4 KOTAK) --}}
    <div class="row g-4 mb-4">
        
        {{-- KOTAK 1: SISWA --}}
        <div class="col-xl-3 col-md-6">
            <div class="card h-100 border-0 shadow-sm hover-up">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <p class="text-muted mb-1 text-uppercase fw-bold" style="font-size: 0.7rem;">Peserta Didik</p>
                            <h2 class="mb-0 fw-bold text-primary">{{ number_format($totalSiswa) }}</h2>
                        </div>
                        <div class="avatar avatar-md bg-label-primary rounded p-2 d-flex align-items-center justify-content-center">
                            <i class="bx bx-user fs-4"></i>
                        </div>
                    </div>
                    @php $persenL = $totalSiswa > 0 ? ($siswaLaki / $totalSiswa) * 100 : 0; @endphp
                    <div class="progress mt-3" style="height: 6px;">
                        <div class="progress-bar bg-info" style="width: {{ $persenL }}%"></div>
                        <div class="progress-bar bg-danger" style="width: {{ 100 - $persenL }}%"></div>
                    </div>
                    <div class="d-flex justify-content-between text-muted small mt-2">
                        <span>L: <strong>{{ number_format($siswaLaki) }}</strong></span>
                        <span>P: <strong>{{ number_format($siswaPerempuan) }}</strong></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- KOTAK 2: GURU --}}
        <div class="col-xl-3 col-md-6">
            <div class="card h-100 border-0 shadow-sm hover-up">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <p class="text-muted mb-1 text-uppercase fw-bold" style="font-size: 0.7rem;">Guru & Tendik</p>
                            <h2 class="mb-0 fw-bold text-success">{{ number_format($totalGuru) }}</h2>
                        </div>
                        <div class="avatar avatar-md bg-label-success rounded p-2 d-flex align-items-center justify-content-center">
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

        {{-- KOTAK 3: SEKOLAH --}}
        <div class="col-xl-3 col-md-6">
            <div class="card h-100 border-0 shadow-sm hover-up">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <p class="text-muted mb-1 text-uppercase fw-bold" style="font-size: 0.7rem;">Total Sekolah</p>
                            <h2 class="mb-0 fw-bold text-info">{{ number_format($totalSekolah) }}</h2>
                        </div>
                        <div class="avatar avatar-md bg-label-info rounded p-2 d-flex align-items-center justify-content-center">
                            <i class="bx bx-home-alt fs-4"></i>
                        </div>
                    </div>
                    <p class="text-muted small mb-0 mt-3">Satuan pendidikan aktif.</p>
                </div>
            </div>
        </div>

        {{-- KOTAK 4: WILAYAH --}}
        <div class="col-xl-3 col-md-6">
            <div class="card h-100 border-0 shadow-sm hover-up">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <p class="text-muted mb-1 text-uppercase fw-bold" style="font-size: 0.7rem;">Cakupan Wilayah</p>
                            <div class="d-flex align-items-baseline gap-2">
                                <h2 class="mb-0 fw-bold text-warning">{{ $totalKabupaten }}</h2>
                                <span class="text-muted small">Kab/Kota</span>
                            </div>
                        </div>
                        <div class="avatar avatar-md bg-label-warning rounded p-2 d-flex align-items-center justify-content-center">
                            <i class="bx bx-map fs-4"></i>
                        </div>
                    </div>
                    <div class="mt-3 pt-2 border-top border-light">
                         <div class="d-flex align-items-center gap-2">
                            <i class='bx bx-subdirectory-right text-muted'></i>
                            <h5 class="mb-0 fw-semibold text-dark">{{ $totalKecamatan }}</h5>
                            <span class="text-muted small">Kecamatan</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SECTION 3: GRAFIK --}}
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
                    <a href="{{ route('admin.kesiswaan.siswa.index') }}" class="list-group-item list-group-item-action p-4 border-bottom-0 d-flex gap-3 align-items-center">
                        <div class="p-2 bg-label-primary rounded"><i class='bx bx-search'></i></div>
                        <div><h6 class="mb-0">Cari Data Siswa</h6><small class="text-muted">Pencarian Detail</small></div>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action p-4 border-bottom-0 d-flex gap-3 align-items-center">
                        <div class="p-2 bg-label-success rounded"><i class='bx bx-download'></i></div>
                        <div><h6 class="mb-0">Unduh Laporan</h6><small class="text-muted">Excel / PDF</small></div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .bg-label-primary { background-color: #e7e7ff !important; color: #696cff !important; }
    .bg-label-success { background-color: #e8fadf !important; color: #71dd37 !important; }
    .bg-label-info    { background-color: #d7f5fc !important; color: #03c3ec !important; }
    .bg-label-warning { background-color: #fff2d6 !important; color: #ffab00 !important; }
    .hover-up:hover { transform: translateY(-5px); transition: 0.3s; }
    .hover-white:hover { color: white !important; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var categories = {!! json_encode($chartCategories) !!}; 
        var dataValues = {!! json_encode($chartData) !!}; 

        var options = {
            chart: { type: 'bar', height: 350, toolbar: { show: false } },
            series: [{ name: 'Jumlah Sekolah', data: dataValues }],
            colors: ['#696cff'],
            plotOptions: { bar: { borderRadius: 5, horizontal: true, barHeight: '50%' } },
            dataLabels: { enabled: true, textAnchor: 'start', style: { colors: ['#fff'] }, offsetX: 0 },
            xaxis: { categories: categories },
            grid: { borderColor: '#f1f1f1' }
        };
        
        var chart = new ApexCharts(document.querySelector("#chartWilayah"), options);
        chart.render();

        // Inisialisasi Tooltip
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
          return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>
@endpush