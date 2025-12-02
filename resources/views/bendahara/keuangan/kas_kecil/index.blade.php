@extends('layouts.admin') {{-- Ganti ini sesuai dengan nama file layout utama Anda --}}

@section('title', 'Buku Kas Kecil')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Keuangan /</span> Buku Kas
    </h4>

    <div class="card">
        <div class="card-header border-bottom">
            <div class="d-flex justify-content-between align-items-center row py-2 gap-3 gap-md-0">
                <div class="col-md-4">
                    <h5 class="card-title mb-0">
                        Laporan Kas: <strong>{{ $kasDipilih->nama_kas ?? 'Semua Kas' }}</strong>
                    </h5>
                </div>
                <div class="col-md-8">
                    {{-- Form Filter Baru --}}
                    <form action="{{ route('bendahara.keuangan.kas.index') }}" method="GET" class="d-flex justify-content-end align-items-center flex-wrap">

                        {{-- Filter Kas --}}
                        <label for="kas_id" class="me-2 d-none d-md-block">Kas:</label>
                        <select name="kas_id" id="kas_id" class="form-select me-2" style="width: 200px;">
                            <option value="">-- Semua Kas --</option>
                            @foreach ($daftarKas as $kas)
                                <option value="{{ $kas->id }}" {{ request('kas_id') == $kas->id ? 'selected' : '' }}>
                                    {{ $kas->nama_kas }}
                                </option>
                            @endforeach
                        </select>

                        {{-- Filter Tipe Mutasi --}}
                        <label for="tipe_mutasi" class="me-2 d-none d-md-block">Tipe:</label>
                        <select name="tipe_mutasi" id="tipe_mutasi" class="form-select me-2" style="width: 150px;">
                            <option value="">-- Semua Tipe --</option>
                            <option value="debit" {{ request('tipe_mutasi') == 'debit' ? 'selected' : '' }}>Debit (Masuk)</option>
                            <option value="kredit" {{ request('tipe_mutasi') == 'kredit' ? 'selected' : '' }}>Kredit (Keluar)</option>
                        </select>

                        {{-- Filter Periode (Bulan) --}}
                        <label for="bulan" class="me-2 d-none d-md-block">Periode:</label>
                        <input type="month" id="bulan" class="form-control me-2" name="bulan" value="{{ request('bulan', date('Y-m')) }}" style="width: 150px;">

                        <button type="submit" class="btn btn-primary ms-2 mt-2 mt-md-0"><i class="ti ti-filter me-1"></i> Filter</button>
                    </form>
                    {{-- Akhir Form Filter Baru --}}
                </div>
            </div>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th rowspan="2" class="text-center align-middle">Tanggal</th>
                        <th rowspan="2" class="align-middle">Keterangan</th>
                        <th colspan="2" class="text-center">Mutasi (Rp)</th>
                        <th rowspan="2" class="align-middle text-end">Saldo (Rp)</th>
                    </tr>
                    <tr>
                        <th class="text-center">Debit (Masuk)</th>
                        <th class="text-center">Kredit (Keluar)</th>
                    </tr>
                </thead>
                <tbody>
                    @php $saldoAwal = $saldoAwal ?? 0; // Pastikan saldoAwal terdefinisi @endphp
                    <tr class="table-secondary">
                        <td colspan="4"><strong>Saldo Awal</strong></td>
                        <td class="text-end fw-bold">{{ number_format($saldoAwal, 0, ',', '.') }}</td>
                    </tr>
                    @forelse ($mutasi as $item)
                    <tr>
                        <td class="text-center">{{ $item->tanggal->format('d-m-Y') }}</td>
                        <td>{{ $item->keterangan }}</td>
                        <td class="text-end text-success">
                            {{ $item->debit > 0 ? number_format($item->debit, 0, ',', '.') : '-' }}
                        </td>
                        <td class="text-end text-danger">
                             {{ $item->kredit > 0 ? number_format($item->kredit, 0, ',', '.') : '-' }}
                        </td>
                        <td class="text-end fw-bold">
                            {{ number_format($item->saldo, 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center p-4">Tidak ada transaksi pada periode ini.</td>
                    </tr>
                    @endforelse
                </tbody>
                 <tfoot class="table-light">
                    <tr>
                        <th colspan="2" class="text-end">Total</th>
                        <th class="text-end text-success">{{ number_format($mutasi->sum('debit'), 0, ',', '.') }}</th>
                        <th class="text-end text-danger">{{ number_format($mutasi->sum('kredit'), 0, ',', '.') }}</th>
                        {{-- Saldo Akhir diambil dari saldo mutasi terakhir atau saldo awal jika mutasi kosong --}}
                        <th class="text-end fw-bold">{{ number_format($mutasi->last()->saldo ?? $saldoAwal, 0, ',', '.') }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="card-footer d-flex justify-content-end">
            <button class="btn btn-secondary me-2"><i class="ti ti-printer me-1"></i> Print</button>
            <button class="btn btn-success me-2"><i class="ti ti-file-spreadsheet me-1"></i> Excel</button>
            <button class="btn btn-danger"><i class="ti ti-file-type-pdf me-1"></i> PDF</button>
        </div>
    </div>
</div>
@endsection
