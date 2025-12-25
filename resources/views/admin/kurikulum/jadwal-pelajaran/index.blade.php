@extends('layouts.admin')

@section('title', 'Penyusunan Jadwal Pelajaran')

@section('content')
<div class="container-fluid flex-grow-1 container-p-y">

    {{-- HEADER (Sticky Option) --}}
    <div class="card mb-3 shadow-sm">
        <div class="card-body p-3">
            <div class="row align-items-center">
                <div class="col-md-5">
                    <h5 class="mb-0 fw-bold text-primary"><i class="bx bx-calendar-edit me-2"></i>Penyusunan Jadwal</h5>
                    <small class="text-muted">Panel kiri akan mengikuti scroll (Sticky).</small>
                </div>
                <div class="col-md-7 text-end">
                    <div class="d-flex justify-content-md-end gap-2">

                        {{-- TOMBOL SYNC (Trigger SweetAlert) --}}
                        @if($rombelId)
                        <button type="button" class="btn btn-outline-warning btn-sm" onclick="confirmSync()">
                            <i class="bx bx-sync me-1"></i> Sync Mapel
                        </button>
                        {{-- Form Tersembunyi untuk Sync --}}
                        <form id="formSync" action="{{ route('admin.kurikulum.jadwal-pelajaran.sync') }}" method="POST" style="display: none;">
                            @csrf
                            <input type="hidden" name="rombel_id" value="{{ $rombelId }}">
                        </form>
                        @endif

                        {{-- FILTER KELAS --}}
                        <form action="{{ route('admin.kurikulum.jadwal-pelajaran.index') }}" method="GET">
                            <select name="rombel_id" class="form-select form-select-sm w-px-250" onchange="this.form.submit()">
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($rombels as $r)
                                    <option value="{{ $r->id }}" {{ $rombelId == $r->id ? 'selected' : '' }}>{{ $r->nama }}</option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(!$rombelId)
        <div class="alert alert-primary text-center py-5">
            <i class='bx bx-chalkboard fs-1 mb-3'></i>
            <h4>Silakan Pilih Kelas Terlebih Dahulu</h4>
        </div>
    @else

        <div class="row position-relative">
            <div class="col-md-3">
                <div class="sticky-sidebar">
                    <div class="card h-100 shadow-sm border-primary border-top border-3">
                        <div class="card-header bg-white py-2 border-bottom">
                            <h6 class="mb-0 fw-bold text-primary"><i class="bx bx-library me-1"></i> Bank Mapel</h6>
                            <small class="text-muted" style="font-size: 10px;">Drag & Scroll Aman</small>
                        </div>

                        <div class="card-body bg-light p-2 scrollable-source position-relative">
                            <div id="source-items" class="d-flex flex-column gap-2 h-100">

                                {{-- LOGIC JIKA KOSONG --}}
                                @if($pembelajarans->isEmpty())
                                    <div class="d-flex flex-column align-items-center justify-content-center h-100 text-center p-3 opacity-75">
                                        <i class='bx bx-data fs-1 text-warning mb-2'></i>
                                        <h6 class="fw-bold text-dark mb-1">Data Mapel Kosong</h6>
                                        <p class="small text-muted mb-3" style="line-height: 1.2;">
                                            Data mata pelajaran belum ditarik dari Rombel.
                                        </p>

                                        {{-- Tombol CTA Langsung --}}
                                        <button class="btn btn-sm btn-warning w-100" onclick="confirmSync()">
                                            <i class='bx bx-sync me-1'></i> Sync Sekarang
                                        </button>
                                    </div>
                                @else
                                    {{-- LOOP DATA SEPERTI BIASA --}}
                                    @foreach($pembelajarans as $p)
                                        @php
                                            $disabled = $p->is_full ? 'disabled-item' : '';
                                            $badgeClass = $p->is_full ? 'bg-danger' : 'bg-success';
                                        @endphp

                                        <div class="mapel-item {{ $disabled }}"
                                             data-id="{{ $p->id }}"
                                             data-nama="{{ $p->nama_mata_pelajaran }}"
                                             data-guru="{{ $p->guru->nama ?? 'Belum ada guru' }}"
                                             data-sisa="{{ $p->sisa }}">

                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="fw-bold text-dark text-truncate" style="max-width: 160px;">{{ $p->nama_mata_pelajaran }}</span>
                                                <span class="badge {{ $badgeClass }} sisa-badge" id="badge-{{ $p->id }}">{{ $p->sisa }}</span>
                                            </div>
                                            <div class="text-muted small mt-1 text-truncate">
                                                {{ $p->guru->nama ?? '-' }}
                                            </div>

                                            @if($p->is_full)
                                                <div class="overlay-blocked"><i class='bx bxs-lock-alt'></i> Habis</div>
                                            @endif
                                        </div>
                                    @endforeach
                                @endif

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- PANEL KANAN: GRID JADWAL --}}
            <div class="col-md-9">
                <div class="schedule-wrapper">
                    @foreach($days as $hari)
                        <div class="day-column">
                            <div class="card mb-3 border-0 shadow-sm bg-primary text-white">
                                <div class="card-body py-2 text-center">
                                    <h6 class="mb-0 fw-bold text-uppercase text-white">{{ $hari }}</h6>
                                </div>
                            </div>

                            @if(isset($jamPelajarans[$hari]))
                                @foreach($jamPelajarans[$hari] as $jam)
                                    <div class="card mb-2 shadow-sm border-0">
                                        <div class="card-header p-2 d-flex justify-content-between align-items-center bg-label-secondary">
                                            <span class="badge bg-primary rounded-circle" style="width:24px;height:24px;padding:0;display:flex;align-items:center;justify-content:center;">{{ $jam->urutan }}</span>
                                            <small class="fw-bold text-dark">{{ \Carbon\Carbon::parse($jam->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jam->jam_selesai)->format('H:i') }}</small>
                                        </div>

                                        <div class="card-body p-2">
                                            @if($jam->tipe == 'kbm')
                                                <div class="slot-drop-zone sortable-target" data-jam-id="{{ $jam->id }}">

                                                    @if(isset($existingJadwal[$jam->id]))
                                                        @php
                                                            $pembelajaranId = $existingJadwal[$jam->id];
                                                            $savedMapel = $pembelajarans->where('id', $pembelajaranId)->first();
                                                        @endphp
                                                        @if($savedMapel)
                                                            <div class="mapel-item"
                                                                 data-id="{{ $savedMapel->id }}"
                                                                 data-nama="{{ $savedMapel->nama_mata_pelajaran }}"
                                                                 data-guru="{{ $savedMapel->guru->nama ?? '-' }}">
                                                                <div class="d-flex justify-content-between">
                                                                    <span class="fw-bold text-dark text-truncate">{{ $savedMapel->nama_mata_pelajaran }}</span>
                                                                    <i class="bx bx-x text-danger btn-delete-item" onclick="deleteJadwal(this)"></i>
                                                                </div>
                                                                <div class="text-muted small mt-1 text-truncate">
                                                                    {{ $savedMapel->guru->nama ?? '-' }}
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @else
                                                        <div class="placeholder-text"><i class='bx bx-down-arrow-alt'></i> Drop</div>
                                                    @endif

                                                </div>
                                            @else
                                                <div class="slot-blocked p-3 text-center rounded">
                                                    <span class="badge bg-label-secondary text-uppercase" style="font-size: 0.7rem;">{{ $jam->nama }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    @endif
</div>

{{-- DATA FLASH UNTUK SWEETALERT (Hidden) --}}
@if(session('success'))
    <div id="flash-success-msg" data-msg="{{ session('success') }}" style="display: none;"></div>
@endif
@if(session('error'))
    <div id="flash-error-msg" data-msg="{{ session('error') }}" style="display: none;"></div>
@endif

@endsection

@push('scripts')
{{-- 1. LOAD LIBRARY (SweetAlert2 & SortableJS) --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>

<style>
    /* CSS STYLE (Sama seperti sebelumnya) */
    .sticky-sidebar { position: -webkit-sticky; position: sticky; top: 20px; height: calc(100vh - 40px); z-index: 100; display: flex; flex-direction: column; }
    .scrollable-source { overflow-y: auto; flex: 1; scrollbar-width: thin; }
    .schedule-wrapper { display: flex; overflow-x: auto; gap: 15px; padding-bottom: 50px; }
    .day-column { flex: 0 0 280px; min-width: 280px; }
    .mapel-item { background: #fff; border: 1px solid #e0e0e0; border-left: 4px solid #696cff; border-radius: 6px; padding: 8px; cursor: grab; position: relative; font-size: 0.85rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); transition: all 0.2s; }
    .mapel-item:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.15); z-index: 5; }
    .mapel-item.disabled-item { background: #f4f4f4; border-left-color: #b0b0b0; opacity: 0.6; cursor: not-allowed; }
    .slot-drop-zone { min-height: 65px; border: 2px dashed #dbe1e6; border-radius: 6px; background-color: #fdfdfd; position: relative; transition: background 0.2s; }
    .slot-drop-zone.sortable-ghost { background: #e8e8ff; border-color: #696cff; }
    .placeholder-text { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: #ccc; font-size: 0.75rem; pointer-events: none; }
    .slot-drop-zone .mapel-item ~ .placeholder-text, .slot-drop-zone .mapel-item + .placeholder-text { display: none; }
    .overlay-blocked { position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,0.85); color: #ff3e1d; display: flex; align-items: center; justify-content: center; font-weight: bold; cursor: not-allowed; }
    .btn-delete-item { cursor: pointer; display: none; }
    .slot-drop-zone .btn-delete-item { display: block; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof Sortable === 'undefined') return;

        // --- 1. HANDLING NOTIFIKASI SWEETALERT SAAT REFRESH (SERVER SIDE) ---
        const flashSuccess = document.getElementById('flash-success-msg');
        const flashError = document.getElementById('flash-error-msg');

        // Config Mixin untuk Notif Pojok Kanan Atas (Mirip Toast tapi stabil)
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        if(flashSuccess) {
            Toast.fire({ icon: 'success', title: flashSuccess.getAttribute('data-msg') });
        }
        if(flashError) {
            Toast.fire({ icon: 'error', title: flashError.getAttribute('data-msg') });
        }

        const rombelId = "{{ $rombelId ?? '' }}";
        if(!rombelId) return;

        // --- 2. CONFIG SORTABLE ---
        const sourceList = document.getElementById('source-items');
        if(sourceList) {
            new Sortable(sourceList, {
                group: { name: 'schedule', pull: 'clone', put: false },
                sort: false, animation: 150, filter: '.disabled-item',
                onMove: function (evt) { return !evt.related.classList.contains('disabled-item'); }
            });
        }

        const targetLists = document.querySelectorAll('.slot-drop-zone');
        targetLists.forEach(function(target) {
            new Sortable(target, {
                group: 'schedule', animation: 150,
                onAdd: function (evt) {
                    const newItem = evt.item;
                    const jamId = target.getAttribute('data-jam-id');
                    const pembelajaranId = newItem.getAttribute('data-id');

                    // Swap Logic
                    const children = Array.from(target.children);
                    const oldItems = children.filter(child => child !== newItem && child.classList.contains('mapel-item'));

                    if (oldItems.length > 0) {
                        oldItems.forEach(oldItem => {
                            const oldId = oldItem.getAttribute('data-id');
                            updateCounterUI(oldId, 1);
                            oldItem.remove();
                        });
                    }

                    const placeholder = target.querySelector('.placeholder-text');
                    if(placeholder) placeholder.style.display = 'none';

                    updateCounterUI(pembelajaranId, -1);
                    saveData(jamId, pembelajaranId);
                }
            });
        });

        // --- 3. FUNGSI UPDATE UI COUNTER ---
        window.updateCounterUI = function(id, delta) {
            const badge = document.getElementById('badge-' + id);
            if(!badge) return;

            const sourceContainer = document.getElementById('source-items');
            const sourceItem = sourceContainer.querySelector(`.mapel-item[data-id="${id}"]`);

            if(sourceItem) {
                let currentSisa = parseInt(sourceItem.getAttribute('data-sisa'));
                let newSisa = currentSisa + delta;

                sourceItem.setAttribute('data-sisa', newSisa);
                badge.innerText = newSisa;

                if(newSisa <= 0) {
                    newSisa = 0; badge.innerText = '0';
                    badge.className = 'badge bg-danger sisa-badge';
                    sourceItem.classList.add('disabled-item');
                    if(!sourceItem.querySelector('.overlay-blocked')) {
                        const overlay = document.createElement('div');
                        overlay.className = 'overlay-blocked'; overlay.innerHTML = "<i class='bx bxs-lock-alt'></i> Habis";
                        sourceItem.appendChild(overlay);
                    }
                } else {
                    badge.className = 'badge bg-success sisa-badge';
                    sourceItem.classList.remove('disabled-item');
                    const overlay = sourceItem.querySelector('.overlay-blocked');
                    if(overlay) overlay.remove();
                }
            }
        }

        // --- 4. AJAX SAVE ---
        window.saveData = function(jamId, pembelajaranId) {
            fetch("{{ route('admin.kurikulum.jadwal-pelajaran.update-ajax') }}", {
                method: "POST",
                headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                body: JSON.stringify({ rombel_id: "{{ $rombelId ?? '' }}", jam_pelajaran_id: jamId, pembelajaran_id: pembelajaranId })
            })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success') {
                    // Berhasil
                    const Toast = Swal.mixin({
                        toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true,
                        didOpen: (toast) => { toast.addEventListener('mouseenter', Swal.stopTimer); toast.addEventListener('mouseleave', Swal.resumeTimer); }
                    });
                    Toast.fire({ icon: 'success', title: 'Tersimpan' });
                } else {
                    // GAGAL / BENTROK
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Menyimpan',
                        text: data.message, // Pesan detail bentrok dari controller
                        confirmButtonText: 'Oke, Kembalikan',
                        allowOutsideClick: false
                    }).then(() => {
                        // Reload halaman agar posisi kartu kembali seperti semula (sebelum di-drag)
                        // Ini cara paling aman untuk "undo" drag and drop yang kompleks
                        location.reload();
                    });
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire('Error', 'Terjadi kesalahan server', 'error');
            });
        }
    });

    // --- 5. FUNGSI KONFIRMASI SYNC (SWEETALERT) ---
    function confirmSync() {
        Swal.fire({
            title: 'Sync Ulang Mapel?',
            text: "Data mapel di panel kiri akan di-reset dari Rombel. Jadwal yang sudah disusun di kanan akan DIHAPUS karena ID-nya berubah.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ffc107', // Warning color
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Reset & Sync',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('formSync').submit();
            }
        })
    }

    // --- 6. FUNGSI DELETE ITEM (SWEETALERT) ---
    window.deleteJadwal = function(btn) {
        // Tampilkan konfirmasi SweetAlert dulu
        Swal.fire({
            title: 'Hapus jadwal?',
            text: "Mapel akan dikembalikan ke panel kiri.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            width: '300px' // Kecil saja
        }).then((result) => {
            if (result.isConfirmed) {
                // Eksekusi Hapus
                const item = btn.closest('.mapel-item');
                const slot = item.closest('.slot-drop-zone');
                const jamId = slot.getAttribute('data-jam-id');
                const mapelId = item.getAttribute('data-id');

                item.remove();

                const placeholder = slot.querySelector('.placeholder-text');
                if(placeholder) placeholder.style.display = 'block';

                window.updateCounterUI(mapelId, 1);
                window.saveData(jamId, null);
            }
        })
    }
</script>
@endpush
