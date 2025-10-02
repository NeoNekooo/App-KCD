@extends('layouts.admin')

@section('content')
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Kepegawaian /</span> Data Guru & Tenaga Kependidikan</h4>

<div class="card">
    <div class="card-header">
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="card-title m-0 me-2">Daftar GTK</h5>
            <div class="d-flex gap-2">
                {{-- Tombol Lihat Data, awalnya tersembunyi --}}
                <a href="#" id="viewSelectedBtn" class="btn btn-info btn-sm" style="display: none;">
                    <i class="bx bx-show-alt me-1"></i> Lihat Data
                </a>

                {{-- Tombol Opsi Export --}}
                <div class="dropdown">
                    <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bx bx-export me-1"></i> Opsi Export
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('admin.kepegawaian.gtk.export.excel', request()->query()) }}">Export Semua</a></li>
                        <li><a class="dropdown-item disabled" href="javascript:void(0);" id="exportSelectedLink">Export yang Dipilih</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.kepegawaian.gtk.index') }}" method="GET">
            <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="bx bx-search"></i></span>
                <input type="text" name="search" class="form-control" placeholder="Cari berdasarkan Nama, NIP, atau NIK..." value="{{ request('search') }}">
            </div>
        </form>
    </div>
    
    {{-- Wrapper ini penting untuk membuat tabel bisa di-scroll ke samping --}}
    <div class="table-responsive text-nowrap">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th><input class="form-check-input" type="checkbox" id="selectAllCheckbox"></th>
                    <th>Induk</th>
                    <th>Nama</th>
                    <th>NIK</th>
                    <th>L/P</th>
                    <th>Tgl Lahir</th>
                    <th>Status Kepegawaian</th>
                    <th>Jenis GTK</th>
                    <th>Jabatan GTK</th>
                    <th>NUPTK</th>
                    <th>Tgl Surat Tugas</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse ($gtks as $gtk)
                <tr>
                    <td><input class="form-check-input row-checkbox" type="checkbox" value="{{ $gtk->id }}"></td>
                    <td><span class="badge bg-label-secondary">{{ $gtk->ptk_induk == 1 ? 'Induk' : 'Non-Induk' }}</span></td>
                    <td><strong>{{ $gtk->nama }}</strong></td>
                    <td>{{ $gtk->nik ?? '-' }}</td>
                    <td>{{ $gtk->jenis_kelamin == 'Laki-laki' ? 'L' : 'P' }}</td>
                    <td>{{ $gtk->tanggal_lahir ? \Carbon\Carbon::parse($gtk->tanggal_lahir)->format('d-m-Y') : '-' }}</td>
                    <td>{{ $gtk->status_kepegawaian_id_str ?? '-' }}</td>
                    <td>{{ $gtk->jenis_ptk_id_str ?? '-' }}</td>
                    <td>{{ $gtk->jabatan_ptk_id_str ?? '-' }}</td>
                    <td>{{ $gtk->nuptk ?? '-' }}</td>
                    <td>{{ $gtk->tanggal_surat_tugas ? \Carbon\Carbon::parse($gtk->tanggal_surat_tugas)->format('d-m-Y') : '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="11" class="text-center py-4">
                        <i class="bx bx-user-x bx-lg text-muted d-block mx-auto mb-2"></i>
                        <span class="text-muted">Tidak ada data untuk ditampilkan.</span>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($gtks->hasPages())
        <div class="card-footer d-flex justify-content-center">
            {{ $gtks->links() }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAllCheckbox = document.getElementById('selectAllCheckbox');
        const rowCheckboxes = document.querySelectorAll('.row-checkbox');
        const viewSelectedBtn = document.getElementById('viewSelectedBtn');
        const exportSelectedLink = document.getElementById('exportSelectedLink');

        function handleCheckboxChange() {
            const checkedCheckboxes = document.querySelectorAll('.row-checkbox:checked');
            
            // Logika untuk tombol "Lihat Data"
            if (checkedCheckboxes.length === 1) {
                const selectedId = checkedCheckboxes[0].value;
                let url = "{{ route('admin.kepegawaian.gtk.show', ':id') }}";
                url = url.replace(':id', selectedId);
                viewSelectedBtn.href = url;
                viewSelectedBtn.style.display = 'inline-block';
            } else {
                viewSelectedBtn.style.display = 'none';
            }

            // Logika untuk link "Export yang Dipilih"
            if (checkedCheckboxes.length > 0) {
                exportSelectedLink.classList.remove('disabled');
            } else {
                exportSelectedLink.classList.add('disabled');
            }
        }

        selectAllCheckbox.addEventListener('change', function() {
            rowCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            handleCheckboxChange();
        });

        rowCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', handleCheckboxChange);
        });

        exportSelectedLink.addEventListener('click', function(e) {
            if (this.classList.contains('disabled')) {
                e.preventDefault();
                return;
            }
            e.preventDefault();
            const selectedIds = Array.from(document.querySelectorAll('.row-checkbox:checked'))
                                   .map(cb => cb.value)
                                   .join(',');

            if (selectedIds) {
                let url = `{{ route('admin.kepegawaian.gtk.export.excel') }}?ids=${selectedIds}`;
                const searchParam = new URLSearchParams(window.location.search).get('search');
                if (searchParam) {
                    url += `&search=${searchParam}`;
                }
                window.location.href = url;
            }
        });

        handleCheckboxChange();
    });
</script>
@endpush

