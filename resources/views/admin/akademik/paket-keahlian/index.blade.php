@extends('layouts.admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <!-- Breadcrumb -->
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="#">Akademik</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Paket Keahlian</li>
            </ol>
        </nav>
    </div>

    <!-- Header + Tombol -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold">
            <span class="text-muted fw-light">Akademik /</span> Daftar Paket Keahlian
        </h4>
    </div>

    <!-- Card Tabel -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Data Paket Keahlian</h5>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-hover table-bordered align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th style="width: 100px;">Kode</th>
                        <th>Program Keahlian</th>
                        <th>Program Studi</th>
                        <th>Ketua</th>
                        <th style="width: 100px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($paket as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><strong>{{ $item->kode }}</strong></td>
                            <td>{{ $item->program_keahlian ?? '-' }}</td>
                            <td>{{ $item->program_studi ?? '-' }}</td>
                            <td>{{ $item->ketua ?? '-' }}</td>
                            <td class="text-center">
                                <a href="" class="text-primary me-2" title="Edit">
                                    <i class="bx bx-edit-alt"></i>
                                </a>
                                <form action="" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-link p-0 m-0 text-danger" title="Hapus" onclick="return confirm('Yakin ingin menghapus data ini?')">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
