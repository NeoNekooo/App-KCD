@extends('layouts.admin')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center py-3 mb-2">
            <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Administrasi /</span> Pengaturan Nomor</h4>

            <div class="d-flex gap-2">
                <button class="btn btn-dark shadow-sm" data-bs-toggle="modal" data-bs-target="#modalHistory">
                    <i class="bx bx-history me-1"></i> Lihat History
                </button>
                <button class="btn btn-primary shadow-sm" onclick="resetForm()" data-bs-toggle="modal"
                    data-bs-target="#modalTambah">
                    <i class="bx bx-plus me-1"></i> Format Baru
                </button>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Kategori / Label</th>
                            <th>Format Surat</th>
                            <th class="text-center">Counter Saat Ini</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($settings as $s)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-3 bg-label-primary rounded p-1">
                                            <i class="bx bx-file fs-4"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold text-dark">{{ $s->judul_kop }}</h6>
                                            <small
                                                class="badge bg-label-secondary text-lowercase">{{ $s->kategori }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <code
                                        class="fw-bold text-primary bg-label-primary px-2 py-1 rounded">{{ $s->format_surat }}</code>
                                    <div class="small text-muted mt-1"><i class="bx bx-right-arrow-alt"></i> Preview:
                                        {{ $s->preview }}</div>
                                </td>
                                <td class="text-center">
                                    <h3 class="mb-0 fw-bold text-dark">
                                        {{ str_pad($s->nomor_terakhir, 3, '0', STR_PAD_LEFT) }}</h3>

                                    {{-- Tombol Reset memicu Modal --}}
                                    <button type="button" class="btn btn-xs btn-link text-danger p-0"
                                        style="font-size: 0.75rem;"
                                        onclick="confirmAction('{{ route('admin.administrasi.pengaturan-nomor.reset', $s->id) }}', 'Reset Counter', 'Yakin ingin mereset counter menjadi 000? Ini bisa menyebabkan nomor ganda di masa depan.', 'warning')">
                                        <i class="bx bx-refresh"></i> Reset ke 0
                                    </button>
                                </td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-icon btn-outline-warning shadow-sm"
                                        onclick="edit({{ $s }})" data-bs-toggle="modal"
                                        data-bs-target="#modalTambah">
                                        <i class="bx bx-pencil"></i>
                                    </button>

                                    {{-- Tombol Hapus memicu Modal --}}
                                    <button type="button" class="btn btn-sm btn-icon btn-outline-danger shadow-sm"
                                        onclick="confirmAction('{{ route('admin.administrasi.pengaturan-nomor.destroy', $s->id) }}', 'Hapus Format', 'Apakah Anda yakin ingin menghapus format penomoran ini?', 'danger', 'DELETE')">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <img src="https://cdni.iconscout.com/illustration/premium/thumb/empty-state-2130362-1800926.png"
                                        alt="Empty" width="150" class="mb-3">
                                    <p class="text-muted">Belum ada format nomor surat yang diatur.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- === MODAL HISTORY === --}}
    <div class="modal fade" id="modalHistory" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title text-white"><i class='bx bx-history me-2'></i> Riwayat Penggunaan Nomor</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body bg-light">
                    <ul class="nav nav-pills mb-3 nav-fill" role="tablist">
                        <li class="nav-item">
                            <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab"
                                data-bs-target="#tab-all">Semua</button>
                        </li>
                        @foreach ($settings as $s)
                            <li class="nav-item">
                                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                    data-bs-target="#tab-{{ $s->kategori }}">{{ $s->judul_kop }}</button>
                            </li>
                        @endforeach
                    </ul>
                    <div class="tab-content p-0 bg-transparent shadow-none">
                        <div class="tab-pane fade show active" id="tab-all" role="tabpanel">
                            @include('admin.administrasi.pengaturan-nomor._list_log', [
                                'dataLogs' => $logs,
                            ])
                        </div>
                        @foreach ($settings as $s)
                            <div class="tab-pane fade" id="tab-{{ $s->kategori }}" role="tabpanel">
                                @php $filteredLogs = $logs->where('kategori', $s->kategori); @endphp
                                @include('admin.administrasi.pengaturan-nomor._list_log', [
                                    'dataLogs' => $filteredLogs,
                                ])
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- === MODAL TAMBAH / EDIT === --}}
    <div class="modal fade" id="modalTambah" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content" action="{{ route('admin.administrasi.pengaturan-nomor.store') }}" method="POST"
                id="formSetting">
                @csrf
                <div id="methodPut"></div>
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white" id="modalTitle">Format Nomor Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Kategori / Peruntukan</label>
                        <select name="kategori" id="kategori" class="form-select" required>
                            <option value="" disabled selected>-- Pilih Kategori --</option>
                            <option value="siswa">Siswa</option>
                            <option value="guru">Guru</option>
                            <option value="sk">SK Sekolah</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Label (Judul)</label>
                        <input type="text" name="judul_kop" id="judul_kop" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Format Penomoran</label>
                        <input type="text" name="format_surat" id="format_surat" class="form-control" required>
                        <div class="form-text mt-2">
                            <small class="badge bg-label-info pointer" onclick="insertTag('{no}')">{no}</small>
                            <small class="badge bg-label-info pointer" onclick="insertTag('{romawi}')">{romawi}</small>
                            <small class="badge bg-label-info pointer" onclick="insertTag('{tahun}')">{tahun}</small>
                            <small class="badge bg-label-info pointer" onclick="insertTag('{bulan}')">{bulan}</small>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-danger">Counter Manual</label>
                        <input type="number" name="nomor_terakhir" id="nomor_terakhir" class="form-control"
                            value="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>

    {{-- === MODAL KONFIRMASI (NEW) === --}}
    <div class="modal fade" id="modalKonfirmasi" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center pt-0">
                    <div id="confirmIcon" class="mb-3">
                        <i class="bx bx-help-circle display-4 text-warning"></i>
                    </div>
                    <h5 id="confirmTitle" class="fw-bold">Konfirmasi</h5>
                    <p id="confirmMessage" class="text-muted mb-4 small"></p>
                    <form id="confirmForm" method="POST">
                        @csrf
                        <div id="confirmMethod"></div>
                        <div class="d-grid gap-2">
                            <button type="submit" id="confirmBtn" class="btn btn-primary">Ya, Lanjutkan</button>
                            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Fungsi untuk memicu Modal Konfirmasi
        function confirmAction(url, title, message, color = 'primary', method = 'POST') {
            const modal = new bootstrap.Modal(document.getElementById('modalKonfirmasi'));
            document.getElementById('confirmForm').action = url;
            document.getElementById('confirmTitle').innerText = title;
            document.getElementById('confirmMessage').innerText = message;

            // Atur warna tombol dan icon
            const btn = document.getElementById('confirmBtn');
            const iconDiv = document.getElementById('confirmIcon');
            btn.className = `btn btn-${color}`;

            let iconClass = 'bx bx-help-circle';
            if (color === 'danger') iconClass = 'bx bx-trash';
            if (color === 'warning') iconClass = 'bx bx-refresh';
            iconDiv.innerHTML = `<i class="${iconClass} display-4 text-${color}"></i>`;

            // Atur Method (DELETE/POST)
            const methodDiv = document.getElementById('confirmMethod');
            if (method === 'DELETE') {
                methodDiv.innerHTML = '<input type="hidden" name="_method" value="DELETE">';
            } else {
                methodDiv.innerHTML = '';
            }

            modal.show();
        }

        function resetForm() {
            document.getElementById('formSetting').action = "{{ route('admin.administrasi.pengaturan-nomor.store') }}";
            document.getElementById('methodPut').innerHTML = '';
            document.getElementById('formSetting').reset();
            let catSelect = document.getElementById('kategori');
            catSelect.style.pointerEvents = 'auto';
            catSelect.style.background = 'white';
            document.getElementById('modalTitle').innerText = "Format Nomor Baru";
        }

        function edit(d) {
            document.getElementById('formSetting').action =
                "{{ route('admin.administrasi.pengaturan-nomor.update', ':id') }}".replace(':id', d.id);
            document.getElementById('methodPut').innerHTML = '<input type="hidden" name="_method" value="PUT">';
            let catSelect = document.getElementById('kategori');
            catSelect.value = d.kategori;
            catSelect.style.pointerEvents = 'none';
            catSelect.style.background = '#e9ecef';
            document.getElementById('judul_kop').value = d.judul_kop;
            document.getElementById('format_surat').value = d.format_surat;
            document.getElementById('nomor_terakhir').value = d.nomor_terakhir;
            document.getElementById('modalTitle').innerText = "Edit Format Nomor";
        }

        function insertTag(tag) {
            let input = document.getElementById('format_surat');
            input.value = input.value + tag;
            input.focus();
        }
    </script>
@endsection
