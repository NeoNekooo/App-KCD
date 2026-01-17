@extends('layouts.admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- 1. HEADER & INFO ROLE --}}
    <div class="d-flex justify-content-between align-items-center py-3 mb-2">
        <div>
            <h4 class="fw-bold m-0 text-primary">
                <span class="text-muted fw-light">Layanan /</span>
                @if (isset($kategoriUrl) && $kategoriUrl)
                    {{ ucwords(str_replace('-', ' ', $kategoriUrl)) }}
                @else
                    {{ $title ?? 'Daftar Pengajuan' }}
                @endif
            </h4>
            <small class="text-muted">
                <i class="bx bx-user-check me-1"></i> Logged as: 
                <span class="fw-bold text-dark">
                    @if(Auth::user()->pegawaiKcd && strcasecmp(Auth::user()->pegawaiKcd->jabatan, 'Kasubag') === 0)
                        Kasubag (Monitoring & Validasi)
                    @elseif(Auth::user()->role == 'Kepala')
                        Kepala KCD (Persetujuan Akhir)
                    @else
                        {{ Auth::user()->role }} - {{ Auth::user()->pegawaiKcd->jabatan ?? 'Verifikator' }}
                    @endif
                </span>
            </small>
        </div>
    </div>

    {{-- 2. STATISTIK CARDS (DATA REALTIME) --}}
    <div class="row mb-4 g-3">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fw-bold small text-uppercase">TIKET BARU</span>
                        <h3 class="mb-0 mt-2 fw-bold text-primary">{{ $count_proses ?? 0 }}</h3>
                    </div>
                    <div class="avatar bg-label-primary rounded p-2"><i class="bx bx-list-plus fs-3"></i></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fw-bold small text-uppercase">TUNGGU BERKAS</span>
                        <h3 class="mb-0 mt-2 fw-bold text-warning">{{ $count_upload ?? 0 }}</h3>
                    </div>
                    <div class="avatar bg-label-warning rounded p-2"><i class="bx bx-cloud-upload fs-3"></i></div>
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
                    <div class="avatar bg-label-info rounded p-2"><i class="bx bx-search-alt fs-3"></i></div>
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
                    <div class="avatar bg-label-success rounded p-2"><i class="bx bx-check-double fs-3"></i></div>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. MAIN DATA TABLE --}}
    <div class="card shadow-sm border-0">
        <div class="card-header border-bottom bg-white py-3">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <h5 class="m-0 fw-bold"><i class="bx bx-list-ul me-2"></i>Daftar Pengajuan</h5>
                
                <form action="{{ url()->current() }}" method="GET" class="d-flex align-items-center gap-2">
                    @if (request('kategori'))
                        <input type="hidden" name="kategori" value="{{ request('kategori') }}">
                    @endif
                    <div class="input-group input-group-merge" style="width: 250px;">
                        <span class="input-group-text bg-light border-light"><i class="bx bx-filter-alt small"></i></span>
                        <select name="status" class="form-select border-light bg-light form-select-sm" onchange="this.form.submit()">
                            <option value="">Semua Status</option>
                            <option value="Proses" {{ request('status') == 'Proses' ? 'selected' : '' }}>Tiket Baru</option>
                            <option value="Verifikasi Berkas" {{ request('status') == 'Verifikasi Berkas' ? 'selected' : '' }}>Di Meja Admin</option>
                            <option value="Verifikasi Kasubag" {{ request('status') == 'Verifikasi Kasubag' ? 'selected' : '' }}>Di Meja Kasubag</option>
                            <option value="Verifikasi Kepala" {{ request('status') == 'Verifikasi Kepala' ? 'selected' : '' }}>Di Meja Kepala</option>
                            <option value="ACC" {{ request('status') == 'ACC' ? 'selected' : '' }}>Selesai (ACC)</option>
                            <option value="Revisi" {{ request('status') == 'Revisi' ? 'selected' : '' }}>Revisi Sekolah</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                        <th class="py-3 ps-4 text-uppercase small fw-bold text-muted">Sekolah & Pemohon</th>
                        <th class="text-uppercase small fw-bold text-muted">Layanan</th>
                        <th class="text-uppercase small fw-bold text-muted">Status</th>
                        <th class="text-end pe-4 text-uppercase small fw-bold text-muted">Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($data as $item)
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
                            <div class="text-wrap small text-dark fw-semibold" style="max-width: 250px;">{{ Str::limit($item->judul, 40) }}</div>
                        </td>
                        <td>
                            @php
                                $stMap = [
                                    'Proses' => ['class' => 'bg-label-primary', 'label' => 'Tiket Baru'],
                                    'Menunggu Upload' => ['class' => 'bg-label-warning', 'label' => 'Tunggu Berkas'],
                                    'Verifikasi Berkas' => ['class' => 'bg-label-info', 'label' => 'Cek Admin'],
                                    'Verifikasi Kasubag' => ['class' => 'bg-label-warning', 'label' => 'Cek Kasubag'],
                                    'Verifikasi Kepala' => ['class' => 'bg-label-danger', 'label' => 'Cek Kepala'],
                                    'ACC' => ['class' => 'bg-label-success', 'label' => 'Selesai (ACC)'],
                                    'Revisi' => ['class' => 'bg-label-danger', 'label' => 'Revisi'],
                                ];
                                $st = $stMap[$item->status] ?? ['class' => 'bg-label-secondary', 'label' => $item->status];
                            @endphp
                            <span class="badge {{ $st['class'] }}">{{ $st['label'] }}</span>
                        </td>

                        <td class="text-end pe-4">
                            {{-- FLOW 1: CEK PERMOHONAN AWAL --}}
                            @if ($item->status == 'Proses')
                                <button class="btn btn-sm btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalSyarat{{ $item->id }}">
                                    <i class='bx bx-list-check me-1'></i> Atur Syarat
                                </button>

                            {{-- FLOW 2: VERIFIKASI DOKUMEN (ADMIN / KASUBAG / KEPALA) --}}
                            @elseif(in_array($item->status, ['Verifikasi Berkas', 'Verifikasi Kasubag', 'Verifikasi Kepala']))
                                <button class="btn btn-sm {{ $item->status == 'Verifikasi Berkas' ? 'btn-primary' : ($item->status == 'Verifikasi Kasubag' ? 'btn-warning' : 'btn-info') }} shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCek{{ $item->id }}">
                                    <i class='bx bx-search-alt me-1'></i> 
                                    @if($item->status == 'Verifikasi Berkas') Periksa (Admin) @elseif($item->status == 'Verifikasi Kasubag') Periksa (Kasubag) @else Approval (Kepala) @endif
                                </button>

                            {{-- FLOW 3: SELESAI --}}
                            @elseif($item->status == 'ACC')
                                <a href="{{ route('cetak.sk', $item->uuid) }}" target="_blank" class="btn btn-sm btn-success shadow-sm">
                                    <i class='bx bx-printer me-1'></i> Cetak SK
                                </a>
                            @endif

                            {{-- MODAL ATUR SYARAT --}}
                            <div class="modal fade" id="modalSyarat{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow-lg">
                                        <div class="modal-header border-bottom">
                                            <h5 class="modal-title fw-bold text-start">Atur Persyaratan Dokumen</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('admin.verifikasi.set_syarat', $item->id) }}" method="POST">
                                            @csrf @method('PUT')
                                            <div class="modal-body bg-light text-start">
                                                <div class="alert alert-primary shadow-sm border-0 mb-3">
                                                    <i class="bx bx-info-circle me-1"></i> Tentukan dokumen yang harus diupload oleh sekolah.
                                                </div>
                                                <div id="containerManual{{ $item->id }}">
                                                    <div class="input-group mb-2">
                                                        <span class="input-group-text"><i class='bx bx-file'></i></span>
                                                        <input type="text" name="syarat[]" class="form-control" placeholder="Contoh: Surat Pengantar" required>
                                                    </div>
                                                </div>
                                                <button type="button" class="btn btn-outline-primary btn-sm w-100 mt-2" onclick="tambahSyaratManual({{ $item->id }})">
                                                    <i class="bx bx-plus"></i> Tambah Dokumen
                                                </button>
                                            </div>
                                            <div class="modal-footer border-top">
                                                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary">Simpan & Kirim ke Sekolah</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            {{-- MODAL CEK (VERIFIKASI BERJENJANG) --}}
                            <div class="modal fade" id="modalCek{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content border-0 shadow-lg">
                                        <div class="modal-header border-bottom">
                                            <div class="text-start">
                                                <h5 class="modal-title fw-bold">
                                                    @if($item->status == 'Verifikasi Kasubag') Validasi Kasubag @elseif($item->status == 'Verifikasi Kepala') Approval Kepala @else Verifikasi Admin @endif
                                                </h5>
                                                <small class="text-muted">Pengirim: {{ $item->nama_sekolah }}</small>
                                            </div>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>

                                        @php
                                            $actionRoute = match ($item->status) {
                                                'Verifikasi Kasubag' => route('admin.verifikasi.kasubag_process', $item->id),
                                                'Verifikasi Kepala' => route('admin.verifikasi.kepala_process', $item->id),
                                                default => route('admin.verifikasi.process', $item->id),
                                            };
                                        @endphp

                                        <form action="{{ $actionRoute }}" method="POST">
                                            @csrf @method('PUT')
                                            <div class="modal-body bg-light text-start">
                                                {{-- KHUSUS KEPALA: PILIH TEMPLATE --}}
                                                @if ($item->status == 'Verifikasi Kepala')
                                                    <div class="card p-3 border-primary bg-white mb-4">
                                                        <label class="form-label fw-bold text-primary"><i class='bx bx-layout me-1'></i> Pilih Format SK untuk Dicetak</label>
                                                        <select name="template_id" class="form-select border-primary" required>
                                                            <option value="" selected disabled>-- Pilih Template --</option>
                                                            @foreach ($templates as $tpl)
                                                                <option value="{{ $tpl->id }}">{{ $tpl->judul_surat }}</option>
                                                            @endforeach
                                                        </select>
                                                        @if ($item->catatan_internal)
                                                            <div class="alert alert-warning mt-3 mb-0 small"><i class='bx bx-note'></i> <b>Note Kasubag:</b> {{ $item->catatan_internal }}</div>
                                                        @endif
                                                    </div>
                                                @endif

                                                <h6 class="fw-bold mb-3"><i class="bx bx-check-square me-2"></i>Kelengkapan Dokumen</h6>
                                                <div class="table-responsive bg-white rounded shadow-sm border mb-3">
                                                    <table class="table table-sm mb-0">
                                                        <thead class="bg-light">
                                                            <tr>
                                                                <th class="ps-3">Nama Dokumen</th>
                                                                <th class="text-center" style="width: 100px;">Lihat</th>
                                                                @if($item->status == 'Verifikasi Berkas') <th class="text-end pe-3" style="width: 150px;">Valid?</th> @endif
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($item->dokumen_syarat ?? [] as $doc)
                                                                @php $uniq = $item->id . '_' . $loop->index; @endphp
                                                                <tr>
                                                                    <td class="ps-3 align-middle">
                                                                        <div class="fw-semibold">{{ $doc['nama'] }}</div>
                                                                        @if ($item->status == 'Verifikasi Berkas')
                                                                            <div id="note{{ $uniq }}" class="mt-1 d-none">
                                                                                <input type="text" name="catatan[{{ $doc['id'] }}]" class="form-control form-control-sm border-danger text-danger" placeholder="Alasan tolak...">
                                                                            </div>
                                                                        @endif
                                                                    </td>
                                                                    <td class="text-center align-middle">
                                                                        <a href="{{ asset('storage/' . $doc['file']) }}" target="_blank" class="btn btn-icon btn-sm btn-label-secondary"><i class="bx bx-show"></i></a>
                                                                    </td>
                                                                    @if($item->status == 'Verifikasi Berkas')
                                                                        <td class="text-end pe-3 align-middle">
                                                                            <div class="form-check form-switch d-flex justify-content-end">
                                                                                <input class="form-check-input" type="checkbox" role="switch" checked onchange="toggleRevisi(this, '{{ $uniq }}')">
                                                                            </div>
                                                                        </td>
                                                                    @endif
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>

                                                @if ($item->status == 'Verifikasi Kasubag')
                                                    <div class="mt-3">
                                                        <label class="form-label fw-bold small text-muted">CATATAN UNTUK KEPALA (INTERNAL)</label>
                                                        <textarea name="catatan_internal" class="form-control" rows="2" placeholder="Contoh: Berkas sudah lengkap, mohon di-acc..."></textarea>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="modal-footer border-top bg-white justify-content-between">
                                                <button type="submit" name="action" value="reject" class="btn btn-outline-danger">
                                                    <i class="bx bx-undo me-1"></i> 
                                                    @if($item->status == 'Verifikasi Kepala') Kembalikan ke Kasubag @elseif($item->status == 'Verifikasi Kasubag') Kembalikan ke Admin @else Minta Revisi Sekolah @endif
                                                </button>
                                                <div class="d-flex gap-2">
                                                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Tutup</button>
                                                    <button type="submit" name="action" value="approve" class="btn btn-primary">
                                                        <i class="bx bx-check-circle me-1"></i> 
                                                        @if ($item->status == 'Verifikasi Kepala') ACC & Terbitkan SK @elseif($item->status == 'Verifikasi Kasubag') Teruskan ke Kepala @else Teruskan ke Kasubag @endif
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center py-5"><i class='bx bx-box fs-1 d-block mb-2 text-muted'></i> Tidak ada pengajuan ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-top">{{ $data->appends(request()->query())->links() }}</div>
    </div>
</div>

<script>
    function tambahSyaratManual(id) {
        let container = document.getElementById('containerManual' + id);
        let div = document.createElement('div');
        div.className = 'input-group mb-2';
        div.innerHTML = `<span class="input-group-text"><i class='bx bx-file'></i></span>
            <input type="text" name="syarat[]" class="form-control" placeholder="Nama dokumen..." required>
            <button class="btn btn-outline-danger" type="button" onclick="this.parentElement.remove()"><i class='bx bx-trash'></i></button>`;
        container.appendChild(div);
    }

    function toggleRevisi(el, uniq) {
        let noteDiv = document.getElementById('note' + uniq);
        if (el.checked) {
            noteDiv.classList.add('d-none');
            noteDiv.querySelector('input').value = '';
        } else {
            noteDiv.classList.remove('d-none');
            noteDiv.querySelector('input').focus();
        }
    }
</script>

<style>
    .bg-label-primary { background-color: #e7e7ff !important; color: #696cff !important; }
    .bg-label-success { background-color: #e8fadf !important; color: #71dd37 !important; }
    .bg-label-warning { background-color: #fff2d6 !important; color: #ffab00 !important; }
    .bg-label-info { background-color: #d7f5fc !important; color: #03c3ec !important; }
    .bg-label-danger { background-color: #ffe0db !important; color: #ff3e1d !important; }
    .btn-label-secondary { background: #ebeef0; color: #8592a3; border: none; }
</style>
@endsection