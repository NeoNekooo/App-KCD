@extends('layouts.admin')

@section('content')

    {{-- 🔥 CSS PREMIUM: ROUNDED & MODERN 🔥 --}}
    <style>
        .rounded-4 { border-radius: 1rem !important; }
        .bg-light-subtle { background-color: #f8f9fa !important; }
        .bg-light-danger { background-color: #fff8f7 !important; }
        .bg-light-success { background-color: #f0fff4 !important; }
        .extra-small { font-size: 0.72rem !important; }
        .shadow-xs { box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.05) !important; }
        .btn-label-danger { background-color: #ffe0db; color: #ff3e1d; border: none; }
        .btn-label-success { background-color: #e8fadf; color: #71dd37; border: none; }
        .nav-tabs-custom .nav-link { border: none; border-bottom: 3px solid transparent; font-weight: 600; color: #8592a3; padding: 1rem 1.2rem; }
        .nav-tabs-custom .nav-link.active { border-bottom-color: #696cff; color: #696cff; background: transparent; }
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">
        {{-- 1. HEADER PAGE --}}
        <div class="d-flex justify-content-between align-items-center py-3 mb-2">
            <div>
                <h4 class="fw-bold m-0 text-primary">
                    <span class="text-muted fw-light">Layanan /</span> Peserta Didik
                </h4>
                <small class="text-muted">
                    <i class="bx bx-shield-quarter me-1"></i> Mode Verifikasi KCD
                </small>
            </div>
        </div>

        {{-- 2. STATISTIK CARDS --}}
        <div class="row mb-4 g-3">
            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100 rounded-4">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted fw-bold small text-uppercase">Tiket Baru</span>
                            <h3 class="mb-0 mt-2 fw-bold text-primary">{{ $count_proses ?? 0 }}</h3>
                        </div>
                        <div class="avatar bg-label-primary rounded p-2"><i class="bx bx-list-plus fs-3"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100 rounded-4">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted fw-bold small text-uppercase">Tunggu Berkas</span>
                            <h3 class="mb-0 mt-2 fw-bold text-warning">{{ $count_upload ?? 0 }}</h3>
                        </div>
                        <div class="avatar bg-label-warning rounded p-2"><i class="bx bx-cloud-upload fs-3"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100 rounded-4">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted fw-bold small text-uppercase">Siap Periksa</span>
                            <h3 class="mb-0 mt-2 fw-bold text-info">{{ $count_verifikasi ?? 0 }}</h3>
                        </div>
                        <div class="avatar bg-label-info rounded p-2"><i class="bx bx-search-alt fs-3"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100 rounded-4">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted fw-bold small text-uppercase">Selesai (ACC)</span>
                            <h3 class="mb-0 mt-2 fw-bold text-success">{{ $count_selesai ?? 0 }}</h3>
                        </div>
                        <div class="avatar bg-label-success rounded p-2"><i class="bx bx-check-double fs-3"></i></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. MAIN TABLE CARD --}}
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header p-0 border-bottom">
                <div class="nav-align-top">
                    <ul class="nav nav-tabs nav-tabs-custom border-bottom-0" role="tablist">
                        <li class="nav-item">
                            <a href="{{ route('admin.verifikasi_pd.index') }}" class="nav-link {{ !request('kategori') ? 'active' : '' }}">
                                <i class="bx bx-grid-alt me-1"></i> Semua
                            </a>
                        </li>
                        @foreach($list_kategori as $cat)
                        <li class="nav-item">
                            <a href="{{ route('admin.verifikasi_pd.index', ['kategori' => $cat]) }}" class="nav-link {{ request('kategori') == $cat ? 'active' : '' }}">
                                {{ $cat }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="table-responsive text-nowrap">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3 ps-4 text-uppercase extra-small fw-bold text-muted">Siswa & Sekolah</th>
                            <th class="text-uppercase extra-small fw-bold text-muted">Perihal</th>
                            <th class="text-uppercase extra-small fw-bold text-muted text-center">Status</th>
                            <th class="text-end pe-4 text-uppercase extra-small fw-bold text-muted">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse ($data as $item)
                            @php $st = trim(strtolower($item->status)); @endphp
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-3">
                                            <span class="avatar-initial rounded-circle bg-label-info fw-bold">{{ substr($item->nama_guru, 0, 1) }}</span>
                                        </div>
                                        <div>
                                            <span class="fw-bold d-block text-dark small">{{ $item->nama_guru }}</span>
                                            <small class="text-muted d-block extra-small">{{ $item->nama_sekolah }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-label-secondary mb-1 rounded-pill" style="font-size: 0.6rem;">{{ strtoupper($item->kategori) }}</span>
                                    <div class="text-wrap extra-small text-dark fw-semibold" style="max-width: 250px;">{{ \Illuminate\Support\Str::limit($item->judul, 45) }}</div>
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill px-3 
                                        @if ($st == 'proses') bg-label-primary
                                        @elseif($st == 'acc') bg-label-success
                                        @elseif(str_contains($st, 'revisi')) bg-label-danger
                                        @elseif($st == 'atur syarat') bg-label-warning
                                        @else bg-label-info @endif">
                                        {{ strtoupper($item->status) }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    @if ($st == 'proses')
                                        <button class="btn btn-sm btn-info rounded-pill px-3 shadow-none" data-bs-toggle="modal" data-bs-target="#modalPeriksaAwal{{ $item->id }}">Periksa</button>
                                    @elseif($st == 'atur syarat')
                                        <button class="btn btn-sm btn-primary rounded-pill px-3 shadow-none" data-bs-toggle="modal" data-bs-target="#modalSyarat{{ $item->id }}">Atur Syarat</button>
                                    @elseif($st == 'verifikasi berkas' || $st == 'perlu revisi')
                                        <button class="btn btn-sm btn-warning text-white rounded-pill px-3 shadow-none" data-bs-toggle="modal" data-bs-target="#modalCek{{ $item->id }}">Verifikasi Syarat</button>
                                    @elseif($st == 'acc')
                                        <div class="btn-group">
                                            <a href="{{ route('cetak.sk', $item->uuid) }}" target="_blank" class="btn btn-sm btn-success rounded-pill px-2 me-1">
                                                <i class='bx bx-printer'></i> Cetak
                                            </a>
                                            <button class="btn btn-sm btn-label-success rounded-pill px-3 shadow-none" data-bs-toggle="modal" data-bs-target="#modalCek{{ $item->id }}">Detail</button>
                                        </div>
                                    @else
                                        <button class="btn btn-sm btn-label-secondary rounded-pill px-3 shadow-none" data-bs-toggle="modal" data-bs-target="#modalCek{{ $item->id }}">Detail</button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center py-5 text-muted small">Tidak ada data masuk.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 border-top d-flex justify-content-center">
                {{ $data->appends(request()->query())->links() }}
            </div>
        </div>
    </div>

    {{-- ================= MODALS SECTION ================= --}}
    @foreach ($data as $item)
        @php $st = trim(strtolower($item->status)); @endphp

        {{-- 1. MODAL PERIKSA AWAL --}}
        <div class="modal fade" id="modalPeriksaAwal{{ $item->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header bg-primary text-white py-3 px-4">
                        <h5 class="modal-title fw-bold text-white mb-0">Validasi Permohonan</h5>
                        <button type="button" class="btn-close bg-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body px-4 py-4">
                        <div class="p-3 bg-light border-start border-4 border-info rounded-3 mb-4">
                            <label class="fw-bold extra-small text-muted text-uppercase d-block mb-1">Surat Permohonan Sekolah:</label>
                            <a href="{{ $item->file_permohonan }}" target="_blank" class="fw-bold text-primary"><i class='bx bx-link-external'></i> Lihat File Surat</a>
                        </div>
                        <div id="formTolakAwal{{ $item->id }}" class="d-none mt-3">
                            <form action="{{ route('admin.verifikasi.reject', $item->id) }}" method="POST">
                                @csrf
                                <label class="form-label extra-small fw-bold text-danger">ALASAN PENOLAKAN</label>
                                <textarea name="alasan_tolak" class="form-control rounded-3 mb-3" placeholder="Sebutkan alasan..." required></textarea>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-danger btn-sm w-100 rounded-pill">Kirim Penolakan</button>
                                    <button type="button" class="btn btn-light btn-sm w-50 rounded-pill" onclick="toggleTolakAwal('{{ $item->id }}', false)">Batal</button>
                                </div>
                            </form>
                        </div>
                        <div id="btnGroupAwal{{ $item->id }}" class="d-flex gap-2">
                            <button type="button" class="btn btn-label-danger w-50 fw-bold rounded-pill" onclick="toggleTolakAwal('{{ $item->id }}', true)">Tolak</button>
                            <form action="{{ route('admin.verifikasi.approve_initial', $item->id) }}" method="POST" class="w-50">
                                @csrf
                                <button type="submit" class="btn btn-success w-100 fw-bold rounded-pill">Setuju</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. MODAL VERIFIKASI BERKAS + PROFIL SISWA --}}
        <div class="modal fade" id="modalCek{{ $item->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <form action="{{ route('admin.verifikasi_pd.process', $item->id) }}" method="POST" id="formCek{{ $item->id }}">
                        @csrf @method('PUT')
                        <div class="modal-header bg-primary text-white border-0 py-3 px-4">
                            <h5 class="modal-title fw-bold text-white mb-0">Rincian & Verifikasi Berkas</h5>
                            <button type="button" class="btn-close bg-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body bg-light-subtle px-4 py-4">
                            
                            {{-- 🔥 TAMBAHAN: PROFIL LENGKAP SISWA 🔥 --}}
                            <div class="card border-0 shadow-xs mb-4 p-3 rounded-4 bg-white">
                                <div class="row g-3">
                                    <div class="col-md-12 mb-2">
                                        <div class="d-flex align-items-center">
                                            <i class='bx bx-user-circle text-primary fs-4 me-2'></i>
                                            <h6 class="mb-0 fw-bold text-dark">PROFIL PESERTA DIDIK</h6>
                                        </div>
                                        <hr class="my-2 opacity-25">
                                    </div>
                                    <div class="col-md-4 col-6">
                                        <small class="text-muted d-block extra-small fw-bold">NAMA LENGKAP</small>
                                        <span class="fw-bold text-dark small text-uppercase">{{ $item->nama_guru }}</span>
                                    </div>
                                    <div class="col-md-4 col-6">
                                        <small class="text-muted d-block extra-small fw-bold">NISN</small>
                                        <span class="fw-bold text-dark small">{{ $item->nip ?? ($item->data_siswa_json['nisn'] ?? '-') }}</span>
                                    </div>
                                </div>
                            </div>

                            {{-- DAFTAR SYARAT --}}
                            <div class="row g-3">
                                @foreach ($item->dokumen_syarat ?? [] as $doc)
                                    @php 
                                        $uniq = $item->id . '_' . $loop->index; 
                                        $isValid = isset($doc['valid']) && ($doc['valid'] === true || $doc['valid'] === 1 || $doc['valid'] === "1");
                                        $isRejected = isset($doc['valid']) && ($doc['valid'] === false || $doc['valid'] === 0 || $doc['valid'] === "0");
                                        $hasFile = !empty($doc['file']);
                                    @endphp
                                    <div class="col-12">
                                        <div class="bg-white p-3 border rounded-4 d-flex align-items-center justify-content-between gap-3 shadow-xs {{ $isValid ? 'bg-light-success border-success border-opacity-25' : ($isRejected ? 'bg-light-danger border-danger border-opacity-25' : '') }}">
                                            <div class="d-flex align-items-center flex-grow-1 overflow-hidden">
                                                <div class="avatar {{ $hasFile ? 'bg-label-primary' : 'bg-label-secondary' }} p-2 rounded-3 me-3">
                                                    <i class='bx {{ $hasFile ? 'bx-file' : 'bx-file-blank' }} fs-4'></i>
                                                </div>
                                                <div class="flex-grow-1 overflow-hidden">
                                                    <div class="fw-bold text-dark small text-truncate">{{ $doc['nama'] }}</div>
                                                    @if($hasFile)
                                                        <a href="{{ $doc['file'] }}" target="_blank" class="text-primary extra-small fw-bold"><i class='bx bx-show-alt me-1'></i> Zoom Berkas</a>
                                                    @else
                                                        <span class="text-muted extra-small italic">Belum diunggah sekolah</span>
                                                    @endif
                                                </div>
                                            </div>

                                            @if($st != 'acc')
                                            <div class="btn-group btn-group-sm border rounded-pill overflow-hidden bg-white">
                                                <input type="radio" class="btn-check" name="status_toggle_{{ $uniq }}" id="valid{{ $uniq }}" value="1" {{ $isValid ? 'checked' : '' }} onchange="setDocStatus('{{ $uniq }}', true, '{{ $item->id }}')">
                                                <label class="btn btn-outline-success border-0 px-3 fw-bold" for="valid{{ $uniq }}">ACC</label>
                                                
                                                <input type="radio" class="btn-check" name="status_toggle_{{ $uniq }}" id="revisi{{ $uniq }}" value="0" {{ $isRejected ? 'checked' : '' }} onchange="setDocStatus('{{ $uniq }}', false, '{{ $item->id }}')">
                                                <label class="btn btn-outline-danger border-0 px-3 fw-bold" for="revisi{{ $uniq }}">REVISI</label>
                                            </div>
                                            @elseif($isValid)
                                                <span class="badge bg-success rounded-pill small">VERIFIED</span>
                                            @endif
                                        </div>

                                        <div id="note{{ $uniq }}" class="{{ $isValid || $st == 'acc' ? 'd-none' : ($isRejected ? '' : 'd-none') }} mt-2">
                                            <div class="p-2 bg-light-danger rounded-3 border border-danger border-opacity-20">
                                                <input type="text" name="catatan[{{ $doc['id'] ?? $loop->index }}]" value="{{ $doc['catatan'] ?? '' }}" class="form-control form-control-sm border-0 bg-transparent extra-small" placeholder="Alasan revisi...">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div id="wrapperTemplate{{ $item->id }}" class="card border-0 shadow-xs mt-4 rounded-4 border-start border-primary border-4 p-3 bg-white d-none">
                                <label class="fw-bold extra-small text-primary text-uppercase d-block mb-2">Pilih Format Penerbitan SK</label>
                                <select name="template_id" id="inputTemplate{{ $item->id }}" class="form-select border-0 bg-light-subtle fw-bold rounded-3">
                                    <option value="" selected disabled>-- Pilih Template SK --</option>
                                    @foreach ($templates as $tpl)
                                        <option value="{{ $tpl->id }}">{{ $tpl->judul_surat }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div id="wrapperAlasan{{ $item->id }}" class="mt-4 d-none">
                                <label class="form-label fw-bold extra-small text-danger text-uppercase mb-2">Instruksi Perbaikan Global</label>
                                <textarea name="alasan_tolak" id="inputAlasanTolak{{ $item->id }}" class="form-control border-0 shadow-xs rounded-4 p-3 extra-small" rows="2" placeholder="Tulis catatan revisi menyeluruh..."></textarea>
                            </div>
                        </div>

                        <div class="modal-footer border-0 px-4 pb-4 pt-2 bg-light-subtle">
                            <button type="button" id="btnClose{{ $item->id }}" class="btn btn-secondary btn-sm w-100 rounded-pill" data-bs-dismiss="modal">Tutup Detail</button>
                            <button type="submit" name="action" value="reject" id="btnReject{{ $item->id }}" class="btn btn-label-danger btn-sm px-4 fw-bold w-100 rounded-pill d-none">Kirim Instruksi Revisi</button>
                            <button type="submit" name="action" value="approve" id="btnApprove{{ $item->id }}" class="btn btn-primary btn-sm px-5 fw-bold w-100 rounded-pill d-none">Selesaikan & Terbitkan SK</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- 3. MODAL ATUR SYARAT --}}
        <div class="modal fade" id="modalSyarat{{ $item->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header bg-primary text-white py-3 px-4">
                        <h5 class="modal-title fw-bold text-white mb-0">Atur Persyaratan</h5>
                        <button type="button" class="btn-close bg-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.verifikasi.set_syarat', $item->id) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="modal-body px-4 py-4">
                            <div id="containerManual{{ $item->id }}">
                                <div class="input-group border rounded-3 mb-2 bg-white overflow-hidden shadow-none">
                                    <span class="input-group-text border-0 bg-white"><i class='bx bx-chevron-right text-primary'></i></span>
                                    <input type="text" name="syarat[]" class="form-control border-0 py-2 small" placeholder="Nama dokumen..." required>
                                </div>
                            </div>
                            <button type="button" class="btn btn-link text-primary btn-sm p-0 mt-1 fw-bold text-decoration-none" onclick="tambahSyaratManual({{ $item->id }})">+ Tambah Syarat</button>
                        </div>
                        <div class="modal-footer border-0 px-4 pb-4 pt-0">
                            <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-bold">Kirim ke Sekolah</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <script>
        function toggleTolakAwal(id, show) {
            document.getElementById('formTolakAwal' + id).classList.toggle('d-none', !show);
            document.getElementById('btnGroupAwal' + id).classList.toggle('d-none', show);
        }

        function setDocStatus(uniqueId, isValid, itemId) {
            let note = document.getElementById('note' + uniqueId);
            if (note) note.classList.toggle('d-none', isValid);
            updateSubmitButton(itemId);
        }

        function updateSubmitButton(itemId) {
            let form = document.getElementById('formCek' + itemId);
            if (!form) return;

            let radios = form.querySelectorAll('input[type="radio"]:checked');
            let totalRadioGroups = form.querySelectorAll('.btn-group').length; 
            let allValid = true;
            
            radios.forEach(radio => { if (radio.value === '0') allValid = false; });

            let btnApprove = document.getElementById('btnApprove' + itemId);
            let btnReject = document.getElementById('btnReject' + itemId);
            let btnClose = document.getElementById('btnClose' + itemId);
            let wrapperAlasan = document.getElementById('wrapperAlasan' + itemId);
            let wrapperTemplate = document.getElementById('wrapperTemplate' + itemId);

            if (radios.length === 0) {
                btnApprove.classList.add('d-none'); btnReject.classList.add('d-none');
                btnClose.classList.remove('d-none'); wrapperAlasan.classList.add('d-none'); wrapperTemplate.classList.add('d-none');
            } 
            else if (allValid && radios.length === totalRadioGroups) {
                btnApprove.classList.remove('d-none'); btnReject.classList.add('d-none');
                btnClose.classList.add('d-none'); wrapperAlasan.classList.add('d-none'); wrapperTemplate.classList.remove('d-none');
            } 
            else {
                btnApprove.classList.add('d-none'); btnReject.classList.remove('d-none');
                btnClose.classList.add('d-none'); wrapperAlasan.classList.remove('d-none'); wrapperTemplate.classList.add('d-none');
            }
        }

        function tambahSyaratManual(id) {
            let container = document.getElementById('containerManual' + id);
            let div = document.createElement('div');
            div.className = 'input-group border rounded-3 mb-2 bg-white overflow-hidden';
            div.innerHTML = `<span class="input-group-text border-0 bg-white px-3"><i class='bx bx-chevron-right text-primary'></i></span>
                <input type="text" name="syarat[]" class="form-control border-0 py-2 small" placeholder="Nama dokumen..." required>
                <button class="btn btn-outline-danger border-0 bg-white" type="button" onclick="this.parentElement.remove()"><i class='bx bx-trash'></i></button>`;
            container.appendChild(div);
        }

        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll('form[id^="formCek"]').forEach(form => {
                let itemId = form.id.replace('formCek', '');
                updateSubmitButton(itemId);
            });
        });
    </script>
@endsection