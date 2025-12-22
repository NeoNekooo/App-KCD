@extends('layouts.admin')

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Kesiswaan /</span> Data Alumni
</h4>

<div class="card">

    {{-- ================= HEADER ================= --}}
    <div class="card-header">
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="card-title m-0">Daftar Alumni</h5>

            <div class="d-flex gap-2">
                <a href="#" id="viewSelectedBtn" class="btn btn-info btn-sm d-none">
                    <i class="bx bx-show-alt me-1"></i> Lihat Data
                </a>

                <div class="dropdown">
                    <button class="btn btn-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="bx bx-export me-1"></i> Export
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item"
                               href="{{ route('admin.alumni.dataAlumni.index', request()->query()) }}">
                                Export Semua
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item disabled" href="#" id="exportSelectedLink">
                                Export yang Dipilih
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= FILTER ================= --}}
    <div class="card-body">
        <form action="{{ route('admin.alumni.dataAlumni.index') }}" method="GET">
            <input type="hidden" name="per_page" value="{{ request('per_page', 15) }}">

            <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="bx bx-search"></i></span>
                <input type="text"
                       name="search"
                       class="form-control"
                       placeholder="Cari Nama, NISN, atau NIK Alumni..."
                       value="{{ request('search') }}">
                <button class="btn btn-primary">Cari</button>
            </div>
        </form>
    </div>

    {{-- ================= TABLE ================= --}}
    <div class="table-responsive text-nowrap">
        <table class="table table-hover">
            <thead>
            <tr>
                <th width="1%">
                    <input class="form-check-input" type="checkbox" id="selectAllCheckbox">
                </th>
                <th>Nama</th>
                <th>L/P</th>
                <th>NISN</th>
                <th>TTL</th>
                <th>Status</th>
            </tr>
            </thead>

            <tbody class="table-border-bottom-0">
            @forelse ($siswas as $siswa)
                <tr>
                    <td>
                        <input class="form-check-input row-checkbox" type="checkbox" value="{{ $siswa->id }}">
                    </td>

                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm me-3">
                                <img class="rounded-circle"
                                     src="https://ui-avatars.com/api/?name={{ urlencode($siswa->nama) }}"
                                     alt="">
                            </div>
                            <div>
                                <span class="fw-semibold">{{ $siswa->nama }}</span><br>
                                <small class="text-muted">
                                    {{ $siswa->nis ?? '-' }}
                                </small>
                            </div>
                        </div>
                    </td>

                    <td>{{ $siswa->jenis_kelamin == 'L' ? 'L' : 'P' }}</td>
                    <td>{{ $siswa->nisn ?? '-' }}</td>
                    <td>
                        {{ $siswa->tempat_lahir }},
                        {{ \Carbon\Carbon::parse($siswa->tanggal_lahir)->format('d M Y') }}
                    </td>
                    <td>
                        <span class="badge bg-label-secondary">Alumni</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        Tidak ada data alumni
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- ================= FOOTER (INI KUNCI CSS) ================= --}}
    <div class="card-footer border-top">
        <div class="row align-items-center g-2">

            {{-- PER PAGE --}}
            <div class="col-md-6 d-flex align-items-center">
                <span class="text-muted small me-2">Menampilkan</span>

                <form action="{{ route('admin.alumni.dataAlumni.index') }}" method="GET" class="d-inline-flex">
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <select name="per_page"
                            class="form-select form-select-sm w-auto"
                            onchange="this.form.submit()">
                        <option value="15" @selected(request('per_page') == 15)>15</option>
                        <option value="35" @selected(request('per_page') == 35)>35</option>
                        <option value="50" @selected(request('per_page') == 50)>50</option>
                        <option value="all" @selected(request('per_page') == 'all')>Semua</option>
                    </select>
                </form>

                <span class="text-muted small ms-2">
                    dari <strong>{{ $siswas->total() }}</strong> data
                </span>
            </div>

            {{-- PAGINATION --}}
            <div class="col-md-6 d-flex justify-content-md-end justify-content-center">
                @if(request('per_page') !== 'all')
                    {{ $siswas->appends(request()->query())->links() }}
                @endif
            </div>

        </div>
    </div>
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
            
            if (checkedCheckboxes.length > 0) {
                viewSelectedBtn.style.display = 'inline-block';
                exportSelectedLink.classList.remove('disabled');
                
                // Ubah teks tombol jika cuma 1 yang dipilih
                if(checkedCheckboxes.length === 1) {
                    viewSelectedBtn.innerHTML = '<i class="bx bx-show-alt me-1"></i> Lihat Detail';
                } else {
                    viewSelectedBtn.innerHTML = '<i class="bx bx-show-alt me-1"></i> Lihat Data (' + checkedCheckboxes.length + ')';
                }

            } else {
                viewSelectedBtn.style.display = 'none';
                exportSelectedLink.classList.add('disabled');
            }
        }

        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                rowCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                handleCheckboxChange();
            });
        }

        rowCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', handleCheckboxChange);
        });

        // ACTION: LIHAT DATA (PINTAR)
        if (viewSelectedBtn) {
            viewSelectedBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const checkedCheckboxes = document.querySelectorAll('.row-checkbox:checked');
                
                if (checkedCheckboxes.length === 1) {
                    // Jika cuma 1, langsung buka detailnya
                    const id = checkedCheckboxes[0].value;
                    // Pastikan URL-nya benar manual karena JS tidak bisa parse route() PHP dynamic parameter dengan mudah di sini
                    // Kita ambil base URL dari window location atau hardcode prefix
                    window.location.href = `/admin/alumni/dataAlumni/${id}`; 
                } else if (checkedCheckboxes.length > 1) {
                    // Jika banyak, buka show-multiple
                    const selectedIds = Array.from(checkedCheckboxes).map(cb => cb.value).join(',');
                    let url = `{{ route('admin.alumni.show-multiple') }}?ids=${selectedIds}`;
                    window.location.href = url;
                }
            });
        }

        // ACTION: EXPORT SELECTED
        if (exportSelectedLink) {
            exportSelectedLink.addEventListener('click', function(e) {
                if (this.classList.contains('disabled')) return;
                e.preventDefault();
                const selectedIds = Array.from(document.querySelectorAll('.row-checkbox:checked')).map(cb => cb.value).join(',');

                if (selectedIds) {
                    window.location.href = url;
                }
            });
        }

        handleCheckboxChange();
    });
</script>
@endpush