@extends('layouts.admin')

@section('content')
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Kepegawaian /</span> Data Guru</h4>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar Guru</h5>
        {{-- Tombol ini akan berubah fungsi & teks via JS --}}
        <a href="#" id="viewSelectedBtn" class="btn btn-info btn-sm" style="display: none;">
            <i class="bx bx-show"></i> Lihat Detail
        </a>
    </div>

    <div class="card-body">
        <form action="{{ route('admin.kepegawaian.guru.index') }}" method="GET" id="filterForm">
            <input type="hidden" name="per_page" value="{{ request('per_page', 15) }}">
            
            {{-- FILTER AREA --}}
            @if(isset($listKabupaten) && count($listKabupaten) > 0)
            <div class="row g-3 mb-4 p-3 bg-lighter rounded">
                <div class="col-12"><small class="text-muted fw-bold text-uppercase">Filter Data</small></div>
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
                    <a href="{{ route('admin.kepegawaian.guru.index') }}" class="btn btn-outline-danger w-100"><i class="bx bx-refresh"></i> Reset</a>
                </div>
            </div>
            @endif

            <div class="row g-2">
                <div class="col-12">
                    <div class="input-group input-group-merge">
                        <span class="input-group-text bg-white"><i class="bx bx-search"></i></span>
                        <input type="text" id="searchInput" name="search" class="form-control" placeholder="Cari Nama, NIP, atau NIK..." value="{{ request('search') }}" autocomplete="off">
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div id="tableContainer">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                        <th width="1%"><input class="form-check-input" type="checkbox" id="selectAllCheckbox"></th>
                        <th>Nama & Identitas</th>
                        @if(isset($listKabupaten) && count($listKabupaten) > 0)
                            <th>Asal Sekolah</th>
                        @endif
                        <th>L/P</th>
                        <th>Status</th>
                        <th>Jabatan</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($gurus as $gtk)
                    <tr>
                        <td><input class="form-check-input row-checkbox" type="checkbox" value="{{ $gtk->id }}"></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-wrapper me-3">
                                    <div class="avatar avatar-sm">
                                        @php
                                            $cleanPath = str_replace('public/', '', $gtk->foto ?? '');
                                            $fileExists = !empty($cleanPath) && Storage::disk('public')->exists($cleanPath);
                                        @endphp
                                        @if($fileExists)
                                            <img src="{{ asset('storage/' . $cleanPath) }}" alt="Avatar" class="rounded-circle" style="width: 100%; height: 100%; object-fit: cover;">
                                        @else
                                            <span class="avatar-initial rounded-circle bg-label-primary fw-bold">{{ substr($gtk->nama, 0, 2) }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="d-flex flex-column">
                                    <span class="fw-semibold text-body text-truncate">{{ $gtk->nama }}</span>
                                    <small class="text-muted">
                                        @if($gtk->nip && trim($gtk->nip) != '-') NIP: {{ $gtk->nip }}
                                        @elseif($gtk->nuptk && trim($gtk->nuptk) != '-') NUPTK: {{ $gtk->nuptk }}
                                        @else - @endif
                                    </small>
                                </div>
                            </div>
                        </td>
                        @if(isset($listKabupaten) && count($listKabupaten) > 0)
                        <td><span class="badge bg-label-dark">{{ $gtk->pengguna && $gtk->pengguna->sekolah ? \Illuminate\Support\Str::limit($gtk->pengguna->sekolah->nama, 30) : '-' }}</span></td>
                        @endif
                        <td>{{ ($gtk->jenis_kelamin == 'L' || $gtk->jenis_kelamin == 'Laki-laki') ? 'L' : 'P' }}</td>
                        <td><span class="badge bg-label-info">{{ $gtk->status_kepegawaian_id_str ?? '-' }}</span></td>
                        <td>{{ $gtk->jabatan_ptk_id_str ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center">
                                <i class="bx bx-user-x bx-lg text-muted mb-2"></i>
                                <h6 class="text-muted">Tidak ada data guru ditemukan.</h6>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer border-top">
            {{ $gurus->appends(request()->query())->links() }}
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

        // --- 1. LIVE SEARCH ---
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                tableContainer.style.opacity = '0.5';
                clearTimeout(timeout);
                timeout = setTimeout(function() {
                    const formData = new FormData(filterForm);
                    const params = new URLSearchParams(formData).toString();
                    const url = `{{ route('admin.kepegawaian.guru.index') }}?${params}`;
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

        // --- 2. CHECKBOX & TOMBOL DETAIL (LOGIKA BARU) ---
        function initCheckboxListeners() {
            const selectAll = document.getElementById('selectAllCheckbox');
            const btnView = document.getElementById('viewSelectedBtn');

            function toggleBtn() {
                const checkedCount = document.querySelectorAll('.row-checkbox:checked').length;
                if (checkedCount > 0) {
                    btnView.style.display = 'inline-block';
                    // Jika pilih 1, teksnya "Lihat Profil". Jika banyak, "Lihat Data Terpilih"
                    btnView.innerHTML = checkedCount === 1 
                        ? '<i class="bx bx-user me-1"></i> Lihat Profil Guru' 
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

            // --- KLIK TOMBOL DETAIL ---
            if(btnView && !btnView.hasAttribute('data-listening')) {
                btnView.setAttribute('data-listening', 'true');
                btnView.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
                    const ids = Array.from(checkedBoxes).map(cb => cb.value);

                    if (ids.length === 0) return;

                    if (ids.length === 1) {
                        // JIKA 1 DATA: Redirect ke Halaman Profil (Show)
                        // Menggunakan replace untuk memasukkan ID ke route
                        var url = "{{ route('admin.kepegawaian.gtk.show', ':id') }}";
                        url = url.replace(':id', ids[0]);
                        window.location.href = url;
                    } else {
                        // JIKA BANYAK DATA: Redirect ke Halaman Multiple
                        var url = `{{ route('admin.kepegawaian.gtk.show-multiple') }}?ids=${ids.join(',')}`;
                        window.location.href = url;
                    }
                });
            }
        }

        initCheckboxListeners();
    });
</script>
@endpush