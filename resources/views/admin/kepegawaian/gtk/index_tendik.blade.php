@extends('layouts.admin')

@section('content')
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">GTK /</span> Data Tendik</h4>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar Tenaga Kependidikan</h5>
        {{-- Tombol ini akan berubah fungsi & teks via JS --}}
        <a href="#" id="viewSelectedBtn" class="btn btn-info btn-sm" style="display: none;">
            <i class="bx bx-show"></i> Lihat Detail
        </a>
    </div>

    <div class="card-body">
        <form action="{{ route('admin.gtk.tendik.index') }}" method="GET" id="filterForm">
            <input type="hidden" name="per_page" value="{{ request('per_page', 15) }}">
            
            {{-- FILTER AREA --}}
            <div class="row g-3 mb-4 p-3 bg-lighter rounded">
                <div class="col-12"><small class="text-muted fw-bold text-uppercase">Filter Data</small></div>
                
                {{-- Filter Kabupaten --}}
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

                {{-- Filter Kecamatan --}}
                <div class="col-md-3">
                    <select name="kecamatan" class="form-select select2" onchange="this.form.submit()" {{ empty($listKecamatan) ? 'disabled' : '' }}>
                        <option value="">- Semua Kecamatan -</option>
                        @foreach($listKecamatan as $kec)
                            <option value="{{ $kec }}" {{ request('kecamatan') == $kec ? 'selected' : '' }}>{{ str_replace('Kec. ', '', $kec) }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Sekolah --}}
                <div class="col-md-4">
                    <select name="sekolah_id" class="form-select select2" onchange="this.form.submit()" {{ empty($listSekolah) ? 'disabled' : '' }}>
                        <option value="">- Semua Sekolah -</option>
                        @foreach($listSekolah as $idSekolah => $namaSekolah)
                            <option value="{{ $idSekolah }}" {{ request('sekolah_id') == $idSekolah ? 'selected' : '' }}>{{ $namaSekolah }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Tombol Reset --}}
                <div class="col-md-2">
                    <a href="{{ route('admin.gtk.tendik.index') }}" class="btn btn-outline-danger w-100"><i class="bx bx-refresh"></i> Reset</a>
                </div>
            </div>

            {{-- SEARCH INPUT --}}
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
                        {{-- Select All Dihapus, diganti # --}}
                        <th width="1%" class="text-center">#</th>
                        <th>Nama & Identitas</th>
                        <th>Unit Kerja</th>
                        <th>Jenis PTK</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($tendiks as $tendik)
                    <tr>
                        <td><input class="form-check-input row-checkbox" type="checkbox" value="{{ $tendik->id }}"></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-wrapper me-3">
                                    <div class="avatar avatar-sm">
                                        @php
                                            $cleanPath = str_replace('public/', '', $tendik->foto ?? '');
                                            $fileExists = !empty($cleanPath) && Storage::disk('public')->exists($cleanPath);
                                        @endphp
                                        @if($fileExists)
                                            <img src="{{ asset('storage/' . $cleanPath) }}" alt="Avatar" class="rounded-circle" style="width: 100%; height: 100%; object-fit: cover;">
                                        @else
                                            <span class="avatar-initial rounded-circle bg-label-warning fw-bold">{{ substr($tendik->nama, 0, 2) }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="d-flex flex-column">
                                    <span class="fw-semibold text-body text-truncate">{{ $tendik->nama }}</span>
                                    <small class="text-muted">
                                        @if($tendik->nip && trim($tendik->nip) != '-') NIP: {{ $tendik->nip }}
                                        @elseif($tendik->nuptk && trim($tendik->nuptk) != '-') NUPTK: {{ $tendik->nuptk }}
                                        @else - @endif
                                    </small>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($tendik->pengguna && $tendik->pengguna->sekolah)
                                <div class="d-flex flex-column">
                                    <span class="fw-medium text-truncate" style="max-width: 200px;">{{ $tendik->pengguna->sekolah->nama }}</span>
                                    <small class="text-muted">{{ $tendik->pengguna->sekolah->kecamatan ?? '-' }}</small>
                                </div>
                            @else 
                                <span class="text-muted fst-italic">-</span> 
                            @endif
                        </td>
                        <td><span class="badge bg-label-info">{{ $tendik->jenis_ptk_id_str ?? '-' }}</span></td>
                        <td><span class="badge bg-label-success">{{ $tendik->status_kepegawaian_id_str ?? '-' }}</span></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center">
                                <i class="bx bx-user-x bx-lg text-muted mb-2"></i>
                                <h6 class="text-muted">Data tendik tidak ditemukan.</h6>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer border-top">
            {{ $tendiks->appends(request()->query())->links() }}
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
                    
                    // Route khusus Tendik
                    const url = `{{ route('admin.gtk.tendik.index') }}?${params}`;
                    
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

        // --- 2. CHECKBOX & TOMBOL DETAIL (TANPA SELECT ALL) ---
        function initCheckboxListeners() {
            const btnView = document.getElementById('viewSelectedBtn');

            function toggleBtn() {
                const checkedCount = document.querySelectorAll('.row-checkbox:checked').length;
                if (checkedCount > 0) {
                    btnView.style.display = 'inline-block';
                    // Jika pilih 1, teksnya "Lihat Profil". Jika banyak, "Lihat Data Terpilih"
                    btnView.innerHTML = checkedCount === 1 
                        ? '<i class="bx bx-user me-1"></i> Lihat Profil Tendik' 
                        : `<i class="bx bx-list-check me-1"></i> Lihat Data Terpilih (${checkedCount})`;
                } else {
                    btnView.style.display = 'none';
                }
            }

            // Hanya listen checkbox per baris (bisa pilih banyak secara manual)
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
                        // JIKA 1 DATA: Redirect ke Profil
                        var url = "{{ route('admin.gtk.show', ':id') }}";
                        url = url.replace(':id', ids[0]);
                        window.location.href = url;
                    } else {
                        // JIKA BANYAK DATA: Redirect ke Multiple
                        var url = `{{ route('admin.gtk.show-multiple') }}?ids=${ids.join(',')}`;
                        window.location.href = url;
                    }
                });
            }
        }

        initCheckboxListeners();
    });
</script>
@endpush