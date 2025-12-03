@extends('layouts.admin')

@section('title', 'Data Mata Pelajaran')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- 🔹 Breadcrumb --}}
        <div class="mb-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#">Akademik</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Mata Pelajaran</li>
                </ol>
            </nav>
        </div>

        {{-- 🔹 Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">
                <span class="text-muted fw-light">Akademik /</span> Daftar Mata Pelajaran
            </h4>
        </div>

        {{-- 🔹 Card Utama --}}
        <div class="card shadow-sm border-0">
            <div class="card-header text-white d-flex justify-content-between align-items-center flex-wrap">
                <h5 class="mb-0"><i class="bx bx-book-open me-2"></i>Daftar Mata Pelajaran</h5>
                {{-- 🔍 Form Pencarian --}}
                <form method="GET" action="{{ route('admin.akademik.mapel.index') }}" class="d-flex"
                    style="max-width: 320px;">
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

            <div class="card-body p-0">
                {{-- 📘 Tabel Mata Pelajaran --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-center">
                            <tr>
                                <th style="width: 5%">#</th>
                                <th style="width: 20%">Kode Mapel</th>
                                <th>Nama Mata Pelajaran</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($mapels as $mapel)
                                <tr>
                                    <td class="text-center">
                                        {{ ($mapels->currentPage() - 1) * $mapels->perPage() + $loop->iteration }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-label-info fs-6 px-3 py-2">{{ $mapel['kode'] }}</span>
                                    </td>
                                    <td class="fw-semibold text-dark">
                                        <i class="bx bx-book-content text-primary me-1"></i>{{ $mapel['nama_mapel'] }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-5">
                                        <i class="bx bx-book bx-lg text-muted mb-2"></i>
                                        <p class="text-muted mb-1">
                                            @if ($searchQuery)
                                                Tidak ditemukan hasil untuk <strong>"{{ $searchQuery }}"</strong>.
                                            @else
                                                Belum ada data Mata Pelajaran.
                                            @endif
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 📄 Pagination --}}
            <div class="card-footer bg-light d-flex justify-content-end py-3">
                {{ $mapels->links() }}
            </div>
        </div>
    </div>
@endsection
