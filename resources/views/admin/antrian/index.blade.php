@extends('layouts.admin')
@section('title', 'Layanan Tamu KCD')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold m-0"><span class="text-muted fw-light">Dashboard /</span> Layanan Tamu</h4>
            <div class="text-muted small mt-1">Kelola Tiket Antrian Tamu Harian Kantor Cabang Dinas</div>
        </div>
        <div class="d-flex flex-wrap justify-content-md-end align-items-center gap-2">
            <form action="{{ route('admin.antrian.index') }}" method="GET" class="d-flex align-items-center gap-2">
                <div class="position-relative">
                    <select name="search_month" class="form-select shadow-sm rounded-pill fw-bold border-primary px-3" 
                        style="height: 38px; width: 140px; font-size: 0.85rem;"
                        onchange="this.form.submit()">
                        <option value="">-- Pilih Bulan --</option>
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ request('search_month') == $m ? 'selected' : '' }}>
                                {{ Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="position-relative">
                    <select name="search_year" class="form-select shadow-sm rounded-pill fw-bold border-primary px-3" 
                        style="height: 38px; width: 100px; font-size: 0.85rem;"
                        onchange="this.form.submit()">
                        @foreach(range(date('Y') - 1, date('Y')) as $y)
                            <option value="{{ $y }}" {{ (request('search_year') ?? date('Y')) == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @if(request('search_month'))
                    <a href="{{ route('admin.antrian.index') }}" class="btn btn-sm btn-outline-secondary shadow-sm rounded-pill fw-bold d-flex align-items-center py-2" title="Reset Filter">
                        <i class='bx bx-reset me-1'></i>Reset
                    </a>
                @endif
            </form>
            <button type="button" class="btn btn-sm btn-outline-primary shadow-sm rounded-pill fw-bold py-2" data-bs-toggle="modal" data-bs-target="#modalKategori">
                <i class='bx bx-category me-2'></i>Kelola Kategori
            </button>
            <a href="{{ route('admin.antrian.export', [
                'search_date' => request('search_date'),
                'search_month' => request('search_month'),
                'search_year' => request('search_year') ?? date('Y')
            ]) }}" class="btn btn-sm btn-success shadow-sm rounded-pill fw-bold py-2">
                <i class='bx bx-spreadsheet me-2'></i>Export Excel
            </a>
            <a href="{{ route('admin.display.antrian') }}" target="_blank" class="btn btn-sm btn-dark shadow-sm rounded-pill fw-bold py-2">
                <i class='bx bx-tv me-2'></i>Buka Layar TV
            </a>
            <a href="{{ route('guest.buku-tamu') }}" target="_blank" class="btn btn-sm btn-primary shadow-sm rounded-pill fw-bold py-2">
                <i class='bx bx-qr-scan me-2'></i>Lihat QR Form
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
            <i class="bx bx-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-warning alert-dismissible fade show rounded-3 mb-4" role="alert">
            <i class="bx bx-error-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
            <h5 class="card-title m-0 fw-bold"><i class="bx bx-list-ul me-2 text-primary"></i>Daftar Riwayat Antrian Tamu</h5>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="fw-semibold text-uppercase font-size-13 py-3" style="width: 50px;">No</th>
                        <th class="fw-semibold text-uppercase font-size-13 py-3">No Antrian</th>
                        <th class="fw-semibold text-uppercase font-size-13 py-3">Tanggal</th>
                        <th class="fw-semibold text-uppercase font-size-13 py-3">Tamu</th>
                        <th class="fw-semibold text-uppercase font-size-13 py-3">No HP</th>
                        <th class="fw-semibold text-uppercase font-size-13 py-3">Detail & Keperluan</th>
                        <th class="fw-semibold text-uppercase font-size-13 py-3">Status</th>
                        <th class="fw-semibold text-uppercase font-size-13 py-3 text-center">Aksi Panggilan</th>
                    </tr>
                </thead>
                <tbody id="antrianTableBody">
                    @include('admin.antrian._table_body')
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-top py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="small text-muted">
                    Menampilkan {{ $antrians->firstItem() ?? 0 }} sampai {{ $antrians->lastItem() ?? 0 }} dari {{ $antrians->total() }} data
                </div>
                <div>
                    {{ $antrians->appends(request()->all())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Kelola Kategori -->
<div class="modal fade" id="modalKategori" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title fw-bold"><i class="bx bx-category me-2 text-primary"></i>Kelola Kategori Keperluan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form action="{{ route('admin.antrian.kategori.store') }}" method="POST" class="mb-4">
                    @csrf
                    <label class="form-label fw-bold small text-uppercase text-muted">Tambah Kategori Baru</label>
                    <div class="input-group overflow-hidden rounded-3 shadow-sm">
                        <input type="text" name="name" class="form-control border-primary" placeholder="Tulis nama kategori..." required>
                        <button class="btn btn-primary" type="submit">SIMPAN</button>
                    </div>
                </form>

                <label class="form-label fw-bold small text-uppercase text-muted mb-3">Daftar Kategori Aktif</label>
                <div class="list-group list-group-flush border rounded-3 overflow-hidden">
                    @forelse($categories as $cat)
                        <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <span class="fw-semibold"><i class="bx bx-check-circle text-success me-2"></i>{{ $cat->name }}</span>
                            <form id="form-delete-{{ $cat->id }}" action="{{ route('admin.antrian.kategori.destroy', $cat->id) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="button" class="btn btn-sm btn-label-danger" onclick="deleteKategori({{ $cat->id }}, event)">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </form>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <i class="bx bx-info-circle fs-1 text-muted mb-2"></i>
                            <p class="text-muted mb-0">Belum ada kategori ditentukan.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<!-- Audio -->
<audio id="bellSound" preload="auto">
    <source src="https://www.myinstants.com/media/sounds/elevator-ding.mp3" type="audio/mpeg">
</audio>

<style>
    /* Paksa SweetAlert2 tampil paling depan, di atas modal manapun */
    .swal2-container {
        z-index: 9999 !important;
    }
    .swal2-shown {
        overflow: hidden !important; 
    }
    /* Rapihin tombol SweetAlert2 biar makin premium */
    .swal2-styled.swal2-confirm {
        border-radius: 99px !important;
        padding-left: 25px !important;
        padding-right: 25px !important;
        font-weight: 700 !important;
    }
    .swal2-styled.swal2-cancel {
        border-radius: 99px !important;
        padding-left: 25px !important;
        padding-right: 25px !important;
    }
</style>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Definisikan fungsi di window agar bisa dipanggil dari onclick
    window.deleteKategori = function(id, e) {
        e.preventDefault();
        const form = document.getElementById('form-delete-' + id);
        
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Hapus Kategori?',
                text: "Kategori ini tidak akan muncul lagi di form tamu.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                buttonsStyling: true, // Gunakan styling default tapi kita tindas di CSS
                confirmButtonColor: '#ff3e1d',
                cancelButtonColor: '#8592a3',
                reverseButtons: true, // Tombol hapus di kanan biar standar
                backdrop: `rgba(0,0,0,0.4)` // Kasih item tipis aja biar nggak numpuk
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        } else {
            if (confirm('Yakin ingin menghapus kategori ini?')) {
                form.submit();
            }
        }
    };

    document.addEventListener('DOMContentLoaded', function() {
        let currentCount = document.querySelectorAll('#antrianTableBody tr').length;
        const tableBody = document.getElementById('antrianTableBody');
        const bell = document.getElementById('bellSound');

        function refreshTable() {
            const urlParams = new URLSearchParams(window.location.search);
            const page = urlParams.get('page') || 1;
            const searchDate = urlParams.get('search_date') || '';
            const searchMonth = urlParams.get('search_month') || '';
            const searchYear = urlParams.get('search_year') || '';

            fetch(`/admin/antrian/partial?page=${page}&search_date=${searchDate}&search_month=${searchMonth}&search_year=${searchYear}`)
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const rows = doc.querySelectorAll('tr');
                    let newCount = rows.length;
                    
                    if(rows.length === 1 && rows[0].querySelector('td[colspan="7"]')) {
                        newCount = 0;
                    }

                    if (newCount > currentCount) {
                        if(bell) bell.play().catch(e => console.log("Audio play blocked"));
                    }
                    
                    currentCount = newCount;
                    tableBody.innerHTML = html;

                    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                        tooltipTriggerList.map(function (tooltipTriggerEl) {
                            return new bootstrap.Tooltip(tooltipTriggerEl);
                        });
                    }
                })
                .catch(err => console.error("Refresh failed:", err));
        }

        setInterval(refreshTable, 5000);

        // Auto Re-open Modal
        @if(session('open_modal_kategori'))
            setTimeout(() => {
                const modalEl = document.getElementById('modalKategori');
                if (modalEl) {
                    const modalKategori = new bootstrap.Modal(modalEl);
                    modalKategori.show();
                    setTimeout(() => {
                        const input = modalEl.querySelector('input[name="name"]');
                        if (input) input.focus();
                    }, 500);
                }
            }, 300);
        @endif
    });
</script>
@endpush
