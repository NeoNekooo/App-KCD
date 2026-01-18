@extends('layouts.admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- =================================================================== --}}
    {{-- 1. HEADER PAGE --}}
    {{-- =================================================================== --}}
    <div class="d-flex justify-content-between align-items-center py-3 mb-2">
        <div>
            <h4 class="fw-bold m-0 text-primary">
                <span class="text-muted fw-light">Layanan /</span>
                @if (isset($kategoriUrl) && $kategoriUrl)
                    {{ ucwords(str_replace('-', ' ', $kategoriUrl)) }}
                @else
                    {{ $title ?? 'Semua Layanan' }}
                @endif
            </h4>
            <small class="text-muted">
                <i class="bx bx-user-check me-1"></i> Mode: 
                <span class="fw-bold text-dark">
                    @if($isKasubag) Kasubag @elseif($isKepala) Kepala KCD @else Verifikator @endif
                </span>
            </small>
        </div>
    </div>

    {{-- =================================================================== --}}
    {{-- 2. STATISTIK CARDS --}}
    {{-- =================================================================== --}}
    <div class="row mb-4 g-3">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fw-bold small text-uppercase">TIKET BARU</span>
                        <h3 class="mb-0 mt-2 fw-bold text-primary">{{ $count_proses ?? 0 }}</h3>
                    </div>
                    <div class="avatar bg-label-primary rounded p-2">
                        <i class="bx bx-list-plus fs-3"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fw-bold small text-uppercase">TUNGGU UPLOAD</span>
                        <h3 class="mb-0 mt-2 fw-bold text-warning">{{ $count_upload ?? 0 }}</h3>
                    </div>
                    <div class="avatar bg-label-warning rounded p-2">
                        <i class="bx bx-cloud-upload fs-3"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fw-bold small text-uppercase">SIAP PERIKSA</span>
                        <h3 class="mb-0 mt-2 fw-bold text-info">{{ $count_verifikasi ?? 0 }}</h3>
                    </div>
                    <div class="avatar bg-label-info rounded p-2">
                        <i class="bx bx-search-alt fs-3"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fw-bold small text-uppercase">SELESAI (ACC)</span>
                        <h3 class="mb-0 mt-2 fw-bold text-success">{{ $count_selesai ?? 0 }}</h3>
                    </div>
                    <div class="avatar bg-label-success rounded p-2">
                        <i class="bx bx-check-double fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- =================================================================== --}}
    {{-- 3. MAIN TABLE CARD --}}
    {{-- =================================================================== --}}
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header border-bottom bg-white py-3 text-dark">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <h5 class="m-0 fw-bold"><i class="bx bx-list-ul me-2 text-primary"></i>Daftar Pengajuan</h5>

                <form action="{{ url()->current() }}" method="GET" class="d-flex align-items-center gap-2">
                    @if (request('kategori'))
                        <input type="hidden" name="kategori" value="{{ request('kategori') }}">
                    @endif
                    <div class="input-group input-group-merge shadow-none border rounded-pill bg-light px-2 overflow-hidden" style="width: 250px;">
                        <span class="input-group-text bg-transparent border-0"><i class="bx bx-filter-alt text-muted"></i></span>
                        <select name="status" class="form-select border-0 bg-transparent shadow-none small fw-bold" onchange="this.form.submit()">
                            <option value="">Semua Status</option>
                            <option value="Proses" {{ request('status') == 'Proses' ? 'selected' : '' }}>Tiket Baru</option>
                            <option value="Verifikasi Berkas" {{ request('status') == 'Verifikasi Berkas' ? 'selected' : '' }}>Verifikasi Berkas</option>
                            <option value="Verifikasi Kasubag" {{ request('status') == 'Verifikasi Kasubag' ? 'selected' : '' }}>Di Meja Kasubag</option>
                            <option value="Verifikasi Kepala" {{ request('status') == 'Verifikasi Kepala' ? 'selected' : '' }}>Di Meja Kepala</option>
                            <option value="ACC" {{ request('status') == 'ACC' ? 'selected' : '' }}>Selesai (ACC)</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="py-3 ps-4 text-uppercase small fw-bold text-muted">Sekolah & Pemohon</th>
                        <th class="text-uppercase small fw-bold text-muted">Layanan</th>
                        <th class="text-uppercase small fw-bold text-muted text-center">Status</th>
                        <th class="text-end pe-4 text-uppercase small fw-bold text-muted">Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($data as $item)
                        @php 
                            $st = strtolower($item->status);
                            $isMyTurn = false;
                            if (!$isKasubag && !$isKepala && in_array($st, ['proses', 'verifikasi berkas'])) {
                                $isMyTurn = true;
                            } elseif ($isKasubag && $st == 'verifikasi kasubag') {
                                $isMyTurn = true;
                            } elseif ($isKepala && $st == 'verifikasi kepala') {
                                $isMyTurn = true;
                            }
                        @endphp
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-3">
                                        <span class="avatar-initial rounded-circle bg-label-primary fw-bold">{{ substr($item->nama_sekolah, 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <span class="fw-bold d-block text-dark small">{{ $item->nama_sekolah }}</span>
                                        <small class="text-muted">{{ $item->nama_guru }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-label-secondary mb-1" style="font-size: 0.65rem;">
                                    {{ strtoupper(str_replace('-', ' ', $item->kategori)) }}
                                </span>
                                <div class="text-wrap extra-small text-dark fw-semibold" style="max-width: 250px;">
                                    {{ \Illuminate\Support\Str::limit($item->judul, 45) }}
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge rounded-pill px-3 
                                    @if($st == 'proses' || $st == 'verifikasi berkas') bg-label-primary
                                    @elseif($st == 'verifikasi kasubag') bg-label-warning
                                    @elseif($st == 'verifikasi kepala') bg-label-info
                                    @elseif($st == 'acc' || $st == 'selesai (acc)') bg-label-success
                                    @elseif($st == 'revisi' || $st == 'perbaikan') bg-label-danger
                                    @else bg-label-secondary
                                    @endif">
                                    {{ $item->status }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                @if($st == 'proses' && $isMyTurn)
                                    <button class="btn btn-sm btn-primary rounded-pill px-3 shadow-none" data-bs-toggle="modal" data-bs-target="#modalSyarat{{ $item->id }}">
                                        Atur Syarat
                                    </button>
                                @elseif(in_array($st, ['verifikasi berkas', 'verifikasi kasubag', 'verifikasi kepala']))
                                    @if($isMyTurn)
                                        @php
                                            $btnColor = ($st == 'verifikasi kasubag') ? 'btn-warning' : (($st == 'verifikasi kepala') ? 'btn-info' : 'btn-primary');
                                        @endphp
                                        <button class="btn btn-sm {{ $btnColor }} rounded-pill px-4 shadow-none" data-bs-toggle="modal" data-bs-target="#modalCek{{ $item->id }}">
                                            @if($isKepala) Tinjau & ACC @else Periksa @endif
                                        </button>
                                    @else
                                        <button class="btn btn-sm btn-outline-secondary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalCek{{ $item->id }}">
                                            <i class='bx bx-show me-1'></i> Detail
                                        </button>
                                    @endif
                                @elseif($st == 'acc' || $st == 'selesai (acc)')
                                    <a href="{{ route('cetak.sk', $item->uuid) }}" target="_blank" class="btn btn-sm btn-outline-success rounded-pill px-3">
                                        <i class='bx bx-printer me-1'></i> Cetak SK
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center py-5 text-muted small">Tidak ada data pengajuan masuk.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-top d-flex justify-content-center">{{ $data->appends(request()->query())->links() }}</div>
    </div>
</div>

{{-- =================================================================== --}}
{{-- 4. MODALS CONTAINER --}}
{{-- =================================================================== --}}
@foreach($data as $item)
    @php 
        $st = strtolower($item->status); 
        $isMyTurn = false;
        if (!$isKasubag && !$isKepala && in_array($st, ['proses', 'verifikasi berkas'])) {
            $isMyTurn = true;
        } elseif ($isKasubag && $st == 'verifikasi kasubag') {
            $isMyTurn = true;
        } elseif ($isKepala && $st == 'verifikasi kepala') {
            $isMyTurn = true;
        }
    @endphp

    {{-- MODAL ATUR SYARAT --}}
    @if($st == 'proses')
    <div class="modal fade" id="modalSyarat{{ $item->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header border-0 pb-0 px-4 pt-4 bg-white">
                    <div class="d-flex align-items-center">
                        <div class="avatar bg-label-primary p-2 rounded-3 me-3">
                            <i class="bx bx-list-plus fs-3"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold mb-0 text-dark">Daftar Persyaratan</h5>
                            <p class="text-muted extra-small mb-0">GTK: {{ $item->nama_guru }}</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.verifikasi.set_syarat', $item->id) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-body px-4 py-4 text-dark">
                        {{-- Pilihan Cepat --}}
                        <div class="mb-3">
                            <label class="form-label extra-small fw-bold text-muted text-uppercase mb-2">Pilihan Cepat</label>
                            <div class="d-flex flex-wrap gap-1">
                                <button type="button" class="btn btn-outline-primary btn-xs rounded-pill" onclick="addPreset('{{ $item->id }}', 'Surat Pengantar')">+ Surat Pengantar</button>
                                <button type="button" class="btn btn-outline-primary btn-xs rounded-pill" onclick="addPreset('{{ $item->id }}', 'SK Pembagian Tugas')">+ SK Tugas</button>
                                <button type="button" class="btn btn-outline-primary btn-xs rounded-pill" onclick="addPreset('{{ $item->id }}', 'Ijazah Terakhir')">+ Ijazah</button>
                                <button type="button" class="btn btn-outline-primary btn-xs rounded-pill" onclick="addPreset('{{ $item->id }}', 'KTP / NUPTK')">+ Identitas</button>
                            </div>
                        </div>

                        <div class="divider my-3"><div class="divider-text extra-small text-muted">Input Dokumen Wajib</div></div>

                        <div id="containerManual{{ $item->id }}">
                            <div class="input-group shadow-none border rounded-3 mb-2 bg-white overflow-hidden">
                                <span class="input-group-text border-0 bg-white"><i class='bx bx-file text-muted'></i></span>
                                <input type="text" name="syarat[]" class="form-control border-0 py-2 shadow-none small" placeholder="Ketik nama dokumen..." required>
                            </div>
                        </div>
                        <button type="button" class="btn btn-link text-primary btn-sm p-0 mt-1 fw-bold text-decoration-none d-flex align-items-center" onclick="tambahSyaratManual({{ $item->id }})">
                            <i class="bx bx-plus-circle me-1 fs-5"></i> Tambah Manual
                        </button>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4 pt-0">
                        @if($isMyTurn)
                            <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 shadow-sm fw-bold">Kirim Permintaan ke Sekolah</button>
                        @else
                            <button type="button" class="btn btn-label-secondary w-100 rounded-pill" disabled>Hanya Untuk Verifikator</button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL CEK BERKAS (DESIGN PROFESIONAL UNTUK SEMUA ROLE) --}}
    @if(in_array($st, ['verifikasi berkas', 'verifikasi kasubag', 'verifikasi kepala', 'acc', 'selesai (acc)']))
    <div class="modal fade" id="modalCek{{ $item->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                @php
                    $actionRoute = route('admin.verifikasi.process', $item->id);
                    if($st == 'verifikasi kasubag') $actionRoute = route('admin.verifikasi.kasubag_process', $item->id);
                    if($st == 'verifikasi kepala') $actionRoute = route('admin.verifikasi.kepala_process', $item->id);
                @endphp
                <form action="{{ $actionRoute }}" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-header border-bottom py-3 px-4 bg-white">
                        <div class="d-flex align-items-center">
                            <div class="avatar bg-label-info p-2 rounded-3 me-3 text-info">
                                <i class="bx bx-shield-quarter fs-3"></i>
                            </div>
                            <div>
                                <h5 class="modal-title fw-bold mb-0 text-dark">Pemeriksaan Dokumen</h5>
                                <p class="text-muted extra-small mb-0">Status Berkas: <span class="badge bg-label-info px-2 py-0 rounded-pill">{{ strtoupper($item->status) }}</span></p>
                            </div>
                        </div>
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body bg-light-subtle px-4 py-4">
                        
                        {{-- Resume Internal untuk Kepala/Kasubag --}}
                        @if(($isKasubag || $isKepala) && !empty($item->catatan_internal))
                        <div class="alert alert-soft-dark border-0 shadow-xs mb-4 rounded-4 d-flex align-items-start">
                            <i class="bx bx-info-circle me-3 fs-4 mt-1"></i>
                            <div>
                                <label class="fw-bold extra-small text-muted text-uppercase d-block mb-1">Catatan Pemeriksa Sebelumnya:</label>
                                <p class="mb-0 small italic text-dark text-decoration-underline">"{{ $item->catatan_internal }}"</p>
                            </div>
                        </div>
                        @endif

                        {{-- Area Khusus Kepala: Memilih Template SK --}}
                        @if($st == 'verifikasi kepala' && $isMyTurn)
                        <div class="card border-0 shadow-xs mb-4 rounded-4 border-start border-primary border-4 bg-white overflow-hidden">
                            <div class="card-body p-3">
                                <label class="fw-bold extra-small text-primary text-uppercase d-block mb-2">Konfigurasi Penerbitan SK</label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text border-0 bg-light-subtle px-3"><i class='bx bx-layout text-muted'></i></span>
                                    <select name="template_id" class="form-select border-0 bg-light-subtle shadow-none rounded-end-3 fw-bold" required>
                                        <option value="" selected disabled>-- Pilih Format SK Yang Akan Dicetak --</option>
                                        @foreach ($templates as $tpl)
                                            <option value="{{ $tpl->id }}">{{ $tpl->judul_surat }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="row g-3">
                            @foreach($item->dokumen_syarat ?? [] as $doc)
                                @php 
                                    $uniq = $item->id . '_' . $loop->index; 
                                    $hasCatatan = !empty($doc['catatan']);
                                    $hasFile = !empty($doc['file']);
                                    $isValid = isset($doc['valid']) && ($doc['valid'] == true || $doc['valid'] == 1);
                                    $isRevised = (!$isValid && $hasFile && $hasCatatan);

                                    // Logika Penting: Kepala, Kasubag, dan Staf bisa menginterupsi validasi saat giliran mereka
                                    $canInteract = $isMyTurn;
                                @endphp
                                <div class="col-12" id="cardDoc{{ $uniq }}">
                                    <div class="bg-white p-3 rounded-4 border shadow-xs d-flex align-items-center justify-content-between gap-3 border-light transition-all">
                                        <div class="d-flex align-items-center flex-grow-1 overflow-hidden">
                                            <div class="avatar bg-label-{{ $isRevised ? 'warning' : ($isValid ? 'success' : 'secondary') }} bg-opacity-10 p-2 rounded-3 me-3 text-{{ $isRevised ? 'warning' : ($isValid ? 'success' : 'secondary') }}">
                                                <i class='bx {{ $isRevised ? 'bx-refresh bx-spin-hover' : ($isValid ? 'bx-check-double' : 'bx-file') }} fs-4'></i>
                                            </div>
                                            <div class="flex-grow-1 overflow-hidden">
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="fw-bold text-dark small text-truncate">{{ $doc['nama'] }}</div>
                                                    @if($isRevised)
                                                        <span class="badge bg-warning text-dark extra-small py-0 px-2 rounded-pill fw-bold" style="font-size: 0.6rem;">REVISI BARU</span>
                                                    @endif
                                                </div>
                                                @if($hasFile)
                                                    <a href="{{ asset('storage/' . $doc['file']) }}" target="_blank" class="text-primary extra-small fw-bold text-decoration-none d-flex align-items-center mt-1">
                                                        <i class='bx bx-link-external me-1'></i> Lihat Dokumen
                                                    </a>
                                                @else
                                                    <span class="text-muted extra-small italic">Belum ada file</span>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- VALIDASI PILIHAN (Fleksibel untuk semua Role yang sedang bertugas) --}}
                                        @if ($canInteract)
                                            <div class="btn-group btn-group-sm shadow-none" role="group">
                                                <input type="radio" class="btn-check" name="status_toggle_{{ $uniq }}" id="valid{{ $uniq }}" autocomplete="off" {{ $isValid ? 'checked' : '' }} onchange="setDocStatus('{{ $uniq }}', true)">
                                                <label class="btn btn-outline-success border-0 px-3 py-2 fw-bold d-flex align-items-center rounded-start-pill" for="valid{{ $uniq }}">
                                                    <i class='bx bx-check me-1'></i> Terima
                                                </label>

                                                <input type="radio" class="btn-check" name="status_toggle_{{ $uniq }}" id="revisi{{ $uniq }}" autocomplete="off" {{ !$isValid ? 'checked' : '' }} onchange="setDocStatus('{{ $uniq }}', false)">
                                                <label class="btn btn-outline-danger border-0 px-3 py-2 fw-bold d-flex align-items-center rounded-end-pill" for="revisi{{ $uniq }}">
                                                    <i class='bx bx-x me-1'></i> Tolak
                                                </label>
                                            </div>
                                        @else
                                            <div class="text-{{ $isValid ? 'success' : 'danger' }} extra-small fw-bold d-flex align-items-center bg-{{ $isValid ? 'success' : 'danger' }} bg-opacity-10 px-3 py-1 rounded-pill">
                                                <i class="bx {{ $isValid ? 'bx-check-circle' : 'bx-error-circle' }} me-1"></i> 
                                                {{ $isValid ? 'VALID' : 'REVISI' }}
                                            </div>
                                        @endif
                                    </div>
                                    
                                    {{-- Kolom Input Catatan Penolakan --}}
                                    @if ($canInteract)
                                        <div id="note{{ $uniq }}" class="{{ $isValid ? 'd-none' : '' }} mt-2 animate__animated animate__fadeIn">
                                            <div class="p-3 bg-light-danger rounded-4 border border-danger border-opacity-25 shadow-sm mx-2">
                                                <label class="form-label extra-small fw-bold text-danger text-uppercase mb-1"><i class='bx bx-error-circle me-1'></i> Alasan Mengapa Ditolak?</label>
                                                <input type="text" name="catatan[{{ $doc['id'] }}]" id="inputNote{{ $uniq }}" value="{{ $doc['catatan'] ?? '' }}" class="form-control form-control-sm border-0 bg-white text-danger extra-small rounded-3 py-2 px-3 shadow-xs" placeholder="Misal: Tanda tangan basah tidak terlihat...">
                                            </div>
                                        </div>
                                    @elseif($hasCatatan && !$isValid)
                                        <div class="mt-1 px-3 py-2 bg-light-danger rounded-3 mx-4 mb-2">
                                            <p class="text-danger extra-small mb-0"><strong>Alasan Penolakan:</strong> "{{ $doc['catatan'] }}"</p>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-4">
                            <label class="form-label fw-bold extra-small text-muted text-uppercase mb-2"><i class='bx bx-message-square-dots me-1'></i> @if($isKepala) Catatan Final / Instruksi @else Catatan Internal / Pesan Lanjutan @endif</label>
                            <textarea name="catatan_internal" class="form-control border-0 shadow-xs rounded-4 bg-white p-3 extra-small" rows="2" 
                                placeholder="{{ $isMyTurn ? 'Berikan catatan untuk jenjang berikutnya...' : 'Tidak ada catatan internal.' }}" 
                                {{ !$isMyTurn ? 'disabled' : '' }}>{{ $item->catatan_internal }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4 pt-2 bg-light-subtle d-flex justify-content-between">
                        @if($isMyTurn)
                            <button type="submit" name="action" value="reject" class="btn btn-label-danger rounded-pill px-4 fw-bold shadow-none border-0">
                                <i class="bx bx-undo me-1"></i> @if($isKepala) Kembalikan ke Kasubag @else Kembalikan Berkas @endif
                            </button>
                            <button type="submit" name="action" value="approve" class="btn btn-primary rounded-pill px-5 shadow-sm fw-bold">
                                <i class="bx bx-check-double me-1"></i> @if($isKepala) Setujui & Terbitkan SK @else Setujui & Teruskan @endif
                            </button>
                        @else
                            <button type="button" class="btn btn-label-secondary w-100 rounded-pill" data-bs-dismiss="modal">Tutup Pratinjau</button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
@endforeach

<script>
    function tambahSyaratManual(id) {
        let container = document.getElementById('containerManual' + id);
        let div = document.createElement('div');
        div.className = 'input-group shadow-none border rounded-3 mb-2 bg-white overflow-hidden animate__animated animate__fadeInDown';
        div.innerHTML = `
            <span class="input-group-text border-0 bg-white px-3"><i class='bx bx-file text-muted'></i></span>
            <input type="text" name="syarat[]" class="form-control border-0 py-2 shadow-none small" placeholder="Nama dokumen..." required>
            <button class="btn btn-outline-danger border-0 bg-white px-3" type="button" onclick="this.parentElement.remove()"><i class='bx bx-trash'></i></button>
        `;
        container.appendChild(div);
    }

    function addPreset(id, text) {
        let container = document.getElementById('containerManual' + id);
        let inputs = container.querySelectorAll('input');
        if(inputs.length === 1 && inputs[0].value === "") {
            inputs[0].value = text;
        } else {
            tambahSyaratManual(id);
            let newInputs = container.querySelectorAll('input');
            newInputs[newInputs.length - 1].value = text;
        }
    }

    function setDocStatus(uniqueId, isValid) {
        let note = document.getElementById('note' + uniqueId);
        let card = document.getElementById('cardDoc' + uniqueId).querySelector('.bg-white');
        let input = document.getElementById('inputNote' + uniqueId);

        if (isValid) {
            note.classList.add('d-none');
            card.style.borderColor = "#eceef1";
            if(input) input.value = ""; 
        } else {
            note.classList.remove('d-none');
            card.style.borderColor = "#ff3e1d";
            if(input) input.focus();
        }
    }
</script>

<style>
    .rounded-4 { border-radius: 1rem !important; }
    .bg-light-subtle { background-color: #f8f9fa !important; }
    .bg-light-danger { background-color: #fff8f7 !important; }
    .extra-small { font-size: 0.75rem !important; }
    .shadow-xs { box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.05) !important; }
    .shadow-lg { box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.1) !important; }
    .transition-all { transition: all 0.2s ease; }
    .scale-125 { transform: scale(1.25); }
    .cursor-pointer { cursor: pointer; }
    .modal-content { border: none !important; }
    .btn-label-danger { background-color: #ffe0db; color: #ff3e1d; border: none; }
    .btn-label-secondary { background-color: #ebeef0; color: #8592a3; }
    .alert-soft-dark { background-color: #f1f1f1; color: #444; border: 1px dashed #ccc !important; }
    .form-control:focus, .form-select:focus { border-color: #696cff; box-shadow: none; }
    .btn-check:checked + .btn-outline-success { background-color: #71dd37 !important; color: #fff !important; border-color: #71dd37 !important; }
    .btn-check:checked + .btn-outline-danger { background-color: #ff3e1d !important; color: #fff !important; border-color: #ff3e1d !important; }
    .divider-text { background: transparent !important; padding: 0 15px; }
    .bx-spin-hover:hover { animation: bx-spin 2s infinite linear; }
</style>
@endsection