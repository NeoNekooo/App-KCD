@extends('layouts.admin')

@section('title', 'Mapping Pengawas Pembina')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold m-0"><span class="text-muted fw-light">PKKS /</span> Mapping Pengawas Pembina</h4>
        <div id="action-buttons" class="d-none">
            <button type="button" class="btn btn-outline-primary btn-sm me-2" id="btn-select-all">Pilih Semua</button>
            <button type="button" class="btn btn-primary shadow-sm" id="btn-save">
                <i class="bx bx-save me-1"></i> Simpan Pemetaan
            </button>
        </div>
    </div>

    <div class="row">
        <!-- Kolom Kiri: Daftar Pengawas -->
        <div class="col-md-4">
            <div class="card mb-4 shadow-none border">
                <div class="card-header border-bottom py-3">
                    <div class="input-group input-group-merge border rounded-pill px-2">
                        <span class="input-group-text bg-transparent border-0"><i class="bx bx-search"></i></span>
                        <input type="text" class="form-control bg-transparent border-0" id="search-pengawas" placeholder="Cari pengawas...">
                    </div>
                </div>
                <div class="card-body p-0" style="max-height: 65vh; overflow-y: auto;">
                    <div class="list-group list-group-flush" id="list-pengawas">
                        @forelse($pengawas as $p)
                        <a href="javascript:void(0);" 
                           class="list-group-item list-group-item-action btn-pengawas border-0 d-flex align-items-center py-3 px-4"
                           data-id="{{ $p->id }}"
                           data-name="{{ $p->name }}"
                           data-search="{{ strtolower($p->name) }}">
                            <div class="avatar avatar-sm me-3">
                                <span class="avatar-initial rounded-circle bg-label-primary shadow-sm">{{ substr($p->name, 0, 1) }}</span>
                            </div>
                            <div class="w-100 overflow-hidden">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 text-truncate" style="max-width: 150px;">{{ $p->name }}</h6>
                                    <span class="badge count-badge rounded-pill shadow-sm" 
                                          data-pid="{{ $p->id }}" 
                                          style="font-size: 10px; background-color: #696cff !important; color: #ffffff !important; min-width: 20px;">
                                        {{ $p->pengawas_pembinas_count ?? 0 }}
                                    </span>
                                </div>
                                <small class="text-muted text-truncate d-block">{{ $p->role }}</small>
                            </div>
                        </a>
                        @empty
                        <div class="p-4 text-center text-muted small">Belum ada data pengawas.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Daftar Sekolah -->
        <div class="col-md-8">
            <div class="card shadow-none border text-center py-5" id="placeholder-mapping">
                <div class="card-body">
                    <div class="mb-3">
                        <i class="bx bx-pointer text-primary" style="font-size: 5rem; opacity: 0.2;"></i>
                    </div>
                    <h5>Pilih pengawas di kolom kiri</h5>
                    <p class="text-muted">Kelola pemetaan sekolah binaan untuk pengawas yang dipilih.</p>
                </div>
            </div>

            <div class="card shadow-none border d-none" id="card-sekolah">
                <div class="card-header border-bottom py-3 d-flex align-items-center bg-light bg-opacity-10">
                    <div class="avatar avatar-sm bg-label-primary me-3 shadow-sm">
                        <span class="avatar-initial rounded" id="initial-name">?</span>
                    </div>
                    <h5 class="mb-0 fw-bold" id="selected-pengawas-name">Nama Pengawas</h5>
                </div>
                <div class="card-body pt-4">
                    <div class="input-group input-group-merge mb-4 rounded-pill border px-2">
                        <span class="input-group-text bg-transparent border-0"><i class="bx bx-search"></i></span>
                        <input type="text" class="form-control bg-transparent border-0" id="search-sekolah" placeholder="Cari nama sekolah atau NPSN...">
                    </div>

                    <div class="row g-3" id="container-sekolah">
                        @foreach($sekolahs as $s)
                        <div class="col-md-6 item-sekolah" data-sid="{{ $s->sekolah_id }}" data-search="{{ strtolower($s->nama . ' ' . $s->npsn) }}">
                            <div class="form-check custom-option custom-option-basic shadow-none">
                                <label class="form-check-label custom-option-content border-2" for="sekolah-{{ $s->sekolah_id }}">
                                    <input class="form-check-input check-sekolah" type="checkbox" value="{{ $s->sekolah_id }}" id="sekolah-{{ $s->sekolah_id }}">
                                    <span class="custom-option-header pb-0">
                                        <span class="h6 mb-1 text-truncate" style="max-width: 200px;">{{ $s->nama }}</span>
                                        <small class="text-muted badge bg-label-secondary" style="font-size: 10px;">{{ $s->npsn }}</small>
                                    </span>
                                    <span class="custom-option-body">
                                        <small class="text-muted d-block text-truncate">
                                            {{ $s->status_sekolah_str }} • {{ $s->kecamatan }}
                                        </small>
                                    </span>
                                </label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .btn-pengawas { cursor: pointer !important; transition: all 0.2s; }
    .btn-pengawas:hover { background-color: rgba(105, 108, 255, 0.05); }
    .btn-pengawas.active { background-color: #696cff !important; color: #fff !important; box-shadow: 0 2px 10px rgba(105, 108, 255, 0.3); }
    .btn-pengawas.active .text-muted, .btn-pengawas.active h6, .btn-pengawas.active .count-badge { color: #fff !important; }
    .btn-pengawas.active .count-badge { background-color: rgba(255,255,255,0.2) !important; }
    .custom-option-content { padding: 0.75rem !important; border-radius: 10px !important; }
    .custom-option-header { margin-bottom: 0 !important; }
    
    /* Tombol Close Putih-Item */
    .btn-close-custom {
        background-color: #ffffff;
        border: none;
        color: #333333;
        font-size: 1.2rem;
        font-weight: bold;
        line-height: 1;
        border-radius: 50%;
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
</style>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let currentPengawasId = null;
        let draftMappings = {}; 

        // 1. Search Pengawas
        const searchInput = document.getElementById('search-pengawas');
        if(searchInput) {
            searchInput.addEventListener('keyup', function() {
                const val = this.value.toLowerCase();
                document.querySelectorAll('.btn-pengawas').forEach(item => {
                    item.style.display = item.getAttribute('data-search').includes(val) ? 'flex' : 'none';
                });
            });
        }

        // 2. Klik Pengawas (Toggle)
        document.querySelectorAll('.btn-pengawas').forEach(button => {
            button.addEventListener('click', function() {
                if (this.classList.contains('active')) {
                    this.classList.remove('active');
                    currentPengawasId = null;
                    document.getElementById('placeholder-mapping').classList.remove('d-none');
                    document.getElementById('card-sekolah').classList.add('d-none');
                    document.getElementById('action-buttons').classList.add('d-none');
                    return;
                }

                document.querySelectorAll('.btn-pengawas').forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                
                currentPengawasId = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');

                document.getElementById('selected-pengawas-name').innerText = name;
                document.getElementById('initial-name').innerText = name.substring(0, 1);
                document.getElementById('placeholder-mapping').classList.add('d-none');
                document.getElementById('card-sekolah').classList.remove('d-none');
                document.getElementById('action-buttons').classList.remove('d-none');

                document.querySelectorAll('.check-sekolah').forEach(cb => cb.checked = false);
                document.querySelectorAll('.item-sekolah').forEach(item => item.style.display = 'block');

                if (draftMappings[currentPengawasId]) {
                    applyDraft(currentPengawasId);
                } else {
                    fetch(`/admin/pkks/mapping-pengawas/get/${currentPengawasId}`)
                        .then(r => r.json())
                        .then(data => {
                            draftMappings[currentPengawasId] = {
                                my_schools: data.my_schools,
                                other_schools: data.other_schools
                            };
                            applyDraft(currentPengawasId);
                        });
                }
            });
        });

        function applyDraft(pid) {
            const draft = draftMappings[pid];
            draft.other_schools.forEach(id => {
                const el = document.querySelector(`.item-sekolah[data-sid="${id}"]`);
                if(el) el.style.display = 'none';
            });
            draft.my_schools.forEach(id => {
                const el = document.querySelector(`.item-sekolah[data-sid="${id}"]`);
                if(el) { el.style.display = 'block'; el.querySelector('.check-sekolah').checked = true; }
            });
            updateBadge(pid, draft.my_schools.length);
        }

        function updateBadge(pid, count) {
            const b = document.querySelector(`.btn-pengawas[data-id="${pid}"] .count-badge`);
            if(b) b.innerText = count;
        }

        document.querySelectorAll('.check-sekolah').forEach(cb => {
            cb.addEventListener('change', function() {
                if (!currentPengawasId) return;
                const selected = Array.from(document.querySelectorAll('.check-sekolah:checked')).map(c => c.value);
                if (draftMappings[currentPengawasId]) {
                    draftMappings[currentPengawasId].my_schools = selected;
                    updateBadge(currentPengawasId, selected.length);
                }
            });
        });

        // 3. Search Sekolah
        const searchSekolah = document.getElementById('search-sekolah');
        if(searchSekolah) {
            searchSekolah.addEventListener('keyup', function() {
                const val = this.value.toLowerCase();
                document.querySelectorAll('.item-sekolah').forEach(item => {
                    const draft = draftMappings[currentPengawasId];
                    const isOtherSchool = draft && draft.other_schools.includes(item.getAttribute('data-sid'));
                    if (!isOtherSchool) {
                        item.style.display = item.getAttribute('data-search').includes(val) ? 'block' : 'none';
                    }
                });
            });
        }

        // 4. Select All
        const btnSelectAll = document.getElementById('btn-select-all');
        if(btnSelectAll) {
            btnSelectAll.addEventListener('click', function() {
                const boxes = document.querySelectorAll('.item-sekolah[style*="display: block"] .check-sekolah');
                const all = Array.from(boxes).every(cb => cb.checked);
                boxes.forEach(cb => cb.checked = !all);
                this.innerText = all ? 'Pilih Semua' : 'Batal Pilih Semua';
                if (currentPengawasId && draftMappings[currentPengawasId]) {
                    const selected = Array.from(document.querySelectorAll('.check-sekolah:checked')).map(c => c.value);
                    draftMappings[currentPengawasId].my_schools = selected;
                    updateBadge(currentPengawasId, selected.length);
                }
            });
        }

        // 5. Simpan (Fetch API)
        const btnSave = document.getElementById('btn-save');
        if(btnSave) {
            btnSave.addEventListener('click', function() {
                if (!currentPengawasId) return;
                const ids = draftMappings[currentPengawasId].my_schools;
                const originalHtml = btnSave.innerHTML;
                btnSave.disabled = true;
                btnSave.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';

                fetch('{{ route("admin.pkks.mapping-pengawas.update") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: JSON.stringify({ pengawas_id: currentPengawasId, sekolah_ids: ids })
                })
                .then(async res => {
                    const data = await res.json();
                    if (!res.ok) throw new Error(data.message || 'Server Error');
                    return data;
                })
                .then(res => {
                    if (res.success) {
                        showSneatToast('success', res.message);
                    } else {
                        throw new Error(res.message);
                    }
                })
                .catch(err => {
                    showSneatToast('danger', err.message || 'Gagal simpan.');
                })
                .finally(() => {
                    btnSave.disabled = false;
                    btnSave.innerHTML = originalHtml;
                });
            });
        }

        function showSneatToast(type, message) {
            const config = {
                'success': { class: 'bg-success', title: 'Sukses', icon: 'bx bx-check-circle' },
                'danger':  { class: 'bg-danger',  title: 'Error',  icon: 'bx bx-error-circle' }
            };
            const c = config[type] || config.success;
            // Gunakan Tombol Close Putih-Item
            const toastHtml = `<div class="bs-toast toast show fade align-items-center ${c.class} border-0 position-fixed top-0 end-0 m-3" role="alert" aria-live="assertive" aria-atomic="true" style="z-index: 9999;"><div class="toast-header"><i class="${c.icon} me-2 text-white"></i><div class="me-auto fw-medium text-white">${c.title}</div><small class="text-white">Sekarang</small><button type="button" class="btn-close-custom ms-2" data-bs-dismiss="toast" aria-label="Close">×</button></div><div class="toast-body text-white">${message}</div></div>`;
            const toastContainer = document.createElement('div');
            toastContainer.innerHTML = toastHtml;
            const toastEl = toastContainer.querySelector('.toast');
            document.body.appendChild(toastEl);
            const bsToast = new bootstrap.Toast(toastEl, { delay: 3000 });
            bsToast.show();
            toastEl.addEventListener('hidden.bs.toast', () => { toastEl.remove(); });
        }
    });
</script>
@endpush
@endsection
