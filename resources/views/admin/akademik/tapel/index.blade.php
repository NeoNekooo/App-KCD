@extends('layouts.admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <!-- Breadcrumb -->
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="#">Akademik</a></li>
                <li class="breadcrumb-item active" aria-current="page">Tahun Pelajaran</li>
            </ol>
        </nav>
    </div>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold">
            <span class="text-muted fw-light">Akademik /</span> Tahun Pelajaran
        </h4>
    </div>

    <!-- Table Card -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Daftar Tahun Pelajaran (dari Database)</h5>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-hover table-bordered align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 60px;">No</th>
                        <th>Kode Tapel</th>
                        <th>Tahun Ajaran</th>
                        <th>Semester</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tapel as $index => $item)
                        @php
                            $tahun = substr($item->tapel, 0, 4);
                            $semester = substr($item->tapel, -1) == '1' ? 'Ganjil' : 'Genap';
                            $tahunPelajaran = $tahun . '/' . ($tahun + 1);
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><strong>{{ $item->tapel }}</strong></td>
                            <td>{{ $tahunPelajaran }}</td>
                            <td>{{ $semester }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">Tidak ada data Tapel</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
