@extends('layouts.admin')

@section('title', 'Mapping Pengawas Pembina')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold m-0"><span class="text-muted fw-light">PKKS /</span> Mapping Pengawas</h4>
        <div id="action-header" class="d-flex align-items-center gap-3 d-none">
            <div class="text-end me-2">
                <div class="small fw-bold text-dark">Pengawas: <span id="sum-name" class="text-primary">-</span></div>
                <div class="small text-muted">Terpilih: <span id="sum-count" class="badge bg-label-primary">0</span></div>
            </div>
            <button type="button" class="btn btn-primary shadow-sm" id="btn-save">
                <i class="bx bx-save me-1"></i> Simpan
            </button>
        </div>
    </div>

    {{-- Filter Jenjang --}}
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body p-3">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <label class="form-label fw-bold small text-uppercase mb-1">Jenjang Sekolah</label>
                    <select id="filter-jenjang" class="form-select border-2 border-primary">
                        <option value="">-- Silakan Pilih Jenjang --</option>
                        @foreach($jenjangs as $j)
                        <option value="{{ $j->nama }}">{{ $j->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-8 text-md-end mt-3 mt-md-0">
                    <div class="small text-muted">
                        <i class="bx bx-info-circle me-1"></i> Klik nama pengawas untuk memuat mapping dan jenjang terkait.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Tabel Sekolah (Kiri) --}}
        <div class="col-md-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header border-bottom py-3 d-flex justify-content-between align-items-center bg-light bg-opacity-50">
                    <h5 class="mb-0 fw-bold small text-uppercase tracking-wider">Daftar Sekolah</h5>
                    <div class="input-group input-group-merge w-50">
                        <span class="input-group-text border-0 bg-white"><i class="bx bx-search"></i></span>
                        <input type="text" class="form-control border-0 bg-white" id="search-sekolah" placeholder="Cari NPSN atau Nama...">
                    </div>
                </div>
                
                {{-- Placeholder Blank State --}}
                <div id="blank-state-sekolah" class="card-body py-5 text-center">
                    <div class="mb-3">
                        <i class="bx bx-select-multiple text-muted opacity-25" style="font-size: 5rem;"></i>
                    </div>
                    <h5 class="text-muted">Pilih Pengawas atau Jenjang</h5>
                    <p class="text-muted small">Pilih pengawas binaan untuk melihat daftar sekolah.</p>
                </div>

                <div class="table-responsive d-none" id="table-container-sekolah" style="max-height: 65vh;">
                    <table class="table table-hover align-middle mb-0" id="table-sekolah">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th width="120">NPSN</th>
                                <th>Nama Sekolah</th>
                                <th width="100" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sekolahs as $s)
                            <tr class="row-sekolah" data-jenjang="{{ $s->bentuk_pendidikan_id_str }}" data-search="{{ strtolower($s->npsn . ' ' . $s->nama) }}" style="display: none;">
                                <td class="fw-bold">{{ $s->npsn }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $s->nama }}</div>
                                    <div class="d-flex align-items-center gap-2 small">
                                        <span class="badge bg-label-secondary" style="font-size: 9px;">{{ $s->bentuk_pendidikan_id_str }}</span>
                                        <span class="text-muted">{{ $s->kecamatan }}</span>
                                    </div>
                                    <div class="owner-info mt-1 d-none" id="owner-{{ $s->sekolah_id }}">
                                        <span class="badge bg-secondary rounded-pill text-white" style="font-size: 10px; opacity: 0.8;">
                                            <i class="bx bx-lock-alt me-1"></i> <span class="owner-name"></span>
                                        </span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="form-check d-flex justify-content-center">
                                        <input class="form-check-input check-sekolah" type="checkbox" 
                                               value="{{ $s->sekolah_id }}" 
                                               id="cb-{{ $s->sekolah_id }}" 
                                               disabled>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Side Panel Pengawas (Kanan) --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm overflow-hidden h-100">
                <div class="card-header bg-primary py-3">
                    <h6 class="mb-0 text-white fw-bold"><i class="bx bx-user-check me-2"></i>Daftar Pengawas</h6>
                </div>
                <div class="card-body p-0">
                    <div class="p-3 border-bottom bg-light bg-opacity-25">
                        <div class="input-group input-group-merge shadow-sm rounded-pill overflow-hidden border-0">
                            <span class="input-group-text border-0 bg-white"><i class="bx bx-search"></i></span>
                            <input type="text" class="form-control border-0 bg-white" id="search-pengawas" placeholder="Cari pengawas...">
                        </div>
                    </div>
                    <div class="list-group list-group-flush" style="max-height: 60vh; overflow-y: auto;">
                        @foreach($pengawas as $p)
                        <a href="javascript:void(0);" 
                           class="list-group-item list-group-item-action btn-pengawas d-flex align-items-center py-3 border-bottom"
                           data-id="{{ $p->id }}"
                           data-name="{{ $p->name }}"
                           data-jenjang="{{ $p->jenjang }}"
                           data-search="{{ strtolower($p->name) }}">
                            <div class="avatar avatar-sm me-3">
                                <span class="avatar-initial rounded-circle bg-label-primary shadow-sm">{{ substr($p->name, 0, 1) }}</span>
                            </div>
                            <div class="w-100 overflow-hidden">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 text-truncate fw-bold">{{ $p->name }}</h6>
                                    <span class="badge bg-primary rounded-pill count-badge" style="font-size: 10px;">{{ $p->pengawas_pembinas_count }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-1">
                                    <small class="text-muted small">ID: {{ $p->id }}</small>
                                    @if($p->jenjang)
                                    <span class="badge bg-label-info" style="font-size: 9px;">{{ $p->jenjang }}</span>
                                    @endif
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .row-sekolah { transition: all 0.2s; }
    .row-sekolah.active-mapping-row {
        background-color: rgba(105, 108, 255, 0.05) !important;
        border-left: 4px solid #696cff;
    }
    .row-sekolah.assigned-to-other { 
        background-color: #f8f9fa !important; 
        opacity: 0.5; 
        filter: grayscale(1);
    }
    .row-sekolah.assigned-to-other td { color: #aaa !important; }
    
    .btn-pengawas { border: none !important; transition: all 0.2s; }
    .btn-pengawas:hover { background-color: #f8f9ff; }
    .btn-pengawas.active { 
        background-color: #696cff !important; 
        color: #fff !important; 
        box-shadow: 0 4px 12px rgba(105, 108, 255, 0.3);
        z-index: 10;
    }
    .btn-pengawas.active h6, .btn-pengawas.active small { color: #fff !important; }
    .btn-pengawas.active .count-badge { background-color: #fff !important; color: #696cff !important; }
    
    .check-sekolah { width: 1.4rem; height: 1.4rem; cursor: pointer; border-width: 2px; }
    .check-sekolah:disabled { cursor: not-allowed; opacity: 0.3; }
    
    .table-responsive::-webkit-scrollbar { width: 5px; }
    .table-responsive::-webkit-scrollbar-thumb { background: #ddd; border-radius: 10px; }
</style>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let currentPengawasId = null;
        let globalMapping = @json($mapping);
        let pengawasData = @json($pengawas->keyBy('id'));

        const filterJenjang = document.getElementById('filter-jenjang');
        const blankState = document.getElementById('blank-state-sekolah');
        const tableContainer = document.getElementById('table-container-sekolah');

        // Fungsi Filter Jenjang
        function applyJenjangFilter(val) {
            if (!val) {
                blankState.classList.remove('d-none');
                tableContainer.classList.add('d-none');
            } else {
                blankState.classList.add('d-none');
                tableContainer.classList.remove('d-none');
                
                document.querySelectorAll('.row-sekolah').forEach(row => {
                    const j = row.getAttribute('data-jenjang');
                    row.style.display = (j === val) ? 'table-row' : 'none';
                });
                
                document.getElementById('search-sekolah').value = '';
                if(currentPengawasId) refreshTable();
            }
        }

        filterJenjang.addEventListener('change', function() {
            applyJenjangFilter(this.value);
        });

        // 2. Klik Pengawas
        document.querySelectorAll('.btn-pengawas').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                let pJenjang = this.getAttribute('data-jenjang');

                document.querySelectorAll('.btn-pengawas').forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                currentPengawasId = id;
                document.getElementById('sum-name').innerText = name;
                document.getElementById('action-header').classList.remove('d-none');

                // 🔥 SMART SCANNING: Jika jenjang di profil kosong, cari dari sekolah yang sudah di-mapping
                if (!pJenjang || pJenjang === 'null' || pJenjang === '') {
                    for (let sid in globalMapping) {
                        if (globalMapping[sid] == id) {
                            const row = document.querySelector(`.row-sekolah .check-sekolah[value="${sid}"]`)?.closest('.row-sekolah');
                            if (row) {
                                pJenjang = row.getAttribute('data-jenjang');
                                break;
                            }
                        }
                    }
                }

                // Terapkan Auto-Filter
                if (pJenjang && pJenjang !== 'null') {
                    filterJenjang.value = pJenjang;
                    applyJenjangFilter(pJenjang);
                } else {
                    // Kalau bener-bener belum ada mapping & profile kosong, biarkan user milih jenjang manual
                    applyJenjangFilter(filterJenjang.value);
                }

                refreshTable();
            });
        });

        function refreshTable() {
            if (!currentPengawasId) return;

            document.querySelectorAll('.row-sekolah').forEach(row => {
                const sid = row.querySelector('.check-sekolah').value;
                const checkbox = row.querySelector('.check-sekolah');
                const ownerBox = row.querySelector('.owner-info');
                const ownerName = row.querySelector('.owner-name');
                
                const currentOwnerId = globalMapping[sid];

                checkbox.disabled = false;
                checkbox.checked = false;
                row.classList.remove('assigned-to-other');
                row.classList.remove('active-mapping-row');
                ownerBox.classList.add('d-none');

                if (currentOwnerId) {
                    if (currentOwnerId == currentPengawasId) {
                        checkbox.checked = true;
                        row.classList.add('active-mapping-row');
                    } else {
                        checkbox.disabled = true;
                        row.classList.add('assigned-to-other');
                        ownerBox.classList.remove('d-none');
                        ownerName.innerText = pengawasData[currentOwnerId] ? pengawasData[currentOwnerId].name : 'Lainnya';
                    }
                }
            });
            updateSummary();
        }

        function updateSummary() {
            const count = document.querySelectorAll('.check-sekolah:checked').length;
            document.getElementById('sum-count').innerText = count;
        }

        document.querySelectorAll('.check-sekolah').forEach(cb => {
            cb.addEventListener('change', updateSummary);
        });

        // Search Sekolah
        document.getElementById('search-sekolah').addEventListener('keyup', function() {
            const val = this.value.toLowerCase();
            const currentJenjang = filterJenjang.value;
            if(!currentJenjang) return;
            document.querySelectorAll('.row-sekolah').forEach(row => {
                const j = row.getAttribute('data-jenjang');
                const s = row.getAttribute('data-search');
                row.style.display = (j === currentJenjang && s.includes(val)) ? 'table-row' : 'none';
            });
        });

        // Search Pengawas
        document.getElementById('search-pengawas').addEventListener('keyup', function() {
            const val = this.value.toLowerCase();
            document.querySelectorAll('.btn-pengawas').forEach(btn => {
                btn.style.display = btn.getAttribute('data-search').includes(val) ? 'flex' : 'none';
            });
        });

        // Simpan (AJAX)
        document.getElementById('btn-save').addEventListener('click', function() {
            if (!currentPengawasId) return;
            const currentJenjang = filterJenjang.value;
            if(!currentJenjang) {
                Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Silakan pilih jenjang terlebih dahulu.' });
                return;
            }
            
            const selectedIds = Array.from(document.querySelectorAll('.check-sekolah:checked')).map(cb => cb.value);
            const btn = this;
            const originalHtml = btn.innerHTML;

            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>';

            fetch('{{ route("admin.pkks.mapping-pengawas.update") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: JSON.stringify({ 
                    pengawas_id: currentPengawasId, 
                    sekolah_ids: selectedIds,
                    jenjang: currentJenjang
                })
            })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    for (let sid in globalMapping) {
                        if (globalMapping[sid] == currentPengawasId) delete globalMapping[sid];
                    }
                    selectedIds.forEach(sid => globalMapping[sid] = currentPengawasId);
                    
                    // Update Badge Count & Jenjang di List Pengawas UI
                    const pengawasLink = document.querySelector(`.btn-pengawas[data-id="${currentPengawasId}"]`);
                    if(pengawasLink) {
                        pengawasLink.querySelector('.count-badge').innerText = selectedIds.length;
                        pengawasLink.setAttribute('data-jenjang', currentJenjang);
                        
                        // Tambah badge jenjang kalau belum ada
                        let jenjangContainer = pengawasLink.querySelector('.text-muted').parentElement;
                        let existingBadge = jenjangContainer.querySelector('.badge.bg-label-info');
                        if(!existingBadge) {
                            let newBadge = document.createElement('span');
                            newBadge.className = 'badge bg-label-info';
                            newBadge.style.fontSize = '9px';
                            newBadge.innerText = currentJenjang;
                            jenjangContainer.appendChild(newBadge);
                        } else {
                            existingBadge.innerText = currentJenjang;
                        }
                    }

                    Swal.fire({ icon: 'success', title: 'Sukses', text: res.message, timer: 1500, showConfirmButton: false });
                    refreshTable();
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: res.message });
                }
            })
            .catch(err => Swal.fire({ icon: 'error', title: 'Error', text: 'Gagal simpan.' }))
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            });
        });
    });
</script>
@endpush
@endsection
