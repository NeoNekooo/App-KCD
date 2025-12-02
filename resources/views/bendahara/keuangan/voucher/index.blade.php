@extends('layouts.admin')

@section('title', 'Manajemen Voucher Siswa')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-2">
                <span class="text-muted fw-light">Keuangan /</span> Manajemen Voucher
            </h4>
            <p class="text-muted">Kelola voucher potongan biaya untuk siswa</p>
        </div>
        <div class="d-flex align-items-center">
            <span class="badge bg-primary me-2">
                <i class="ti ti-users me-1"></i>
                {{ $siswas->count() }} Siswa
            </span>
        </div>
    </div>

    {{-- Notifikasi --}}
    @if (session('success'))
        <div class="alert alert-success d-flex align-items-center" role="alert">
            <span class="alert-icon text-success me-2"><i class="ti ti-check ti-xs"></i></span>
            {{ session('success') }}
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger d-flex align-items-center" role="alert">
           <span class="alert-icon text-danger me-2"><i class="ti ti-alert-triangle ti-xs"></i></span>
            <div>
                <strong>Terjadi kesalahan:</strong>
                <ul class="mb-0 mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <!-- Form Tambah Voucher -->
        <div class="col-12 col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">
                        <i class="ti ti-discount-2 me-2"></i>
                        Tambah Voucher Baru
                    </h5>
                    <span class="badge bg-label-info">Potongan Biaya</span>
                </div>
                <div class="card-body">
                    <form action="{{ route('bendahara.keuangan.voucher.store') }}" method="POST" id="voucherForm">
                        @csrf
                        <div class="row">
                            <div class="mb-3 col-12">
                                <label for="siswa_id" class="form-label">Pilih Siswa</label>
                                <select id="siswa_id" name="siswa_id" class="form-select select2" required>
                                    <option value="">-- Cari atau pilih siswa --</option>
                                    @foreach ($daftarSiswa as $siswa)
                                        <option value="{{ $siswa->id }}" @selected(old('siswa_id') == $siswa->id) data-nis="{{ $siswa->nisn }}">
                                            {{ $siswa->nama }} (NIS: {{ $siswa->nisn }})
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">Pilih siswa yang akan menerima voucher</div>
                            </div>

                            <div class="mb-3 col-12">
                                <label for="nilai_voucher" class="form-label">Nilai Voucher</label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text">Rp</span>
                                    <input class="form-control" type="number" id="nilai_voucher" name="nilai_voucher"
                                           value="{{ old('nilai_voucher') }}" placeholder="50000" min="0" required />
                                </div>
                                <div class="form-text">Masukkan jumlah potongan biaya</div>
                            </div>

                            <div class="mb-3 col-12 col-md-6">
                                <label for="tahun_pelajaran_id" class="form-label">Tahun Ajaran</label>
                                <select id="tahun_pelajaran_id" name="tahun_pelajaran_id" class="form-select" required>
                                    @foreach ($tahunAjarans as $ta)
                                        <option value="{{ $ta->id }}" @selected(old('tahun_pelajaran_id') == $ta->id)>
                                            {{ $ta->tahun_ajaran }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3 col-12 col-md-6">
                                <label for="keterangan" class="form-label">Keterangan</label>
                                <input class="form-control" type="text" id="keterangan" name="keterangan"
                                       value="{{ old('keterangan') }}" placeholder="Contoh: Beasiswa, Diskon, dll" />
                                <div class="form-text">Jenis atau alasan pemberian voucher</div>
                            </div>
                        </div>

                        <div class="mt-4 d-flex gap-2">
                            <button type="submit" class="btn btn-primary d-flex align-items-center">
                                <i class="ti ti-device-floppy me-1"></i> Simpan Voucher
                            </button>
                            <button type="reset" class="btn btn-outline-secondary">Reset Form</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Statistik Ringkas -->
        <div class="col-12 col-lg-6 mb-4">
            <div class="card h-100">
                <h5 class="card-header">
                    <i class="ti ti-chart-pie me-2"></i>
                    Ringkasan Keuangan
                </h5>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="card bg-label-primary h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div class="card-info">
                                            <h6 class="mb-2">Total Voucher</h6>
                                            <h4 class="text-primary mb-0">
                                                Rp {{ number_format($siswas->sum(function($siswa) { return $siswa->vouchers->sum('nilai_voucher'); }), 0, ',', '.') }}
                                            </h4>
                                        </div>
                                        <div class="card-icon">
                                            <span class="badge bg-primary rounded p-2">
                                                <i class="ti ti-discount-2 ti-sm"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card bg-label-success h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div class="card-info">
                                            <h6 class="mb-2">Total Pembayaran</h6>
                                            <h4 class="text-success mb-0">
                                                Rp {{ number_format($siswas->sum(function($siswa) { return $siswa->pembayarans->sum('jumlah_bayar'); }), 0, ',', '.') }}
                                            </h4>
                                        </div>
                                        <div class="card-icon">
                                            <span class="badge bg-success rounded p-2">
                                                <i class="ti ti-cash ti-sm"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card bg-label-warning h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div class="card-info">
                                            <h6 class="mb-2">Total Kewajiban</h6>
                                            <h4 class="text-warning mb-0">
                                                Rp {{ number_format($siswas->sum(function($siswa) {
                                                    return $siswa->tagihans->sum('jumlah_tagihan') + $siswa->tunggakans->sum('total_tunggakan_awal');
                                                }), 0, ',', '.') }}
                                            </h4>
                                        </div>
                                        <div class="card-icon">
                                            <span class="badge bg-warning rounded p-2">
                                                <i class="ti ti-report-money ti-sm"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card bg-label-danger h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div class="card-info">
                                            <h6 class="mb-2">Sisa Tagihan</h6>
                                            <h4 class="text-danger mb-0">
                                                @php
                                                    $totalKewajiban = $siswas->sum(function($siswa) {
                                                        return $siswa->tagihans->sum('jumlah_tagihan') + $siswa->tunggakans->sum('total_tunggakan_awal');
                                                    });
                                                    $totalBayar = $siswas->sum(function($siswa) {
                                                        return $siswa->pembayarans->sum('jumlah_bayar');
                                                    });
                                                    $totalVoucher = $siswas->sum(function($siswa) {
                                                        return $siswa->vouchers->sum('nilai_voucher');
                                                    });
                                                    $sisaTagihan = $totalKewajiban - $totalBayar - $totalVoucher;
                                                @endphp
                                                Rp {{ number_format($sisaTagihan, 0, ',', '.') }}
                                            </h4>
                                        </div>
                                        <div class="card-icon">
                                            <span class="badge bg-danger rounded p-2">
                                                <i class="ti ti-alert-triangle ti-sm"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Ringkasan Keuangan Siswa -->
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="ti ti-users me-2"></i>
                        Ringkasan Keuangan Siswa
                    </h5>
                    <div class="d-flex gap-2">
                        <div class="input-group input-group-merge w-auto">
                            <span class="input-group-text" id="basic-addon-search31"><i class="ti ti-search"></i></span>
                            <input type="text" class="form-control" placeholder="Cari siswa..." id="searchTable">
                        </div>
                    </div>
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover" id="siswaTable">
                        <thead>
                            <tr>
                                <th width="25%">Nama Siswa</th>
                                <th width="15%">Total Kewajiban</th>
                                <th width="15%">Total Bayar</th>
                                <th width="15%">Total Voucher</th>
                                <th width="15%">Sisa Tagihan</th>
                                <th width="15%">Status</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @foreach ($siswas as $siswa)
                                @php
                                    $totalKewajiban = $siswa->tagihans->sum('jumlah_tagihan') + $siswa->tunggakans->sum('total_tunggakan_awal');
                                    $totalBayar = $siswa->pembayarans->sum('jumlah_bayar');
                                    $totalVoucher = $siswa->vouchers->sum('nilai_voucher');
                                    $sisaTagihan = $totalKewajiban - $totalBayar - $totalVoucher;

                                    // Determine status
                                    if ($sisaTagihan <= 0) {
                                        $status = 'Lunas';
                                        $statusClass = 'success';
                                    } elseif ($sisaTagihan <= ($totalKewajiban * 0.2)) {
                                        $status = 'Menunggu Pelunasan';
                                        $statusClass = 'info';
                                    } else {
                                        $status = 'Belum Lunas';
                                        $statusClass = 'warning';
                                    }
                                @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-3">
                                                <span class="avatar-initial rounded-circle bg-label-primary">
                                                    {{ substr($siswa->nama, 0, 1) }}
                                                </span>
                                            </div>
                                            <div>
                                                <strong class="text-primary">{{ $siswa->nama }}</strong>
                                                <div class="text-muted small">NIS: {{ $siswa->nisn }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-medium">Rp {{ number_format($totalKewajiban, 0, ',', '.') }}</span>
                                    </td>
                                    <td class="text-success">
                                        <span class="fw-medium">Rp {{ number_format($totalBayar, 0, ',', '.') }}</span>
                                    </td>
                                    <td class="text-info">
                                        <span class="fw-medium">Rp {{ number_format($totalVoucher, 0, ',', '.') }}</span>
                                    </td>
                                    <td>
                                        <span class="fw-bold {{ $sisaTagihan > 0 ? 'text-danger' : 'text-success' }}">
                                            Rp {{ number_format($sisaTagihan, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $statusClass }}">{{ $status }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Menampilkan {{ $siswas->count() }} dari {{ $siswas->count() }} siswa
                    </div>
                    <div>
                        <button class="btn btn-outline-primary btn-sm">
                            <i class="ti ti-download me-1"></i> Ekspor Data
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card.h-100 .card-body {
        display: flex;
        flex-direction: column;
    }
    .avatar-initial {
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
    }
    .select2-container--default .select2-selection--single {
        height: 42px;
        border: 1px solid #d9dee3;
        border-radius: 0.375rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 40px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px;
    }
</style>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Select2 if available
        if (typeof $.fn.select2 !== 'undefined') {
            $('.select2').select2({
                placeholder: 'Cari atau pilih siswa',
                allowClear: true
            });
        }

        // Search functionality for the table
        const searchInput = document.getElementById('searchTable');
        const table = document.getElementById('siswaTable');
        const tableRows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                const filter = this.value.toLowerCase();

                for (let i = 0; i < tableRows.length; i++) {
                    const row = tableRows[i];
                    const studentName = row.cells[0].textContent.toLowerCase();

                    if (studentName.includes(filter)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                }
            });
        }

    $(document).ready(function() {
        // Inisialisasi Select2 pada elemen dengan ID 'siswa_id'
        $('#siswa_id').select2({
            placeholder: "-- Cari atau pilih siswa --",
            allowClear: true // Opsi ini memungkinkan pengguna menghapus pilihan
        });
    });
        // Form validation
        const voucherForm = document.getElementById('voucherForm');
        if (voucherForm) {
            voucherForm.addEventListener('submit', function(e) {
                const nilaiVoucher = document.getElementById('nilai_voucher').value;
                if (nilaiVoucher <= 0) {
                    e.preventDefault();
                    alert('Nilai voucher harus lebih dari 0');
                    return false;
                }
            });
        }
    });
</script>
@endpush
