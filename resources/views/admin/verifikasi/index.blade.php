@extends('layouts.admin')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- =================================================================== --}}
        {{-- 1. HEADER PAGE (DINAMIS SESUAI KATEGORI TUGAS) --}}
        {{-- =================================================================== --}}
        <div class="d-flex justify-content-between align-items-center py-3 mb-2">
            <div>
                <h4 class="fw-bold m-0 text-primary">
                    <span class="text-muted fw-light">Layanan /</span>
                    {{-- Tampilkan Nama Layanan yang sedang dibuka --}}
                    @if (isset($kategoriUrl) && $kategoriUrl)
                        {{ ucwords(str_replace('-', ' ', $kategoriUrl)) }}
                    @else
                        {{ $title ?? 'Semua Layanan' }}
                    @endif
                </h4>
                <small class="text-muted">
                    @if (Auth::user()->role == 'Pegawai')
                        <i class="bx bx-user-check me-1"></i> Mode Tugas:
                        <span class="fw-bold text-dark">
                            {{ isset($kategoriUrl) ? 'Spesifik (' . ucwords(str_replace('-', ' ', $kategoriUrl)) . ')' : 'Umum (Semua Layanan)' }}
                        </span>
                    @else
                        Pantau dan kelola progres pengajuan berkas sekolah.
                    @endif
                </small>
            </div>
        </div>

        {{-- =================================================================== --}}
        {{-- 2. STATISTIK CARDS (DATA REALTIME) --}}
        {{-- =================================================================== --}}
        <div class="row mb-4 g-3">
            {{-- Card 1: Perlu Syarat --}}
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

            {{-- Card 2: Menunggu Upload --}}
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

            {{-- Card 3: Siap Diperiksa --}}
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

            {{-- Card 4: Selesai --}}
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
        <div class="card shadow-sm border-0">
            <div class="card-header border-bottom bg-white py-3">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                    <h5 class="m-0 fw-bold"><i class="bx bx-list-ul me-2"></i>Daftar Pengajuan</h5>

                    {{-- FILTER COMPACT --}}
                    <form action="{{ url()->current() }}" method="GET" class="d-flex align-items-center gap-2">

                        {{-- Pertahankan Kategori di URL (Agar tidak hilang saat filter status/page) --}}
                        @if (request('kategori'))
                            <input type="hidden" name="kategori" value="{{ request('kategori') }}">
                        @endif

                        <div class="input-group input-group-merge" style="width: 250px;">
                            <span class="input-group-text bg-light border-light"><i
                                    class="bx bx-filter-alt small"></i></span>
                            <select name="status" class="form-select border-light bg-light form-select-sm"
                                onchange="this.form.submit()">
                                <option value="">Semua Status</option>
                                <option value="Proses" {{ request('status') == 'Proses' ? 'selected' : '' }}>Tiket Baru
                                </option>
                                <option value="Verifikasi Berkas"
                                    {{ request('status') == 'Verifikasi Berkas' ? 'selected' : '' }}>Di Meja Admin</option>
                                <option value="Verifikasi Kasubag"
                                    {{ request('status') == 'Verifikasi Kasubag' ? 'selected' : '' }}>Di Meja Kasubag
                                </option>
                                <option value="Verifikasi Kepala"
                                    {{ request('status') == 'Verifikasi Kepala' ? 'selected' : '' }}>Di Meja Kepala
                                </option>
                                <option value="ACC" {{ request('status') == 'ACC' ? 'selected' : '' }}>Selesai (ACC)
                                </option>
                                <option value="Revisi" {{ request('status') == 'Revisi' ? 'selected' : '' }}>Revisi Sekolah
                                </option>
                            </select>
                        </div>

                        @if (request('status'))
                            <a href="{{ url()->current() }}?{{ http_build_query(request()->except('status')) }}"
                                class="btn btn-sm btn-icon btn-outline-secondary border-light" title="Reset Filter">
                                <i class="bx bx-refresh"></i>
                            </a>
                        @endif
                    </form>
                </div>
            </div>

            @if ($data->count() > 0)
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
                            @foreach ($data as $item)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-3">
                                                <span class="avatar-initial rounded-circle bg-label-primary"><i
                                                        class='bx bxs-school'></i></span>
                                            </div>
                                            <div>
                                                <span
                                                    class="fw-bold d-block text-dark small">{{ $item->nama_sekolah }}</span>
                                                <small class="text-muted">{{ $item->nama_guru }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-label-secondary mb-1" style="font-size: 0.7rem;">
                                            {{ strtoupper(str_replace('-', ' ', $item->kategori)) }}
                                        </span>
                                        <div class="text-wrap small text-dark fw-semibold" style="max-width: 250px;">
                                            {{ Str::limit($item->judul, 40) }}
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $statusClass = match ($item->status) {
                                                'Proses', 'Verifikasi Berkas' => 'bg-label-primary',
                                                'Verifikasi Kasubag' => 'bg-label-warning',
                                                'Verifikasi Kepala' => 'bg-label-info',
                                                'ACC' => 'bg-label-success',
                                                'Revisi', 'Ditolak' => 'bg-label-danger',
                                                default => 'bg-label-secondary',
                                            };
                                            $statusLabel = match ($item->status) {
                                                'Proses' => 'Tiket Baru',
                                                'Verifikasi Berkas' => 'Cek Admin',
                                                'Verifikasi Kasubag' => 'Cek Kasubag',
                                                'Verifikasi Kepala' => 'Cek Kepala',
                                                default => $item->status,
                                            };
                                        @endphp
                                        <span class="badge {{ $statusClass }}">
                                            {{ $statusLabel }}
                                        </span>
                                        @if ($item->status == 'ACC' && $item->nomor_sk)
                                            <div class="d-block mt-1 small text-muted"><i class='bx bx-hash'></i>
                                                {{ $item->nomor_sk }}</div>
                                        @endif
                                    </td>

                                    <td class="text-end pe-4">
                                        {{-- 1. Tombol Atur Syarat (Tiket Baru) --}}
                                        @if ($item->status == 'Proses')
                                            <button class="btn btn-sm btn-primary shadow-sm" data-bs-toggle="modal"
                                                data-bs-target="#modalSyarat{{ $item->id }}">
                                                <i class='bx bx-list-check me-1'></i> Atur Syarat
                                            </button>

                                            {{-- 2. Tombol Verifikasi (Admin/Kasubag/Kepala) --}}
                                        @elseif(in_array($item->status, ['Verifikasi Berkas', 'Verifikasi Kasubag', 'Verifikasi Kepala']))
                                            <button
                                                class="btn btn-sm {{ $item->status == 'Verifikasi Berkas' ? 'btn-primary' : ($item->status == 'Verifikasi Kasubag' ? 'btn-warning' : 'btn-info') }} shadow-sm"
                                                data-bs-toggle="modal" data-bs-target="#modalCek{{ $item->id }}">
                                                @if ($item->status == 'Verifikasi Berkas')
                                                    <i class='bx bx-search-alt me-1'></i> Periksa (Admin)
                                                @elseif($item->status == 'Verifikasi Kasubag')
                                                    <i class='bx bx-edit-alt me-1'></i> Periksa (Kasubag)
                                                @elseif($item->status == 'Verifikasi Kepala')
                                                    <i class='bx bx-pen me-1'></i> Approval (Kepala)
                                                @endif
                                            </button>

                                            {{-- 3. Tombol Cetak (ACC) --}}
                                        @elseif($item->status == 'ACC')
                                            @if ($item->template_id)
                                                <a href="{{ route('cetak.sk', $item->uuid) }}" target="_blank"
                                                    class="btn btn-sm btn-success shadow-sm">
                                                    <i class='bx bx-printer me-1'></i> Cetak SK
                                                </a>
                                            @else
                                                <span class="badge bg-label-secondary" title="Template belum dipilih"><i
                                                        class='bx bx-error-circle'></i> Template Missing</span>
                                            @endif

                                            {{-- 4. Status Revisi --}}
                                        @elseif($item->status == 'Revisi')
                                            <span class="badge bg-danger"><i class='bx bx-time'></i> Tunggu Revisi</span>
                                        @endif

                                        {{-- INCLUDE MODAL (LANGSUNG DI SINI BIAR MUDAH) --}}

                                        {{-- ========================================================== --}}
                                        {{-- START: MODAL SYARAT (Admin)                                --}}
                                        {{-- ========================================================== --}}
                                        <div class="modal fade" id="modalSyarat{{ $item->id }}" tabindex="-1"
                                            aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow-lg">
                                                    <div class="modal-header border-bottom">
                                                        <h5 class="modal-title fw-bold">Atur Persyaratan Dokumen</h5>
                                                        <button type="button" class="btn-close btn-close-animated"
                                                            data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <form action="{{ route('admin.verifikasi.set_syarat', $item->id) }}"
                                                        method="POST">
                                                        @csrf @method('PUT')
                                                        <div class="modal-body bg-light">
                                                            <div
                                                                class="alert alert-primary shadow-sm border-0 mb-3 text-start">
                                                                <i class="bx bx-info-circle me-1"></i>
                                                                Tentukan dokumen apa saja yang harus diupload oleh
                                                                <b>{{ $item->nama_sekolah }}</b>.
                                                            </div>
                                                            <label
                                                                class="form-label fw-bold text-uppercase small text-muted">Daftar
                                                                Dokumen</label>
                                                            <div id="containerManual{{ $item->id }}">
                                                                <div class="input-group mb-2">
                                                                    <span class="input-group-text bg-white"><i
                                                                            class='bx bx-file'></i></span>
                                                                    <input type="text" name="syarat[]"
                                                                        class="form-control"
                                                                        placeholder="Contoh: Surat Pengantar Kepala Sekolah"
                                                                        required>
                                                                </div>
                                                            </div>
                                                            <button type="button"
                                                                class="btn btn-outline-primary btn-sm w-100 dashed-border mt-2"
                                                                onclick="tambahSyaratManual({{ $item->id }})">
                                                                <i class="bx bx-plus"></i> Tambah Dokumen Lain
                                                            </button>
                                                        </div>
                                                        <div class="modal-footer border-top bg-white">
                                                            <button type="button" class="btn btn-label-secondary"
                                                                data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-primary">Simpan & Minta
                                                                Upload</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- ========================================================== --}}
                                        {{-- START: MODAL CEK (SMART MODAL)                             --}}
                                        {{-- ========================================================== --}}
                                        <div class="modal fade" id="modalCek{{ $item->id }}" tabindex="-1"
                                            aria-hidden="true">
                                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                                <div class="modal-content border-0 shadow-lg">
                                                    <div class="modal-header border-bottom">
                                                        <div>
                                                            <h5 class="modal-title fw-bold">
                                                                @if ($item->status == 'Verifikasi Kasubag')
                                                                    Validasi Kasubag
                                                                @elseif($item->status == 'Verifikasi Kepala')
                                                                    Approval Kepala
                                                                @else
                                                                    Verifikasi Admin
                                                                @endif
                                                            </h5>
                                                            <small class="text-muted">Pengirim:
                                                                {{ $item->nama_sekolah }}</small>
                                                        </div>
                                                        <button type="button" class="btn-close btn-close-animated"
                                                            data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>

                                                    @php
                                                        $actionRoute = match ($item->status) {
                                                            'Verifikasi Kasubag' => route(
                                                                'admin.verifikasi.kasubag_process',
                                                                $item->id,
                                                            ),
                                                            'Verifikasi Kepala' => route(
                                                                'admin.verifikasi.kepala_process',
                                                                $item->id,
                                                            ),
                                                            default => route('admin.verifikasi.process', $item->id),
                                                        };
                                                    @endphp

                                                    <form action="{{ $actionRoute }}" method="POST">
                                                        @csrf @method('PUT')
                                                        <div class="modal-body bg-light">
                                                            {{-- Info Singkat --}}
                                                            <div class="card shadow-sm border-0 mb-3">
                                                                <div class="card-body p-3 d-flex align-items-center gap-3">
                                                                    <div
                                                                        class="avatar bg-label-{{ $item->status == 'Verifikasi Kepala' ? 'info' : ($item->status == 'Verifikasi Kasubag' ? 'warning' : 'primary') }} rounded p-2">
                                                                        <i class="bx bx-file fs-3"></i>
                                                                    </div>
                                                                    <div>
                                                                        <h6 class="mb-0 fw-bold">{{ $item->judul }}</h6>
                                                                        <small
                                                                            class="text-muted">{{ $item->kategori }}</small>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            {{-- DROPDOWN PILIH TEMPLATE (KHUSUS KEPALA) --}}
                                                            @if ($item->status == 'Verifikasi Kepala')
                                                                <div
                                                                    class="card p-3 border border-primary bg-white mb-4 animate__animated animate__fadeIn">
                                                                    <label class="form-label fw-bold text-primary mb-2">
                                                                        <i class='bx bx-layout me-1'></i> Pilih Format SK
                                                                        yang Akan Dicetak
                                                                    </label>
                                                                    <select name="template_id"
                                                                        class="form-select border-primary" required>
                                                                        <option value="" selected disabled>-- Pilih
                                                                            Template Surat --</option>
                                                                        @foreach ($templates as $tpl)
                                                                            <option value="{{ $tpl->id }}">
                                                                                {{ $tpl->judul_surat }} (Kertas:
                                                                                {{ $tpl->ukuran_kertas }})
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                    <div class="form-text small text-muted mt-1">
                                                                        *Template ini akan menentukan desain PDF saat
                                                                        dicetak nanti.
                                                                    </div>

                                                                    @if (!empty($item->catatan_internal))
                                                                        <div
                                                                            class="alert alert-warning mt-3 mb-0 d-flex align-items-center">
                                                                            <i class='bx bx-note me-2'></i>
                                                                            <div><strong>Note Kasubag:</strong>
                                                                                {{ $item->catatan_internal }}</div>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            @endif

                                                            <h6 class="fw-bold mb-3 mt-4"><i
                                                                    class="bx bx-check-square me-2"></i>Kelengkapan Dokumen
                                                            </h6>
                                                            <div
                                                                class="table-responsive bg-white rounded shadow-sm border">
                                                                <table class="table table-sm mb-0">
                                                                    <thead class="bg-light">
                                                                        <tr>
                                                                            <th class="ps-3">Nama Dokumen</th>
                                                                            <th class="text-center" style="width: 100px;">
                                                                                Lihat</th>
                                                                            <th class="text-end pe-3"
                                                                                style="width: 150px;">Status</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @forelse($item->dokumen_syarat ?? [] as $doc)
                                                                            @php $uniq = $item->id . '_' . $loop->index; @endphp
                                                                            <tr id="rowDoc{{ $uniq }}">
                                                                                <td class="ps-3 align-middle">
                                                                                    <div class="fw-semibold text-dark">
                                                                                        {{ $doc['nama'] ?? 'Nama Dokumen' }}
                                                                                    </div>
                                                                                    @if ($item->status == 'Verifikasi Berkas')
                                                                                        <div id="note{{ $uniq }}"
                                                                                            class="mt-1">
                                                                                            <input type="text"
                                                                                                name="catatan[{{ $doc['id'] ?? $loop->index }}]"
                                                                                                class="form-control form-control-sm border-danger text-danger"
                                                                                                placeholder="Alasan tolak..."
                                                                                                style="font-size: 0.85rem;">
                                                                                        </div>
                                                                                    @endif
                                                                                </td>
                                                                                <td class="text-center align-middle">
                                                                                    <a href="{{ $doc['file'] ? asset('storage/' . $doc['file']) : '#' }}"
                                                                                        target="_blank"
                                                                                        class="btn btn-icon btn-sm btn-label-secondary">
                                                                                        <i class="bx bx-show"></i>
                                                                                    </a>
                                                                                </td>
                                                                                <td class="text-end pe-3 align-middle">
                                                                                    @if ($item->status == 'Verifikasi Berkas')
                                                                                        <div
                                                                                            class="form-check form-switch d-flex justify-content-end">
                                                                                            <input class="form-check-input"
                                                                                                type="checkbox"
                                                                                                role="switch"
                                                                                                id="chk{{ $uniq }}"
                                                                                                onchange="toggleCatatan('{{ $uniq }}')">
                                                                                        </div>
                                                                                    @else
                                                                                        <span
                                                                                            class="badge bg-label-success"><i
                                                                                                class='bx bx-check'></i>
                                                                                            Ada</span>
                                                                                    @endif
                                                                                </td>
                                                                            </tr>
                                                                        @empty
                                                                            <tr>
                                                                                <td colspan="3"
                                                                                    class="text-center py-3 text-muted">
                                                                                    Belum ada dokumen yang diupload.</td>
                                                                            </tr>
                                                                        @endforelse
                                                                    </tbody>
                                                                </table>
                                                            </div>

                                                            {{-- FORM KASUBAG --}}
                                                            @if ($item->status == 'Verifikasi Kasubag')
                                                                <div class="mt-3 animate__animated animate__fadeIn">
                                                                    <label
                                                                        class="form-label fw-bold small text-uppercase text-muted">Catatan
                                                                        Internal (Opsional)</label>
                                                                    <textarea name="catatan_internal" class="form-control" rows="2" placeholder="Pesan untuk Kepala KCD..."></textarea>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div
                                                            class="modal-footer border-top bg-white justify-content-between">
                                                            <div>
                                                                <button type="submit" name="action" value="reject"
                                                                    class="btn btn-outline-danger">
                                                                    <i class="bx bx-undo me-1"></i>
                                                                    {{ $item->status == 'Verifikasi Kepala' ? 'Tolak' : 'Minta Revisi' }}
                                                                </button>
                                                            </div>
                                                            <div class="d-flex gap-2">
                                                                <button type="button" class="btn btn-label-secondary"
                                                                    data-bs-dismiss="modal">Tutup</button>
                                                                <button type="submit" name="action" value="approve"
                                                                    class="btn {{ $item->status == 'Verifikasi Kepala' ? 'btn-info' : ($item->status == 'Verifikasi Kasubag' ? 'btn-warning' : 'btn-success') }}">
                                                                    @if ($item->status == 'Verifikasi Kepala')
                                                                        <i class="bx bx-pen me-1"></i> ACC & Tanda
                                                                        Tangan
                                                                    @elseif($item->status == 'Verifikasi Kasubag')
                                                                        <i class="bx bx-send me-1"></i> Teruskan ke
                                                                        Kepala
                                                                    @else
                                                                        <i class="bx bx-check-circle me-1"></i> Lanjut
                                                                        Kasubag
                                                                    @endif
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- END: MODAL CEK --}}

                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div
                    class="px-4 py-3 border-top d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                    <small class="text-muted">Total {{ $data->total() }} data</small>
                    <div>{{ $data->appends(request()->query())->links() }}</div>
                </div>
            @else
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class='bx bx-box text-light' style="font-size: 6rem;"></i>
                    </div>
                    <h5 class="text-muted fw-bold">Tidak ada pengajuan ditemukan.</h5>
                    <p class="text-muted small">Coba ubah filter atau tunggu sekolah mengirim berkas.</p>
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

            if (chk.checked) {
                note.classList.add('d-none');
                row.classList.add('table-success-soft');
                let inputNote = note.querySelector('input');
                if (inputNote) inputNote.value = '';
            } else {
                note.classList.remove('d-none');
                row.classList.remove('table-success-soft');
            }
        }
    </script>

    <style>
        .table-success-soft {
            background-color: #f0fdf4 !important;
            transition: background 0.3s;
        }

        .btn-label-secondary {
            background: #ebeef0;
            color: #8592a3;
            border: none;
        }

        .btn-label-secondary:hover {
            background: #e1e4e6;
        }

        .bg-label-primary {
            background-color: #e7e7ff !important;
            color: #696cff !important;
        }

        .bg-label-success {
            background-color: #e8fadf !important;
            color: #71dd37 !important;
        }

        .bg-label-danger {
            background-color: #ff3e1d29 !important;
            color: #ff3e1d !important;
        }

        .bg-label-warning {
            background-color: #fff2d6 !important;
            color: #ffab00 !important;
        }

        .bg-label-info {
            background-color: #d7f5fc !important;
            color: #03c3ec !important;
        }

        .btn-close-animated {
            background-color: white !important;
            border-radius: 50%;
            padding: 0.5rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            background-size: 40%;
        }

        .btn-close-animated:hover {
            transform: rotate(90deg) scale(1.1);
            background-color: #f0f2f5 !important;
        }
    </style>
@endsection
