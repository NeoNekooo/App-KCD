@extends('layouts.admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- 🔹 Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Akademik /</span> Tahun Pelajaran
        </h4>
        <a href="{{ route('admin.akademik.tapel.sinkron') }}" class="btn btn-success btn-sm shadow-sm">
            <i class="bx bx-sync me-1"></i> Sinkron Tapel
        </a>
    </div>

    {{-- 🔹 Card Utama --}}
    <div class="card shadow-sm border-0">
        <div class="card-header text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bx bx-calendar me-2"></i>Daftar Tahun Pelajaran</h5>
            <span class="badge bg-primary text-white">{{ $tapel->count() }} Data</span>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th style="width: 5%">#</th>
                            <th>Kode Tapel</th>
                            <th>Tahun Ajaran</th>
                            <th>Semester</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tapel as $index => $item)
                            <tr>
                                <td class="text-center fw-semibold">{{ $index + 1 }}</td>
                                <td class="text-center">
                                    <span class="badge bg-label-info fs-6 px-3 py-2">{{ $item->kode_tapel }}</span>
                                </td>
                                <td class="fw-semibold text-primary text-center">{{ $item->tahun_ajaran }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $item->semester == 'Ganjil' ? 'bg-warning text-dark' : 'bg-info text-dark' }}">
                                        {{ ucfirst($item->semester) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($item->is_active)
                                        <span class="badge bg-success fs-6 px-3 py-2">
                                            <i class="bx bx-check-circle me-1"></i> Aktif
                                        </span>
                                    @else
                                        <form action="{{ route('admin.akademik.tapel.aktif', $item->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-primary btn-sm rounded-pill">
                                                <i class="bx bx-bolt-circle me-1"></i> Jadikan Aktif
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <i class="bx bx-calendar-x bx-lg text-muted mb-2"></i>
                                    <p class="text-muted mb-0">Belum ada data Tahun Pelajaran</p>
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
