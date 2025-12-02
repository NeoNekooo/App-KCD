@extends('layouts.admin') {{-- Ganti ini sesuai dengan nama file layout utama Anda --}}

@section('title', 'Pengaturan Iuran Sekolah')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Keuangan /</span> Pengaturan Iuran Sekolah
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
        <!-- Form Tambah Iuran -->
        <div class="col-12 mb-4">
            <div class="card">
                <h5 class="card-header">Formulir Tambah Iuran Baru</h5>
                <div class="card-body">
                    <form action="{{ route('bendahara.keuangan.iuran.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="mb-3 col-md-4">
                                <label for="nama_iuran" class="form-label">Nama Iuran</label>
                                <input class="form-control" type="text" id="nama_iuran" name="nama_iuran" placeholder="Contoh: SPP Juli 2024" value="{{ old('nama_iuran') }}" required />
                            </div>
                            <div class="mb-3 col-md-2">
                                <label for="tipe_iuran" class="form-label">Tipe Iuran</label>
                                <select id="tipe_iuran" name="tipe_iuran" class="form-select" required>
                                    <option value="Bulanan" @selected(old('tipe_iuran') == 'Bulanan')>Bulanan</option>
                                    <option value="Bebas" @selected(old('tipe_iuran') == 'Bebas')>Bebas</option>
                                </select>
                            </div>
                            <div class="mb-3 col-md-3">
                                <label for="besaran_default" class="form-label">Besaran (Rp)</label>
                                <input class="form-control" type="number" id="besaran_default" name="besaran_default" placeholder="150000" value="{{ old('besaran_default') }}" required />
                            </div>
                             <div class="mb-3 col-md-3">
                                <label for="tahun_pelajaran_id" class="form-label">Untuk Tahun Ajaran</label>
                            <select id="tahun_pelajaran_id" name="tahun_pelajaran_id" class="form-select" required>
    {{-- $tahunAjarans di sini adalah objek tunggal/dummy dari Controller --}}
    @if ($tahunAjarans->id)
        <option value="{{ $tahunAjarans->id }}" selected>{{ $tahunAjarans->tahun_ajaran }}</option>
    @else
        <option value="" disabled selected>{{ $tahunAjarans->tahun_ajaran }}</option>
    @endif
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

        <!-- Daftar Iuran -->
        <div class="col-12">
            <div class="card">
                <h5 class="card-header">Daftar Iuran Sekolah</h5>
                <div class="table-responsive text-nowrap">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Iuran</th>
                                <th>Tipe</th>
                                <th>Besaran</th>
                                <th>Tahun Ajaran</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse ($iurans as $iuran)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><strong>{{ $iuran->nama_iuran }}</strong></td>
                                <td>
                                    @if ($iuran->tipe_iuran == 'Bulanan')
                                        <span class="badge bg-label-info me-1">Bulanan</span>
                                    @else
                                        <span class="badge bg-label-warning me-1">Bebas</span>
                                    @endif
                                </td>
                                <td>Rp {{ number_format($iuran->besaran_default, 0, ',', '.') }}</td>
                              <td>{{ $iuran->tapel?->tahun_ajaran }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical"></i></button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="javascript:void(0);"><i class="ti ti-pencil me-1"></i> Edit</a>
                                            <form action="{{ route('bendahara.keuangan.iuran.destroy', $iuran->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus iuran ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item"><i class="ti ti-trash me-1"></i> Hapus</button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">Belum ada data iuran.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
