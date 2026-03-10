@extends('layouts.admin')

@section('content')
    <style>
        .rounded-4 {
            border-radius: 1rem !important;
        }

        .shadow-soft {
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08) !important;
        }

        .filter-select {
            border: 1px solid #d9dee3 !important;
            border-radius: 0.5rem;
            color: #566a7f;
            font-weight: 500;
            transition: 0.3s;
        }

        .filter-select:focus {
            border-color: #696cff !important;
            box-shadow: 0 0 0 0.25rem rgba(105, 108, 255, 0.1) !important;
            outline: none;
        }

        .filter-search-group {
            background-color: #fff;
            border: 1px solid #d9dee3;
            border-radius: 0.5rem;
            transition: all 0.3s;
            overflow: hidden;
        }

        .filter-search-group:focus-within {
            border-color: #696cff;
            box-shadow: 0 0 0 0.25rem rgba(105, 108, 255, 0.1);
        }

        .filter-search-group input {
            border: none;
            background: transparent;
            box-shadow: none !important;
        }

        .table-hover>tbody>tr {
            transition: all 0.2s ease;
        }

        .table-hover>tbody>tr:hover {
            background-color: rgba(105, 108, 255, 0.03);
            transform: translateY(-1px);
        }
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4 animate__animated animate__fadeInDown">
            <span class="text-muted fw-light">Kepegawaian /</span> Data Tenaga Kependidikan
        </h4>

        <div class="card border-0 shadow-soft rounded-4 animate__animated animate__fadeInUp">
            <div class="card-header bg-transparent py-4 border-bottom">
                <h5 class="mb-0 fw-bold text-dark">Daftar Tendik Wilayah KCD</h5>
            </div>

            <div class="card-body mt-4 pb-2">
                <form action="{{ route('admin.gtk.tendik.index') }}" method="GET" id="filterForm">
                    <input type="hidden" name="per_page" value="{{ request('per_page', 15) }}">

                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-2">Kabupaten/Kota</label>
                            <select name="kabupaten_kota" id="selectKabupaten"
                                class="form-select filter-select auto-submit">
                                <option value="">- Semua Wilayah -</option>
                                @foreach ($listKabupaten as $kab)
                                    @php $kabValue = str_replace(['Kab. ', 'Kota '], '', $kab); @endphp
                                    <option value="{{ $kabValue }}"
                                        {{ request('kabupaten_kota') == $kabValue ? 'selected' : '' }}>{{ $kabValue }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-2">Kecamatan</label>
                            <select name="kecamatan" id="selectKecamatan" class="form-select filter-select auto-submit"
                                {{ empty($listKecamatan) ? 'disabled' : '' }}>
                                <option value="">- Semua Kecamatan -</option>
                                @foreach ($listKecamatan as $kec)
                                    @php $kecValue = str_replace('Kec. ', '', $kec); @endphp
                                    <option value="{{ $kecValue }}"
                                        {{ request('kecamatan') == $kecValue ? 'selected' : '' }}>{{ $kecValue }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-2">Satuan Pendidikan</label>
                            <select name="sekolah_id" id="selectSekolah" class="form-select filter-select auto-submit"
                                {{ empty($listSekolah) ? 'disabled' : '' }}>
                                <option value="">- Semua Sekolah -</option>
                                @foreach ($listSekolah as $idS => $namaS)
                                    <option value="{{ $idS }}"
                                        {{ request('sekolah_id') == $idS ? 'selected' : '' }}>{{ $namaS }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <a href="{{ route('admin.gtk.tendik.index') }}"
                                class="btn btn-outline-danger w-100 rounded-pill fw-bold shadow-xs">
                                <i class="bx bx-reset me-1"></i> Reset
                            </a>
                        </div>

                        <div class="col-12 mt-3">
                            <div class="filter-search-group d-flex align-items-center pe-1">
                                <span class="ps-3 text-muted"><i class="bx bx-search fs-5 mt-1"></i></span>
                                <input type="text" id="searchInput" name="search" class="form-control px-3 py-2"
                                    placeholder="Cari Nama Tendik, NIK, atau NIP..." value="{{ request('search') }}"
                                    autocomplete="off">
                                <button type="submit"
                                    class="btn btn-primary btn-sm rounded fw-bold my-1 ms-1 px-4">Cari</button>
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
                                <th class="py-3 text-uppercase text-muted" style="font-size: 0.75rem; font-weight: 700;">
                                    Nama & Identitas</th>
                                <th class="py-3 text-uppercase text-muted" style="font-size: 0.75rem; font-weight: 700;">
                                    Asal Sekolah</th>
                                <th class="py-3 text-uppercase text-muted text-center"
                                    style="font-size: 0.75rem; font-weight: 700;">Induk</th>
                                <th class="py-3 text-uppercase text-muted text-center"
                                    style="font-size: 0.75rem; font-weight: 700;">Jenis PTK</th>
                                <th class="py-3 text-uppercase text-muted" style="font-size: 0.75rem; font-weight: 700;">
                                    Status Kepegawaian</th>
                                <th width="1%" class="py-3 text-uppercase text-muted text-end pe-4"
                                    style="font-size: 0.75rem; font-weight: 700;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0">
                            @forelse ($tendiks as $index => $gtk)
                                <tr>
                                    <td class="ps-4 fw-medium text-muted">{{ $tendiks->firstItem() + $index }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-md me-3 flex-shrink-0">
                                                @php
                                                    $cleanPath = str_replace('public/', '', $gtk->foto ?? '');
                                                    $fileExists =
                                                        !empty($cleanPath) &&
                                                        Storage::disk('public')->exists($cleanPath);
                                                @endphp
                                                @if ($fileExists)
                                                    <img src="{{ asset('storage/' . $cleanPath) }}" alt="Avatar"
                                                        class="rounded-circle shadow-xs" style="object-fit: cover;">
                                                @else
                                                    {{-- 🔥 GANTI JADI ICON ORANG 🔥 --}}
                                                    <div
                                                        class="avatar-initial rounded-circle bg-label-warning shadow-xs d-flex align-items-center justify-content-center">
                                                        <i class='bx bx-user fs-4'></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="d-flex flex-column text-truncate" style="max-width: 250px;">
                                                <a href="{{ route('admin.gtk.show', $gtk->id) }}"
                                                    class="fw-bold text-primary text-decoration-none text-truncate hover-underline"
                                                    title="Lihat Profil {{ $gtk->nama }}">
                                                    {{ $gtk->nama }}
                                                </a>
                                                <small class="text-muted font-monospace" style="font-size: 0.75rem;">
                                                    @if ($gtk->nip && trim($gtk->nip) != '-')
                                                        <span class="text-primary">NIP. {{ $gtk->nip }}</span>
                                                    @elseif($gtk->nik && trim($gtk->nik) != '-')
                                                        <span class="text-info">NIK. {{ $gtk->nik }}</span>
                                                    @else
                                                        <span class="opacity-50">Belum ada Identitas</span>
                                                    @endif
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-label-secondary rounded-pill px-3 py-2 fw-bold text-truncate"
                                            style="max-width: 200px;">
                                            <i class="bx bx-buildings me-1"></i>
                                            {{ $gtk->sekolah->nama ?? 'Belum Terhubung' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span
                                            class="badge bg-label-{{ $gtk->ptk_induk == 1 ? 'success' : 'secondary' }} rounded-pill px-3 py-1 fw-bold"
                                            style="font-size: 0.7rem;">
                                            {{ $gtk->ptk_induk == 1 ? 'Ya' : 'Tidak' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-label-dark rounded-pill px-3 py-1 fw-bold"
                                            style="font-size: 0.7rem;">
                                            {{ $gtk->jenis_ptk_id_str ?? '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span
                                            class="text-dark small fw-medium">{{ $gtk->status_kepegawaian_id_str ?? '-' }}</span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="{{ route('admin.gtk.show', $gtk->id) }}"
                                            class="btn btn-sm btn-icon btn-label-warning rounded-circle shadow-none"
                                            data-bs-toggle="tooltip" title="Lihat Profil Tendik">
                                            <i class="bx bx-show-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center my-3">
                                            <div
                                                class="avatar avatar-xl bg-label-secondary rounded-circle mb-3 d-flex justify-content-center align-items-center">
                                                <i class="bx bx-user-x fs-1"></i>
                                            </div>
                                            <h6 class="fw-bold text-dark mb-1">Data Tendik Tidak Ditemukan</h6>
                                            <p class="text-muted small mb-0">Sesuaikan filter wilayah atau kata pencarian
                                                Anda.</p>
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
                            <form action="{{ route('admin.gtk.tendik.index') }}" method="GET"
                                class="d-inline-block m-0">
                                @foreach (request()->except(['per_page', 'page']) as $key => $value)
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endforeach
                                <select name="per_page"
                                    class="form-select border-0 shadow-xs bg-light text-dark fw-bold py-1 px-3"
                                    style="width: auto; border-radius: 20px; font-size: 0.8rem;"
                                    onchange="this.form.submit()">
                                    <option value="15" {{ request('per_page') == '15' ? 'selected' : '' }}>15</option>
                                    <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100
                                    </option>
                                </select>
                            </form>
                            <span class="text-muted fw-semibold small ms-2 border-start ps-3">Total: <span
                                    class="text-dark">{{ $tendiks->total() }}</span> Tendik</span>
                        </div>
                        <div class="col-md-6 d-flex justify-content-md-end small-pagination">
                            {{ $tendiks->appends(request()->query())->links() }}
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

            let timeout = null;
            $('#searchInput').on('input', function() {
                document.getElementById('tableContainer').style.opacity = '0.5';
                clearTimeout(timeout);
                timeout = setTimeout(() => filterForm.submit(), 600);
            });

            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        });
    </script>
@endpush
