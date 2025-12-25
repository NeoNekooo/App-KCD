@extends('layouts.admin')

@section('title', 'Rekap Jadwal Pelajaran')

@push('styles')
<style>
    :root {
        --primary-color: #696cff;
        --secondary-bg: #f5f5f9;
        --border-color: #dfe3e7;
    }
    

    /* ==================================================
       1. FIX LAYOUT CHECKBOX & HILANGKAN DOUBLE CENTANG
       ================================================== */
    .btn-check-custom {
        position: relative; /* Penting untuk posisi input absolute */
        display: block;
        width: 100%;
        cursor: pointer;
        margin: 0;
    }

    /* Sembunyikan input asli sepenuhnya tapi tetap bisa diklik (menutupi area) */
    .btn-check-custom input[type="checkbox"] {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        z-index: 10;
        cursor: pointer;
    }

    /* Container utama (Kotak Putih) */
    .chip-body {
        display: flex !important;           /* Wajib flex */
        align-items: center !important;     /* Vertikal tengah */
        justify-content: flex-start !important; /* Mulai dari kiri */
        flex-direction: row !important;     /* Horizontal (Sejajar) */
        gap: 12px;                          /* Jarak antara kotak dan teks */

        width: 100%;
        padding: 10px 12px;
        background-color: #fff;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        transition: all 0.2s ease;
    }

    /* Kotak Custom Checkbox */
    .custom-checkbox-box {
        flex-shrink: 0; /* Agar kotak tidak mengecil/gepeng */
        width: 24px;
        height: 24px;
        border: 2px solid #b4b9bf;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #fff;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        color: #fff; /* Warna icon centang */
    }

    /* Icon Centang (Boxicons) */
    .custom-checkbox-box i {
        font-size: 16px;
        transform: scale(0); /* Awalnya icon tidak terlihat */
        transition: transform 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    /* Teks Kelas */
    .chip-text {
        font-size: 0.85rem;
        font-weight: 600;
        color: #566a7f;
        line-height: 1.2;
        /* Opsional: Jika nama kelas kepanjangan */
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* ============================
       STATE: SAAT DIPILIH (CHECKED)
       ============================ */

    /* Hover effect */
    .btn-check-custom:hover .chip-body {
        background-color: #f8f9fa;
        border-color: #b4b9bf;
    }

    /* Checked: Background Container */
    .btn-check-custom input:checked + .chip-body {
        background-color: #eff1ff;
        border-color: var(--primary-color);
        box-shadow: 0 2px 4px rgba(105, 108, 255, 0.15);
    }

    /* Checked: Kotak Checkbox berubah warna */
    .btn-check-custom input:checked + .chip-body .custom-checkbox-box {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }

    /* Checked: Munculkan icon centang */
    .btn-check-custom input:checked + .chip-body .custom-checkbox-box i {
        transform: scale(1);
    }

    /* Checked: Teks jadi biru */
    .btn-check-custom input:checked + .chip-body .chip-text {
        color: var(--primary-color);
        font-weight: 700;
    }

    /* Sisa CSS lama kamu (Tab Floating, Table, dll) biarkan di bawah ini... */
    .nav-tabs-floating { display: flex; flex-wrap: wrap; gap: 12px; padding: 10px 5px 20px 5px; border-bottom: none; margin-bottom: 0; }
    .nav-tabs-floating .nav-item { margin: 0; }
    .nav-tabs-floating .nav-link { border: none; background-color: #fff; color: #566a7f; font-weight: 600; font-size: 0.85rem; padding: 10px 24px; border-radius: 50px; box-shadow: 0 4px 6px rgba(0,0,0,0.02), 0 2px 4px rgba(0,0,0,0.02), 0 0 0 1px rgba(0,0,0,0.05); transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1); display: flex; align-items: center; gap: 8px; }
    .nav-tabs-floating .nav-link i { font-size: 1.1rem; color: #a1acb8; transition: color 0.3s; }
    .nav-tabs-floating .nav-link:hover { transform: translateY(-4px); box-shadow: 0 12px 20px rgba(0,0,0,0.08); color: var(--primary-color); }
    .nav-tabs-floating .nav-link:hover i { color: var(--primary-color); }
    .nav-tabs-floating .nav-link.active { background: linear-gradient(135deg, #696cff 0%, #595cd9 100%); color: #fff; box-shadow: 0 8px 16px rgba(105, 108, 255, 0.4); transform: translateY(-2px); }
    .nav-tabs-floating .nav-link.active i { color: #fff; }
    .table-container-scroll { max-height: 75vh; overflow: auto; position: relative; border: 1px solid var(--border-color); border-radius: 12px; box-shadow: 0 2px 6px rgba(0,0,0,0.03); }
    .table-jadwal thead th { position: sticky; top: 0; z-index: 20; background-color: #fff; border-bottom: 2px solid #d9d9d9; }
    .col-sticky-1 { position: sticky; left: 0; z-index: 10; background-color: #fdfdfd; width: 60px; border-right: 1px solid #eee; }
    .col-sticky-2 { position: sticky; left: 60px; z-index: 10; background-color: #fdfdfd; width: 100px; border-right: 1px solid #eee; }
    .table-jadwal thead th.col-sticky-1, .table-jadwal thead th.col-sticky-2 { z-index: 30; }
    .mapel-card { border-radius: 6px; padding: 6px 8px; border-left: 3px solid rgba(0,0,0,0.1); font-size: 0.8rem; text-align: left; background-color: #fff; cursor: default; transition: transform 0.2s; }
    .mapel-card:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); z-index: 5; position: relative; }
    .bg-soft-0 { background: #e8fadf; color: #1b4d12; border-color: #c9e8be; }
    .bg-soft-1 { background: #e6f7ff; color: #004578; border-color: #bae3ff; }
    .bg-soft-2 { background: #fff0f0; color: #780800; border-color: #ffd1d1; }
    .bg-soft-3 { background: #fff9e6; color: #8a6d3b; border-color: #faebcc; }
    .bg-soft-4 { background: #f3e5f5; color: #4a148c; border-color: #e1bee7; }
    .bg-soft-5 { background: #e0f7fa; color: #006064; border-color: #b2ebf2; }
    .bg-soft-6 { background: #fff3e0; color: #e65100; border-color: #ffe0b2; }
    .bg-soft-7 { background: #fce4ec; color: #880e4f; border-color: #f8bbd0; }
    .bg-soft-8 { background: #eceff1; color: #37474f; border-color: #cfd8dc; }
    .bg-soft-9 { background: #f1f8e9; color: #33691e; border-color: #dcedc8; }
    .td-empty { background-image: radial-gradient(#e0e0e0 1px, transparent 1px); background-size: 10px 10px; opacity: 0.5; }
    .bg-istirahat { background-color: #5f6163; color: #fff; font-weight: bold; font-size: 0.75rem; padding: 4px 12px; border-radius: 50px; }
    #btn-pdf-wrapper { transition: all 0.3s ease; }
</style>
@endpush

@section('content')
<div class="container-fluid flex-grow-1 container-p-y">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-primary"><i class="bx bx-calendar-star me-2"></i>Rekap Jadwal Pelajaran</h4>
            <small class="text-muted">
                @if($tapelAktif)
                    Tahun Ajaran: {{ $tapelAktif->tahun_ajaran }} ({{ ucfirst($tapelAktif->semester) }})
                @else
                    <span class="text-danger"><i class="bx bx-error"></i> Tidak ada Tahun Ajaran Aktif</span>
                @endif
            </small>
        </div>
    </div>

    {{-- FILTER KELAS --}}
    <div class="card shadow-sm mb-4 filter-card noprint">
        <div class="card-header bg-white py-3 cursor-pointer" data-bs-toggle="collapse" data-bs-target="#collapseFilter">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-dark"><i class="bx bx-filter-alt me-1 text-primary"></i> Filter Kelas</h6>
                <small class="text-muted"><i class="bx bx-chevron-down"></i> Buka/Tutup</small>
            </div>
        </div>

        <div class="collapse show" id="collapseFilter">
            <div class="card-body bg-light pt-3">
                <form id="formFilterJadwal" action="{{ route('admin.kurikulum.jadwal-pelajaran.rekap') }}" method="GET">

                    {{-- HEADER FILTER --}}
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-primary" onclick="toggleAllGlobal(this)">
                                <i class="bx bx-check-double me-1"></i> Pilih Semua
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="uncheckAll()">
                                <i class="bx bx-reset me-1"></i> Reset
                            </button>
                        </div>
                        <div id="btn-pdf-wrapper" style="display: none;">
                            <button type="submit" formaction="{{ route('admin.kurikulum.jadwal-pelajaran.cetak-pdf') }}" formtarget="_blank" class="btn btn-danger">
                                <i class="bx bxs-file-pdf me-1"></i> Download PDF
                            </button>
                        </div>
                    </div>

                    {{-- LIST KELAS --}}
                    <div class="row g-3">
                        @foreach($rombelGroups as $label => $dataKelas)
                            <div class="col-xl-3 col-md-6 col-12">
                                <div class="card h-100 border shadow-sm">
                                    <div class="card-header bg-white border-bottom py-2 d-flex justify-content-between align-items-center px-3">
                                        <span class="fw-bold small text-uppercase text-secondary">{{ $label }}</span>
                                        <button type="button" class="btn btn-xs btn-outline-primary" onclick="checkGroup(this)">
                                            Pilih Group
                                        </button>
                                    </div>

                                    <div class="card-body p-2 bg-white" style="max-height: 250px; overflow-y: auto;">
                                        <div class="row g-2">
                                           @foreach($dataKelas as $r)
    <div class="col-6">
    <label class="btn-check-custom d-flex align-items-center" title="{{ $r->nama }}" style="gap: 8px; cursor: pointer;">
        <input type="checkbox"
               class="rombel-check"
               name="rombel_ids[]"
               value="{{ $r->id }}"
               onchange="loadJadwalAjax()"
               {{ in_array($r->id, $selectedRombels) ? 'checked' : '' }}>

        <span class="chip-body">
            <span class="chip-text">{{ $r->nama }}</span>
        </span>
    </label>
</div>
@endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- HASIL AJAX --}}
    <div id="jadwalResultContainer">
        <div id="loading-spinner" class="text-center py-5" style="display: none;">
            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;"></div>
            <p class="mt-3 text-muted fw-bold">Sedang memuat data...</p>
        </div>

        <div id="content-area">
            {{-- HASIL TABEL & LEGEND GURU ADA DI SINI --}}
            @include('admin.kurikulum.jadwal-pelajaran._table_result')
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let debounceTimer;

    function loadJadwalAjax() {
        updatePdfButtonVisibility();
        document.getElementById('content-area').style.opacity = '0.5';
        document.getElementById('loading-spinner').style.display = 'block';

        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            const form = document.getElementById('formFilterJadwal');
            const params = new URLSearchParams(new FormData(form));

            fetch(`${form.action}?${params.toString()}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' }
            })
            .then(res => res.text())
            .then(html => {
                document.getElementById('content-area').innerHTML = html;
                document.getElementById('content-area').style.opacity = '1';
                document.getElementById('loading-spinner').style.display = 'none';
                initTooltips();
            })
            .catch(err => {
                console.error(err);
                document.getElementById('loading-spinner').style.display = 'none';
                document.getElementById('content-area').style.opacity = '1';
            });
        }, 500);
    }

    function updatePdfButtonVisibility() {
        const hasChecked = document.querySelectorAll('.rombel-check:checked').length > 0;
        document.getElementById('btn-pdf-wrapper').style.display = hasChecked ? 'block' : 'none';
    }

    function checkGroup(btn) {
        let isSelecting = btn.innerText.includes("Pilih Group") || btn.innerText.includes("Pilih Semua");
        btn.closest('.card').querySelectorAll('.rombel-check').forEach(c => c.checked = isSelecting);

        btn.innerText = isSelecting ? "Batal Pilih" : "Pilih Group";
        btn.classList.toggle('btn-outline-primary', !isSelecting);
        btn.classList.toggle('btn-outline-danger', isSelecting);
        loadJadwalAjax();
    }

    function toggleAllGlobal(btn) {
        let isSelecting = btn.innerText.includes("Pilih Semua");
        document.querySelectorAll('.rombel-check').forEach(c => c.checked = isSelecting);

        btn.innerHTML = isSelecting ? '<i class="bx bx-x me-1"></i> Batalkan Semua' : '<i class="bx bx-check-double me-1"></i> Pilih Semua';
        btn.classList.toggle('btn-primary', !isSelecting);
        btn.classList.toggle('btn-danger', isSelecting);

        document.querySelectorAll('button[onclick="checkGroup(this)"]').forEach(b => {
            b.innerText = isSelecting ? "Batal Pilih" : "Pilih Group";
            b.classList.toggle('btn-outline-primary', !isSelecting);
            b.classList.toggle('btn-outline-danger', isSelecting);
        });
        loadJadwalAjax();
    }

    function uncheckAll() {
        document.querySelectorAll('.rombel-check').forEach(c => c.checked = false);
        let gBtn = document.querySelector('button[onclick="toggleAllGlobal(this)"]');
        if(gBtn) { gBtn.innerHTML = '<i class="bx bx-check-double me-1"></i> Pilih Semua'; gBtn.classList.replace('btn-danger', 'btn-primary'); }

        document.querySelectorAll('button[onclick="checkGroup(this)"]').forEach(b => {
            b.innerText = "Pilih Group";
            b.classList.replace('btn-outline-danger', 'btn-outline-primary');
        });
        loadJadwalAjax();
    }

    function initTooltips() {
        [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]')).map(el => new bootstrap.Tooltip(el));
    }

    document.addEventListener('DOMContentLoaded', () => { initTooltips(); updatePdfButtonVisibility(); });
</script>
@endpush
