@extends('layouts.admin')

@section('content')
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Pengaturan /</span> Manajemen Hari Libur</h4>

<div class="row">
    {{-- Form Tambah Hari Libur --}}
    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><h5 class="mb-0">Tambah Hari Libur Baru</h5></div>
            <div class="card-body">
                <form action="{{ route('admin.pengaturan.hari-libur.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="tanggal" class="form-label">Tanggal</label>
                        <input type="date" class="form-control @error('tanggal') is-invalid @enderror" id="tanggal" name="tanggal" required>
                        @error('tanggal')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <input type="text" class="form-control @error('keterangan') is-invalid @enderror" id="keterangan" name="keterangan" required>
                        @error('keterangan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="btn btn-primary">Tambah</button>
                </form>
            </div>
        </div>
    </div>

    {{-- Tabel Daftar Hari Libur --}}
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><h5 class="mb-0">Daftar Hari Libur</h5></div>
            <div class="card-body">
                 @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead><tr><th>Tanggal</th><th>Keterangan</th><th>Aksi</th></tr></thead>
                        <tbody>
                            @forelse ($hariLibur as $libur)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($libur->tanggal)->isoFormat('dddd, D MMMM Y') }}</td>
                                <td>{{ $libur->keterangan }}</td>
                                <td>
                                    <form action="{{ route('admin.pengaturan.hari-libur.destroy', $libur->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus hari libur ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-icon btn-sm btn-outline-danger">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center">Belum ada data hari libur.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
