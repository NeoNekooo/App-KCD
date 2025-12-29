@extends('layouts.admin')

@section('styles')
<style>
    /* --- 1. VISUAL CHECKBOX YANG LEBIH JELAS --- */
    /* Checkbox asli disembunyikan */
    .custom-hidden-checkbox {
        position: absolute;
        opacity: 0;
        cursor: pointer;
        height: 0;
        width: 0;
    }

    /* Label yang membungkus (Card Style) */
    .rombel-card-label {
        display: flex;
        align-items: center;
        padding: 8px 12px;
        border-radius: 8px;
        border: 1px solid #d9dee3; /* Garis pinggir default (abu) */
        background-color: #fff;
        transition: all 0.2s ease-in-out;
        cursor: pointer;
        font-size: 0.9rem;
        user-select: none;
    }

    /* Kotak Ceklis Visual (Buatan) */
    .rombel-check-icon {
        width: 18px;
        height: 18px;
        border: 2px solid #b4bdc6; /* Warna garis kotak (abu tua) */
        border-radius: 4px;
        margin-right: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        background: #fff;
    }

    /* KONDISI: SAAT DICENTANG */
    .custom-hidden-checkbox:checked + .rombel-card-label {
        background-color: #e7f1ff; /* Background biru muda */
        border-color: #696cff;     /* Border biru */
        color: #696cff;
        font-weight: 600;
        box-shadow: 0 2px 4px rgba(105, 108, 255, 0.2);
    }

    .custom-hidden-checkbox:checked + .rombel-card-label .rombel-check-icon {
        background-color: #696cff; /* Kotak jadi biru penuh */
        border-color: #696cff;
    }

    /* Membuat tanda centang (✓) menggunakan CSS Pseudo-element */
    .custom-hidden-checkbox:checked + .rombel-card-label .rombel-check-icon::after {
        content: '';
        width: 5px;
        height: 9px;
        border: solid white;
        border-width: 0 2px 2px 0;
        transform: rotate(45deg);
        margin-bottom: 2px;
    }

    /* KONDISI: HOVER (Saat mouse di atasnya) */
    .rombel-card-label:hover {
        border-color: #696cff;
        background-color: #fcfdfd;
    }

    /* --- 2. AREA SCROLL & STICKY BUTTON --- */
    .rombel-scroll-box {
        max-height: 400px; /* Tinggi maksimal list */
        overflow-y: auto;
        padding-right: 5px; /* Spasi untuk scrollbar */
    }

    /* Scrollbar cantik */
    .rombel-scroll-box::-webkit-scrollbar { width: 5px; }
    .rombel-scroll-box::-webkit-scrollbar-track { background: #f1f1f1; }
    .rombel-scroll-box::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 10px; }

    /* STICKY FOOTER UNTUK TOMBOL SIMPAN */
    .form-sticky-footer {
        position: sticky;
        bottom: 0;
        background: white;
        padding-top: 15px;
        padding-bottom: 5px;
        border-top: 1px solid #eee;
        z-index: 10;
        margin-top: 10px;
        /* Efek bayangan ke atas agar terlihat melayang */
        box-shadow: 0 -4px 10px rgba(0,0,0,0.02);
    }
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Pengaturan /</span> Manajemen Hari Libur
    </h4>

    <div class="row">
        {{-- KOLOM KIRI: FORM INPUT --}}
        <div class="col-lg-5 mb-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-white border-bottom pt-4 pb-3">
                    <h5 class="mb-0 text-primary fw-bold"><i class='bx bx-calendar-plus me-2'></i>Buat Jadwal Baru</h5>
                </div>

                {{-- Gunakan position-relative agar sticky footer bekerja relatif terhadap card ini --}}
                <div class="card-body mt-4 position-relative">
                    <form action="{{ route('admin.pengaturan.hari-libur.store') }}" method="POST">
                        @csrf

                        {{-- Keterangan --}}
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="keterangan" name="keterangan" placeholder="Contoh: Libur Nasional" required value="{{ old('keterangan') }}">
                            <label for="keterangan">Keterangan Libur</label>
                        </div>

                        {{-- Tanggal --}}
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="form-floating">
                                    <input type="date" class="form-control" id="tgl_mulai" name="tanggal_mulai" required value="{{ old('tanggal_mulai') }}">
                                    <label for="tgl_mulai">Dari Tanggal</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-floating">
                                    <input type="date" class="form-control" id="tgl_selesai" name="tanggal_selesai" required value="{{ old('tanggal_selesai') }}">
                                    <label for="tgl_selesai">Sampai Tanggal</label>
                                </div>
                            </div>
                        </div>

                        {{-- Tipe --}}
                        <div class="mb-3">
                            <label class="form-label d-block fw-semibold text-muted small text-uppercase">Berlaku Untuk</label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="tipe" id="tipeGlobal" value="global"
                                    {{ old('tipe', 'global') == 'global' ? 'checked' : '' }} onchange="toggleRombel('global')">
                                <label class="btn btn-outline-primary" for="tipeGlobal">
                                    <i class='bx bx-globe me-1'></i> Semua Kelas
                                </label>

                                <input type="radio" class="btn-check" name="tipe" id="tipeKhusus" value="khusus"
                                    {{ old('tipe') == 'khusus' ? 'checked' : '' }} onchange="toggleRombel('khusus')">
                                <label class="btn btn-outline-primary" for="tipeKhusus">
                                    <i class='bx bx-filter-alt me-1'></i> Kelas Tertentu
                                </label>
                            </div>
                        </div>

                        {{-- AREA PEMILIHAN KELAS (DEFAULT HIDDEN) --}}
                        <div id="rombelContainer" style="display: none;">
                            <div class="d-flex justify-content-between align-items-end mb-2">
                                <label class="form-label fw-bold mb-0 text-dark">Pilih Kelas</label>
                                <span class="badge bg-primary rounded-pill" id="countBadge">0 Dipilih</span>
                            </div>

                            {{-- Search --}}
                            <div class="input-group input-group-sm mb-2">
                                <span class="input-group-text bg-white border-end-0"><i class='bx bx-search text-muted'></i></span>
                                <input type="text" id="searchKelas" class="form-control border-start-0 ps-0" placeholder="Cari kelas (misal: PPLG)...">
                            </div>

                            {{-- List Box --}}
                            <div class="rombel-scroll-box border rounded p-3 bg-light">
                                @if(isset($rombels) && $rombels->count() > 0)

                                    {{-- Select All --}}
                                    <div class="form-check mb-3 pb-2 border-bottom">
                                        <input class="form-check-input" type="checkbox" id="checkAll">
                                        <label class="form-check-label fw-bold text-dark" for="checkAll">
                                            Pilih Semua Kelas
                                        </label>
                                    </div>

                                    {{-- Loop Tingkat --}}
                                    @foreach($rombels as $tingkat => $kelasGroup)
                                        <div class="mb-3 tingkat-group-wrapper">
                                            {{-- Header Tingkat --}}
                                            <div class="form-check mb-2">
                                                <input class="form-check-input check-tingkat" type="checkbox" data-target=".group-{{ Str::slug($tingkat) }}">
                                                <label class="form-check-label fw-bold text-secondary">{{ $tingkat }}</label>
                                            </div>

                                            {{-- Grid Item Kelas --}}
                                            <div class="row g-2 ps-1">
                                                @foreach($kelasGroup as $rombel)
                                                    <div class="col-6 rombel-item" data-name="{{ strtolower($rombel->nama) }}">

                                                        {{-- CHECKBOX ITEM --}}
                                                        <input type="checkbox" class="custom-hidden-checkbox rombel-checkbox group-{{ Str::slug($tingkat) }}"
                                                               name="rombels[]" value="{{ $rombel->id }}" id="rombel-{{ $rombel->id }}">

                                                        {{-- LABEL VISUAL (Dengan Kotak Ceklis Buatan) --}}
                                                        <label class="rombel-card-label" for="rombel-{{ $rombel->id }}">
                                                            {{-- Ini kotak kotaknya --}}
                                                            <span class="rombel-check-icon"></span>
                                                            <span class="text-truncate">{{ $rombel->nama }}</span>
                                                        </label>

                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach

                                @else
                                    <div class="text-center py-5 text-muted">
                                        <i class='bx bx-error mb-2' style="font-size: 2rem"></i><br>
                                        Data kelas kosong.
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- TOMBOL SIMPAN (STICKY) --}}
                        <div class="form-sticky-footer">
                            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">
                                <i class='bx bx-save me-1'></i> Simpan Jadwal
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: TABEL --}}
        <div class="col-lg-7">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-white border-bottom pt-4 pb-3">
                    <h5 class="mb-0 text-dark fw-bold">Daftar Hari Libur</h5>
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Keterangan</th>
                                <th>Periode</th>
                                <th>Target</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($hariLibur as $h)
                            <tr>
                                <td><span class="fw-semibold text-dark">{{ $h->keterangan }}</span></td>
                                <td>
                                    @php
                                        $m = \Carbon\Carbon::parse($h->tanggal_mulai);
                                        $s = \Carbon\Carbon::parse($h->tanggal_selesai);
                                    @endphp
                                    <div class="d-flex flex-column">
                                        @if($m->eq($s))
                                            <span>{{ $m->isoFormat('D MMM Y') }}</span>
                                        @else
                                            <span class="text-primary">{{ $m->isoFormat('D MMM') }} - {{ $s->isoFormat('D MMM Y') }}</span>
                                            <small class="text-muted" style="font-size: 10px">{{ $m->diffInDays($s) + 1 }} Hari</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($h->tipe == 'global')
                                        <span class="badge bg-label-success">Semua Kelas</span>
                                    @else
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-label-warning me-1">Khusus</span>
                                            <button type="button" class="btn btn-icon btn-xs btn-outline-secondary rounded-circle"
                                                    data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="top"
                                                    title="<div class='text-start small'>{{ $h->rombels->pluck('nama')->join(', ') }}</div>">
                                                <i class='bx bx-search'></i>
                                            </button>
                                            <small class="ms-1 text-muted">({{ $h->rombels->count() }})</small>
                                        </div>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <form action="{{ route('admin.pengaturan.hari-libur.destroy', $h->id) }}" method="POST" onsubmit="return confirm('Hapus data ini?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-icon btn-sm btn-label-danger"><i class='bx bx-trash'></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">Belum ada data.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SCRIPT --}}
<script>
    document.addEventListener("DOMContentLoaded", function() {

        // --- 1. SEARCH FILTER ---
        const searchInput = document.getElementById('searchKelas');
        if(searchInput) {
            searchInput.addEventListener('keyup', function(e) {
                const term = e.target.value.toLowerCase();
                const items = document.querySelectorAll('.rombel-item');

                items.forEach(item => {
                    const name = item.getAttribute('data-name');
                    item.style.display = name.includes(term) ? 'block' : 'none';
                });

                document.querySelectorAll('.tingkat-group-wrapper').forEach(wrapper => {
                    const anyVisible = Array.from(wrapper.querySelectorAll('.rombel-item')).some(el => el.style.display !== 'none');
                    wrapper.style.display = anyVisible ? 'block' : 'none';
                });
            });
        }

        // --- 2. TOGGLE FORM ---
        const container = document.getElementById('rombelContainer');
        const radioKhusus = document.getElementById('tipeKhusus');

        window.toggleRombel = function(val) {
            container.style.display = (val === 'khusus') ? 'block' : 'none';
        }

        if(radioKhusus && radioKhusus.checked) toggleRombel('khusus');

        // --- 3. CHECKBOX LOGIC ---
        const checkAll = document.getElementById('checkAll');
        const allRombels = document.querySelectorAll('.rombel-checkbox');
        const allTingkats = document.querySelectorAll('.check-tingkat');
        const countBadge = document.getElementById('countBadge');

        function updateCounter() {
            const count = document.querySelectorAll('.rombel-checkbox:checked').length;
            countBadge.innerText = count + " Dipilih";
        }

        if(checkAll) {
            checkAll.addEventListener('change', function() {
                const checked = this.checked;
                document.querySelectorAll('input[type="checkbox"]').forEach(c => c.checked = checked);
                updateCounter();
            });
        }

        allTingkats.forEach(grp => {
            grp.addEventListener('change', function() {
                const target = this.getAttribute('data-target');
                document.querySelectorAll(target).forEach(c => c.checked = this.checked);
                updateMainCheckState();
                updateCounter();
            });
        });

        allRombels.forEach(c => {
            c.addEventListener('change', function() {
                const groupClass = Array.from(this.classList).find(cls => cls.startsWith('group-'));
                if(groupClass) updateTingkatState(groupClass);
                updateMainCheckState();
                updateCounter();
            });
        });

        function updateTingkatState(groupClass) {
            const parent = document.querySelector(`.check-tingkat[data-target=".${groupClass}"]`);
            const children = document.querySelectorAll('.' + groupClass);
            const checkedCount = document.querySelectorAll('.' + groupClass + ':checked').length;

            if(parent) {
                parent.checked = (checkedCount === children.length && children.length > 0);
                parent.indeterminate = (checkedCount > 0 && checkedCount < children.length);
            }
        }

        function updateMainCheckState() {
            if(!checkAll) return;
            const total = allRombels.length;
            const checked = document.querySelectorAll('.rombel-checkbox:checked').length;
            checkAll.checked = (total === checked && total > 0);
            checkAll.indeterminate = (checked > 0 && checked < total);
        }

        // --- 4. AUTO DATE ---
        const t1 = document.getElementById('tgl_mulai');
        const t2 = document.getElementById('tgl_selesai');
        if(t1 && t2) {
            t1.addEventListener('change', () => { if(!t2.value) t2.value = t1.value; });
        }

        // --- 5. TOOLTIP ---
        [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]')).map(function (el) {
            return new bootstrap.Tooltip(el)
        });
    });
</script>
@endsection
