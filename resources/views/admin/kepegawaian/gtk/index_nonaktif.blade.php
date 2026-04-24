@extends('layouts.admin')

@use('App\Services\EncryptionService')

@section('content')
    <style>
        .rounded-4 { border-radius: 1rem !important; }
        .shadow-soft { box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08) !important; }
        .filter-select { border: 1px solid #d9dee3 !important; border-radius: 0.5rem; color: #566a7f; font-weight: 500; transition: 0.3s; }
        .filter-select:focus { border-color: #696cff !important; box-shadow: 0 0 0 0.25rem rgba(105, 108, 255, 0.1) !important; outline: none; }
        .filter-search-group { background-color: #fff; border: 1px solid #d9dee3; border-radius: 0.5rem; transition: all 0.3s; overflow: hidden; }
        .filter-search-group:focus-within { border-color: #696cff; box-shadow: 0 0 0 0.25rem rgba(105, 108, 255, 0.1); }
        .filter-search-group input { border: none; background: transparent; box-shadow: none !important; }
        .table-hover>tbody>tr { transition: all 0.2s ease; }
        .table-hover>tbody>tr:hover { background-color: rgba(255, 62, 29, 0.03); transform: translateY(-1px); }
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4 animate__animated animate__fadeInDown">
            <span class="text-muted fw-light">Kepegawaian /</span> Data GTK Non-Aktif
        </h4>

        <div class="card border-0 shadow-soft rounded-4 animate__animated animate__fadeInUp">
            <div class="card-header bg-transparent py-4 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-danger"><i class="bx bx-user-x me-2"></i>Daftar GTK (Mutasi/Keluar/Pensiun)</h5>
                <span class="badge bg-label-danger">Arsip Data</span>
            </div>

            <div class="card-body mt-4 pb-2">
                <form action="{{ route('admin.gtk.nonaktif.index') }}" method="GET" id="filterForm">
                    <input type="hidden" name="per_page" value="{{ request('per_page', 15) }}">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-2">Kabupaten/Kota</label>
                            <select name="kabupaten_kota" id="selectKabupaten" class="form-select filter-select auto-submit">
                                <option value="">- Semua Wilayah -</option>
                                @foreach ($listKabupaten as $kab)
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
                            <a href="{{ route('admin.gtk.nonaktif.index') }}" class="btn btn-outline-danger w-100 rounded-pill fw-bold shadow-xs">
                                <i class="bx bx-reset me-1"></i> Reset
                            </a>
                        </div>

                        <div class="col-12 mt-3">
                            <div class="filter-search-group d-flex align-items-center pe-1">
                                <span class="ps-3 text-muted"><i class="bx bx-search fs-5 mt-1"></i></span>
                                <input type="text" id="searchInput" name="search" class="form-control px-3 py-2"
                                    placeholder="Cari Nama, NIP, atau Status (Mutasi, Keluar, dll)..." value="{{ request('search') }}" autocomplete="off">
                                <button type="submit" class="btn btn-danger btn-sm rounded fw-bold my-1 ms-1 px-4">Cari</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div id="tableContainer" class="mt-2">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th width="1%" class="ps-4">No</th>
                                <th class="py-3 text-uppercase text-muted" style="font-size: 0.75rem; font-weight: 700;">Nama & Identitas</th>
                                <th class="py-3 text-uppercase text-muted" style="font-size: 0.75rem; font-weight: 700;">Asal Sekolah</th>
                                <th class="py-3 text-uppercase text-muted text-center" style="font-size: 0.75rem; font-weight: 700;">L/P</th>
                                <th class="py-3 text-uppercase text-muted text-center" style="font-size: 0.75rem; font-weight: 700;">Status Akhir</th>
                                <th class="py-3 text-uppercase text-muted" style="font-size: 0.75rem; font-weight: 700;">NIK & NUPTK</th>
                                <th width="1%" class="py-3 text-uppercase text-muted text-end pe-4" style="font-size: 0.75rem; font-weight: 700;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0">
                            @forelse ($gtks as $index => $gtk)
                                <tr>
                                    <td class="ps-4 fw-medium text-muted">{{ $gtks->firstItem() + $index }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-md me-3 flex-shrink-0">
                                                @php
                                                    $cleanPath = str_replace('public/', '', $gtk->foto ?? '');
                                                    $fileExists = !empty($cleanPath) && Storage::disk('public')->exists($cleanPath);
                                                @endphp
                                                @if ($fileExists)
                                                    <img src="{{ asset('storage/' . $cleanPath) }}" alt="Avatar" class="rounded-circle shadow-xs" style="object-fit: cover;">
                                                @else
                                                    <div class="avatar-initial rounded-circle bg-label-secondary shadow-xs d-flex align-items-center justify-content-center">
                                                        <i class='bx bx-user fs-4'></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="d-flex flex-column text-truncate" style="max-width: 250px;">
                                                <span class="fw-bold text-dark text-truncate">{{ $gtk->nama }}</span>
                                                <small class="text-muted font-monospace" style="font-size: 0.75rem;">
                                                    @if ($gtk->nip && trim($gtk->nip) != '-') NIP. {{ $gtk->nip }} @else <span class="opacity-50">NIP: -</span> @endif
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <small class="fw-bold text-muted d-block text-truncate" style="max-width: 150px;">
                                            <i class="bx bx-buildings me-1"></i> {{ $gtk->sekolah->nama ?? '-' }}
                                        </small>
                                    </td>
                                    <td class="text-center font-monospace">{{ $gtk->jenis_kelamin }}</td>
                                    <td class="text-center">
                                        @php
                                            $badgeClass = 'bg-label-danger';
                                            if(str_contains(strtolower($gtk->status), 'mutasi')) $badgeClass = 'bg-label-warning';
                                            if(str_contains(strtolower($gtk->status), 'pensiun')) $badgeClass = 'bg-label-info';
                                        @endphp
                                        <span class="badge {{ $badgeClass }} rounded-pill px-3 py-1 fw-bold" style="font-size: 0.7rem;">
                                            {{ $gtk->status ?? 'Non-Aktif' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold small text-primary">NIK: {{ EncryptionService::decrypt($gtk->nik) ?? '-' }}</span>
                                            <small class="text-muted">NUPTK: {{ $gtk->nuptk ?? '-' }}</small>
                                        </div>
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="{{ route('admin.gtk.show', $gtk->id) }}" class="btn btn-sm btn-icon btn-label-secondary rounded-circle" data-bs-toggle="tooltip" title="Lihat Profil">
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
                                            <h6 class="fw-bold text-dark mb-1">Tidak Ada Data GTK Non-Aktif</h6>
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
                             <span class="text-muted fw-semibold small">Total: <span class="text-dark">{{ $gtks->total() }}</span> Data</span>
                        </div>
                        <div class="col-md-6 d-flex justify-content-md-end">
                            {{ $gtks->links() }}
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

            // CASCADING RESET LOGIC
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

            // Live Search Debounce
            let timeout = null;
            $('#searchInput').on('input', function() {
                document.getElementById('tableContainer').style.opacity = '0.5';
                clearTimeout(timeout);
                timeout = setTimeout(() => filterForm.submit(), 600);
            });
        });
    </script>
@endpush
