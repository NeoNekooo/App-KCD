 @extends('layouts.admin')

@section('content')
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Kesiswaan /</span> Data Siswa Tidak Aktif/Lulus</h4>

<div class="card">
    {{-- HEADER: JUDUL & TOMBOL AKSI --}}
    <div class="card-header">
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="card-title m-0 me-2">Daftar Siswa</h5>
            <div class="d-flex gap-2">
                {{-- Tombol Lihat Data (Muncul saat checkbox dipilih) --}}
                <a href="#" id="viewSelectedBtn" class="btn btn-info btn-sm" style="display: none;">
                    <i class="bx bx-show-alt me-1"></i> Lihat Data
                </a>

                {{-- Tombol Export --}}
                <div class="dropdown">
                    <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bx bx-export me-1"></i> Export
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('admin.kesiswaan.siswa.export.excel', request()->query()) }}">Export Semua</a></li>
                        <li><a class="dropdown-item disabled" href="javascript:void(0);" id="exportSelectedLink">Export yang Dipilih</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- BODY: FILTER & PENCARIAN --}}
    <div class="card-body">
        <form action="{{ route('admin.kesiswaan.siswa.index') }}" method="GET" id="filterForm">
            <input type="hidden" name="per_page" value="{{ request('per_page', 15) }}">

            <div class="row g-2">
                {{-- Filter Rombel --}}
                <div class="col-md-3">
                    <select name="rombel_id" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Kelas</option>
                        @foreach($rombels as $rombel)
                            <option value="{{ $rombel->id }}" {{ request('rombel_id') == $rombel->id ? 'selected' : '' }}>
                                {{ $rombel->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Pencarian --}}
                <div class="col-md-9">
                    <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="bx bx-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Cari Nama, NISN, atau NIK Siswa..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary">Cari</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- TABLE: DATA SISWA --}}
    <div class="table-responsive text-nowrap">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th width="1%"><input class="form-check-input" type="checkbox" id="selectAllCheckbox"></th>
                    <th>Nama Siswa</th>
                    <th>L/P</th>
                    <th>NISN</th>
                    <th>TTL</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse ($siswas as $siswa)
                <tr>
                    {{-- Checkbox Row --}}
                    <td><input class="form-check-input row-checkbox" type="checkbox" value="{{ $siswa->id }}"></td>

                    {{-- KOLOM NAMA & FOTO (BISA DIKLIK) --}}
                    <td style="min-width: 250px;">
                        <div class="d-flex justify-content-start align-items-center">
                            {{-- Avatar Wrapper --}}
                            <div class="avatar-wrapper me-3">
                                <a href="{{ route('admin.kesiswaan.siswa.show', $siswa->id) }}">
                                    <div class="avatar avatar-sm">
                                        @if(!empty($siswa->foto) && \Illuminate\Support\Facades\Storage::disk('public')->exists($siswa->foto))
                                            <img src="{{ asset('storage/' . $siswa->foto) }}" alt="Avatar" class="rounded-circle" style="width: 100%; height: 100%; object-fit: cover;">
                                        @else
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($siswa->nama) }}&background=random&color=ffffff&size=100"
                                                 alt="Avatar"
                                                 class="rounded-circle"
                                                 style="width: 100%; height: 100%; object-fit: cover;">
                                        @endif
                                    </div>
                                </a>
                            </div>

                            {{-- Nama Wrapper (Link ke Show) --}}
                            <div class="d-flex flex-column">
                                <a href="{{ route('admin.kesiswaan.siswa.show', $siswa->id) }}" class="text-body text-truncate fw-semibold">
                                    {{ $siswa->nama }}
                                </a>
                                <small class="text-muted">
                                    {{ $siswa->nis ? 'NIS: '.$siswa->nis : ($siswa->nik ? 'NIK: '.$siswa->nik : '-') }}
                                </small>
                            </div>
                        </div>
                    </td>

                    <td>{{ ($siswa->jenis_kelamin == 'L' || $siswa->jenis_kelamin == 'Laki-laki') ? 'L' : 'P' }}</td>
                    <td>{{ $siswa->nisn ?? '-' }}</td>
                    <td>
                        {{ $siswa->tempat_lahir ?? '' }},
                        {{ $siswa->tanggal_lahir ? \Carbon\Carbon::parse($siswa->tanggal_lahir)->format('d M Y') : '-' }}
                    </td>

                    {{-- Badge Rombel --}}
                    <td>
                            <span class="badge bg-label-primary">
                                {{ $siswa->status}}
                            </span>

                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <div class="d-flex flex-column align-items-center justify-content-center">
                            <i class="bx bx-user-x bx-lg text-muted mb-2"></i>
                            <h6 class="text-muted">Tidak ada data siswa ditemukan.</h6>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- FOOTER: PAGINATION & PER PAGE --}}
    <div class="card-footer border-top">
        <div class="row align-items-center">
            <div class="col-md-6 d-flex align-items-center mb-2 mb-md-0">
                <span class="text-muted me-2 small">Menampilkan</span>
                <form action="{{ route('admin.kesiswaan.siswa.index') }}" method="GET" class="d-inline-block">
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <input type="hidden" name="rombel_id" value="{{ request('rombel_id') }}">
                    <select name="per_page" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
                        <option value="15" {{ request('per_page') == '15' ? 'selected' : '' }}>15</option>
                        <option value="35" {{ request('per_page') == '35' ? 'selected' : '' }}>35</option>
                        <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50</option>
                        <option value="all" {{ request('per_page') == 'all' ? 'selected' : '' }}>Semua</option>
                    </select>
                </form>
                <span class="text-muted ms-2 small">dari <strong>{{ $siswas->total() }}</strong> data</span>
            </div>
            <div class="col-md-6 d-flex justify-content-md-end justify-content-center">
                @if(request('per_page') != 'all')
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
                    window.location.href = `/admin/kesiswaan/siswa/${id}`;
                } else if (checkedCheckboxes.length > 1) {
                    // Jika banyak, buka show-multiple
                    const selectedIds = Array.from(checkedCheckboxes).map(cb => cb.value).join(',');
                    let url = `{{ route('admin.kesiswaan.siswa.show-multiple') }}?ids=${selectedIds}`;
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
                    let url = `{{ route('admin.kesiswaan.siswa.export.excel') }}?ids=${selectedIds}`;
                    window.location.href = url;
                }
            });
        }

        handleCheckboxChange();
    });
</script>
@endpush
