@extends('layouts.admin') {{-- Sesuaikan dengan layout admin Tuan --}}

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header text-white">
            <h5 class="mb-0">📚 Data Ekstrakurikuler</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 5%">#</th>
                            <th>Nama Ekstrakurikuler</th>
                            <th>Alias</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($ekskul as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item['nama'] }}</td>
                                <td>{{ $item['alias'] }}</td>
                                <td>{{ $item['keterangan'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">
                                    Belum ada data ekstrakurikuler
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
