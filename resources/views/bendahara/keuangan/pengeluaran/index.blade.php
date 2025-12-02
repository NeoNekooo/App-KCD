@extends('layouts.admin')

@section('title', 'Manajemen Pengeluaran')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Keuangan /</span> Pengeluaran
    </h4>

    {{-- Notifikasi --}}
    @if (session('success'))
        <div class="alert alert-success d-flex align-items-center" role="alert">
            <span class="alert-icon text-success me-2"><i class="ti ti-check ti-xs"></i></span>
            {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger d-flex align-items-center" role="alert">
           <span class="alert-icon text-danger me-2"><i class="ti ti-ban ti-xs"></i></span>
            <span>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </span>
        </div>
    @endif

    <div class="row">
        <!-- Form Tambah Pengeluaran -->
        <div class="col-12 mb-4">
            <div class="card">
                <h5 class="card-header">Catat Pengeluaran Baru</h5>
                <div class="card-body">
                    <form action="{{ route('bendahara.keuangan.pengeluaran.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="mb-3 col-md-3">
                                <label for="tanggal" class="form-label">Tanggal</label>
                                <input class="form-control" type="date" id="tanggal" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required />
                            </div>
                            <div class="mb-3 col-md-4">
                                <label for="uraian" class="form-label">Uraian Pengeluaran</label>
                                <input class="form-control" type="text" id="uraian" name="uraian" value="{{ old('uraian') }}" placeholder="Contoh: Beli ATK Kantor" required />
                            </div>
                            <div class="mb-3 col-md-2">
                                <label for="nominal" class="form-label">Nominal (Rp)</label>
                                <input class="form-control" type="number" id="nominal" name="nominal" value="{{ old('nominal') }}" placeholder="100000" required />
                            </div>
                             <div class="mb-3 col-md-3">
                                <label for="master_kas_id" class="form-label">Ambil Dari Kas</label>
                                <select id="master_kas_id" name="master_kas_id" class="form-select" required>
                                     @foreach ($daftarKas as $kas)
                                        <option value="{{ $kas->id }}" @selected(old('master_kas_id') == $kas->id)>{{ $kas->nama_kas }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mt-2">
                            <button type="submit" class="btn btn-primary me-2"><i class="ti ti-device-floppy me-1"></i> Simpan</button>
                            <button type="reset" class="btn btn-label-secondary">Reset</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Daftar Pengeluaran -->
        <div class="col-12">
            <div class="card">
                 <h5 class="card-header">Riwayat Pengeluaran</h5>
                <div class="table-responsive text-nowrap">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Uraian</th>
                                <th>Nominal</th>
                                <th>Sumber Kas</th>
                                <th>Petugas</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse ($pengeluarans as $item)
                            <tr>
                                <td>{{ $item->tanggal->format('d M Y') }}</td>
                                <td><strong>{{ $item->uraian }}</strong></td>
                                <td class="text-danger">Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
                                <td>{{ $item->masterKas->nama_kas ?? 'N/A' }}</td>
                                <td>{{ $item->petugas->nama_lengkap ?? 'N/A' }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical"></i></button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="javascript:void(0);"><i class="ti ti-pencil me-1"></i> Edit</a>
                                            <form action="{{ route('bendahara.keuangan.pengeluaran.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item"><i class="ti ti-trash me-1"></i> Hapus</button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                                <tr><td colspan="6" class="text-center">Belum ada data pengeluaran.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

