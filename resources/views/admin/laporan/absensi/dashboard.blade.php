@extends('layouts.admin')

@section('content')
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Laporan /</span> Dashboard Absensi</h4>

{{-- Bagian Kartu Statistik Utama --}}
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <h6 class="mb-1">Persentase Kehadiran Hari Ini</h6>
                <h2 class="mb-0 fw-bold text-primary">{{ $persentaseKehadiran }}%</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <h6 class="mb-1">Siswa Hadir Hari Ini</h6>
                <h2 class="mb-0 fw-bold">{{ $hadirHariIni }} <span class="fs-6 text-muted">/ {{ $totalSiswa }} Siswa</span></h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <h6 class="mb-1">Total Siswa Aktif</h6>
                <h2 class="mb-0 fw-bold">{{ $totalSiswa }}</h2>
            </div>
        </div>
    </div>
</div>

{{-- Bagian Grafik --}}
<div class="row g-4">
    {{-- Pie Chart --}}
    <div class="col-md-5">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Rekap Status Hari Ini</h5>
            </div>
            <div class="card-body">
                <canvas id="pieChartStatusHariIni"></canvas>
            </div>
        </div>
    </div>

    {{-- Bar Chart --}}
    <div class="col-md-7">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Tren Absensi (Sakit/Izin/Alfa) 7 Hari Terakhir</h5>
            </div>
            <div class="card-body">
                <canvas id="barChartTrenAbsensi"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Load library Chart.js dari CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Data dari Controller
    const rekapStatus = @json($rekapStatusHariIni);
    const labelsTren = @json($labelsTren);
    const dataTren = @json($dataTren);

    // --- Konfigurasi Pie Chart ---
    const pieChartCtx = document.getElementById('pieChartStatusHariIni').getContext('2d');
    new Chart(pieChartCtx, {
        type: 'doughnut', // doughnut lebih modern dari pie
        data: {
            labels: Object.keys(rekapStatus),
            datasets: [{
                label: 'Jumlah Siswa',
                data: Object.values(rekapStatus),
                backgroundColor: [
                    'rgba(40, 199, 111, 0.8)', // Hadir (Success)
                    'rgba(255, 159, 64, 0.8)', // Terlambat (Warning)
                    'rgba(0, 184, 255, 0.8)',  // Izin (Info)
                    'rgba(113, 102, 240, 0.8)',// Sakit (Primary)
                    'rgba(255, 77, 73, 0.8)'   // Alfa (Danger)
                ],
                borderColor: '#fff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed !== null) {
                                label += context.parsed + ' siswa';
                            }
                            return label;
                        }
                    }
                }
            }
        }
    });

    // --- Konfigurasi Bar Chart ---
    const barChartCtx = document.getElementById('barChartTrenAbsensi').getContext('2d');
    new Chart(barChartCtx, {
        type: 'bar',
        data: {
            labels: labelsTren,
            datasets: [{
                label: 'Jumlah Siswa Absen',
                data: dataTren,
                backgroundColor: 'rgba(255, 77, 73, 0.5)',
                borderColor: 'rgba(255, 77, 73, 1)',
                borderWidth: 1,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        // Hanya tampilkan angka bulat di sumbu Y
                        precision: 0
                    }
                }
            },
            plugins: {
                legend: {
                    display: false // Sembunyikan legenda karena sudah jelas
                }
            }
        }
    });
</script>
@endpush