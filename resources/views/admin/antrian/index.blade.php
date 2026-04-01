@extends('layouts.admin')
@section('title', 'Layanan Tamu KCD')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold m-0"><span class="text-muted fw-light">Dashboard /</span> Layanan Tamu</h4>
            <div class="text-muted small mt-1">Kelola Tiket Antrian Tamu Harian Kantor Cabang Dinas</div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.display.antrian') }}" target="_blank" class="btn btn-dark shadow-sm rounded-pill fw-bold">
                <i class='bx bx-tv me-2'></i>Buka Layar TV
            </a>
            <a href="{{ route('guest.buku-tamu') }}" target="_blank" class="btn btn-primary shadow-sm rounded-pill fw-bold">
                <i class='bx bx-qr-scan me-2'></i>Lihat QR Form
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
            <i class="bx bx-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-warning alert-dismissible fade show rounded-3 mb-4" role="alert">
            <i class="bx bx-error-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
            <h5 class="card-title m-0 fw-bold"><i class="bx bx-list-ul me-2 text-primary"></i>Daftar Tunggu Hari Ini</h5>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="fw-semibold text-uppercase font-size-13 py-3" style="width: 50px;">No</th>
                        <th class="fw-semibold text-uppercase font-size-13 py-3">No Antrian</th>
                        <th class="fw-semibold text-uppercase font-size-13 py-3">Tamu</th>
                        <th class="fw-semibold text-uppercase font-size-13 py-3">No HP</th>
                        <th class="fw-semibold text-uppercase font-size-13 py-3">Detail & Keperluan</th>
                        <th class="fw-semibold text-uppercase font-size-13 py-3">Status</th>
                        <th class="fw-semibold text-uppercase font-size-13 py-3 text-center">Aksi Panggilan</th>
                    </tr>
                </thead>
                <tbody id="antrianTableBody">
                    @include('admin.antrian._table_body')
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Audio -->
<audio id="bellSound" preload="auto">
    <source src="https://www.myinstants.com/media/sounds/elevator-ding.mp3" type="audio/mpeg">
</audio>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let currentCount = document.querySelectorAll('#antrianTableBody tr').length;
        const tableBody = document.getElementById('antrianTableBody');
        const bell = document.getElementById('bellSound');

        function refreshTable() {
            fetch("/admin/antrian/partial")
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    // Hitung jumlah <tr> yang datanya bukan "Belum ada antrian" (colspan=6)
                    const rows = doc.querySelectorAll('tr');
                    let newCount = rows.length;
                    
                    // Jika baris pertama punya colspan=7, berarti kosong
                    if(rows.length === 1 && rows[0].querySelector('td[colspan="7"]')) {
                        newCount = 0;
                    }

                    if (newCount > currentCount) {
                        if(bell) bell.play().catch(e => console.log("Audio play blocked"));
                    }
                    
                    currentCount = newCount;
                    tableBody.innerHTML = html;

                    // Re-init tooltips jika ada
                    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                        tooltipTriggerList.map(function (tooltipTriggerEl) {
                            return new bootstrap.Tooltip(tooltipTriggerEl);
                        });
                    }
                })
                .catch(err => console.error("Refresh failed:", err));
        }

        // Jalankan setiap 5 detik
        setInterval(refreshTable, 5000);
    });
</script>
@endpush
