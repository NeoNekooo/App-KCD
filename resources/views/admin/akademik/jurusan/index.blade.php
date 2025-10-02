@extends('layouts.admin') {{-- sesuaikan layout Sneat Tuan --}}

@section('title', 'Data Jurusan')

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header  text-white">
            <h5 class="mb-0">Daftar Konsentrasi Keahlian</h5>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th>Kode</th>
                        <th>Nama Konsentrasi Keahlian</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($jurusan as $key => $j)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td><strong>{{ $j->kode }}</strong></td>
                            <td>{{ $j->jurusan_id_str }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">Belum ada data jurusan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
