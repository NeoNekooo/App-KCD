@extends('layouts.admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Monitoring /</span> Log Sinkronisasi Sekolah
    </h4>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-primary">
                <i class="bx bx-sync me-2"></i> Riwayat Sinkronisasi Terakhir
            </h5>
            <button onclick="window.location.reload()" class="btn btn-sm btn-outline-primary">
                <i class="bx bx-refresh me-1"></i> Refresh Data
            </button>
        </div>
        
        <div class="table-responsive text-nowrap">
            <table class="table table-hover table-striped">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>NPSN</th>
                        <th>Nama Sekolah</th>
                        <th>Status</th>
                        <th>Tabel Target</th>
                        <th>Waktu Sinkronisasi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($logs as $index => $log)
                        <tr>
                            <td>{{ $logs->firstItem() + $index }}</td>
                            <td><span class="badge bg-label-dark">{{ $log->npsn }}</span></td>
                            <td class="fw-bold text-wrap" style="max-width: 250px;">{{ $log->nama_sekolah }}</td>
                            <td>
                                @if(str_contains(strtolower($log->status), 'masuk semua'))
                                    <span class="badge bg-label-success"><i class="bx bx-check-circle me-1"></i>{{ $log->status }}</span>
                                @else
                                    <span class="badge bg-label-warning"><i class="bx bx-error-circle me-1"></i>{{ $log->status }}</span>
                                @endif
                            </td>
                            <td><code>{{ $log->tabel_tujuan }}</code></td>
                            <td>
                                <div class="d-flex flex-column">
                                    {{-- diffForHumans akan nampilin "5 menit yang lalu", "1 jam yang lalu" dll --}}
                                    <span class="fw-bold text-primary">{{ \Carbon\Carbon::parse($log->updated_at)->diffForHumans() }}</span>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($log->updated_at)->format('d M Y, H:i') }} WIB</small>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="bx bx-info-circle fs-4 mb-2"></i><br>
                                Belum ada data sinkronisasi dari sekolah manapun.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if($logs->hasPages())
            <div class="card-footer bg-white border-top">
                {{ $logs->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection