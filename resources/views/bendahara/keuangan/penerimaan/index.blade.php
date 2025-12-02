@extends('layouts.admin')

@section('title', 'Penerimaan Pembayaran Siswa')

@push('styles')
<style>
    .student-list-container {
        max-height: 70vh; /* Sedikit lebih pendek agar tidak menempel ke bawah */
        overflow-y: auto;
    }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Keuangan /</span> Penerimaan
    </h4>

    {{-- Notifikasi --}}
    @if (session('success'))
        <div class="alert alert-success d-flex align-items-center" role="alert">
            <span class="alert-icon text-success me-2"><i class="ti ti-check ti-xs"></i></span>
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger d-flex align-items-center" role="alert">
            <span class="alert-icon text-danger me-2"><i class="ti ti-ban ti-xs"></i></span>
            {{ session('error') }}
        </div>
    @endif

    <div class="row">
        <!-- Kolom Kiri: Daftar Siswa -->
        <div class="col-md-4">
            <div class="card">
                <h5 class="card-header">Cari Siswa</h5>
                <div class="card-body">
                    {{-- Menggunakan input-group для estetika Sneat --}}
                    <div class="input-group input-group-merge">
                        <span class="input-group-text" id="student-search-icon"><i class="ti ti-search"></i></span>
                        <input type="text" id="studentSearch" class="form-control" placeholder="Ketik nama atau NIS..." aria-label="Ketik nama atau NIS..." aria-describedby="student-search-icon">
                    </div>
                </div>
                <div class="student-list-container">
                    {{-- Mengganti ul dengan div dan list-group-flush --}}
                    <div class="list-group list-group-flush" id="studentList">
                        @foreach ($siswas as $siswa)
                            <a href="{{ route('bendahara.keuangan.penerimaan.index', ['siswa_id' => $siswa->id]) }}"
                               class="list-group-item list-group-item-action @if(request('siswa_id') == $siswa->id) active @endif">
                                <div>
                                    <h6 class="mb-1">{{ $siswa->nama }}</h6>
                                    <small class="text-muted">NIS: {{ $siswa->nisn }}</small>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Detail Siswa & Form -->
        <div class="col-md-8">
            @if ($siswaDipilih)
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-lg me-3">
                                <span class="avatar-initial rounded-circle bg-label-primary">
                                    <i class="ti ti-user ti-lg"></i>
                                </span>
                            </div>
                            <div>
                                <h5 class="mb-0">{{ $siswaDipilih->nama }}</h5>
                                <small class="text-muted">NIS: {{ $siswaDipilih->nis }} | Kelas: {{ $siswaDipilih->rombel->nama_rombel ?? 'N/A' }}</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="nav-align-top">
                    <ul class="nav nav-tabs nav-fill" role="tablist">
                        <li class="nav-item">
                            <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#kewajiban" aria-controls="kewajiban" aria-selected="true">
                                <i class="tf-icons ti ti-file-dollar ti-xs me-1"></i> Kewajiban
                            </button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#form-transaksi" aria-controls="form-transaksi" aria-selected="false">
                                <i class="tf-icons ti ti-forms ti-xs me-1"></i> Formulir Transaksi
                            </button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#history" aria-controls="history" aria-selected="false">
                                <i class="tf-icons ti ti-history ti-xs me-1"></i> History Transaksi
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content p-0"> {{-- Menghapus padding dari parent --}}
                        <!-- Tab Kewajiban -->
                        <div class="tab-pane fade show active" id="kewajiban" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Nama Iuran</th>
                                            <th>Jumlah Tagihan</th>
                                            <th>Sisa Tagihan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $totalSisa = 0; @endphp
                                        @forelse ($siswaDipilih->tagihans->merge($siswaDipilih->tunggakans) as $item)
                                            @php
                                                $sisa = $item instanceof \App\Models\Tagihan ? $item->sisa_tagihan : $item->sisa_tunggakan;
                                                $total = $item instanceof \App\Models\Tagihan ? $item->jumlah_tagihan : $item->total_tunggakan_awal;
                                                $totalSisa += $sisa;
                                            @endphp
                                            <tr>
                                                <td>{{ $item->iuran->nama_iuran }}</td>
                                                <td>Rp {{ number_format($total, 0, ',', '.') }}</td>
                                                <td class="text-danger fw-bold">Rp {{ number_format($sisa, 0, ',', '.') }}</td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="3" class="text-center p-4">Tidak ada kewajiban yang belum lunas.</td></tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <th colspan="2" class="text-end">Total Sisa Tagihan:</th>
                                            <th class="text-danger fw-bold">Rp {{ number_format($totalSisa, 0, ',', '.') }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <!-- Tab Formulir Transaksi -->
                        <div class="tab-pane fade" id="form-transaksi" role="tabpanel">
                           <form class="card-body" action="{{ route('bendahara.keuangan.penerimaan.store') }}" method="POST"> {{-- Menambahkan padding di sini --}}
                                @csrf
                                <input type="hidden" name="siswa_id" value="{{ $siswaDipilih->id }}">
                                <div class="row">
                                    <div class="mb-3 col-12">
                                        <label for="pembayaran_untuk" class="form-label">Pembayaran Untuk</label>
                                   <select id="pembayaran_untuk" name="pembayaran_untuk" class="form-select" required>
    <option value="">-- Pilih Iuran --</option>
    @foreach ($siswaDipilih->tagihans as $tagihan)
        <option value="tagihan_{{ $tagihan->id }}">
            {{ $tagihan->iuran?->nama_iuran ?? '[Iuran Bulanan Error/Terhapus]' }} (Sisa: Rp {{ number_format($tagihan->sisa_tagihan, 0, ',', '.') }})
        </option>
    @endforeach
    @foreach ($siswaDipilih->tunggakans as $tunggakan)
        <option value="tunggakan_{{ $tunggakan->id }}">
            {{ $tunggakan->iuran?->nama_iuran ?? '[Iuran Bebas Error/Terhapus]' }} (Sisa: Rp {{ number_format($tunggakan->sisa_tunggakan, 0, ',', '.') }})
        </option>
    @endforeach
</select>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label for="jumlah_bayar" class="form-label">Jumlah Bayar (Rp)</label>
                                        <input class="form-control" type="number" id="jumlah_bayar" name="jumlah_bayar" required />
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label for="tanggal_bayar" class="form-label">Tanggal Bayar</label>
                                        <input class="form-control" type="date" id="tanggal_bayar" name="tanggal_bayar" value="{{ date('Y-m-d') }}" required />
                                    </div>
                                     <div class="mb-3 col-12">
                                        <label for="master_kas_id" class="form-label">Masuk Ke Kas</label>
                                        <select id="master_kas_id" name="master_kas_id" class="form-select" required>
                                            @foreach ($daftarKas as $kas)
                                                <option value="{{ $kas->id }}">{{ $kas->nama_kas }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                {{-- Menambahkan ikon pada tombol --}}
                                <button type="submit" class="btn btn-primary"><i class="ti ti-send me-1"></i> Simpan Transaksi</button>
                           </form>
                        </div>

                        <!-- Tab History -->
                        <div class="tab-pane fade p-4" id="history" role="tabpanel"> {{-- Menambahkan padding di sini --}}
                             <ul class="list-group">
                                @forelse($siswaDipilih->pembayarans as $pembayaran)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                        <h6 class="mb-0">Pembayaran {{ $pembayaran->iuran?->nama_iuran ?? 'Iuran Tidak Ditemukan' }}</h6>
                                            <small class="text-muted">{{ $pembayaran->tanggal_bayar->format('d M Y') }} oleh {{ $pembayaran->petugas->nama_lengkap ?? 'N/A' }}</small>
                                        </div>
                                        <strong class="text-success">+ Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}</strong>
                                    </li>
                                @empty
                                     <li class="list-group-item text-center">Belum ada riwayat transaksi.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            @else
                <div class="card">
                    {{-- Menyempurnakan empty state --}}
                    <div class="card-body text-center p-5">
                        <i class="ti ti-user-search ti-xl mb-3 text-primary"></i>
                        <h5>Pilih Siswa</h5>
                        <p>Silakan pilih siswa dari daftar di sebelah kiri untuk melihat detail dan melakukan transaksi.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Script sederhana untuk filter daftar siswa
    document.getElementById('studentSearch').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let listItems = document.querySelectorAll('#studentList a');

        listItems.forEach(function(item) {
            let text = item.textContent.toLowerCase();
            if (text.includes(filter)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    });
</script>
@endpush

