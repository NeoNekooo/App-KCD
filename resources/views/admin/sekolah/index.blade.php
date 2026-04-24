@extends('layouts.admin')

@section('content')

    {{-- 🔥 CSS PREMIUM: ROUNDED, ANIMATED & MODERN 🔥 --}}
    <style>
        .rounded-4 { border-radius: 1rem !important; }
        .rounded-5 { border-radius: 1.25rem !important; }
        .shadow-xs { box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.05) !important; }
        .shadow-soft { box-shadow: 0 8px 25px rgba(105, 108, 255, 0.08) !important; }
        
        /* Table Enhancements */
        .table-hover > tbody > tr { transition: all 0.2s ease; }
        .table-hover > tbody > tr:hover { background-color: rgba(105, 108, 255, 0.03); transform: translateY(-1px); }
        .table > :not(caption) > * > * { padding: 1rem 1.25rem; }
        
        /* Pagination Styling */
        .small-pagination .pagination { margin-bottom: 0; justify-content: flex-end; }
        .small-pagination .page-link { border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; margin: 0 2px; padding: 0; font-size: 0.85rem; font-weight: 600; color: #566a7f; border: none; transition: all 0.2s; }
        .small-pagination .page-item.active .page-link { background-color: #696cff; color: #fff; box-shadow: 0 2px 6px rgba(105, 108, 255, 0.4); }
        .small-pagination .page-item.disabled .page-link { color: #b4bdc6; background-color: transparent; }
        .small-pagination .page-link:hover:not(.active):not(.disabled) { background-color: rgba(105, 108, 255, 0.1); color: #696cff; }

        /* Animation Keyframes */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up { animation: fadeInUp 0.5s ease-out forwards; }
        
        /* Utility */
        .hover-primary:hover { color: #696cff !important; }
        
        /* Card Hover Effect */
        .stat-card { transition: transform 0.3s cubic-bezier(0.25, 0.8, 0.25, 1), box-shadow 0.3s ease; }
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }

        /* 🔥 Premium Filter Form Style 🔥 */
        .filter-select {
            background-color: #fff;
            border: 1px solid #d9dee3 !important; 
            border-radius: 0.5rem;
            color: #566a7f;
            font-weight: 500;
            transition: all 0.3s;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }
        .filter-select:focus {
            border-color: #696cff !important;
            box-shadow: 0 0 0 0.25rem rgba(105, 108, 255, 0.1) !important;
            outline: 0;
        }
        .filter-select:disabled {
            background-color: #f8f9fa;
            color: #a1acb8;
            border-color: #e4e6e8 !important;
            opacity: 1;
        }
        
        .filter-search-group {
            background-color: #fff;
            border: 1px solid #d9dee3;
            border-radius: 0.5rem;
            transition: all 0.3s;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
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
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">
        {{-- BREADCRUMB --}}
        <h4 class="fw-bold py-3 mb-4 animate-fade-in-up">
            <span class="text-muted fw-light">Monitoring /</span> Data Satuan Pendidikan
        </h4>

        {{-- 1. RINGKASAN JUMLAH SEKOLAH --}}
        <div class="row g-4 mb-4 animate-fade-in-up" style="animation-delay: 0.1s;">
            <div class="col-sm-6 col-xl-4">
                <div class="card h-100 border-0 stat-card shadow-sm rounded-4">
                    <div class="card-body d-flex align-items-center justify-content-between p-4">
                        <div>
                            <span class="d-block text-muted text-uppercase fw-bold mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">Total Sekolah</span>
                            <h2 class="mb-0 fw-bolder text-primary" style="font-size: 2.2rem;">{{ number_format($totalSekolah) }}</h2>
                        </div>
                        <div class="avatar avatar-lg shadow-xs rounded-circle bg-label-primary d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                            <i class="bx bx-buildings fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-sm-6 col-xl-4">
                <div class="card h-100 border-0 stat-card shadow-sm rounded-4">
                    <div class="card-body d-flex align-items-center justify-content-between p-4">
                        <div>
                            <span class="d-block text-muted text-uppercase fw-bold mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">Sekolah Negeri</span>
                            <h2 class="mb-0 fw-bolder text-success" style="font-size: 2.2rem;">{{ number_format($totalNegeri) }}</h2>
                        </div>
                        <div class="avatar avatar-lg shadow-xs rounded-circle bg-label-success d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                            <i class="bx bx-check-shield fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-sm-6 col-xl-4">
                <div class="card h-100 border-0 stat-card shadow-sm rounded-4">
                    <div class="card-body d-flex align-items-center justify-content-between p-4">
                        <div>
                            <span class="d-block text-muted text-uppercase fw-bold mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">Sekolah Swasta</span>
                            <h2 class="mb-0 fw-bolder text-warning" style="font-size: 2.2rem;">{{ number_format($totalSwasta) }}</h2>
                        </div>
                        <div class="avatar avatar-lg shadow-xs rounded-circle bg-label-warning d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                            <i class="bx bx-home-heart fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. MAIN CARD --}}
        <div class="card border-0 shadow-soft rounded-4 animate-fade-in-up" style="animation-delay: 0.2s;">
            
            {{-- HEADER & BUTTON EXPORT --}}
            <div class="card-header bg-transparent py-4 border-bottom d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <h5 class="card-title fw-bold m-0 text-dark">Daftar Sekolah Binaan</h5>
                <button type="button" id="btnExport" class="btn btn-success fw-bold rounded-pill shadow-sm px-4 py-2">
                    <i class="bx bx-spreadsheet me-1 fs-6"></i> Export Excel
                </button>
            </div>

            {{-- 🔥 FILTER FORM BERJENJANG (BERBORDER & ELEGAN) 🔥 --}}
            <div class="card-body mt-4 pb-1">
                <form action="{{ route('admin.sekolah.index') }}" method="GET" id="filterForm">
                    <input type="hidden" name="per_page" value="{{ request('per_page', 15) }}">
                    <div class="row g-3 align-items-end">
                        
                        {{-- 1. KABUPATEN --}}
                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-2">Kabupaten/Kota</label>
                            <select name="kabupaten_kota" id="selectKabupaten" class="form-select filter-select">
                                <option value="">- Semua Wilayah -</option>
                                @foreach($listKabupaten as $kab)
                                    @php $kabName = str_replace(['Kab. ', 'Kota '], '', $kab); @endphp
                                    <option value="{{ $kabName }}" {{ request('kabupaten_kota') == $kabName ? 'selected' : '' }}>{{ $kabName }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        {{-- 2. KECAMATAN --}}
                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-2">Kecamatan</label>
                            <select name="kecamatan" id="selectKecamatan" class="form-select filter-select" {{ !request('kabupaten_kota') ? 'disabled' : '' }}>
                                <option value="">- Pilih Kec -</option>
                                @if(request('kabupaten_kota') && !empty($listKecamatan))
                                    @foreach($listKecamatan as $kec)
                                        @php $kecName = str_replace('Kec. ', '', $kec); @endphp
                                        <option value="{{ $kecName }}" {{ request('kecamatan') == $kecName ? 'selected' : '' }}>{{ $kecName }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        {{-- 3. JENJANG --}}
                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-2">Jenjang</label>
                            <select name="jenjang" id="selectJenjang" class="form-select filter-select" {{ !request('kecamatan') ? 'disabled' : '' }}>
                                <option value="">- Semua Jenjang -</option>
                                @if(request('kecamatan') && !empty($listJenjang))
                                    @foreach($listJenjang as $j)
                                        <option value="{{ $j }}" {{ request('jenjang') == $j ? 'selected' : '' }}>{{ $j }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        {{-- 4. STATUS --}}
                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-2">Status</label>
                            <select name="status_sekolah" id="selectStatus" class="form-select filter-select" {{ !request('jenjang') ? 'disabled' : '' }}>
                                <option value="">- Semua Status -</option>
                                @if(request('jenjang') && !empty($listStatus))
                                    @foreach($listStatus as $s)
                                        <option value="{{ $s }}" {{ request('status_sekolah') == $s ? 'selected' : '' }}>{{ $s }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        {{-- 5. SEARCH, SUBMIT, & RESET --}}
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-2">Pencarian Cepat</label>
                            <div class="d-flex gap-2">
                                <div class="filter-search-group d-flex align-items-center flex-grow-1 pe-1">
                                    <span class="ps-3 text-muted"><i class="bx bx-search fs-5 mt-1"></i></span>
                                    <input type="text" name="search" class="form-control px-2" placeholder="Ketik nama / NPSN..." value="{{ request('search') }}">
                                    <button type="submit" class="btn btn-primary btn-sm rounded fw-bold my-1 ms-1 px-3">Cari</button>
                                </div>
                                
                                {{-- Tombol Reset Filter --}}
                                @if(request()->anyFilled(['kabupaten_kota', 'kecamatan', 'jenjang', 'status_sekolah', 'search']))
                                    <a href="{{ route('admin.sekolah.index') }}" class="btn btn-outline-danger btn-icon rounded shadow-xs" data-bs-toggle="tooltip" title="Reset Filter">
                                        <i class='bx bx-reset fs-5'></i>
                                    </a>
                                @endif
                            </div>
                        </div>

                    </div>
                </form>
            </div>

            <hr class="mt-4 mb-0 border-light">

            {{-- TABEL --}}
            <div class="table-responsive text-nowrap mt-2">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 text-uppercase text-muted" style="font-size: 0.75rem; font-weight: 700; width: 1%;">No</th>
                            <th class="py-3 text-uppercase text-muted" style="font-size: 0.75rem; font-weight: 700;">Satuan Pendidikan</th>
                            <th class="py-3 text-uppercase text-muted" style="font-size: 0.75rem; font-weight: 700;">NPSN</th>
                            <th class="py-3 text-uppercase text-muted" style="font-size: 0.75rem; font-weight: 700;">Jenjang</th>
                            <th class="py-3 text-uppercase text-muted" style="font-size: 0.75rem; font-weight: 700;">Status</th>
                            <th class="py-3 text-uppercase text-muted" style="font-size: 0.75rem; font-weight: 700;">Lokasi</th>
                            <th class="py-3 text-uppercase text-muted" style="font-size: 0.75rem; font-weight: 700;">Terakhir Sinkron</th>
                            <th class="py-3 text-uppercase text-muted text-end pe-4" style="font-size: 0.75rem; font-weight: 700;">Detail</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse ($sekolahs as $index => $sekolah)
                        <tr>
                            <td class="ps-4 fw-medium text-muted">{{ $sekolahs->firstItem() + $index }}</td>
                            <td style="min-width: 250px;">
                                <div class="d-flex align-items-center">
                                    <div class="avatar me-3 flex-shrink-0">
                                        <img src="{{ $sekolah->logo_url }}" alt="Logo" class="rounded-circle shadow-xs" 
                                            style="object-fit: cover; border: 2px solid #fff;" loading="lazy"
                                            onerror="this.onerror=null;this.src='{{ asset('assets/img/avatars/default-school.png') }}';">
                                    </div>
                                    <div class="d-flex flex-column text-truncate" style="max-width: 280px;">
                                        <a href="{{ route('admin.sekolah.show', $sekolah->id) }}" class="fw-bold text-dark text-truncate text-decoration-none hover-primary" title="Lihat Detail">{{ $sekolah->nama }}</a>
                                        <small class="text-muted text-truncate">{{ $sekolah->email ?? '-' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge bg-label-dark font-monospace">{{ $sekolah->npsn ?? '-' }}</span></td>
                            <td><span class="fw-semibold text-body">{{ $sekolah->bentuk_pendidikan_id_str ?? '-' }}</span></td>
                            <td>
                                <span class="badge rounded-pill bg-label-{{ ($sekolah->status_sekolah_str == 'Negeri') ? 'success' : 'warning' }} px-3 py-1 fw-bold" style="font-size: 0.7rem;">
                                    {{ $sekolah->status_sekolah_str ?? '-' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="text-dark small fw-bold">{{ str_replace('Kec. ', '', $sekolah->kecamatan ?? '-') }}</span>
                                    <small class="text-muted" style="font-size: 0.7rem;">{{ str_replace(['Kab. ', 'Kota '], '', $sekolah->kabupaten_kota ?? '-') }}</small>
                                </div>
                            </td>
                            
                            {{-- TERAKHIR SINKRON --}}
                            <td>
                                @if($sekolah->terakhir_sinkron)
                                    <div class="d-flex flex-column">
                                        <span class="badge bg-label-success mb-1" style="width: fit-content; font-size: 0.65rem;">
                                            <i class="bx bx-check-circle me-1"></i> {{ \Carbon\Carbon::parse($sekolah->terakhir_sinkron)->diffForHumans() }}
                                        </span>
                                        <small class="text-muted" style="font-size: 0.7rem;">
                                            {{ \Carbon\Carbon::parse($sekolah->terakhir_sinkron)->format('d M Y, H:i') }}
                                        </small>
                                    </div>
                                @else
                                    <span class="badge bg-label-secondary" data-bs-toggle="tooltip" title="Sekolah ini belum pernah melakukan sinkronisasi data">Belum Sinkron</span>
                                @endif
                            </td>

                            <td class="text-end pe-4">
                                <a href="{{ route('admin.sekolah.show', $sekolah->id) }}" class="btn btn-sm btn-icon btn-label-info rounded-circle shadow-none" data-bs-toggle="tooltip" title="Lihat Detail Sekolah">
                                    <i class="bx bx-show-alt"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center my-3">
                                    <div class="avatar avatar-xl bg-label-secondary rounded-circle mb-3 d-flex justify-content-center align-items-center">
                                        <i class="bx bx-buildings fs-1"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1">Data Sekolah Tidak Ditemukan</h6>
                                    <p class="text-muted small mb-0">Coba ubah kriteria pencarian atau filter Anda.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- PAGINATION --}}
            <div class="card-footer border-top bg-transparent py-3 px-4">
                <div class="row align-items-center g-3">
                    <div class="col-md-6 d-flex align-items-center flex-wrap gap-2">
                        <span class="text-muted fw-semibold small">Menampilkan:</span>
                        <form action="{{ route('admin.sekolah.index') }}" method="GET" class="d-inline-block m-0">
                            @foreach(request()->except(['per_page', 'page']) as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach
                            <select name="per_page" class="form-select border-0 shadow-xs bg-light text-dark fw-bold py-1 px-3" style="width: auto; border-radius: 20px; font-size: 0.8rem;" onchange="this.form.submit()">
                                <option value="15" {{ request('per_page') == '15' ? 'selected' : '' }}>15 Baris</option>
                                <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50 Baris</option>
                                <option value="all" {{ request('per_page') == 'all' ? 'selected' : '' }}>Semua Data</option>
                            </select>
                        </form>
                        <span class="text-muted fw-semibold small ms-2 border-start ps-3">Total: <span class="text-dark">{{ $sekolahs->total() }}</span> Sekolah</span>
                    </div>
                    <div class="col-md-6 d-flex justify-content-md-end small-pagination">
                        {{ $sekolahs->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
{{-- Butuh Jquery buat logic AJAX Dropdown --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function() {
        
        // Inisialisasi Tooltip
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // ==========================================
        // LOGIKA FILTER BERJENJANG (CASCADING AJAX)
        // ==========================================
        
        // 1. Saat Kabupaten Berubah -> Ambil data Kecamatan
        $('#selectKabupaten').on('change', function() {
            let kab = $(this).val();
            let $kecSelect = $('#selectKecamatan');
            let $jenjangSelect = $('#selectJenjang');
            let $statusSelect = $('#selectStatus');

            // Reset anak-anaknya ke state awal
            $kecSelect.html('<option value="">- Pilih Kec -</option>').prop('disabled', true);
            $jenjangSelect.html('<option value="">- Semua Jenjang -</option>').prop('disabled', true);
            $statusSelect.html('<option value="">- Semua Status -</option>').prop('disabled', true);

            if (kab) {
                // Tambahin loading text
                $kecSelect.html('<option value="">Memuat...</option>').prop('disabled', true);
                
                $.ajax({
                    url: "{{ route('admin.ajax.kecamatan') }}",
                    type: "GET",
                    data: { kabupaten: kab },
                    success: function(res) {
                        let html = '<option value="">- Pilih Kec -</option>';
                        $.each(res, function(i, val) {
                            let namaKec = val.replace('Kec. ', '');
                            html += `<option value="${namaKec}">${namaKec}</option>`;
                        });
                        $kecSelect.html(html).prop('disabled', false); // Aktifin Select Kecamatan
                    }
                });
            } else {
                // Jika user pilih "- Semua -" di Kabupaten, langsung submit form buat nampilin semua data se-Provinsi
                $('#filterForm').submit();
            }
        });

        // 2. Saat Kecamatan Berubah -> Ambil data Jenjang
        $('#selectKecamatan').on('change', function() {
            let kab = $('#selectKabupaten').val();
            let kec = $(this).val();
            let $jenjangSelect = $('#selectJenjang');
            let $statusSelect = $('#selectStatus');

            // Reset
            $jenjangSelect.html('<option value="">- Semua Jenjang -</option>').prop('disabled', true);
            $statusSelect.html('<option value="">- Semua Status -</option>').prop('disabled', true);

            if (kec) {
                $jenjangSelect.html('<option value="">Memuat...</option>').prop('disabled', true);
                
                $.ajax({
                    url: "{{ route('admin.ajax.jenjang') }}",
                    type: "GET",
                    data: { kabupaten: kab, kecamatan: kec },
                    success: function(res) {
                        let html = '<option value="">- Semua Jenjang -</option>';
                        $.each(res, function(i, val) {
                            html += `<option value="${val}">${val}</option>`;
                        });
                        $jenjangSelect.html(html).prop('disabled', false); // Aktifin Select Jenjang
                    }
                });
            } else {
                $('#filterForm').submit();
            }
        });

        // 3. Saat Jenjang Berubah -> Ambil data Status
        $('#selectJenjang').on('change', function() {
            let kab = $('#selectKabupaten').val();
            let kec = $('#selectKecamatan').val();
            let jenjang = $(this).val();
            let $statusSelect = $('#selectStatus');

            // Reset
            $statusSelect.html('<option value="">- Semua Status -</option>').prop('disabled', true);

            if (jenjang) {
                $statusSelect.html('<option value="">Memuat...</option>').prop('disabled', true);
                
                $.ajax({
                    url: "{{ route('admin.ajax.status') }}",
                    type: "GET",
                    data: { kabupaten: kab, kecamatan: kec, jenjang: jenjang },
                    success: function(res) {
                        let html = '<option value="">- Semua Status -</option>';
                        $.each(res, function(i, val) {
                            html += `<option value="${val}">${val}</option>`;
                        });
                        $statusSelect.html(html).prop('disabled', false); // Aktifin Select Status
                    }
                });
            } else {
                $('#filterForm').submit();
            }
        });

        // 4. Saat Status berubah, langsung submit form
        $('#selectStatus').on('change', function() {
            $('#filterForm').submit();
        });


        // Export Excel Logic
        const btnExport = document.getElementById('btnExport');
        if(btnExport) {
            btnExport.addEventListener('click', function(e) {
                e.preventDefault();
                let url = "{{ route('admin.sekolah.export-excel') }}";
                const currentParams = window.location.search; 
                if (currentParams) {
                    url += currentParams;
                }
                window.open(url, '_blank');
            });
        }
    });
</script>
@endpush