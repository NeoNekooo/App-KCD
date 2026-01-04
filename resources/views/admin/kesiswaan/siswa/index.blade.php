@extends('layouts.admin')

@section('content')
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Kesiswaan /</span> Data Siswa</h4>

<div class="card">
    {{-- HEADER & BUTTONS --}}
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar Siswa</h5>
        
        {{-- Tombol Lihat Detail (Muncul via JS) --}}
        <a href="#" id="viewSelectedBtn" class="btn btn-info btn-sm" style="display: none;">
            <i class="bx bx-show"></i> Lihat Detail
        </a>
    </div>

    {{-- FILTER & SEARCH --}}
    <div class="card-body">
        <form action="{{ route('admin.kesiswaan.siswa.index') }}" method="GET" id="filterForm">
            <input type="hidden" name="per_page" value="{{ request('per_page', 15) }}">

            @if(isset($listKabupaten) && count($listKabupaten) > 0)
            <div class="row g-3 mb-4 p-3 bg-lighter rounded">
                <div class="col-12"><small class="text-muted fw-bold text-uppercase">Filter Wilayah</small></div>
                <div class="col-md-3">
                    <select name="kabupaten_kota" class="form-select select2" onchange="this.form.submit()">
                        <option value="">- Semua Kab/Kota -</option>
                        @foreach($listKabupaten as $kab)
                            <option value="{{ $kab }}" {{ request('kabupaten_kota') == $kab ? 'selected' : '' }}>
                                {{ str_replace(['Kab. ', 'Kota '], '', $kab) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="kecamatan" class="form-select select2" onchange="this.form.submit()" {{ empty($listKecamatan) ? 'disabled' : '' }}>
                        <option value="">- Semua Kecamatan -</option>
                        @foreach($listKecamatan as $kec)
                            <option value="{{ $kec }}" {{ request('kecamatan') == $kec ? 'selected' : '' }}>{{ str_replace('Kec. ', '', $kec) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <select name="sekolah_id" class="form-select select2" onchange="this.form.submit()" {{ empty($listSekolah) ? 'disabled' : '' }}>
                        <option value="">- Semua Sekolah -</option>
                        @foreach($listSekolah as $idSekolah => $namaSekolah)
                            <option value="{{ $idSekolah }}" {{ request('sekolah_id') == $idSekolah ? 'selected' : '' }}>{{ $namaSekolah }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('admin.kesiswaan.siswa.index') }}" class="btn btn-outline-danger w-100"><i class="bx bx-refresh"></i> Reset</a>
                </div>
            </div>
            @endif

            <div class="row g-2">
                <div class="col-12">
                    <div class="input-group input-group-merge">
                        <span class="input-group-text bg-white"><i class="bx bx-search"></i></span>
                        <input type="text" id="searchInput" name="search" class="form-control" placeholder="Cari Nama Siswa, NISN, atau NIK..." value="{{ request('search') }}" autocomplete="off">
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- TABLE CONTAINER --}}
    <div id="tableContainer">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                        <th width="1%"><input class="form-check-input" type="checkbox" id="selectAllCheckbox"></th>
                        <th>Identitas Siswa</th>
                        @if(isset($listKabupaten) && count($listKabupaten) > 0)
                            <th>Asal Sekolah</th>
                        @endif
                        <th>L/P</th>
                        <th>Nomor Induk</th>
                        <th>Kelas</th>
                        {{-- KOLOM DETAIL DIHAPUS --}}
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($siswas as $siswa)
                    <tr>
                        <td><input class="form-check-input row-checkbox" type="checkbox" value="{{ $siswa->id }}"></td>
                        <td style="min-width: 250px;">
                            <div class="d-flex align-items-center">
                                <div class="avatar me-3">
                                    {{-- Menggunakan logic public path fix agar gambar aman --}}
                                    @php
                                        $cleanPath = str_replace('public/', '', $siswa->foto ?? '');
                                        // Jika ada accessor foto_url, bisa pakai itu, tapi ini fallback manual yg aman
                                        $fileExists = !empty($cleanPath) && \Illuminate\Support\Facades\Storage::disk('public')->exists($cleanPath);
                                    @endphp
                                    @if($fileExists)
                                        <img src="{{ asset('storage/' . $cleanPath) }}" alt="Avatar" class="rounded-circle" style="object-fit: cover;">
                                    @else
                                        <span class="avatar-initial rounded-circle bg-label-primary fw-bold">{{ substr($siswa->nama, 0, 2) }}</span>
                                    @endif
                                </div>
                                <div class="d-flex flex-column">
                                    <span class="fw-semibold text-body text-truncate">{{ $siswa->nama }}</span>
                                    <small class="text-muted">TTL: {{ $siswa->tempat_lahir }}, {{ $siswa->tanggal_lahir ? date('d-m-Y', strtotime($siswa->tanggal_lahir)) : '-' }}</small>
                                </div>
                            </div>
                        </td>

                        @if(isset($listKabupaten) && count($listKabupaten) > 0)
                        <td>
                            <span class="badge bg-label-dark">
                                {{ $siswa->pengguna && $siswa->pengguna->sekolah ? \Illuminate\Support\Str::limit($siswa->pengguna->sekolah->nama, 30) : '-' }}
                            </span>
                        </td>
                        @endif

                        <td>{{ ($siswa->jenis_kelamin == 'L' || $siswa->jenis_kelamin == 'Laki-laki') ? 'L' : 'P' }}</td>
                        
                        <td>
                            <div class="d-flex flex-column">
                                <span class="fw-bold small">{{ $siswa->nisn ?? '-' }}</span>
                                <small class="text-muted">{{ $siswa->nik ?? '-' }}</small>
                            </div>
                        </td>

                        <td><span class="badge bg-label-primary">{{ $siswa->rombel ? $siswa->rombel->nama : 'Belum Masuk Kelas' }}</span></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ (isset($listKabupaten) && count($listKabupaten) > 0) ? '6' : '5' }}" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center">
                                <i class="bx bx-user-x bx-lg text-muted mb-2"></i>
                                <h6 class="text-muted">Tidak ada data siswa ditemukan.</h6>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer border-top">
            {{ $siswas->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        const searchInput = document.getElementById('searchInput');
        const tableContainer = document.getElementById('tableContainer');
        const filterForm = document.getElementById('filterForm');
        let timeout = null;

        // 1. LIVE SEARCH
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                tableContainer.style.opacity = '0.5';
                clearTimeout(timeout);
                timeout = setTimeout(function() {
                    const formData = new FormData(filterForm);
                    const params = new URLSearchParams(formData).toString();
                    const url = `{{ route('admin.kesiswaan.siswa.index') }}?${params}`;
                    window.history.replaceState({}, '', url);

                    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(r => r.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        tableContainer.innerHTML = doc.getElementById('tableContainer').innerHTML;
                        tableContainer.style.opacity = '1';
                        initCheckboxListeners();
                    });
                }, 300); 
            });
        }

        // 2. CHECKBOX & BUTTON LOGIC
        function initCheckboxListeners() {
            const selectAll = document.getElementById('selectAllCheckbox');
            const btnView = document.getElementById('viewSelectedBtn');

            function toggleBtn() {
                const checkedCount = document.querySelectorAll('.row-checkbox:checked').length;
                if (checkedCount > 0) {
                    btnView.style.display = 'inline-block';
                    btnView.innerHTML = checkedCount === 1 
                        ? '<i class="bx bx-user me-1"></i> Lihat Profil Siswa' 
                        : `<i class="bx bx-list-check me-1"></i> Lihat Data Terpilih (${checkedCount})`;
                } else {
                    btnView.style.display = 'none';
                }
            }

            if(selectAll) {
                const newSelectAll = selectAll.cloneNode(true);
                selectAll.parentNode.replaceChild(newSelectAll, selectAll);
                newSelectAll.addEventListener('change', function() {
                    document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = this.checked);
                    toggleBtn();
                });
            }

            document.querySelectorAll('.row-checkbox').forEach(cb => {
                cb.addEventListener('change', toggleBtn);
            });

            // 3. ACTION CLICK
            if(btnView && !btnView.hasAttribute('data-listening')) {
                btnView.setAttribute('data-listening', 'true');
                btnView.addEventListener('click', function(e) {
                    e.preventDefault();
                    const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
                    const ids = Array.from(checkedBoxes).map(cb => cb.value);

                    if (ids.length === 0) return;

                    if (ids.length === 1) {
                        // Single: Go to Profile
                        var url = "{{ route('admin.kesiswaan.siswa.show', ':id') }}";
                        url = url.replace(':id', ids[0]);
                        window.location.href = url;
                    } else {
                        // Multiple: Go to List Comparison
                        var url = `{{ route('admin.kesiswaan.siswa.show-multiple') }}?ids=${ids.join(',')}`;
                        window.location.href = url;
                    }
                });
            }
        }

        initCheckboxListeners();
    });
</script>
@endpush