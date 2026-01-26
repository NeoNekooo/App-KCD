@extends('layouts.admin')

@section('content')

    {{-- 🔥 CSS TAMBAHAN 🔥 --}}
    <style>
        .rounded-4 {
            border-radius: 1rem !important;
        }

        .bg-light-subtle {
            background-color: #f8f9fa !important;
        }

        .bg-light-danger {
            background-color: #fff8f7 !important;
        }

        .extra-small {
            font-size: 0.75rem !important;
        }

        .shadow-xs {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.05) !important;
        }

        .btn-label-danger {
            background-color: #ffe0db;
            color: #ff3e1d;
            border: none;
        }

        .btn-label-secondary {
            background-color: #ebeef0;
            color: #8592a3;
        }

        .avatar-initial {
            font-size: 1.2rem;
        }
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">
        {{-- 1. HEADER PAGE --}}
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
                        @if ($isKasubag)
                            Kasubag
                        @elseif($isKepala)
                            Kepala KCD
                        @else
                            Verifikator
                        @endif
                    </span>
                </small>
            </div>
        </div>

        {{-- 2. STATISTIK CARDS --}}
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

        {{-- 3. MAIN TABLE CARD --}}
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header border-bottom bg-white py-3 text-dark">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                    <h5 class="m-0 fw-bold"><i class="bx bx-list-ul me-2 text-primary"></i>Daftar Pengajuan</h5>

                    <form action="{{ url()->current() }}" method="GET" class="d-flex align-items-center gap-2">
                        @if (request('kategori'))
                            <input type="hidden" name="kategori" value="{{ request('kategori') }}">
                        @endif
                        <div class="input-group input-group-merge shadow-none border rounded-pill bg-light px-2 overflow-hidden"
                            style="width: 250px;">
                            <span class="input-group-text bg-transparent border-0"><i
                                    class="bx bx-filter-alt text-muted"></i></span>
                            <select name="status" class="form-select border-0 bg-transparent shadow-none small fw-bold"
                                onchange="this.form.submit()">
                                <option value="">Semua Status</option>
                                <option value="Proses" {{ request('status') == 'Proses' ? 'selected' : '' }}>Tiket Baru
                                </option>
                                <option value="Atur Syarat" {{ request('status') == 'Atur Syarat' ? 'selected' : '' }}>Atur
                                    Syarat</option>
                                <option value="Verifikasi Berkas"
                                    {{ request('status') == 'Verifikasi Berkas' ? 'selected' : '' }}>Verifikasi Berkas
                                </option>
                                <option value="Verifikasi Kasubag"
                                    {{ request('status') == 'Verifikasi Kasubag' ? 'selected' : '' }}>Di Meja Kasubag
                                </option>
                                <option value="Verifikasi Kepala"
                                    {{ request('status') == 'Verifikasi Kepala' ? 'selected' : '' }}>Di Meja Kepala
                                </option>
                                <option value="ACC" {{ request('status') == 'ACC' ? 'selected' : '' }}>Selesai (ACC)
                                </option>
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
                                $st = trim(strtolower($item->status));
                                $isMyTurn = false;

                                if (
                                    !$isKasubag &&
                                    !$isKepala &&
                                    in_array($st, [
                                        'proses',
                                        'atur syarat',
                                        'verifikasi berkas',
                                        'revisi',
                                        'need_revision',
                                        'perlu revisi',
                                    ])
                                ) {
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
                                            <span
                                                class="avatar-initial rounded-circle bg-label-primary fw-bold">{{ substr($item->nama_sekolah, 0, 1) }}</span>
                                        </div>
                                        <div>
                                            <span class="fw-bold d-block text-dark small">{{ $item->nama_sekolah }}</span>
                                            <small class="text-muted d-block">{{ $item->nama_guru }}</small>
                                            @if ($item->file_permohonan)
                                                <a href="{{ $item->file_permohonan }}" target="_blank"
                                                    class="extra-small fw-bold text-primary mt-1 d-inline-flex align-items-center">
                                                    <i class='bx bx-file-find me-1'></i> Lihat Surat Sekolah
                                                </a>
                                            @endif
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
                                    <span
                                        class="badge rounded-pill px-3 
                                    @if ($st == 'proses') bg-label-primary
                                    @elseif($st == 'atur syarat') bg-label-info
                                    @elseif($st == 'verifikasi berkas') bg-label-primary
                                    @elseif($st == 'verifikasi kasubag') bg-label-warning
                                    @elseif($st == 'verifikasi kepala') bg-label-info
                                    @elseif($st == 'acc' || $st == 'selesai' || $st == 'selesai (acc)') bg-label-success
                                    @elseif(str_contains($st, 'revisi') || str_contains($st, 'tolak') || str_contains($st, 'rejected')) bg-label-danger
                                    @else bg-label-secondary @endif">
                                        {{ $item->status }}
                                    </span>
                                    @if ((str_contains($st, 'revisi') || str_contains($st, 'tolak')) && $item->alasan_tolak)
                                        <div class="mt-2 text-start bg-light-danger p-2 rounded border border-danger border-opacity-10"
                                            style="min-width: 150px;">
                                            <small class="text-danger fw-bold d-block extra-small text-uppercase">
                                                <i class='bx bx-info-circle'></i> Alasan:
                                            </small>
                                            <small class="text-dark d-block lh-1" style="font-size: 0.7rem;">
                                                {{ $item->alasan_tolak }}
                                            </small>
                                        </div>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    @if ($st == 'proses' && $isMyTurn)
                                        <button class="btn btn-sm btn-info rounded-pill px-3 shadow-none"
                                            data-bs-toggle="modal" data-bs-target="#modalPeriksaAwal{{ $item->id }}">
                                            Periksa
                                        </button>
                                    @elseif($st == 'atur syarat' && $isMyTurn)
                                        <button class="btn btn-sm btn-primary rounded-pill px-3 shadow-none"
                                            data-bs-toggle="modal" data-bs-target="#modalSyarat{{ $item->id }}">
                                            Atur Syarat
                                        </button>
                                    @elseif(in_array($st, [
                                            'verifikasi berkas',
                                            'verifikasi kasubag',
                                            'verifikasi kepala',
                                            'revisi',
                                            'perlu revisi',
                                            'need_revision',
                                        ]))
                                        @if ($isMyTurn)
                                            @php
                                                $btnColor =
                                                    $st == 'verifikasi kasubag'
                                                        ? 'btn-warning'
                                                        : ($st == 'verifikasi kepala'
                                                            ? 'btn-info'
                                                            : 'btn-primary');
                                            @endphp
                                            <button class="btn btn-sm {{ $btnColor }} rounded-pill px-4 shadow-none"
                                                data-bs-toggle="modal" data-bs-target="#modalCek{{ $item->id }}">
                                                @if ($isKepala)
                                                    Tinjau & ACC
                                                @else
                                                    Periksa
                                                @endif
                                            </button>
                                        @else
                                            <button class="btn btn-sm btn-outline-secondary rounded-pill px-3"
                                                data-bs-toggle="modal" data-bs-target="#modalCek{{ $item->id }}">
                                                <i class='bx bx-show me-1'></i> Detail
                                            </button>
                                        @endif
                                    @elseif(str_contains($st, 'tolak') || str_contains($st, 'rejected'))
                                        <button class="btn btn-sm btn-outline-danger rounded-pill px-3 shadow-none"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalDetailReject{{ $item->id }}">
                                            <i class='bx bx-info-circle me-1'></i> Detail
                                        </button>
                                    @elseif($st == 'acc' || $st == 'selesai' || $st == 'selesai (acc)')
                                        <div class="d-flex justify-content-end gap-1">
                                            <a href="{{ route('cetak.sk', $item->uuid) }}" target="_blank"
                                                class="btn btn-sm btn-outline-success rounded-pill px-3"
                                                data-bs-toggle="tooltip" title="Download SK">
                                                <i class='bx bx-printer'></i> Cetak SK
                                            </a>
                                            <form action="{{ route('admin.verifikasi.resend_acc', $item->id) }}"
                                                method="POST" class="d-inline"
                                                onsubmit="return confirm('Kirim ulang notifikasi ACC & Link SK ke sekolah?');">
                                                @csrf
                                                <button type="submit"
                                                    class="btn btn-sm btn-warning rounded-pill px-3 text-white"
                                                    data-bs-toggle="tooltip" title="Kirim Ulang Notifikasi ke Sekolah">
                                                    <i class='bx bx-send'></i>
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted small">Tidak ada data pengajuan
                                    masuk.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 border-top d-flex justify-content-center">
                {{ $data->appends(request()->query())->links() }}</div>
        </div>
    </div>

    {{-- MODALS CONTAINER --}}
    @foreach ($data as $item)
        @php
            $st = trim(strtolower($item->status));
            $isMyTurn = false;
            if (
                !$isKasubag &&
                !$isKepala &&
                in_array($st, ['proses', 'atur syarat', 'verifikasi berkas', 'revisi', 'need_revision', 'perlu revisi'])
            ) {
                $isMyTurn = true;
            } elseif ($isKasubag && $st == 'verifikasi kasubag') {
                $isMyTurn = true;
            } elseif ($isKepala && $st == 'verifikasi kepala') {
                $isMyTurn = true;
            }
        @endphp

        {{-- MODAL 1: PERIKSA PERMOHONAN AWAL (TIKET BARU) --}}
        @if ($st == 'proses')
            <div class="modal fade" id="modalPeriksaAwal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg rounded-4">
                        {{-- 🔥 HEADER DIUBAH JADI BIRU AGAR X PUTIH TERLIHAT 🔥 --}}
                        <div class="modal-header bg-primary text-white border-bottom-0 py-3 px-4">
                            <div class="d-flex align-items-center">
                                <i class='bx bx-check-shield fs-3 me-2'></i>
                                <h5 class="modal-title fw-bold text-white mb-0">Validasi Permohonan Awal</h5>
                            </div>
                            {{-- 🔥 UPDATE: Tombol Close Background Putih 🔥 --}}
                            <button type="button" class="btn-close bg-white opacity-100" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body px-4 py-4 text-dark text-center">
                            <div class="p-3 bg-light rounded-4 mb-4 border-start border-4 border-info text-start">
                                <label class="fw-bold extra-small text-muted text-uppercase d-block mb-1">Cek Surat
                                    Sekolah:</label>
                                <a href="{{ $item->file_permohonan }}" target="_blank" class="fw-bold text-primary">
                                    <i class='bx bx-link-external'></i> Klik Untuk Lihat Surat Permohonan
                                </a>
                            </div>
                            <p class="small text-muted">Apakah surat permohonan dari sekolah ini sudah benar?</p>

                            <div id="formTolakAwal{{ $item->id }}" class="d-none mt-3 text-start">
                                <form action="{{ route('admin.verifikasi.reject', $item->id) }}" method="POST">
                                    @csrf
                                    <label class="form-label extra-small fw-bold text-danger">ALASAN PENOLAKAN</label>
                                    <textarea name="alasan_tolak" class="form-control mb-2" placeholder="Sebutkan alasan..." required></textarea>
                                    <div class="d-flex gap-2">
                                        <button type="submit"
                                            class="btn btn-danger btn-sm w-100 rounded-pill shadow-sm">Kirim
                                            Penolakan</button>
                                        <button type="button" class="btn btn-light btn-sm w-50 rounded-pill"
                                            onclick="toggleTolakAwal('{{ $item->id }}', false)">Batal</button>
                                    </div>
                                </form>
                            </div>

                            <div id="btnGroupAwal{{ $item->id }}" class="d-flex gap-2">
                                <button type="button" class="btn btn-label-danger w-50 rounded-pill"
                                    onclick="toggleTolakAwal('{{ $item->id }}', true)">Tolak</button>
                                <form action="{{ route('admin.verifikasi.approve_initial', $item->id) }}" method="POST"
                                    class="w-50">
                                    @csrf
                                    <button type="submit"
                                        class="btn btn-success w-100 rounded-pill shadow-sm">Setuju</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- MODAL 2: ATUR SYARAT --}}
        @if ($st == 'atur syarat')
            <div class="modal fade" id="modalSyarat{{ $item->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg rounded-4">
                        {{-- HEADER STANDAR (WARNA PRIMARY) --}}
                        <div class="modal-header bg-primary text-white border-0 py-3 px-4">
                            <div class="d-flex align-items-center">
                                <i class='bx bx-list-plus fs-3 me-2'></i>
                                <div>
                                    <h5 class="modal-title fw-bold text-white mb-0">Daftar Persyaratan</h5>
                                    <small class="opacity-75">GTK: {{ $item->nama_guru }}</small>
                                </div>
                            </div>
                            {{-- 🔥 UPDATE: Tombol Close Background Putih 🔥 --}}
                            <button type="button" class="btn-close bg-white opacity-100" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <form action="{{ route('admin.verifikasi.set_syarat', $item->id) }}" method="POST">
                            @csrf @method('PUT')
                            <div class="modal-body px-4 py-4 text-dark">
                                <div class="p-2 bg-light rounded-3 mb-3 d-flex align-items-center gap-2 border">
                                    <i class='bx bx-info-circle text-primary fs-4'></i>
                                    <div class="flex-grow-1">
                                        <small class="text-muted d-block" style="font-size: 0.6rem;">REVIEW
                                            PERMOHONAN:</small>
                                        <a href="{{ $item->file_permohonan }}" target="_blank"
                                            class="fw-bold text-dark extra-small">Buka Surat Permohonan.pdf</a>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label extra-small fw-bold text-muted text-uppercase mb-2">Pilihan
                                        Cepat</label>
                                    <div class="d-flex flex-wrap gap-1">
                                        <button type="button" class="btn btn-outline-primary btn-xs rounded-pill"
                                            onclick="addPreset('{{ $item->id }}', 'Surat Pengantar')">+ Surat
                                            Pengantar</button>
                                        <button type="button" class="btn btn-outline-primary btn-xs rounded-pill"
                                            onclick="addPreset('{{ $item->id }}', 'SK Pembagian Tugas')">+ SK
                                            Tugas</button>
                                        <button type="button" class="btn btn-outline-primary btn-xs rounded-pill"
                                            onclick="addPreset('{{ $item->id }}', 'Ijazah Terakhir')">+
                                            Ijazah</button>
                                    </div>
                                </div>
                                <div id="containerManual{{ $item->id }}">
                                    <div class="input-group shadow-none border rounded-3 mb-2 bg-white overflow-hidden">
                                        <span class="input-group-text border-0 bg-white"><i
                                                class='bx bx-file text-muted'></i></span>
                                        <input type="text" name="syarat[]"
                                            class="form-control border-0 py-2 shadow-none small"
                                            placeholder="Ketik nama dokumen..." required>
                                    </div>
                                </div>
                                <button type="button"
                                    class="btn btn-link text-primary btn-sm p-0 mt-1 fw-bold text-decoration-none d-flex align-items-center"
                                    onclick="tambahSyaratManual({{ $item->id }})">
                                    <i class="bx bx-plus-circle me-1 fs-5"></i> Tambah Manual
                                </button>
                            </div>
                            <div class="modal-footer border-0 px-4 pb-4 pt-0">
                                <button type="submit"
                                    class="btn btn-primary w-100 rounded-pill py-2 shadow-sm fw-bold">Kirim Permintaan ke
                                    Sekolah</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        {{-- MODAL 3: CEK BERKAS / DETAIL REVISI --}}
        @if (in_array($st, [
                'verifikasi berkas',
                'verifikasi kasubag',
                'verifikasi kepala',
                'revisi',
                'need_revision',
                'perlu revisi',
            ]))
            <div class="modal fade" id="modalCek{{ $item->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg rounded-4">
                        @php
                            $actionRoute = route('admin.verifikasi.process', $item->id);
                            if ($st == 'verifikasi kasubag') {
                                $actionRoute = route('admin.verifikasi.kasubag_process', $item->id);
                            }
                            if ($st == 'verifikasi kepala') {
                                $actionRoute = route('admin.verifikasi.kepala_process', $item->id);
                            }

                            // Warna Header Berdasarkan Status
                            $headerColor = 'bg-primary'; // Default
                            if ($st == 'verifikasi kasubag') {
                                $headerColor = 'bg-warning';
                            }
                            if ($st == 'verifikasi kepala') {
                                $headerColor = 'bg-info';
                            }

                            $iconH = 'bx-shield-quarter';
                            if ($st == 'verifikasi kasubag') {
                                $iconH = 'bx-user-check';
                            }
                            if ($st == 'verifikasi kepala') {
                                $iconH = 'bx-check-shield';
                            }
                        @endphp
                        <form action="{{ $actionRoute }}" method="POST" id="formCek{{ $item->id }}">
                            @csrf @method('PUT')

                            {{-- HEADER STANDAR (BERWARNA) --}}
                            <div class="modal-header {{ $headerColor }} text-white border-0 py-3 px-4">
                                <div class="d-flex align-items-center">
                                    <i class='bx {{ $iconH }} fs-3 me-2'></i>
                                    <div>
                                        <h5 class="modal-title fw-bold text-white mb-0">Pemeriksaan Dokumen</h5>
                                        <small class="opacity-75">Status: {{ strtoupper($item->status) }}</small>
                                    </div>
                                </div>
                                {{-- 🔥 UPDATE: Tombol Close Background Putih 🔥 --}}
                                <button type="button" class="btn-close bg-white opacity-100" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>

                            <div class="modal-body bg-light-subtle px-4 py-4">

                                @if ($item->alasan_tolak && (str_contains($st, 'verifikasi berkas') || str_contains($st, 'revisi')))
                                    <div
                                        class="card border-0 shadow-xs mb-4 rounded-4 border-start border-danger border-4 bg-white p-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar bg-label-danger rounded p-2 me-3">
                                                <i class="bx bx-error-circle fs-3"></i>
                                            </div>
                                            <div>
                                                <span
                                                    class="fw-bold extra-small text-danger text-uppercase d-block">Catatan
                                                    Penolakan/Revisi:</span>
                                                <p class="text-dark small mb-0 fw-semibold">{{ $item->alasan_tolak }}</p>
                                                <small class="text-muted extra-small">Oleh: Atasan / Verifikator
                                                    sebelumnya</small>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if ($st == 'verifikasi kepala' && $isMyTurn)
                                    <div
                                        class="card border-0 shadow-xs mb-4 rounded-4 border-start border-primary border-4 bg-white p-3">
                                        <label
                                            class="fw-bold extra-small text-primary text-uppercase d-block mb-2">Konfigurasi
                                            Penerbitan SK</label>
                                        <select name="template_id" class="form-select border-0 bg-light-subtle fw-bold"
                                            required>
                                            <option value="" selected disabled>-- Pilih Format SK
                                                ({{ ucwords(str_replace('-', ' ', $item->kategori)) }})
                                                --</option>
                                            @forelse ($templates->where('sub_kategori', $item->kategori) as $tpl)
                                                <option value="{{ $tpl->id }}">{{ $tpl->judul_surat }}</option>
                                            @empty
                                                <option value="" disabled>⚠ Tidak ada template khusus</option>
                                            @endforelse
                                        </select>
                                    </div>
                                @endif

                                <div class="row g-3">
                                    @foreach ($item->dokumen_syarat ?? [] as $doc)
                                        @php
                                            $uniq = $item->id . '_' . $loop->index;
                                            $isValid =
                                                isset($doc['valid']) && ($doc['valid'] == true || $doc['valid'] == 1);
                                        @endphp
                                        <div class="col-12" id="cardDoc{{ $uniq }}">
                                            <div
                                                class="bg-white p-3 rounded-4 border d-flex align-items-center justify-content-between gap-3 shadow-xs">
                                                <div class="d-flex align-items-center flex-grow-1 overflow-hidden">
                                                    <div
                                                        class="avatar bg-label-secondary bg-opacity-10 p-2 rounded-3 me-3 text-secondary">
                                                        <i class='bx bx-file fs-4'></i>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <div class="fw-bold text-dark small">{{ $doc['nama'] }}</div>
                                                        @if (!empty($doc['url']))
                                                            <a href="{{ $doc['url'] }}" target="_blank"
                                                                class="text-primary extra-small fw-bold"><i
                                                                    class='bx bx-link-external me-1'></i> Lihat Dokumen</a>
                                                        @else
                                                            <span class="text-muted extra-small italic">Belum ada
                                                                file</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                @if ($isMyTurn)
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <input type="radio" class="btn-check"
                                                            name="status_toggle_{{ $uniq }}"
                                                            id="valid{{ $uniq }}" autocomplete="off"
                                                            value="1" {{ $isValid ? 'checked' : '' }}
                                                            onchange="setDocStatus('{{ $uniq }}', true, '{{ $item->id }}')">
                                                        <label
                                                            class="btn btn-outline-success border-0 px-3 py-2 fw-bold rounded-start-pill"
                                                            for="valid{{ $uniq }}"><i
                                                                class='bx bx-check me-1'></i> Terima</label>

                                                        <input type="radio" class="btn-check"
                                                            name="status_toggle_{{ $uniq }}"
                                                            id="revisi{{ $uniq }}" autocomplete="off"
                                                            value="0" {{ !$isValid ? 'checked' : '' }}
                                                            onchange="setDocStatus('{{ $uniq }}', false, '{{ $item->id }}')">
                                                        <label
                                                            class="btn btn-outline-danger border-0 px-3 py-2 fw-bold rounded-end-pill"
                                                            for="revisi{{ $uniq }}"><i class='bx bx-x me-1'></i>
                                                            Tolak</label>
                                                    </div>
                                                @endif
                                            </div>
                                            {{-- @if ($isMyTurn)
                                                <div id="note{{ $uniq }}"
                                                    class="{{ $isValid ? 'd-none' : '' }} mt-2">
                                                    <div
                                                        class="p-3 bg-light-danger rounded-4 border border-danger border-opacity-25 mx-2 shadow-xs">
                                                        <label
                                                            class="form-label extra-small fw-bold text-danger text-uppercase mb-1">Alasan
                                                            Penolakan</label>
                                                        <input type="text" name="catatan[{{ $doc['id'] }}]"
                                                            value="{{ $doc['catatan'] ?? '' }}"
                                                            class="form-control form-control-sm text-danger border-0 bg-white"
                                                            placeholder="Kenapa dokumen ini ditolak?">
                                                    </div>
                                                </div>
                                            @endif --}}

                                            @if ($isMyTurn)
                                                <div id="note{{ $uniq }}"
                                                    class="{{ $isValid ? 'd-none' : '' }} mt-2">
                                                    <div
                                                        class="p-3 bg-light-danger rounded-4 border border-danger border-opacity-25 mx-2 shadow-xs">
                                                        <label
                                                            class="form-label extra-small fw-bold text-danger text-uppercase mb-1">Alasan
                                                            Penolakan</label>
                                                        <input type="text" name="catatan[{{ $doc['id'] }}]"
                                                            value="{{ $doc['catatan'] ?? '' }}"
                                                            class="form-control form-control-sm text-danger border-0 bg-white"
                                                            placeholder="Kenapa dokumen ini ditolak?">
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>

                                <div id="wrapperAlasan{{ $item->id }}" class="mt-4 d-none">
                                    <label class="form-label fw-bold extra-small text-danger text-uppercase mb-2">
                                        <i class='bx bx-error-circle me-1'></i> Alasan Revisi (Kesimpulan)
                                    </label>
                                    <textarea name="alasan_tolak" id="inputAlasanTolak{{ $item->id }}"
                                        class="form-control border-0 shadow-xs rounded-4 bg-white p-3 extra-small" rows="2"
                                        placeholder="Sebutkan alasan utama kenapa berkas dikembalikan..."></textarea>
                                </div>

                                <div class="mt-4">
                                    <label class="form-label fw-bold extra-small text-muted text-uppercase mb-2">Catatan
                                        Internal / Pesan Lanjutan</label>
                                    <textarea name="catatan_internal" class="form-control border-0 shadow-xs rounded-4 bg-white p-3 extra-small"
                                        rows="2" {{ !$isMyTurn ? 'disabled' : '' }}>{{ $item->catatan_internal }}</textarea>
                                </div>
                            </div>
                            <div
                                class="modal-footer border-0 px-4 pb-4 pt-2 bg-light-subtle d-flex justify-content-between">
                                @if ($isMyTurn)
                                    <button type="submit" name="action" value="reject"
                                        id="btnReject{{ $item->id }}"
                                        class="btn btn-label-danger rounded-pill px-4 fw-bold shadow-none border-0 w-100">
                                        <i class='bx bx-x-circle me-1'></i> Kembalikan Berkas (Revisi)
                                    </button>

                                    <button type="submit" name="action" value="approve"
                                        id="btnApprove{{ $item->id }}"
                                        class="btn btn-primary rounded-pill px-5 shadow-sm fw-bold w-100 d-none">
                                        <i class='bx bx-check-circle me-1'></i> Setujui & Teruskan
                                    </button>
                                @else
                                    <button type="button" class="btn btn-label-secondary w-100 rounded-pill"
                                        data-bs-dismiss="modal">Tutup</button>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        {{-- MODAL 4: DETAIL PENOLAKAN PERMANEN --}}
        @if (str_contains($st, 'tolak') || str_contains($st, 'rejected'))
            <div class="modal fade" id="modalDetailReject{{ $item->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-sm">
                    <div class="modal-content border-0 shadow-lg rounded-4 text-center">
                        {{-- HEADER MERAH STANDAR --}}
                        <div
                            class="modal-header bg-danger text-white border-0 py-3 justify-content-center position-relative">
                            <h6 class="modal-title fw-bold text-white mb-0">Informasi Penolakan</h6>
                            {{-- 🔥 UPDATE: Tombol Close Background Putih 🔥 --}}
                            <button type="button"
                                class="btn-close bg-white opacity-100 position-absolute top-50 end-0 translate-middle-y me-3"
                                data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <i class='bx bx-x-circle text-danger mb-3' style="font-size: 3.5rem;"></i>
                            <h6 class="fw-bold mb-1">Alasan Penolakan:</h6>
                            <p class="text-muted small mb-3">
                                {{ $item->alasan_tolak ?? 'Maaf, permohonan ini tidak dapat disetujui.' }}</p>
                            <div class="bg-light p-2 rounded small text-muted">
                                <i class='bx bx-time me-1'></i> Tanggal: {{ $item->updated_at->format('d/m/Y') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    <script>
        function toggleTolakAwal(id, show) {
            document.getElementById('formTolakAwal' + id).classList.toggle('d-none', !show);
            document.getElementById('btnGroupAwal' + id).classList.toggle('d-none', show);
        }

        function tambahSyaratManual(id) {
            let container = document.getElementById('containerManual' + id);
            let div = document.createElement('div');
            div.className =
                'input-group shadow-none border rounded-3 mb-2 bg-white overflow-hidden animate__animated animate__fadeInDown';
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
            if (inputs.length === 1 && inputs[0].value === "") {
                inputs[0].value = text;
            } else {
                tambahSyaratManual(id);
                let newIn = container.querySelectorAll('input');
                newIn[newIn.length - 1].value = text;
            }
        }

        function setDocStatus(uniqueId, isValid, itemId) {
            let note = document.getElementById('note' + uniqueId);
            if (note) note.classList.toggle('d-none', isValid);
            updateSubmitButton(itemId);
        }

        function updateSubmitButton(itemId) {
            let form = document.getElementById('formCek' + itemId);
            if (!form) return;

            let checkedRadios = form.querySelectorAll('input[type="radio"]:checked');
            let allValid = true;
            let countChecked = 0;

            checkedRadios.forEach(radio => {
                if (radio.value === '0') allValid = false;
                countChecked++;
            });

            let btnApprove = document.getElementById('btnApprove' + itemId);
            let btnReject = document.getElementById('btnReject' + itemId);
            let inputAlasan = document.getElementById('inputAlasanTolak' + itemId);
            let wrapperAlasan = document.getElementById('wrapperAlasan' + itemId); // <-- TAMBAHKAN DEFINISI INI

            if (countChecked > 0 && allValid) {
                if (btnApprove) btnApprove.classList.remove('d-none');
                if (btnReject) btnReject.classList.add('d-none');
                if (wrapperAlasan) wrapperAlasan.classList.add('d-none');
                if (inputAlasan) inputAlasan.removeAttribute('required');
            } else if (countChecked > 0 && !allValid) { // Pastikan ada yang dicek & ada yang tolak
                if (btnApprove) btnApprove.classList.add('d-none');
                if (btnReject) btnReject.classList.remove('d-none');
                if (wrapperAlasan) wrapperAlasan.classList.remove('d-none');
                if (inputAlasan) inputAlasan.setAttribute('required', 'required');
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            let forms = document.querySelectorAll('form[id^="formCek"]');
            forms.forEach(form => {
                let itemId = form.id.replace('formCek', '');
                updateSubmitButton(itemId);
            });
        });
    </script>
@endsection
