@extends('layouts.admin')

@section('title', 'Data Mata Pelajaran')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="#">Akademik</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Mata Pelajaran</li>
                </ol>
            </nav>
        </div>
        <h4 class="fw-bold mb-4">
            <span class="text-muted fw-light">Akademik /</span> Daftar Mata Pelajaran
        </h4>
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Daftar Mata Pelajaran</h5>
                <form method="GET" action="{{ route('admin.akademik.mapel.index') }}" class="d-flex"
                    style="max-width: 300px;">
                    <div class="input-group input-group-sm">
                        <input type="text" name="search" class="form-control" placeholder="Cari Kode atau Nama..."
                            value="{{ $searchQuery }}">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class='bx bx-search-alt'></i>
                        </button>
                        @if ($searchQuery)
                            <a href="{{ route('admin.akademik.mapel.index') }}" class="btn btn-outline-danger"
                                title="Reset Pencarian">
                                <i class='bx bx-x'></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table table-bordered table-striped table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th style="width: 200px;">Kode Mapel</th>
                                <th>Nama Mata Pelajaran</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($mapels as $mapel)
                                <tr>
                                    <td>
                                        {{ ($mapels->currentPage() - 1) * $mapels->perPage() + $loop->iteration }}</td>
                                    <td><strong>{{ $mapel['kode'] }}</strong></td>
                                    <td>{{ $mapel['nama_mapel'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">
                                        @if ($searchQuery)
                                            Data Mata Pelajaran dengan kata kunci **"{{ $searchQuery }}"** tidak ditemukan.
                                        @else
                                            Belum ada data Mata Pelajaran.
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer d-flex justify-content-end py-2">
                    {{ $mapels->links() }}
                </div>

            </div>
        </div>
    </div>
@endsection
