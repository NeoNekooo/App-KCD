@extends('layouts.admin')

@section('title', 'Program Keahlian')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Akademik /</span> Program Keahlian
    </h4>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Program Keahlian</h5>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-striped table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 60px;">#</th>
                        <th>Kode</th>
                        <th>Nama Program</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($programs as $index => $program)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $program->kode ?? '-' }}</td>
                            <td>{{ $program->nama_program ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted">- Tidak ada data -</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
