@extends('layouts.admin')

@section('content')
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Kepegawaian /</span> Data Guru</h4>

<div class="card">
    {{-- HEADER: JUDUL & TOMBOL AKSI ATAS (HANYA LIHAT DATA) --}}
    <div class="card-header">
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="card-title m-0 me-2">Daftar Guru</h5>
            <div>
                {{-- Tombol Lihat Data (Muncul saat checkbox dipilih) --}}
                <a href="#" id="viewSelectedBtn" class="btn btn-info btn-sm" style="display: none;">
                    <i class="bx bx-show-alt me-1"></i> Lihat Data
                </a>
            </div>
        </div>
    </div>

    {{-- FILTER WILAYAH & PENCARIAN --}}
    <div class="card-body">
        <form action="{{ route('admin.kepegawaian.guru.index') }}" method="GET" id="filterForm">
            <input type="hidden" name="per_page" value="{{ request('per_page', 15) }}">

            <div class="row g-2">
                {{-- Filter Kabupaten --}}
                <div class="col-md-3">
                    <select name="kabupaten_kota" class="form-select" onchange="this.form.submit()">
                        <option value="">- Semua Kab/Kota -</option>
                        @foreach($listKabupaten as $kab)
                            <option value="{{ $kab }}" {{ request('kabupaten_kota') == $kab ? 'selected' : '' }}>
                                {{ $kab }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Kecamatan --}}
                <div class="col-md-3">
                    <select name="kecamatan" class="form-select" onchange="this.form.submit()">
                        <option value="">- Semua Kecamatan -</option>
                        @foreach($listKecamatan as $kec)
                            <option value="{{ $kec }}" {{ request('kecamatan') == $kec ? 'selected' : '' }}>
                                {{ $kec }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Pencarian --}}
                <div class="col-md-6">
                    <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="bx bx-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Cari Nama, NIP, atau NIK..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary">Cari</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- TABEL DATA --}}
    <div class="table-responsive text-nowrap">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th width="1%"><input class="form-check-input" type="checkbox" id="selectAllCheckbox"></th>
                    <th>Status Induk</th>
                    <th>Identitas Guru</th>
                    <th>NIK</th>
                    <th>L/P</th>
                    <th>Domisili</th>
                    <th>Status Pegawai</th>
                    <th>Jenis GTK</th>
                    <th>Jabatan</th>
                    <th>NUPTK</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse ($gurus as $gtk)
                <tr>
                    {{-- Checkbox --}}
                    <td><input class="form-check-input row-checkbox" type="checkbox" value="{{ $gtk->id }}"></td>

                    {{-- Status Induk --}}
                    <td>
                        <span class="badge bg-label-{{ $gtk->ptk_induk == 1 ? 'success' : 'secondary' }}">
                            {{ $gtk->ptk_induk == 1 ? 'Induk' : 'Non-Induk' }}
                        </span>
                    </td>

                    {{-- Nama & Foto --}}
                    <td style="min-width: 250px;">
                        <div class="d-flex justify-content-start align-items-center">
                            <div class="avatar-wrapper me-3">
                                <div class="avatar avatar-sm">
                                    @if(!empty($gtk->foto) && \Illuminate\Support\Facades\Storage::disk('public')->exists($gtk->foto))
                                        <img src="{{ asset('storage/' . $gtk->foto) }}" alt="Avatar" class="rounded-circle" style="width: 100%; height: 100%; object-fit: cover;object-position: 50% 20%; ">
                                    @else
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($gtk->nama) }}&background=random&color=ffffff&size=100" alt="Avatar" class="rounded-circle">
                                    @endif
                                </div>
                            </div>
                            <div class="d-flex flex-column">
                                <span class="fw-semibold text-truncate text-body">{{ $gtk->nama }}</span>
                                <small class="text-muted">
                                    @if($gtk->nip && trim($gtk->nip) != '-') NIP: {{ $gtk->nip }}
                                    @elseif($gtk->nuptk && trim($gtk->nuptk) != '-') NUPTK: {{ $gtk->nuptk }}
                                    @else - @endif
                                </small>
                            </div>
                        </div>
                    </td>

                    <td>{{ $gtk->nik ?? '-' }}</td>
                    <td>{{ ($gtk->jenis_kelamin == 'L' || $gtk->jenis_kelamin == 'Laki-laki') ? 'L' : 'P' }}</td>
                    
                    {{-- Domisili --}}
                    <td>
                        <div class="d-flex flex-column">
                            <span class="small fw-semibold">{{ $gtk->kecamatan ?? '-' }}</span>
                            <small class="text-muted">{{ $gtk->kabupaten_kota ?? '-' }}</small>
                        </div>
                    </td>

                    <td><span class="badge bg-label-primary">{{ $gtk->status_kepegawaian_id_str ?? '-' }}</span></td>
                    <td><span class="badge bg-label-info">{{ $gtk->jenis_ptk_id_str ?? '-' }}</span></td>
                    <td>{{ $gtk->jabatan_ptk_id_str ?? '-' }}</td>
                    <td>{{ $gtk->nuptk ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center py-5">
                        <div class="d-flex flex-column align-items-center justify-content-center">
                            <i class="bx bx-user-x bx-lg text-muted mb-2"></i>
                            <h6 class="text-muted">Tidak ada data guru ditemukan.</h6>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- FOOTER PAGINATION --}}
    <div class="card-footer border-top">
        <div class="row align-items-center">
            <div class="col-md-6 d-flex align-items-center mb-2 mb-md-0">
                <span class="text-muted me-2 small">Menampilkan</span>
                <form action="{{ route('admin.kepegawaian.guru.index') }}" method="GET" class="d-inline-block">
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <input type="hidden" name="kabupaten_kota" value="{{ request('kabupaten_kota') }}">
                    <input type="hidden" name="kecamatan" value="{{ request('kecamatan') }}">
                    
                    <select name="per_page" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
                        <option value="15" {{ request('per_page') == '15' ? 'selected' : '' }}>15</option>
                        <option value="35" {{ request('per_page') == '35' ? 'selected' : '' }}>35</option>
                        <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50</option>
                        <option value="all" {{ request('per_page') == 'all' ? 'selected' : '' }}>Semua</option>
                    </select>
                </form>
                <span class="text-muted ms-2 small">dari <strong>{{ $gurus->total() }}</strong> data</span>
            </div>
            <div class="col-md-6 d-flex justify-content-md-end justify-content-center">
                @if(request('per_page') != 'all')
                    {{ $gurus->appends(request()->query())->links() }}
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
        
        // --- Logic Checkbox & Tombol Lihat Data ---
        function handleCheckboxChange() {
            const checkedCheckboxes = document.querySelectorAll('.row-checkbox:checked');

            if (checkedCheckboxes.length > 0) {
                viewSelectedBtn.style.display = 'inline-block';
                
                if(checkedCheckboxes.length === 1) {
                    viewSelectedBtn.innerHTML = '<i class="bx bx-show-alt me-1"></i> Lihat Data';
                } else {
                    viewSelectedBtn.innerHTML = '<i class="bx bx-show-alt me-1"></i> Lihat Data (' + checkedCheckboxes.length + ')';
                }
            } else {
                viewSelectedBtn.style.display = 'none';
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

        // --- Logic Tombol Lihat Data (Multiple) ---
        if (viewSelectedBtn) {
            viewSelectedBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const checkedCheckboxes = document.querySelectorAll('.row-checkbox:checked');

                if (checkedCheckboxes.length > 0) {
                    const selectedIds = Array.from(checkedCheckboxes).map(cb => cb.value).join(',');
                    // Arahkan ke route show multiple
                    let url = `{{ route('admin.kepegawaian.gtk.show-multiple') }}?ids=${selectedIds}`;
                    window.location.href = url;
                }
            });
        }

        // Init Checkbox State
        handleCheckboxChange();
    });
</script>
@endpush