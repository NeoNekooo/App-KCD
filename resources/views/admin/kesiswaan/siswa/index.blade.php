@extends('layouts.admin')

@section('content')

{{-- 🔥 CSS PREMIUM & ANIMASI 🔥 --}}
<style>
    .rounded-4 { border-radius: 1rem !important; }
    .shadow-soft { box-shadow: 0 8px 25px rgba(0, 0, 0, 0.04) !important; }
    .filter-select { border: 1px solid #d9dee3 !important; border-radius: 0.5rem; color: #566a7f; font-weight: 500; transition: 0.3s; }
    .filter-select:focus { border-color: #696cff !important; box-shadow: 0 0 0 0.25rem rgba(105, 108, 255, 0.1) !important; outline: none; }
    .filter-search-group { background-color: #fff; border: 1px solid #d9dee3; border-radius: 0.5rem; transition: all 0.3s; overflow: hidden; }
    .filter-search-group:focus-within { border-color: #696cff; box-shadow: 0 0 0 0.25rem rgba(105, 108, 255, 0.1); }
    .filter-search-group input { border: none; background: transparent; box-shadow: none !important; }
    .table-hover > tbody > tr { transition: all 0.2s ease; }
    .table-hover > tbody > tr:hover { background-color: rgba(105, 108, 255, 0.03); transform: translateY(-1px); }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4 animate__animated animate__fadeInDown">
        <span class="text-muted fw-light">Kesiswaan /</span> Data Siswa
    </h4>

    <div class="card border-0 shadow-soft rounded-4 animate__animated animate__fadeInUp">
        <div class="card-header bg-transparent py-4 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-dark">Daftar Siswa</h5>
            
            <div class="d-flex gap-2">
                {{-- Tombol Export Excel --}}
                <button type="button" id="btnExport" class="btn btn-success btn-sm rounded-pill shadow-xs">
                    <i class="bx bx-spreadsheet me-1"></i> Export Excel
                </button>
            </div>
        </div>

        <div class="card-body mt-4 pb-2">
            <form action="{{ route('admin.kesiswaan.siswa.index') }}" method="GET" id="filterForm">
                <input type="hidden" name="per_page" value="{{ request('per_page', 15) }}">
                
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-2">Kabupaten/Kota</label>
                        <select name="kabupaten_kota" id="selectKabupaten" class="form-select filter-select auto-submit">
                            <option value="">- Semua Wilayah -</option>
                            @foreach($listKabupaten as $kab)
                                @php $kabValue = str_replace(['Kab. ', 'Kota '], '', $kab); @endphp
                                <option value="{{ $kabValue }}" {{ request('kabupaten_kota') == $kabValue ? 'selected' : '' }}>{{ $kabValue }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-2">Kecamatan</label>
                        <select name="kecamatan" id="selectKecamatan" class="form-select filter-select auto-submit" {{ empty($listKecamatan) ? 'disabled' : '' }}>
                            <option value="">- Semua Kecamatan -</option>
                            @foreach($listKecamatan as $kec)
                                @php $kecValue = str_replace('Kec. ', '', $kec); @endphp
                                <option value="{{ $kecValue }}" {{ request('kecamatan') == $kecValue ? 'selected' : '' }}>{{ $kecValue }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-2">Satuan Pendidikan</label>
                        <select name="sekolah_id" id="selectSekolah" class="form-select filter-select auto-submit" {{ empty($listSekolah) ? 'disabled' : '' }}>
                            <option value="">- Semua Sekolah -</option>
                            @foreach($listSekolah as $idS => $namaS)
                                <option value="{{ $idS }}" {{ request('sekolah_id') == $idS ? 'selected' : '' }}>{{ $namaS }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <a href="{{ route('admin.kesiswaan.siswa.index') }}" class="btn btn-outline-danger w-100 rounded-pill fw-bold shadow-xs">
                            <i class="bx bx-reset me-1"></i> Reset
                        </a>
                    </div>

                    <div class="col-12 mt-3">
                        <div class="filter-search-group d-flex align-items-center pe-1">
                            <span class="ps-3 text-muted"><i class="bx bx-search fs-5 mt-1"></i></span>
                            <input type="text" id="searchInput" name="search" class="form-control px-3 py-2" placeholder="Cari Nama Siswa, NISN, NIK, atau Nama Sekolah..." value="{{ request('search') }}" autocomplete="off">
                            <button type="submit" class="btn btn-primary btn-sm rounded fw-bold my-1 ms-1 px-4">Cari</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div id="tableContainer" class="mt-2">
            <div class="table-responsive text-nowrap">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th width="1%" class="ps-4 text-center">No</th> 
                            <th class="py-3 text-uppercase text-muted" style="font-size: 0.75rem; font-weight: 700;">Identitas Siswa</th>
                            <th class="py-3 text-uppercase text-muted" style="font-size: 0.75rem; font-weight: 700;">Asal Sekolah</th>
                            <th class="py-3 text-uppercase text-muted text-center" style="font-size: 0.75rem; font-weight: 700;">L/P</th>
                            <th class="py-3 text-uppercase text-muted" style="font-size: 0.75rem; font-weight: 700;">Nomor Induk</th>
                            <th class="py-3 text-uppercase text-muted" style="font-size: 0.75rem; font-weight: 700;">Kelas</th>
                            <th width="1%" class="py-3 text-uppercase text-muted text-end pe-4" style="font-size: 0.75rem; font-weight: 700;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse ($siswas as $index => $siswa)
                        <tr>
                            <td class="ps-4 text-center fw-medium text-muted">
                                {{ $siswas->firstItem() + $index }}
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-md me-3 flex-shrink-0">
                                        @php
                                            $cleanPath = str_replace('public/', '', $siswa->foto ?? '');
                                            $fileExists = !empty($cleanPath) && Storage::disk('public')->exists($cleanPath);
                                        @endphp
                                        @if($fileExists)
                                            <img src="{{ asset('storage/' . $cleanPath) }}" alt="Avatar" class="rounded-circle shadow-xs" style="object-fit: cover;">
                                        @else
                                            <div class="avatar-initial rounded-circle bg-label-primary shadow-xs d-flex align-items-center justify-content-center">
                                                <i class='bx bx-user fs-4'></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="d-flex flex-column text-truncate" style="max-width: 250px;">
                                        <span class="fw-bold text-dark text-truncate">{{ $siswa->nama }}</span>
                                        <small class="text-muted font-monospace" style="font-size: 0.75rem;">
                                            TTL: {{ $siswa->tempat_lahir ?? '-' }}, {{ $siswa->tanggal_lahir ? (\Carbon\Carbon::parse($siswa->tanggal_lahir)->format('d-m-Y')) : '-' }}
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-label-secondary rounded-pill px-3 py-2 fw-bold text-truncate" style="max-width: 200px;">
                                    <i class="bx bx-buildings me-1"></i> {{ $siswa->sekolah->nama ?? 'Belum Terhubung' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="fw-bold {{ ($siswa->jenis_kelamin == 'L' || $siswa->jenis_kelamin == 'Laki-laki') ? 'text-info' : 'text-danger' }}">
                                    {{ ($siswa->jenis_kelamin == 'L' || $siswa->jenis_kelamin == 'Laki-laki') ? 'L' : 'P' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold small text-primary">NISN: {{ \App\Services\EncryptionService::decrypt($siswa->nisn) ?? '-' }}</span>
                                    <small class="text-muted">NIK: {{ \App\Services\EncryptionService::decrypt($siswa->nik) ?? '-' }}</small>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-label-info rounded-pill px-3 py-1 fw-bold" style="font-size: 0.7rem;">
                                    {{ $siswa->rombel ? $siswa->rombel->nama : 'Belum Masuk Kelas' }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('admin.kesiswaan.siswa.show', $siswa->id) }}" class="btn btn-sm btn-icon btn-label-primary rounded-circle shadow-none" data-bs-toggle="tooltip" title="Lihat Profil Siswa">
                                    <i class="bx bx-show-alt"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center my-3">
                                    <div class="avatar avatar-xl bg-label-secondary rounded-circle mb-3 d-flex justify-content-center align-items-center">
                                        <i class="bx bx-user-x fs-1"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1">Data Siswa Tidak Ditemukan</h6>
                                    <p class="text-muted small mb-0">Sesuaikan filter wilayah atau kata pencarian Anda.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer border-top bg-transparent py-3 px-4">
                <div class="row align-items-center g-3">
                    <div class="col-md-6 d-flex align-items-center flex-wrap gap-2">
                        <span class="text-muted fw-semibold small">Baris per halaman:</span>
                        <form action="{{ route('admin.kesiswaan.siswa.index') }}" method="GET" class="d-inline-block m-0">
                            @foreach(request()->except(['per_page', 'page']) as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach
                            <select name="per_page" class="form-select border-0 shadow-xs bg-light text-dark fw-bold py-1 px-3" style="width: auto; border-radius: 20px; font-size: 0.8rem;" onchange="this.form.submit()">
                                <option value="15" {{ request('per_page') == '15' ? 'selected' : '' }}>15</option>
                                <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100</option>
                            </select>
                        </form>
                        <span class="text-muted fw-semibold small ms-2 border-start ps-3">Total: <span class="text-dark">{{ $siswas->total() }}</span> Siswa</span>
                    </div>
                    <div class="col-md-6 d-flex justify-content-md-end small-pagination">
                        {{ $siswas->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterForm = document.getElementById('filterForm');
        
        // --- 1. CASCADING FILTER LOGIC ---
        $('#selectKabupaten').on('change', function() {
            $('#selectKecamatan').val(''); 
            $('#selectSekolah').val('');   
            filterForm.submit();
        });

        $('#selectKecamatan').on('change', function() {
            $('#selectSekolah').val('');   
            filterForm.submit();
        });

        $('#selectSekolah').on('change', function() {
            filterForm.submit();
        });

        // --- 2. LIVE SEARCH DEBOUNCE ---
        const searchInput = document.getElementById('searchInput');
        const tableContainer = document.getElementById('tableContainer');
        let timeout = null;
        
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                tableContainer.style.opacity = '0.5';
                clearTimeout(timeout);
                timeout = setTimeout(() => filterForm.submit(), 600); 
            });
        }

        // --- 3. EXPORT EXCEL LOGIC ---
        const btnExport = document.getElementById('btnExport');
        if(btnExport) {
            btnExport.addEventListener('click', function(e) {
                e.preventDefault();
                let url = "{{ route('admin.kesiswaan.siswa.export-excel') }}";
                const currentParams = window.location.search; 
                
                // Export akan membawa parameter URL saat ini (search & filter)
                if (currentParams) {
                    url += currentParams;
                }
                
                window.open(url, '_blank');
            });
        }

        // Init Tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });
</script>
@endpush