@extends('layouts.admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    
    {{-- 1. HEADER PAGE --}}
    <div class="d-flex justify-content-between align-items-center py-3 mb-2">
        <div>
            <h4 class="fw-bold m-0">
                <span class="text-muted fw-light">Layanan /</span> {{ $title ?? 'Verifikasi Masuk' }}
            </h4>
            <small class="text-muted">Pantau dan kelola progres pengajuan berkas sekolah.</small>
        </div>
    </div>

    {{-- 2. STATISTIK CARDS --}}
    <div class="row mb-4 g-3">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm border-start border-primary border-3">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-bold text-uppercase">Perlu Syarat</span>
                            <h4 class="mb-0 mt-1 fw-bold text-primary">{{ $data->where('status', 'Proses')->count() }}</h4>
                        </div>
                        <span class="badge bg-label-primary p-2 rounded"><i class="bx bx-list-plus fs-4"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm border-start border-warning border-3">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-bold text-uppercase">Menunggu Upload</span>
                            <h4 class="mb-0 mt-1 fw-bold text-warning">{{ $data->where('status', 'Menunggu Upload')->count() }}</h4>
                        </div>
                        <span class="badge bg-label-warning p-2 rounded"><i class="bx bx-cloud-upload fs-4"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm border-start border-info border-3">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-bold text-uppercase">Siap Diperiksa</span>
                            <h4 class="mb-0 mt-1 fw-bold text-info">{{ $data->where('status', 'Verifikasi Berkas')->count() }}</h4>
                        </div>
                        <span class="badge bg-label-info p-2 rounded"><i class="bx bx-search-alt fs-4"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm border-start border-success border-3">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-bold text-uppercase">Selesai (ACC)</span>
                            <h4 class="mb-0 mt-1 fw-bold text-success">{{ $data->where('status', 'ACC')->count() }}</h4>
                        </div>
                        <span class="badge bg-label-success p-2 rounded"><i class="bx bx-check-double fs-4"></i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. ALERT MESSAGES --}}
    @if(session('success')) 
        <div class="alert alert-success alert-dismissible shadow-sm border-0" role="alert">
            <i class='bx bx-check-circle me-2'></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div> 
    @endif

    {{-- 4. MAIN TABLE CARD WITH INTEGRATED FILTER --}}
    <div class="card shadow-sm border-0">
        <div class="card-header border-bottom bg-white py-3">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <h5 class="m-0 fw-bold"><i class="bx bx-list-ul me-2"></i>Daftar Pengajuan</h5>
                
                {{-- FILTER COMPACT --}}
                <form action="{{ url()->current() }}" method="GET" class="d-flex align-items-center gap-2">
                    <div class="input-group input-group-merge" style="width: 280px;">
                        <span class="input-group-text bg-light border-light"><i class="bx bx-filter-alt small"></i></span>
                        <select name="status" class="form-select border-light bg-light" onchange="this.form.submit()">
                            <option value="">Semua Status</option>
                            <option value="Proses" {{ request('status') == 'Proses' ? 'selected' : '' }}>Tiket Baru</option>
                            <option value="Menunggu Upload" {{ request('status') == 'Menunggu Upload' ? 'selected' : '' }}>Menunggu Upload</option>
                            <option value="Verifikasi Berkas" {{ request('status') == 'Verifikasi Berkas' ? 'selected' : '' }}>Siap Diperiksa</option>
                            <option value="ACC" {{ request('status') == 'ACC' ? 'selected' : '' }}>Sudah ACC</option>
                            <option value="Ditolak" {{ request('status') == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>
                    @if(request('status'))
                        <a href="{{ url()->current() }}" class="btn btn-icon btn-outline-secondary border-light" title="Reset">
                            <i class="bx bx-refresh"></i>
                        </a>
                    @endif
                </form>
            </div>
        </div>

        @if($data->count() > 0)
            <div class="table-responsive text-nowrap">
                <table class="table table-hover align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3 ps-4 text-uppercase small fw-bold text-muted">Sekolah & Pemohon</th>
                            <th class="text-uppercase small fw-bold text-muted">Perihal</th>
                            <th class="text-uppercase small fw-bold text-muted">Status</th>
                            <th class="text-end pe-4 text-uppercase small fw-bold text-muted">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach($data as $item)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-3">
                                        <span class="avatar-initial rounded-circle bg-label-primary"><i class='bx bxs-school'></i></span>
                                    </div>
                                    <div>
                                        <span class="fw-bold d-block text-dark small">{{ $item->nama_sekolah }}</span>
                                        <small class="text-muted">{{ $item->nama_guru }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-label-secondary mb-1" style="font-size: 0.7rem;">{{ strtoupper(str_replace('-', ' ', $item->kategori)) }}</span>
                                <div class="text-wrap small text-dark fw-semibold" style="max-width: 250px;">{{ Str::limit($item->judul, 50) }}</div>
                            </td>
                            <td>
                                @php
                                    $statusClass = match($item->status) {
                                        'Proses' => 'bg-label-primary',
                                        'Menunggu Upload' => 'bg-label-warning',
                                        'Verifikasi Berkas' => 'bg-label-info',
                                        'ACC' => 'bg-label-success',
                                        'Ditolak' => 'bg-label-danger',
                                        default => 'bg-label-secondary'
                                    };
                                    $statusIcon = match($item->status) {
                                        'Proses' => 'bx-loader-circle',
                                        'Menunggu Upload' => 'bx-time',
                                        'Verifikasi Berkas' => 'bx-search-alt',
                                        'ACC' => 'bx-check-double',
                                        'Ditolak' => 'bx-x-circle',
                                        default => 'bx-question-mark'
                                    };
                                @endphp
                                <span class="badge {{ $statusClass }} d-inline-flex align-items-center">
                                    <i class='bx {{ $statusIcon }} me-1'></i> {{ $item->status }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                @if($item->status == 'Proses')
                                    <button class="btn btn-sm btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalSyarat{{ $item->id }}">
                                        <i class='bx bx-list-check me-1'></i> Atur Syarat
                                    </button>
                                @elseif($item->status == 'Verifikasi Berkas' || $item->status == 'Ditolak' || $item->status == 'ACC')
                                    <button class="btn btn-sm btn-success shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCek{{ $item->id }}">
                                        <i class='bx bx-search-alt me-1'></i> Periksa
                                    </button>
                                @endif

                                {{-- MODAL ATUR PERSYARATAN --}}
                                <div class="modal fade text-start" id="modalSyarat{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header border-bottom">
                                                <h5 class="modal-title fw-bold text-dark"><i class='bx bx-list-plus me-2 text-primary'></i>Permintaan Persyaratan</h5>
                                                <button type="button" class="btn-close btn-close-animated" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('admin.verifikasi.minta_syarat', $item->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-body bg-light">
                                                    <div class="alert alert-primary d-flex align-items-center mb-3 py-2" role="alert">
                                                        <i class='bx bx-info-circle me-2'></i>
                                                        <div class="small">Pilih dokumen yang wajib diupload oleh sekolah.</div>
                                                    </div>
                                                    <div class="card shadow-none border mb-3">
                                                        <div class="card-header py-2 bg-white fw-bold small text-uppercase text-muted">Dokumen Standar</div>
                                                        <div class="card-body pt-2 pb-1">
                                                            @php $presets = ['Surat Pengantar', 'SK Pangkat Terakhir', 'Fotokopi KTP', 'Kartu Keluarga']; @endphp
                                                            @foreach($presets as $key => $val)
                                                            <div class="form-check mb-2">
                                                                <input class="form-check-input" type="checkbox" name="syarat[]" value="{{ $val }}" id="c{{ $key }}_{{ $item->id }}" checked>
                                                                <label class="form-check-label text-dark" for="c{{ $key }}_{{ $item->id }}">{{ $val }}</label>
                                                            </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                    <div class="card shadow-none border">
                                                        <div class="card-header py-2 bg-white fw-bold small text-uppercase text-muted">Dokumen Tambahan</div>
                                                        <div class="card-body pt-3">
                                                            <div id="containerManual{{ $item->id }}"></div>
                                                            <button type="button" class="btn btn-outline-primary btn-sm w-100 dashed-border" onclick="tambahSyaratManual('{{ $item->id }}')">
                                                                <i class='bx bx-plus'></i> Tambah Item Lain
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-top bg-white">
                                                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary">Kirim Permintaan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                {{-- MODAL PEMERIKSAAN BERKAS --}}
                                <div class="modal fade text-start" id="modalCek{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header bg-success text-white">
                                                <h5 class="modal-title text-white fw-bold"><i class='bx bx-check-shield me-2'></i>Verifikasi Dokumen</h5>
                                                <button type="button" class="btn-close btn-close-animated" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body p-0 bg-light">
                                                <form id="formCek{{ $item->id }}" action="{{ route('admin.verifikasi.simpan_cek', $item->id) }}" method="POST">
                                                    @csrf
                                                    <table class="table table-striped mb-0">
                                                        <thead class="bg-white sticky-top shadow-sm">
                                                            <tr>
                                                                <th class="ps-4 py-3">Dokumen & File</th>
                                                                <th class="pe-4 py-3 text-start" style="width: 40%;">Validasi</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @if($item->dokumen_syarat)
                                                                @foreach($item->dokumen_syarat as $index => $syarat)
                                                                <tr id="rowDoc{{ $item->id }}_{{ $index }}" class="{{ (isset($syarat['valid']) && $syarat['valid']) ? 'table-success-soft' : '' }}">
                                                                    <td class="ps-4 align-top pt-3">
                                                                        <div class="fw-bold text-dark mb-1">{{ $syarat['nama'] }}</div>
                                                                        @if(!empty($syarat['file']))
                                                                            <a href="{{ $syarat['file'] }}" target="_blank" class="btn btn-xs btn-outline-primary rounded-pill">
                                                                                <i class='bx bx-link-external me-1'></i> Buka File
                                                                            </a>
                                                                        @else
                                                                            <span class="badge bg-label-danger" style="font-size: 0.65rem;">Belum diupload</span>
                                                                        @endif
                                                                    </td>
                                                                    <td class="pe-4 pt-3 text-start">
                                                                        <div class="form-check m-0 mb-2">
                                                                            <input class="form-check-input" type="checkbox" 
                                                                                   name="verifikasi[{{ $index }}][valid]" value="1" 
                                                                                   id="chk{{ $item->id }}_{{ $index }}" 
                                                                                   onchange="toggleCatatan('{{ $item->id }}_{{ $index }}')"
                                                                                   {{ (isset($syarat['valid']) && $syarat['valid']) ? 'checked' : '' }}>
                                                                            <label class="form-check-label fw-bold text-dark" for="chk{{ $item->id }}_{{ $index }}">Dokumen Valid</label>
                                                                        </div>
                                                                        <textarea class="form-control form-control-sm border-warning {{ (isset($syarat['valid']) && $syarat['valid']) ? 'd-none' : '' }}" 
                                                                                  name="verifikasi[{{ $index }}][catatan]" 
                                                                                  id="note{{ $item->id }}_{{ $index }}" 
                                                                                  placeholder="Tulis alasan ditolak..." rows="2">{{ $syarat['catatan'] ?? '' }}</textarea>
                                                                    </td>
                                                                </tr>
                                                                @endforeach
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                </form>
                                            </div>
                                            <div class="modal-footer bg-white border-top">
                                                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Tutup</button>
                                                <button type="submit" form="formCek{{ $item->id }}" class="btn btn-success px-4 shadow-sm fw-bold">Simpan & Kabari Sekolah</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 border-top d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <small class="text-muted">Menampilkan {{ $data->count() }} dari total pengajuan</small>
                <div>{{ $data->appends(request()->query())->links() }}</div>
            </div>
        @else
            <div class="text-center py-5">
                <i class='bx bx-box text-light mb-3' style="font-size: 80px;"></i>
                <h5 class="text-muted fw-bold">Data tidak ditemukan.</h5>
                <p class="text-muted small">Coba ubah filter status atau periksa kembali kategori ini.</p>
            </div>
        @endif
    </div>
</div>

<script>
function tambahSyaratManual(id) {
    let container = document.getElementById('containerManual' + id);
    let div = document.createElement('div');
    div.className = 'input-group mb-2 animate__animated animate__fadeInDown';
    div.innerHTML = `
        <span class="input-group-text bg-white"><i class='bx bx-file'></i></span>
        <input type="text" name="syarat[]" class="form-control" placeholder="Nama dokumen..." required>
        <button class="btn btn-outline-danger" type="button" onclick="this.parentElement.remove()">
            <i class='bx bx-trash'></i>
        </button>
    `;
    container.appendChild(div);
    div.querySelector('input').focus();
}

function toggleCatatan(uniqueId) {
    let chk = document.getElementById('chk' + uniqueId);
    let note = document.getElementById('note' + uniqueId);
    let row = document.getElementById('rowDoc' + uniqueId);
    
    if(chk.checked) {
        note.classList.add('d-none');
        row.classList.add('table-success-soft');
    } else {
        note.classList.remove('d-none');
        row.classList.remove('table-success-soft');
    }
}
</script>

<style>
.table-success-soft { background-color: #f0fdf4 !important; transition: background 0.3s; }
.btn-label-secondary { background: #ebeef0; color: #8592a3; border:none; }
.btn-label-secondary:hover { background: #e1e4e6; }
.dashed-border { border: 2px dashed #d9dee3; transition: all 0.3s; }
.dashed-border:hover { border-color: #696cff; background: #f5f5f9; }
.bg-label-primary { background-color: #e7e7ff !important; color: #696cff !important; }
.bg-label-success { background-color: #e8fadf !important; color: #71dd37 !important; }
.bg-label-danger { background-color: #ff3e1d29 !important; color: #ff3e1d !important; }
.bg-label-warning { background-color: #fff2d6 !important; color: #ffab00 !important; }
.bg-label-info { background-color: #d7f5fc !important; color: #03c3ec !important; }

.btn-close-animated {
    background-color: white !important; 
    border-radius: 50%;
    padding: 0.5rem; 
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    background-size: 40%;
}
.btn-close-animated:hover { transform: rotate(90deg) scale(1.1); background-color: #f0f2f5 !important; }
</style>
@endsection