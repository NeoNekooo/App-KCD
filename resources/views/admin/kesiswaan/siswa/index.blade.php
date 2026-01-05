@extends('layouts.admin')

@section('content')
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Kesiswaan /</span> Data Siswa</h4>

<div class="card">
    {{-- HEADER & BUTTONS --}}
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar Siswa</h5>
        
        <div>
            {{-- Tombol Export Excel (Selalu Muncul) --}}
            <button type="button" id="btnExport" class="btn btn-success btn-sm">
                <i class="bx bx-spreadsheet me-1"></i> Export Excel
            </button>

            {{-- Tombol Lihat Detail (Muncul via JS saat checkbox dipilih) --}}
            <button type="button" id="viewSelectedBtn" class="btn btn-primary btn-sm ms-2" style="display: none;">
                <i class="bx bx-user me-1"></i> Lihat Detail Siswa
            </button>
        </div>
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
                        <input type="text" id="searchInput" name="search" class="form-control" placeholder="Cari Nama Siswa, NISN, NIK, atau Nama Sekolah..." value="{{ request('search') }}" autocomplete="off">
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
                        <th width="1%" class="text-center">#</th>
                        <th>Identitas Siswa</th>
                        @if(isset($listKabupaten) && count($listKabupaten) > 0)
                            <th>Asal Sekolah</th>
                        @endif
                        <th>L/P</th>
                        <th>Nomor Induk</th>
                        <th>Kelas</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($siswas as $siswa)
                    <tr onclick="toggleRow(this)" style="cursor: pointer;">
                        <td class="text-center" onclick="event.stopPropagation()">
                            <input class="form-check-input row-checkbox" type="checkbox" name="selected_ids[]" value="{{ $siswa->id }}">
                        </td>
                        <td style="min-width: 250px;">
                            <div class="d-flex align-items-center">
                                <div class="avatar me-3">
                                    @php
                                        $cleanPath = str_replace('public/', '', $siswa->foto ?? '');
                                        $fileExists = !empty($cleanPath) && Storage::disk('public')->exists($cleanPath);
                                    @endphp
                                    @if($fileExists)
                                        <img src="{{ asset('storage/' . $cleanPath) }}" alt="Avatar" class="rounded-circle" style="width: 100%; height: 100%; object-fit: cover;">
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

        // 2. LOGIKA CHECKBOX, DETAIL, & EXPORT
        function initCheckboxListeners() {
            const btnView = document.getElementById('viewSelectedBtn');
            const btnExport = document.getElementById('btnExport'); // Ambil tombol export
            const checkboxes = document.querySelectorAll('.row-checkbox');

            function updateButtonState() {
                const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
                const count = checkedBoxes.length;

                // Handle Tombol Detail
                if (count > 0) {
                    btnView.style.display = 'inline-block';
                    if (count === 1) {
                        btnView.innerHTML = '<i class="bx bx-user me-1"></i> Lihat Detail Siswa';
                    } else {
                        btnView.innerHTML = `<i class="bx bx-list-ul me-1"></i> Lihat ${count} Siswa Terpilih`;
                    }
                    // Update Text Export biar user tau dia mau export yang dipilih
                    btnExport.innerHTML = `<i class="bx bx-check-square me-1"></i> Export (${count}) Terpilih`;
                } else {
                    btnView.style.display = 'none';
                    // Balikin Text Export ke Default
                    btnExport.innerHTML = `<i class="bx bx-spreadsheet me-1"></i> Export Excel`;
                }
            }

            checkboxes.forEach(cb => {
                cb.addEventListener('change', updateButtonState);
            });

            window.toggleRow = function(row) {
                if (event.target.type !== 'checkbox') {
                    const cb = row.querySelector('.row-checkbox');
                    if (cb) {
                        cb.checked = !cb.checked;
                        cb.dispatchEvent(new Event('change'));
                    }
                }
            };

            // 3. ACTION KLIK TOMBOL VIEW
            if(btnView && !btnView.hasAttribute('data-listening')) {
                btnView.setAttribute('data-listening', 'true');
                btnView.addEventListener('click', function(e) {
                    e.preventDefault();
                    const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
                    const ids = Array.from(checkedBoxes).map(cb => cb.value);

                    if (ids.length === 0) return;

                    if (ids.length === 1) {
                        let url = "{{ route('admin.kesiswaan.siswa.show', ':id') }}";
                        url = url.replace(':id', ids[0]);
                        window.location.href = url;
                    } else {
                        let url = `{{ route('admin.kesiswaan.siswa.show-multiple') }}?ids=${ids.join(',')}`;
                        window.location.href = url;
                    }
                });
            }

            // 4. ACTION KLIK TOMBOL EXPORT
            if(btnExport && !btnExport.hasAttribute('data-listening')) {
                btnExport.setAttribute('data-listening', 'true');
                btnExport.addEventListener('click', function(e) {
                    e.preventDefault();
                    const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
                    let url = "{{ route('admin.kesiswaan.siswa.export-excel') }}";

                    // Logika: Jika ada yang dicentang -> Export ID itu saja
                    // Jika KOSONG -> Export semua sesuai filter/search URL saat ini
                    if (checkedBoxes.length > 0) {
                        const ids = Array.from(checkedBoxes).map(cb => cb.value);
                        url += `?ids=${ids.join(',')}`;
                    } else {
                        // Ambil parameter search/filter dari URL browser saat ini
                        const currentParams = window.location.search; 
                        if (currentParams) {
                            url += currentParams;
                        }
                    }
                    
                    // Buka di tab baru biar halaman gak refresh
                    window.open(url, '_blank');
                });
            }
        }

        initCheckboxListeners();
    });
</script>
@endpush