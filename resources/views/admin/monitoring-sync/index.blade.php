@extends('layouts.admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Monitoring /</span> Log Sinkronisasi Sekolah
    </h4>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center flex-wrap gap-3">
            <h5 class="mb-0 fw-bold text-primary">
                <i class="bx bx-sync me-2"></i> Riwayat Sinkronisasi Terakhir
            </h5>
            
            {{-- 🔥 AREA PENCARIAN & TOMBOL REFRESH 🔥 --}}
            <div class="d-flex align-items-center gap-2">
                <div class="input-group input-group-merge" style="width: 300px;">
                    <span class="input-group-text"><i class="bx bx-search"></i></span>
                    <input type="text" id="searchInput" class="form-control" placeholder="Cari NPSN atau Nama Sekolah..." aria-label="Cari Sekolah">
                </div>
                <button onclick="window.location.reload()" class="btn btn-outline-primary" data-bs-toggle="tooltip" title="Refresh Data">
                    <i class="bx bx-refresh"></i>
                </button>
            </div>
        </div>
        
        <div class="table-responsive text-nowrap">
            <table class="table table-hover table-striped">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>NPSN</th>
                        <th>Nama Sekolah</th>
                        <th>Status Data</th>
                        <th>Waktu Sinkronisasi</th>
                    </tr>
                </thead>
                {{-- ID tableBody ditambahkan untuk target manipulasi AJAX --}}
                <tbody class="table-border-bottom-0" id="tableBody">
                    @forelse($logs as $index => $log)
                        <tr>
                            <td>{{ $logs->firstItem() + $index }}</td>
                            <td><span class="badge bg-label-dark">{{ $log->npsn }}</span></td>
                            <td class="fw-bold text-wrap" style="max-width: 300px;">{{ $log->nama_sekolah }}</td>
                            <td>
                                @if(str_contains(strtolower($log->status), 'masuk semua'))
                                    <span class="badge bg-label-success"><i class="bx bx-check-circle me-1"></i>{{ $log->status }}</span>
                                @else
                                    <span class="badge bg-label-warning"><i class="bx bx-error-circle me-1"></i>{{ $log->status }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-primary">{{ \Carbon\Carbon::parse($log->updated_at)->diffForHumans() }}</span>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($log->updated_at)->format('d M Y, H:i') }} WIB</small>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                <i class="bx bx-info-circle fs-4 mb-2"></i><br>
                                Belum ada data sinkronisasi yang cocok.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- ID paginationContainer ditambahkan untuk sinkronisasi page saat di-search --}}
        <div class="card-footer bg-white border-top" id="paginationContainer">
            @if($logs->hasPages())
                {{-- appends(request()->query()) biar pas klik page 2, search-nya gak ilang --}}
                {{ $logs->appends(request()->query())->links('pagination::bootstrap-5') }}
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('searchInput');
        const tableBody = document.getElementById('tableBody');
        const paginationContainer = document.getElementById('paginationContainer');

        let timeout = null;

        // Memicu pencarian setiap kali user mengetik
        searchInput.addEventListener('keyup', function () {
            // Hapus timeout lama biar gak nge-spam server (Debouncing)
            clearTimeout(timeout);
            let query = this.value;

            timeout = setTimeout(() => {
                // Efek loading transparan
                tableBody.style.opacity = '0.5';

                // Fetch data pakai AJAX Vanilla JS
                fetch(`{{ route('admin.monitoring-sync.index') }}?search=${query}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => response.text())
                .then(html => {
                    // Ekstrak HTML yang baru dari response server
                    let parser = new DOMParser();
                    let doc = parser.parseFromString(html, 'text/html');

                    // Ganti isi tabel dan pagination dengan data hasil pencarian
                    tableBody.innerHTML = doc.getElementById('tableBody').innerHTML;
                    
                    let newPagination = doc.getElementById('paginationContainer');
                    if (newPagination) {
                        paginationContainer.innerHTML = newPagination.innerHTML;
                    }

                    // Hilangkan efek loading
                    tableBody.style.opacity = '1';
                })
                .catch(error => {
                    console.error('Error fetching data:', error);
                    tableBody.style.opacity = '1';
                });
            }, 400); // Nunggu 400ms setelah user berhenti ngetik baru cari data
        });
    });
</script>
@endpush
@endsection