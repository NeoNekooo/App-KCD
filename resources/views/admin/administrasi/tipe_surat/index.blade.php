@extends('layouts.admin')

@section('content')
    {{-- === CSS LIBRARY (Quill v2.0.2) === --}}
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/quill-html-edit-button@2.2.11/dist/quill-html-edit-button.min.css"
        rel="stylesheet">

    <style>
        /* 1. FONT DEFINITIONS */
        .ql-font-times-new-roman {
            font-family: 'Times New Roman', Times, serif;
        }

        .ql-font-arial {
            font-family: Arial, Helvetica, sans-serif;
        }

        .ql-font-courier-new {
            font-family: 'Courier New', Courier, monospace;
        }

        .ql-font-calibri {
            font-family: 'Calibri', sans-serif;
        }

        .ql-font-verdana {
            font-family: Verdana, Geneva, sans-serif;
        }

        /* 2. DROPDOWN MENU */
        .ql-snow .ql-picker.ql-size {
            width: 90px !important;
        }

        .ql-snow .ql-picker.ql-font {
            width: 150px !important;
        }

        .ql-snow .ql-picker.ql-size .ql-picker-label::before,
        .ql-snow .ql-picker.ql-size .ql-picker-item::before {
            content: attr(data-value) !important;
        }

        .ql-snow .ql-picker.ql-size .ql-picker-label:not([data-value])::before,
        .ql-snow .ql-picker.ql-size .ql-picker-item:not([data-value])::before {
            content: '12px' !important;
        }

        .ql-snow .ql-picker.ql-font .ql-picker-label[data-value="Times New Roman"]::before,
        .ql-snow .ql-picker.ql-font .ql-picker-item[data-value="Times New Roman"]::before {
            content: 'Times New Roman' !important;
            font-family: 'Times New Roman';
        }

        .ql-snow .ql-picker.ql-font .ql-picker-label[data-value="Arial"]::before,
        .ql-snow .ql-picker.ql-font .ql-picker-item[data-value="Arial"]::before {
            content: 'Arial' !important;
            font-family: Arial;
        }

        .ql-snow .ql-picker.ql-font .ql-picker-label[data-value="Courier New"]::before,
        .ql-snow .ql-picker.ql-font .ql-picker-item[data-value="Courier New"]::before {
            content: 'Courier New' !important;
            font-family: 'Courier New';
        }

        .ql-snow .ql-picker.ql-font .ql-picker-label[data-value="Calibri"]::before,
        .ql-snow .ql-picker.ql-font .ql-picker-item[data-value="Calibri"]::before {
            content: 'Calibri' !important;
            font-family: 'Calibri';
        }

        .ql-snow .ql-picker.ql-font .ql-picker-label[data-value="Verdana"]::before,
        .ql-snow .ql-picker.ql-font .ql-picker-item[data-value="Verdana"]::before {
            content: 'Verdana' !important;
            font-family: Verdana;
        }

        .ql-snow .ql-picker.ql-font .ql-picker-label:not([data-value])::before,
        .ql-snow .ql-picker.ql-font .ql-picker-item:not([data-value])::before {
            content: 'Sans Serif' !important;
        }

        /* 3. TOOLBAR & LAYOUT */
        #toolbar-container {
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-top-left-radius: 0.375rem;
            border-top-right-radius: 0.375rem;
            border-bottom: 1px solid #ccc;
            position: sticky;
            top: 0;
            z-index: 100;
            padding: 5px 10px !important;
            width: 100%;
        }

        .ql-toolbar.ql-snow {
            border: none !important;
            display: flex !important;
            flex-flow: row wrap !important;
            align-items: center;
            gap: 2px;
            padding: 0 !important;
        }

        .ql-toolbar.ql-snow button {
            width: 26px !important;
            height: 26px !important;
            padding: 3px !important;
            margin: 0 1px !important;
            float: none !important;
        }

        .ql-toolbar.ql-snow button svg {
            width: 18px !important;
            height: 18px !important;
        }

        .ql-snow .ql-picker {
            height: 26px !important;
            margin-right: 5px;
        }

        .ql-snow .ql-picker-label {
            padding-left: 6px !important;
            line-height: 24px;
            border: 1px solid #ddd;
            border-radius: 3px;
        }

        /* Tombol Tabel */
        .table-controls-group {
            display: none;
            border-left: 1px solid #ccc;
            padding-left: 8px;
            margin-left: 5px;
            background-color: #e8f0fe;
            border-radius: 4px;
        }

        .ql-table_delete,
        .ql-table_row_del,
        .ql-table_col_del {
            color: #ff3e1d !important;
        }

        /* Editor Wrapper */
        #editor-wrapper-bg {
            background-color: #e9ecef;
            padding: 40px;
            border: 1px solid #ddd;
            border-top: none;
            border-bottom-left-radius: 0.375rem;
            border-bottom-right-radius: 0.375rem;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            overflow-x: auto;
            width: 100%;
        }

        #editor_konten_quill {
            width: fit-content;
            min-width: 210mm;
        }

        .ql-editor {
            background-color: white !important;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
            margin: 0 auto;
            border: 1px solid #ccc !important;
            min-height: 297mm;
            padding: 2.54cm 2.54cm !important;
            white-space: pre-wrap !important;
            font-family: 'Times New Roman', Times, serif;
            font-size: 12px;
        }

        /* Utils & Chips */
        .var-chip {
            cursor: pointer;
            transition: all 0.2s;
            font-size: 0.8rem;
            border: 1px solid #e0e0e0;
        }

        .var-chip:hover {
            background-color: #e7e7ff !important;
            color: #696cff !important;
            border-color: #696cff;
            transform: translateY(-1px);
        }

        .paper-A4 {
            width: 210mm;
        }

        .paper-F4 {
            width: 215mm;
            min-height: 330mm;
        }

        .paper-Legal {
            width: 216mm;
            min-height: 356mm;
        }

        .paper-Letter {
            width: 216mm;
            min-height: 279mm;
        }

        .ql-editor.show-guide::after {
            content: "";
            position: absolute;
            top: 0;
            bottom: 0;
            left: 50%;
            width: 1px;
            background-image: linear-gradient(to bottom, rgba(255, 0, 0, 0.3) 5px, transparent 5px);
            background-size: 1px 10px;
            background-repeat: repeat-y;
            transform: translateX(-50%);
            pointer-events: none;
            z-index: 999;
            display: block;
        }

        /* Button Styles */
        .btn-soft-primary {
            background-color: rgba(105, 108, 255, 0.16);
            color: #696cff;
            border: none;
        }

        .btn-soft-primary:hover {
            background-color: #696cff;
            color: #fff;
        }

        .btn-soft-danger {
            background-color: rgba(255, 62, 29, 0.16);
            color: #ff3e1d;
            border: none;
        }

        .btn-soft-danger:hover {
            background-color: #ff3e1d;
            color: #fff;
        }

        .btn-icon {
            width: 32px;
            height: 32px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.375rem;
        }

        /* Toast */
        #copy-toast {
            visibility: hidden;
            min-width: 250px;
            background-color: #333;
            color: #fff;
            text-align: center;
            border-radius: 50px;
            padding: 12px 24px;
            position: fixed;
            z-index: 9999;
            left: 50%;
            bottom: 30px;
            transform: translateX(-50%) translateY(20px);
            opacity: 0;
            transition: all 0.3s ease-in-out;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        #copy-toast.show {
            visibility: visible;
            transform: translateX(-50%) translateY(0);
            opacity: 1;
        }

        #copy-toast i {
            font-size: 18px;
            color: #4ade80;
        }

        /* 4. TABLE RESIZER STYLES */
        .ql-editor table {
            table-layout: fixed !important;
            width: 100% !important;
            border-collapse: collapse;
            margin-bottom: 1em;
        }

        .ql-editor td {
            border: 1px solid #000;
            padding: 5px;
            position: relative;
            box-sizing: border-box;
            overflow-wrap: break-word;
            word-wrap: break-word;
            hyphens: auto;
            vertical-align: top;
        }

        .ql-editor td:hover::after {
            content: '';
            position: absolute;
            right: -3px;
            top: 0;
            bottom: 0;
            width: 6px;
            cursor: col-resize;
            background-color: rgba(105, 108, 255, 0.2);
            z-index: 10;
        }

        .resizing-cursor {
            cursor: col-resize !important;
            user-select: none;
        }

        .ql-editor td img {
            max-width: 100%;
            height: auto;
        }
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Administrasi /</span> Template Surat</h4>

        @php
            $kategoriAktif = request()->kategori ?? 'siswa';
            $variables = [];
            // ... (Variable logic kept same) ...
            if ($kategoriAktif == 'siswa') {
                $variables = [
                    ['code' => '{{ nama }}', 'desc' => 'Nama Siswa'],
                    ['code' => '{{ nisn }}', 'desc' => 'NISN'],
                    ['code' => '{{ nipd }}', 'desc' => 'NIPD'],
                    ['code' => '{{ kelas }}', 'desc' => 'Kelas'],
                    ['code' => '{{ tempat_lahir }}', 'desc' => 'Tempat Lahir'],
                    ['code' => '{{ tanggal_lahir }}', 'desc' => 'Tanggal Lahir'],
                    ['code' => '{{ alamat }}', 'desc' => 'Alamat'],
                    ['code' => '{{ nama_wali }}', 'desc' => 'Nama Wali'],
                    ['code' => '{{ tanggal }}', 'desc' => 'Tanggal Surat'],
                ];
            } elseif ($kategoriAktif == 'guru') {
                $variables = [
                    ['code' => '{{ nama }}', 'desc' => 'Nama Guru'],
                    ['code' => '{{ nuptk }}', 'desc' => 'NUPTK'],
                    ['code' => '{{ nip }}', 'desc' => 'NIP'],
                    ['code' => '{{ jabatan }}', 'desc' => 'Jabatan'],
                    ['code' => '{{ unit_kerja }}', 'desc' => 'Unit Kerja'],
                    ['code' => '{{ alamat }}', 'desc' => 'Alamat Guru'],
                    ['code' => '{{ tanggal }}', 'desc' => 'Tanggal Surat'],
                ];
            } else {
                $variables = [
                    ['code' => '{{ nomor_surat }}', 'desc' => 'Nomor Surat'],
                    ['code' => '{{ perihal }}', 'desc' => 'Perihal'],
                    ['code' => '{{ nama_tujuan }}', 'desc' => 'Nama Tujuan'],
                    ['code' => '{{ instansi }}', 'desc' => 'Instansi'],
                    ['code' => '{{ hari_ini }}', 'desc' => 'Hari Ini'],
                    ['code' => '{{ tanggal }}', 'desc' => 'Tanggal Surat'],
                ];
            }
        @endphp

        <ul class="nav nav-pills mb-3">
            <li class="nav-item"><a class="nav-link {{ $kategoriAktif == 'siswa' ? 'active' : '' }}"
                    href="{{ route('admin.administrasi.tipe-surat.index', ['kategori' => 'siswa']) }}"><i
                        class='bx bx-user me-1'></i> Siswa</a></li>
            <li class="nav-item"><a class="nav-link {{ $kategoriAktif == 'guru' ? 'active' : '' }}"
                    href="{{ route('admin.administrasi.tipe-surat.index', ['kategori' => 'guru']) }}"><i
                        class='bx bx-briefcase-alt-2 me-1'></i> Guru</a></li>
            <li class="nav-item"><a class="nav-link {{ $kategoriAktif == 'sk' ? 'active' : '' }}"
                    href="{{ route('admin.administrasi.tipe-surat.index', ['kategori' => 'sk']) }}"><i
                        class='bx bx-file me-1'></i> Surat SK / Umum</a></li>
        </ul>

        <div class="row">
            <div class="col-md-9">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 fw-bold text-primary">
                            {{ isset($template) ? 'Edit Template' : 'Buat Template Baru' }}</h5>
                    </div>
                    <div class="card-body mt-3">
                        <form
                            action="{{ isset($template) ? route('admin.administrasi.tipe-surat.update', $template->id) : route('admin.administrasi.tipe-surat.store') }}"
                            method="POST" id="formTemplate">
                            @csrf
                            @if (isset($template))
                                @method('PUT')
                            @endif
                            <input type="hidden" name="kategori" value="{{ $kategoriAktif }}">

                            <div class="row g-3 mb-4">
                                <div class="col-md-7">
                                    <label class="form-label fw-bold">Judul Surat</label>
                                    <input type="text" name="judul_surat" class="form-control"
                                        placeholder="Contoh: Surat Keputusan"
                                        value="{{ old('judul_surat', $template->judul_surat ?? '') }}" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Ukuran Kertas</label>
                                    <select name="ukuran_kertas" id="paperSizeSelect" class="form-select">
                                        @foreach (['A4', 'F4', 'Legal', 'Letter'] as $uk)
                                            <option value="{{ $uk }}"
                                                {{ old('ukuran_kertas', $template->ukuran_kertas ?? 'A4') == $uk ? 'selected' : '' }}>
                                                {{ $uk }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <input type="hidden" name="font_size" value="12">
                                <div class="col-md-2 d-flex align-items-end">
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" id="toggleCenterGuide">
                                        <label class="form-check-label small text-muted" for="toggleCenterGuide">Garis
                                            Tengah</label>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <small class="text-muted d-block mb-1">Klik variabel untuk menyalin:</small>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach (array_slice($variables, 0, 5) as $var)
                                        <span class="badge bg-white text-secondary border var-chip"
                                            onclick="copyToClipboard('{{ $var['code'] }}')">{{ $var['desc'] }}</span>
                                    @endforeach
                                    <button type="button" class="btn btn-xs btn-primary rounded-pill ms-auto"
                                        data-bs-toggle="modal" data-bs-target="#variableModal">Lihat Semua</button>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold mb-1">Isi Dokumen</label>
                                <div id="toolbar-container"></div>
                                <div id="editor-wrapper-bg">
                                    <div id="editor_konten_quill"></div>
                                </div>
                                <textarea name="template_isi" id="template_isi_hidden" style="display:none;">{{ old('template_isi', $template->template_isi ?? '') }}</textarea>
                            </div>

                            <div class="d-flex gap-2 pt-2 border-top">
                                <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i> Simpan
                                    Template</button>
                                @if (isset($template))
                                    <a href="{{ route('admin.administrasi.tipe-surat.index', ['kategori' => $kategoriAktif]) }}"
                                        class="btn btn-outline-secondary">Batal</a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 fw-bold text-secondary">List {{ ucfirst($kategoriAktif) }}</h6>
                    </div>
                    <ul class="list-group list-group-flush">
                        @foreach ($templates as $t)
                            <li class="list-group-item list-group-item-action py-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1 text-truncate" style="max-width: 130px;">{{ $t->judul_surat }}
                                        </h6>
                                        <small class="badge bg-label-secondary">{{ $t->ukuran_kertas }}</small>
                                    </div>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('admin.administrasi.tipe-surat.edit', $t->id) }}"
                                            class="btn btn-sm btn-soft-primary btn-icon" data-bs-toggle="tooltip"
                                            title="Edit"><i class="bx bx-pencil"></i></a>

                                        {{-- === [MODIFIKASI 1] TOMBOL HAPUS LAMA DIGANTI TOMBOL TRIGGER MODAL === --}}
                                        <button type="button" class="btn btn-sm btn-soft-danger btn-icon"
                                            data-bs-toggle="modal" data-bs-target="#deleteModal"
                                            data-action="{{ route('admin.administrasi.tipe-surat.destroy', $t->id) }}"
                                            title="Hapus">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                        {{-- =================================================================== --}}
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal & Toast --}}
    <div class="modal fade" id="variableModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white">Daftar Variabel</h5><button type="button"
                        class="btn-close btn-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body bg-light">
                    <div class="row g-3">
                        @foreach ($variables as $var)
                            <div class="col-md-4">
                                <div class="card p-3 var-chip" onclick="copyToClipboard('{{ $var['code'] }}')">
                                    {{ $var['desc'] }} <code class="ms-auto">{{ $var['code'] }}</code></div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- === [MODIFIKASI 2] MODAL KONFIRMASI HAPUS (BARU) === --}}
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="deleteForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <p class="mb-0">Apakah Anda yakin ingin menghapus template surat ini?</p>
                        <small class="text-danger">Tindakan ini tidak dapat dibatalkan.</small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- ====================================================== --}}

    <div id="copy-toast"><i class='bx bx-check-circle'></i> <span>Variabel berhasil disalin!</span></div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/quill-html-edit-button@2.2.11/dist/quill-html-edit-button.min.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl)
                })

                // === [MODIFIKASI 3] LOGIKA JAVASCRIPT UNTUK MODAL HAPUS ===
                const deleteModal = document.getElementById('deleteModal');
                if (deleteModal) {
                    deleteModal.addEventListener('show.bs.modal', function(event) {
                        // Tombol yang memicu modal
                        const button = event.relatedTarget;
                        // Ambil link dari data-action
                        const actionUrl = button.getAttribute('data-action');
                        // Update action pada form di dalam modal
                        const modalForm = deleteModal.querySelector('#deleteForm');
                        modalForm.setAttribute('action', actionUrl);
                    });
                }
                // ==========================================================
            });

            let toastTimeout;

            function copyToClipboard(text) {
                navigator.clipboard.writeText(text).then(function() {
                    var x = document.getElementById("copy-toast");
                    x.querySelector('span').innerText = text + " disalin!";
                    x.className = "show";
                    if (toastTimeout) clearTimeout(toastTimeout);
                    toastTimeout = setTimeout(() => x.className = x.className.replace("show", ""), 3000);
                }, function(err) {
                    alert('Gagal menyalin: ' + err);
                });
            }

            document.addEventListener("DOMContentLoaded", () => {
                const editorDiv = document.querySelector('#editor_konten_quill');
                const hiddenInput = document.querySelector('#template_isi_hidden');
                const paperSelect = document.getElementById('paperSizeSelect');
                const guideToggle = document.getElementById('toggleCenterGuide');

                // 1. INIT PARCHMENT & REGISTERS
                const Size = Quill.import('attributors/style/size');
                Size.whitelist = ['10px', '11px', '12px', '13px', '14px', '16px', '18px', '20px', '24px', '36px'];
                Quill.register(Size, true);
                const Font = Quill.import('attributors/style/font');
                Font.whitelist = ['Times New Roman', 'Arial', 'Courier New', 'Calibri', 'Verdana'];
                Quill.register(Font, true);

                // --- HELPER: RESET LEBAR (Untuk Kolom Baru) ---
                const resetTableColumnWidths = (quillInstance) => {
                    setTimeout(() => {
                        const editor = quillInstance.root;
                        const tables = editor.querySelectorAll('table');
                        tables.forEach(table => {
                            if (table.style.tableLayout !== 'fixed') {
                                table.style.tableLayout = 'fixed';
                                table.style.width = '100%';
                            }
                            const row = table.rows[0];
                            if (row) {
                                const percentage = 100 / row.cells.length;
                                Array.from(row.cells).forEach(cell => cell.style.width =
                                    percentage + '%');
                            }
                        });
                    }, 10);
                };

                // 2. CONFIG
                const toolbarOptions = [
                    [{
                        'header': [1, 2, 3, 4, 5, 6, false]
                    }],
                    [{
                        'font': Font.whitelist
                    }, {
                        'size': Size.whitelist
                    }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{
                        'color': []
                    }, {
                        'background': []
                    }],
                    [{
                        'list': 'ordered'
                    }, {
                        'list': 'bullet'
                    }],
                    [{
                        'align': []
                    }],
                    ['link', 'image', 'code-block'],
                    ['table_add'],
                    ['table_row_up', 'table_row_down', 'table_row_del'],
                    ['table_col_left', 'table_col_right', 'table_col_del'],
                    ['table_delete'],
                    ['clean', 'htmlEditButton']
                ];

                // 3. ICON BUILDER
                const createIcon = (svg, color = 'currentColor') =>
                    `<svg viewBox="0 0 24 24" fill="none" stroke="${color}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">${svg}</svg>`;

                // 4. LOAD DATA (RAW HTML) BEFORE QUILL INIT
                // Ini mencegah Quill menghapus style width saat inisialisasi
                if (hiddenInput.value.trim()) {
                    editorDiv.innerHTML = hiddenInput.value.trim();
                }

                // 5. INIT QUILL
                const quill = new Quill(editorDiv, {
                    theme: 'snow',
                    modules: {
                        table: true,
                        htmlEditButton: {
                            msg: "Edit HTML",
                            okText: "Simpan",
                            cancelText: "Batal",
                            buttonHTML: "&lt;&gt;"
                        },
                        toolbar: {
                            container: toolbarOptions,
                            handlers: {
                                'table_add': function() {
                                    this.quill.getModule('table').insertTable(3, 3);
                                    resetTableColumnWidths(this.quill);
                                },
                                'table_row_up': function() {
                                    this.quill.getModule('table').insertRowAbove();
                                },
                                'table_row_down': function() {
                                    this.quill.getModule('table').insertRowBelow();
                                },
                                'table_row_del': function() {
                                    this.quill.getModule('table').deleteRow();
                                },
                                'table_col_left': function() {
                                    this.quill.getModule('table').insertColumnLeft();
                                    resetTableColumnWidths(this.quill);
                                },
                                'table_col_right': function() {
                                    this.quill.getModule('table').insertColumnRight();
                                    resetTableColumnWidths(this.quill);
                                },
                                'table_col_del': function() {
                                    this.quill.getModule('table').deleteColumn();
                                    resetTableColumnWidths(this.quill);
                                },
                                'table_delete': function() {
                                    this.quill.getModule('table').deleteTable();
                                }
                            }
                        }
                    }
                });

                // === SOLUSI UTAMA: RESTORE WIDTHS SETELAH QUILL LOADING ===
                // Quill mungkin tetap membersihkan style width saat render pertama.
                // Kita akan membaca ulang data asli dan memaksa style width kembali ke DOM Quill.
                setTimeout(() => {
                    const originalHTML = hiddenInput.value.trim();
                    if (originalHTML) {
                        // Parse HTML asli di memori (virtual)
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(originalHTML, 'text/html');
                        const savedTables = doc.querySelectorAll('table');
                        const editorTables = editorDiv.querySelectorAll('table');

                        editorTables.forEach((table, index) => {
                            if (savedTables[index]) {
                                const savedRow = savedTables[index].querySelector('tr');
                                const editorRow = table.querySelector('tr');

                                if (savedRow && editorRow) {
                                    // Paksa style table fixed agar width sel bekerja
                                    table.style.tableLayout = 'fixed';
                                    table.style.width = '100%';

                                    Array.from(savedRow.cells).forEach((savedCell, cellIndex) => {
                                        if (savedCell.style.width && editorRow.cells[
                                                cellIndex]) {
                                            // Kembalikan width ke cell di editor
                                            editorRow.cells[cellIndex].style.width = savedCell
                                                .style.width;
                                        }
                                    });
                                }
                            }
                        });
                    }
                }, 100); // Delay sedikit untuk memastikan Quill selesai render

                // Pindahkan Toolbar
                const toolbarElement = editorDiv.previousSibling;
                if (toolbarElement && toolbarElement.classList.contains('ql-toolbar')) {
                    document.getElementById('toolbar-container').appendChild(toolbarElement);
                }

                // Setup Icons (sama seperti sebelumnya)
                const btnAdd = document.querySelector('.ql-table_add');
                if (btnAdd) {
                    btnAdd.innerHTML = createIcon(
                        '<rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="3" y1="15" x2="21" y2="15"></line><line x1="9" y1="3" x2="9" y2="21"></line><line x1="15" y1="3" x2="15" y2="21"></line>'
                    );
                    btnAdd.title = "Buat Tabel Baru";
                }
                const btnRowUp = document.querySelector('.ql-table_row_up');
                if (btnRowUp) {
                    btnRowUp.innerHTML = createIcon(
                        '<path d="M5 12h14"/><path d="M12 5v14"/><path d="M12 5l-4 4"/><path d="M12 5l4 4"/>');
                    btnRowUp.title = "Tambah Baris Atas";
                }
                const btnRowDown = document.querySelector('.ql-table_row_down');
                if (btnRowDown) {
                    btnRowDown.innerHTML = createIcon(
                        '<path d="M5 12h14"/><path d="M12 5v14"/><path d="M12 19l-4-4"/><path d="M12 19l4-4"/>');
                    btnRowDown.title = "Tambah Baris Bawah";
                }
                const btnRowDel = document.querySelector('.ql-table_row_del');
                if (btnRowDel) {
                    btnRowDel.innerHTML = createIcon('<line x1="5" y1="12" x2="19" y2="12"></line>', 'red');
                    btnRowDel.title = "Hapus Baris";
                }
                const btnColLeft = document.querySelector('.ql-table_col_left');
                if (btnColLeft) {
                    btnColLeft.innerHTML = createIcon(
                        '<path d="M12 5v14"/><path d="M5 12h14"/><path d="M5 12l4-4"/><path d="M5 12l4 4"/>');
                    btnColLeft.title = "Tambah Kolom Kiri";
                }
                const btnColRight = document.querySelector('.ql-table_col_right');
                if (btnColRight) {
                    btnColRight.innerHTML = createIcon(
                        '<path d="M12 5v14"/><path d="M5 12h14"/><path d="M19 12l-4-4"/><path d="M19 12l4 4"/>');
                    btnColRight.title = "Tambah Kolom Kanan";
                }
                const btnColDel = document.querySelector('.ql-table_col_del');
                if (btnColDel) {
                    btnColDel.innerHTML = createIcon('<line x1="12" y1="5" x2="12" y2="19"></line>', 'red');
                    btnColDel.title = "Hapus Kolom";
                }
                const btnDelTable = document.querySelector('.ql-table_delete');
                if (btnDelTable) {
                    btnDelTable.innerHTML = createIcon(
                        '<polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>',
                        'red');
                    btnDelTable.title = "Hapus Tabel";
                }

                // Logic Tombol Tabel
                const tableControlsGroups = document.querySelectorAll(
                    '.ql-formats:has(.ql-table_row_up), .ql-formats:has(.ql-table_col_left), .ql-formats:has(.ql-table_delete)'
                );
                tableControlsGroups.forEach(group => {
                    group.classList.add('table-controls-group');
                    group.style.display = 'none';
                });
                quill.on('selection-change', (range) => {
                    if (range) {
                        const formats = quill.getFormat(range);
                        const isInTable = formats['table-cell'] !== undefined || formats['table'] !== undefined;
                        tableControlsGroups.forEach(group => group.style.display = isInTable ? 'flex' : 'none');
                    }
                });

                function updateEditorVisuals() {
                    const editorElement = editorDiv.querySelector('.ql-editor');
                    if (!editorElement) return;
                    editorElement.className = 'ql-editor';
                    editorElement.classList.add('paper-' + paperSelect.value);
                    editorElement.style.lineHeight = '1.5';
                    if (guideToggle.checked) editorElement.classList.add('show-guide');
                }
                paperSelect.addEventListener('change', updateEditorVisuals);
                guideToggle.addEventListener('change', updateEditorVisuals);
                setTimeout(updateEditorVisuals, 100);

                // ============================================================
                // 7. FITUR RESIZE (LOGIKA DRAG)
                // ============================================================
                const editorContent = editorDiv.querySelector('.ql-editor');
                let isResizing = false;
                let currentCell = null;
                let nextCell = null;
                let startX = 0;
                let startWidth = 0;
                let nextStartWidth = 0;

                const getTargetCell = (e) => {
                    const cell = e.target.closest('td');
                    if (!cell) return null;
                    const rect = cell.getBoundingClientRect();
                    const offsetX = e.clientX - rect.left;
                    if (rect.width - offsetX < 10) return cell;
                    return null;
                };

                editorContent.addEventListener('mousemove', function(e) {
                    if (isResizing && currentCell && nextCell) {
                        e.preventDefault();
                        const diff = e.pageX - startX;
                        const newCurrentWidth = startWidth + diff;
                        const newNextWidth = nextStartWidth - diff;
                        if (newCurrentWidth > 30 && newNextWidth > 30) {
                            currentCell.style.width = newCurrentWidth + 'px';
                            nextCell.style.width = newNextWidth + 'px';
                        }
                        return;
                    }
                    const target = getTargetCell(e);
                    if (target && target.nextElementSibling) {
                        target.style.cursor = 'col-resize';
                        editorContent.style.cursor = 'col-resize';
                    } else {
                        const cell = e.target.closest('td');
                        if (cell) cell.style.cursor = 'text';
                        else editorContent.style.cursor = 'text';
                    }
                });

                editorContent.addEventListener('mousedown', function(e) {
                    const target = getTargetCell(e);
                    if (target && target.nextElementSibling) {
                        isResizing = true;
                        currentCell = target;
                        nextCell = target.nextElementSibling;
                        startX = e.pageX;
                        startWidth = currentCell.offsetWidth;
                        nextStartWidth = nextCell.offsetWidth;
                        document.body.classList.add('resizing-cursor');
                        e.preventDefault();
                    }
                });

                document.addEventListener('mouseup', function() {
                    if (isResizing) {
                        isResizing = false;
                        currentCell = null;
                        nextCell = null;
                        document.body.classList.remove('resizing-cursor');
                        editorContent.style.cursor = 'text';
                    }
                });

                // Observer: Hanya untuk tabel BARU yang belum punya width
                const observer = new MutationObserver(function(mutations) {
                    const tables = editorContent.querySelectorAll('table');
                    tables.forEach(tbl => {
                        if (tbl.style.tableLayout !== 'fixed') {
                            tbl.style.tableLayout = 'fixed';
                            tbl.style.width = '100%';
                        }
                        const rowOne = tbl.rows[0];
                        if (!rowOne) return;

                        // Cek apakah ada width (dari restore function di atas)
                        let hasDefinedWidth = false;
                        for (let i = 0; i < rowOne.cells.length; i++) {
                            if (rowOne.cells[i].style.width && rowOne.cells[i].style.width !== "") {
                                hasDefinedWidth = true;
                                break;
                            }
                        }
                        // Jika benar-benar kosong (tabel baru dibuat), baru reset rata
                        if (!hasDefinedWidth) {
                            const pct = 100 / rowOne.cells.length;
                            Array.from(rowOne.cells).forEach(c => c.style.width = pct + '%');
                        }
                    });
                });

                // Nyalakan observer setelah restore function berjalan
                setTimeout(() => {
                    observer.observe(editorContent, {
                        childList: true,
                        subtree: true
                    });
                }, 500);

                // Submit Form
                document.getElementById('formTemplate').addEventListener('submit', function() {
                    let htmlContent = editorDiv.querySelector('.ql-editor').innerHTML;
                    htmlContent = htmlContent.replace(/ {2,}/g, match => '&nbsp;'.repeat(match.length));
                    hiddenInput.value = htmlContent;
                });
            });
        </script>
    @endpush
@endsection
