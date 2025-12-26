@extends('layouts.admin')

@section('content')
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Alumni /</span> Rekap Data Alumni</h4>

<div class="card">
    <div class="card-body">

        {{-- JUDUL TENGAH --}}
        <div class="text-center mb-4">
            <h5 class="mb-1 fw-bold">REKAPITULASI KELULUSAN SISWA</h5>
            <span class="text-muted">SMK NURUL ISLAM CIANJUR</span>
        </div>

        <div class="table-responsive">
    <table class="table table-bordered table-hover text-nowrap">
        <thead class="table-light">
            <tr class="text-center align-middle">
                <th width="50">NO</th>
                <th>PAKET KEAHLIAN / JURUSAN</th>
                <th width="120">LAKI-LAKI</th>
                <th width="120">PEREMPUAN</th>
                <th width="120">JML TOTAL</th>
            </tr>
        </thead>
        <tbody>
@forelse($data as $i => $row)
    <tr>
        <td class="text-center">{{ $i + 1 }}</td>
        <td>{{ $row->jurusan }}</td>
        <td class="text-center">{{ $row->laki_laki }}</td>
        <td class="text-center">{{ $row->perempuan }}</td>
        <td class="text-center fw-semibold">{{ $row->total }}</td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="text-center text-muted">
            Tidak ada data alumni
        </td>
    </tr>
@endforelse
</tbody>

    </table>
</div>


    </div>
</div>


@endsection
