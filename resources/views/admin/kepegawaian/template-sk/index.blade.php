@extends('layouts.admin')

@section('content')
    {{-- === CSS LIBRARY (Quill v2.0.2) === --}}
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/quill-html-edit-button@2.2.11/dist/quill-html-edit-button.min.css"
        rel="stylesheet">

    <style>
        /* =========================================
               1. LOGIKA FONT UTAMA (SINKRON DENGAN PREVIEW)
               ========================================= */

        /* Font Utama Editor (Default: Times New Roman) */
        .ql-editor {
            font-family: 'Times New Roman', Times, serif !important;
            font-size: 12px;
            color: #000;
            line-height: 1.42;
            padding: 2.54cm !important;
            /* Default Padding */
            background-color: white !important;
            height: 297mm;
            /* Default A4 Height */
            overflow-y: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        /* RESET QUILL SNOW */
        .ql-editor h1,
        .ql-editor h2,
        .ql-editor h3,
        .ql-editor p,
        .ql-editor li,
        .ql-editor div {
            font-family: unset;
        }

        /* Helper Classes untuk Dropdown (Preview di Toolbar) */
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

        .ql-font-tahoma {
            font-family: Tahoma, Geneva, sans-serif;
        }

        .ql-font-georgia {
            font-family: Georgia, serif;
        }

        /* =========================================
               2. KUSTOMISASI DROPDOWN TOOLBAR
               ========================================= */
        .ql-snow .ql-picker.ql-font {
            width: 170px !important;
        }

        .ql-snow .ql-picker.ql-size {
            width: 100px !important;
        }

        /* --- PERBAIKAN: MENAMPILKAN ANGKA PADA SIZE --- */
        /* Memaksa label Font DAN Size untuk mengambil teks dari atribut data-value */
        .ql-snow .ql-picker.ql-font .ql-picker-label::before,
        .ql-snow .ql-picker.ql-font .ql-picker-item::before,
        .ql-snow .ql-picker.ql-size .ql-picker-label::before,
        .ql-snow .ql-picker.ql-size .ql-picker-item::before {
            content: attr(data-value) !important;
        }

        /* Styling Item Dropdown */
        .ql-picker.ql-font .ql-picker-item[data-value="Times New Roman"]::before {
            font-family: 'Times New Roman';
            content: 'Times New Roman' !important;
        }

        .ql-picker.ql-font .ql-picker-item[data-value="Arial"]::before {
            font-family: Arial;
            content: 'Arial' !important;
        }

        .ql-picker.ql-font .ql-picker-item[data-value="Courier New"]::before {
            font-family: 'Courier New';
            content: 'Courier New' !important;
        }

        .ql-picker.ql-font .ql-picker-item[data-value="Calibri"]::before {
            font-family: 'Calibri';
            content: 'Calibri' !important;
        }

        .ql-picker.ql-font .ql-picker-item[data-value="Verdana"]::before {
            font-family: Verdana;
            content: 'Verdana' !important;
        }

        .ql-picker.ql-font .ql-picker-item[data-value="Tahoma"]::before {
            font-family: Tahoma;
            content: 'Tahoma' !important;
        }

        .ql-picker.ql-font .ql-picker-item[data-value="Georgia"]::before {
            font-family: Georgia;
            content: 'Georgia' !important;
        }

        /* Default Label Font */
        .ql-snow .ql-picker.ql-font .ql-picker-label:not([data-value])::before {
            content: 'Times New Roman' !important;
            font-family: 'Times New Roman';
        }

        /* Default Label Size (Agar tidak kosong saat load) */
        .ql-snow .ql-picker.ql-size .ql-picker-label:not([data-value])::before {
            content: '12px' !important;
        }

        /* =========================================
               3. UI & LAYOUT EDITOR
               ========================================= */
        #editor-wrapper-bg {
            background-color: #e0e0e0ff;
            padding: 40px 0;
            border: 1px solid #ddd;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 40px;
            overflow-x: auto;
            width: 100%;
            min-height: 600px;
        }

        .page-instance {
            position: relative;
            display: flex;
            flex-direction: column;
            width: 210mm;
            transition: width 0.3s;
            height: fit-content;
        }

        .page-header-info {
            position: absolute;
            top: -30px;
            left: 0;
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 2px;
            pointer-events: none;
        }

        .page-label {
            font-weight: bold;
            font-size: 11px;
            color: #000000ff;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-delete-page {
            pointer-events: auto;
            background-color: #ff3e1d;
            color: white;
            border: none;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 11px;
            cursor: pointer;
        }

        .page-toolbar-container {
            position: sticky;
            top: 0;
            z-index: 50;
            background: #f8f9fa;
            border-bottom: 1px solid #ccc;
        }

        .ql-toolbar.ql-snow {
            border: none !important;
            display: flex !important;
            flex-flow: row wrap !important;
            gap: 2px;
            padding: 5px !important;
        }

        .ql-toolbar.ql-snow button {
            width: 28px !important;
            height: 28px !important;
        }

        /* KOP SURAT */
        .page-instance.has-kop:first-child .ql-editor {
            padding: 5.54cm 2.54cm 2.54cm 2.54cm !important;
        }

        .page-instance:not(:first-child) .ql-editor {
            padding: 2.54cm !important;
        }

        /* Chips */
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
        }

        /* Paper Sizes */
        .page-instance.paper-A4 {
            width: 210mm;
        }

        .page-instance.paper-A4 .ql-editor {
            height: 297mm;
        }

        .page-instance.paper-F4 {
            width: 215mm;
        }

        .page-instance.paper-F4 .ql-editor {
            height: 330mm;
        }

        .page-instance.paper-Legal {
            width: 216mm;
        }

        .page-instance.paper-Legal .ql-editor {
            height: 356mm;
        }

        .page-instance.paper-Letter {
            width: 216mm;
        }

        .page-instance.paper-Letter .ql-editor {
            height: 279mm;
        }

        /* Table */
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
            word-wrap: break-word;
        }

        .ql-editor td:hover::after {
            content: '';
            position: absolute;
            right: -4px;
            top: 0;
            bottom: 0;
            width: 8px;
            cursor: col-resize;
            z-index: 10;
        }

        .table-controls-group {
            display: none;
            background-color: #e8f0fe;
            border-radius: 4px;
            margin-left: 5px;
        }

        .resizing-cursor {
            cursor: col-resize !important;
            user-select: none;
        }

        /* Copy Toast */
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
        }

        #copy-toast.show {
            visibility: visible;
            transform: translateX(-50%) translateY(0);
            opacity: 1;
        }
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Administrasi /</span> Template Surat SK</h4>

        @php
            // VARIABEL HANYA UNTUK SK
            $variables = [
                ['code' => '{{ nama }}', 'desc' => 'Nama Penerima'],
                ['code' => '{{ jabatan }}', 'desc' => 'Jabatan'],
                ['code' => '{{ nip }}', 'desc' => 'NIP/Niy'],
                ['code' => '{{ tanggal }}', 'desc' => 'Tanggal Surat'],
                ['code' => '{{ nomor_surat }}', 'desc' => 'Nomor Surat'],
                ['code' => '{{ perihal }}', 'desc' => 'Perihal'],
                ['code' => '{{ alamat }}', 'desc' => 'Alamat'],
            ];
        @endphp

        <div class="row">
            <div class="col-md-9">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 fw-bold text-primary">
                            {{ isset($template) ? 'Edit Template SK' : 'Buat Template SK Baru' }}</h5>
                    </div>
                    <div class="card-body mt-3">
                        {{-- FORM START --}}
                        <form
                            action="{{ isset($template) ? route('admin.kepegawaian.TemplateSk.update', $template->id) : route('admin.kepegawaian.TemplateSk.store') }}"
                            method="POST" id="formTemplate">
                            @csrf
                            @if (isset($template))
                                @method('PUT')
                            @endif

                            <div class="row g-3 mb-4">
                                <div class="col-md-7">
                                    <label class="form-label fw-bold">Judul Surat</label>
                                    <input type="text" name="judul_surat" class="form-control"
                                        placeholder="Contoh: SK Pengangkatan"
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

                                <div class="col-md-2 d-flex flex-column justify-content-end gap-2">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="use_kop" id="toggleKop"
                                            value="1"
                                            {{ old('use_kop', $template->use_kop ?? 0) == 1 ? 'checked' : '' }}
                                            style="cursor:pointer;">
                                        <label class="form-check-label small text-muted" for="toggleKop"
                                            style="cursor:pointer;">Jarak Kop 3cm</label>
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
                                <div id="editor-wrapper-bg"></div>
                                <textarea name="template_isi" id="template_isi_hidden" style="display:none;">{{ old('template_isi', $template->template_isi ?? '') }}</textarea>
                            </div>

                            <div class="d-flex gap-2 pt-2 border-top">
                                <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i> Simpan
                                    Template</button>
                                <a href="{{ route('admin.kepegawaian.TemplateSk.index') }}"
                                    class="btn btn-outline-secondary">Batal / Kembali</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- SIDEBAR LIST TEMPLATE SK --}}
            <div class="col-md-3">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 fw-bold text-secondary">Daftar Template SK</h6>
                    </div>
                    <ul class="list-group list-group-flush">
                        @foreach ($templates as $t)
                            <li class="list-group-item list-group-item-action py-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="me-2">
                                        <h6 class="mb-1 text-truncate" style="max-width: 130px;">{{ $t->judul_surat }}
                                        </h6>
                                        <small class="badge bg-label-secondary">{{ $t->ukuran_kertas }}</small>
                                    </div>

                                    {{-- === TOMBOL DIPERBAIKI (BUTTON GROUP) === --}}
                                    <div class="btn-group btn-group-sm shadow-sm" role="group" aria-label="Aksi">
                                        <a href="{{ route('admin.kepegawaian.TemplateSk.edit', $t->id) }}"
                                            class="btn btn-primary" title="Edit">
                                            <i class="bx bx-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                            data-bs-target="#deleteModal"
                                            data-action="{{ route('admin.kepegawaian.TemplateSk.destroy', $t->id) }}"
                                            title="Hapus">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </div>
                                    {{-- ======================================= --}}

                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- MODALS --}}
    <div class="modal fade" id="variableModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white">Daftar Variabel SK</h5>
                    <button type="button" class="btn-close btn-white" data-bs-dismiss="modal"></button>
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

    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5><button type="button" class="btn-close"
                        data-bs-dismiss="modal"></button>
                </div>
                <form id="deleteForm" method="POST" action="">@csrf @method('DELETE')
                    <div class="modal-body">
                        <p class="mb-0">Yakin hapus template ini?</p><small class="text-danger">Tak bisa
                            dibatalkan.</small>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-danger">Ya,
                            Hapus</button></div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deletePageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Hapus Halaman?</h5><button type="button" class="btn-close"
                        data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">Hapus halaman ini beserta isinya?</p>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-outline-secondary"
                        data-bs-dismiss="modal">Batal</button><button type="button" class="btn btn-danger"
                        id="confirmDeletePageBtn">Ya, Hapus</button></div>
            </div>
        </div>
    </div>

    <div id="copy-toast"><i class='bx bx-check-circle'></i> <span>Variabel berhasil disalin!</span></div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/quill-html-edit-button@2.2.11/dist/quill-html-edit-button.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // 1. REGISTER FONT WHITELIST & SIZE
                const Size = Quill.import('attributors/style/size');
                Size.whitelist = ['10px', '11px', '12px', '13px', '14px', '16px', '18px', '20px', '24px', '36px'];
                Quill.register(Size, true);

                const Font = Quill.import('attributors/style/font');
                // MENAMBAHKAN FONT BARU KE WHITELIST
                Font.whitelist = ['Times New Roman', 'Arial', 'Courier New', 'Calibri', 'Verdana', 'Tahoma', 'Georgia'];
                Quill.register(Font, true);

                // Register Table Cell Custom
                try {
                    const TableCell = Quill.import('formats/table-cell');
                    class CustomTableCell extends TableCell {
                        static formats(domNode) {
                            const formats = super.formats(domNode) || {};
                            if (domNode.style.width) formats['width'] = domNode.style.width;
                            else if (domNode.getAttribute('width')) formats['width'] = domNode.getAttribute(
                                'width');
                            return formats;
                        }
                        format(name, value) {
                            if (name === 'width') {
                                if (value) {
                                    this.domNode.style.width = value;
                                    this.domNode.setAttribute('width', value);
                                } else {
                                    this.domNode.style.width = '';
                                    this.domNode.removeAttribute('width');
                                }
                            } else {
                                super.format(name, value);
                            }
                        }
                    }
                    Quill.register('formats/table-cell', CustomTableCell, true);
                } catch (e) {
                    console.error("Gagal register TableCell", e);
                }

                // 2. ICONS
                const icons = Quill.import('ui/icons');
                const iconsMap = {
                    'ql-table_add': [
                        '<rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="3" y1="15" x2="21" y2="15"></line><line x1="9" y1="3" x2="9" y2="21"></line><line x1="15" y1="3" x2="15" y2="21"></line>',
                        'Buat Tabel'
                    ],
                    'ql-table_row_up': [
                        '<path d="M5 12h14"/><path d="M12 5v14"/><path d="M12 5l-4 4"/><path d="M12 5l4 4"/>',
                        'Baris Atas'
                    ],
                    'ql-table_row_down': [
                        '<path d="M5 12h14"/><path d="M12 5v14"/><path d="M12 19l-4-4"/><path d="M12 19l4-4"/>',
                        'Baris Bawah'
                    ],
                    'ql-table_row_del': ['<line x1="5" y1="12" x2="19" y2="12"></line>', 'Hapus Baris', 'red'],
                    'ql-table_col_left': [
                        '<path d="M12 5v14"/><path d="M5 12h14"/><path d="M5 12l4-4"/><path d="M5 12l4 4"/>',
                        'Kolom Kiri'
                    ],
                    'ql-table_col_right': [
                        '<path d="M12 5v14"/><path d="M5 12h14"/><path d="M19 12l-4-4"/><path d="M19 12l4 4"/>',
                        'Kolom Kanan'
                    ],
                    'ql-table_col_del': ['<line x1="12" y1="5" x2="12" y2="19"></line>', 'Hapus Kolom', 'red'],
                    'ql-table_delete': [
                        '<polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>',
                        'Hapus Tabel', 'red'
                    ]
                };
                const createIcon = (svg, color = 'currentColor') =>
                    `<svg viewBox="0 0 24 24" fill="none" stroke="${color}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">${svg}</svg>`;
                Object.entries(iconsMap).forEach(([key, val]) => {
                    icons[key.replace('ql-', '')] = createIcon(val[0], val[2] || 'currentColor');
                });

                // 3. EDITOR INIT
                let quillInstances = [];
                const pageDivider = '<div class="page-break-divider"></div>';
                const editorWrapperBg = document.getElementById('editor-wrapper-bg');
                const hiddenInput = document.getElementById('template_isi_hidden');
                const paperSelect = document.getElementById('paperSizeSelect');
                const kopToggle = document.getElementById('toggleKop');

                function updateKopStatus() {
                    const firstPage = document.querySelector('.page-instance:first-child');
                    if (firstPage) {
                        if (kopToggle.checked) firstPage.classList.add('has-kop');
                        else firstPage.classList.remove('has-kop');
                    }
                }

                function restoreTableWidths(quillInstance, rawHTML) {
                    if (!rawHTML) return;
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(rawHTML, 'text/html');
                    const sourceTables = doc.querySelectorAll('table');
                    const editorTables = quillInstance.root.querySelectorAll('table');
                    editorTables.forEach((table, index) => {
                        if (sourceTables[index]) {
                            table.style.tableLayout = 'fixed';
                            table.style.width = '100%';
                            const sourceRow = sourceTables[index].querySelector('tr');
                            const targetRow = table.querySelector('tr');
                            if (sourceRow && targetRow) {
                                Array.from(sourceRow.cells).forEach((sourceCell, i) => {
                                    const w = sourceCell.style.width || sourceCell.getAttribute(
                                        'width');
                                    if (w && targetRow.cells[i]) {
                                        targetRow.cells[i].style.width = w;
                                        const blot = Quill.find(targetRow.cells[i]);
                                        if (blot) blot.format('width', w);
                                    }
                                });
                            }
                        }
                    });
                }

                function debounce(func, wait) {
                    let timeout;
                    return function(...args) {
                        const context = this;
                        clearTimeout(timeout);
                        timeout = setTimeout(() => func.apply(context, args), wait);
                    };
                }

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
                    ['table_row_up', 'table_row_down', 'table_row_del', 'table_col_left', 'table_col_right',
                        'table_col_del', 'table_delete'
                    ],
                    ['clean', 'htmlEditButton']
                ];

                function createPage(content = '') {
                    const pageIndex = quillInstances.length + 1;
                    const pageWrapper = document.createElement('div');
                    pageWrapper.className = `page-instance paper-${paperSelect.value}`;
                    pageWrapper.id = `page-${pageIndex}`;
                    if (pageIndex === 1 && kopToggle.checked) pageWrapper.classList.add('has-kop');

                    let deleteBtn = (pageIndex > 1 || content.trim() !== '') ?
                        `<button type="button" class="btn-delete-page" onclick="requestDeletePage(this)"><i class='bx bx-trash'></i> Hapus Halaman</button>` :
                        '';

                    pageWrapper.innerHTML = `
                        <div class="page-header-info"><span class="page-label">HALAMAN ${pageIndex}</span>${deleteBtn}</div>
                        <div class="page-toolbar-container"></div>
                        <div class="editor-container"></div>
                    `;
                    editorWrapperBg.appendChild(pageWrapper);

                    const quill = new Quill(pageWrapper.querySelector('.editor-container'), {
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
                                    },
                                    'table_col_right': function() {
                                        this.quill.getModule('table').insertColumnRight();
                                    },
                                    'table_col_del': function() {
                                        this.quill.getModule('table').deleteColumn();
                                    },
                                    'table_delete': function() {
                                        this.quill.getModule('table').deleteTable();
                                    }
                                }
                            }
                        }
                    });

                    // Match TD Width on Paste
                    quill.clipboard.addMatcher('TD', function(node, delta) {
                        const width = node.style.width || node.getAttribute('width');
                        if (width) {
                            delta.forEach(op => {
                                op.attributes = op.attributes || {};
                                op.attributes.width = width;
                            });
                        }
                        return delta;
                    });

                    // Setup Toolbar & Icons
                    const toolbarContainer = pageWrapper.querySelector('.page-toolbar-container');
                    const generatedToolbar = pageWrapper.querySelector('.ql-toolbar');
                    if (generatedToolbar) {
                        toolbarContainer.appendChild(generatedToolbar);
                        setupTableIcons(generatedToolbar, quill);
                    }

                    initTableResizer(pageWrapper.querySelector('.ql-editor'), quill);

                    if (content && content.trim() !== '') {
                        quill.clipboard.dangerouslyPasteHTML(0, content);
                        setTimeout(() => {
                            restoreTableWidths(quill, content);
                        }, 50);
                    }

                    // Table Layout Observer
                    const observer = new MutationObserver(function(mutations) {
                        const tables = quill.root.querySelectorAll('table');
                        tables.forEach(tbl => {
                            if (tbl.style.tableLayout !== 'fixed') {
                                tbl.style.tableLayout = 'fixed';
                                tbl.style.width = '100%';
                            }
                            const rowOne = tbl.rows[0];
                            if (rowOne && !rowOne.cells[0].style.width) {
                                const pct = 100 / rowOne.cells.length;
                                Array.from(rowOne.cells).forEach(c => c.style.width = pct + '%');
                            }
                        });
                    });
                    observer.observe(quill.root, {
                        childList: true,
                        subtree: true
                    });

                    quill.on('text-change', debounce((delta, oldDelta, source) => {
                        if (source === 'user') handlePagination(quill);
                    }, 250));

                    quillInstances.push(quill);
                    return quill;
                }

                function setupTableIcons(toolbarEl, quillInstance) {
                    Object.entries(iconsMap).forEach(([cls, val]) => {
                        const btn = toolbarEl.querySelector('.' + cls);
                        if (btn) btn.title = val[1];
                    });
                    const tableControls = toolbarEl.querySelectorAll(
                        '.ql-formats:has(.ql-table_row_up), .ql-formats:has(.ql-table_col_left), .ql-formats:has(.ql-table_delete)'
                    );
                    tableControls.forEach(g => {
                        g.classList.add('table-controls-group');
                        g.style.display = 'none';
                    });
                    quillInstance.on('selection-change', (range) => {
                        if (range) {
                            const formats = quillInstance.getFormat(range);
                            const isInTable = formats['table-cell'] || formats['table'] || formats['width'];
                            tableControls.forEach(g => g.style.display = isInTable ? 'flex' : 'none');
                        }
                    });
                }

                function initTableResizer(editorContent, quillInstance) {
                    let isResizing = false,
                        currentCell = null,
                        nextCell = null,
                        startX = 0,
                        startWidth = 0,
                        nextStartWidth = 0;
                    editorContent.addEventListener('mousedown', function(e) {
                        const cell = e.target.closest('td');
                        if (!cell) return;
                        const rect = cell.getBoundingClientRect();
                        if (e.clientX - rect.left > rect.width - 10 && cell.nextElementSibling) {
                            isResizing = true;
                            currentCell = cell;
                            nextCell = cell.nextElementSibling;
                            startX = e.pageX;
                            startWidth = currentCell.offsetWidth;
                            nextStartWidth = nextCell.offsetWidth;
                            document.body.classList.add('resizing-cursor');
                            e.preventDefault();
                        }
                    });
                    editorContent.addEventListener('mousemove', function(e) {
                        if (isResizing && currentCell && nextCell) {
                            const diff = e.pageX - startX;
                            if (currentCell.offsetWidth + diff > 30 && nextCell.offsetWidth - diff > 30) {
                                currentCell.style.width = (startWidth + diff) + 'px';
                                nextCell.style.width = (nextStartWidth - diff) + 'px';
                            }
                        } else {
                            const cell = e.target.closest('td');
                            if (cell) {
                                const rect = cell.getBoundingClientRect();
                                if (e.clientX - rect.left > rect.width - 10 && cell.nextElementSibling) cell
                                    .style.cursor = 'col-resize';
                                else cell.style.cursor = 'text';
                            }
                        }
                    });
                    document.addEventListener('mouseup', function() {
                        if (isResizing && currentCell && nextCell) {
                            const currentBlot = Quill.find(currentCell);
                            const nextBlot = Quill.find(nextCell);
                            if (currentBlot) currentBlot.format('width', currentCell.style.width);
                            if (nextBlot) nextBlot.format('width', nextCell.style.width);
                            isResizing = false;
                            document.body.classList.remove('resizing-cursor');
                        }
                    });
                }

                function handlePagination(quillInstance) {
                    const editorRoot = quillInstance.root;
                    quillInstances = quillInstances.filter(q => document.body.contains(q.root));
                    let currentIndex = quillInstances.indexOf(quillInstance);
                    while (editorRoot.scrollHeight > editorRoot.clientHeight + 1) {
                        let nextQuill = quillInstances[currentIndex + 1];
                        if (!nextQuill) {
                            createPage();
                            nextQuill = quillInstances[quillInstances.length - 1];
                        }
                        const textContent = quillInstance.getText();
                        const length = quillInstance.getLength();
                        let splitIndex = textContent.lastIndexOf('\n', textContent.length - 2);
                        if (splitIndex < 0) splitIndex = length - 1;
                        else splitIndex += 1;
                        if (splitIndex <= 0) break;
                        const contentToMove = quillInstance.getContents(splitIndex);
                        quillInstance.deleteText(splitIndex, length - splitIndex);
                        const nextContent = nextQuill.getContents();
                        nextQuill.setContents(contentToMove.concat(nextContent), 'api');
                    }
                }

                // --- INIT MODAL PAGES ---
                const deletePageModalElement = document.getElementById('deletePageModal');
                const deletePageModal = new bootstrap.Modal(deletePageModalElement);
                const confirmDeletePageBtn = document.getElementById('confirmDeletePageBtn');
                let pageToDelete = null;

                window.requestDeletePage = function(btn) {
                    pageToDelete = btn.closest('.page-instance');
                    deletePageModal.show();
                };

                confirmDeletePageBtn.addEventListener('click', function() {
                    if (pageToDelete) {
                        const quillToRemove = quillInstances.find(q => pageToDelete.contains(q.root));
                        if (quillToRemove) quillInstances = quillInstances.filter(q => q !== quillToRemove);
                        pageToDelete.remove();
                        document.querySelectorAll('.page-instance .page-label').forEach((el, idx) => el
                            .innerText = `HALAMAN ${idx + 1}`);
                        deletePageModal.hide();
                        updateKopStatus();
                    }
                });

                // --- EXECUTE INITIAL LOAD ---
                let rawContent = hiddenInput.value;
                if (rawContent && rawContent.trim()) {
                    const pages = rawContent.split(pageDivider);
                    if (pages.length > 0) pages.forEach(p => {
                        if (p.trim()) createPage(p);
                    });
                    else createPage(rawContent);
                } else {
                    createPage();
                }
                updateKopStatus();

                // --- EVENT LISTENERS UI ---
                paperSelect.addEventListener('change', function() {
                    document.querySelectorAll('.page-instance').forEach(el => el.className =
                        `page-instance paper-${this.value}`);
                    updateKopStatus();
                });
                kopToggle.addEventListener('change', updateKopStatus);

                document.getElementById('formTemplate').addEventListener('submit', function() {
                    let combinedHTML = '';
                    document.querySelectorAll('.page-instance .ql-editor').forEach((editor, idx) => {
                        const editorContent = editor.innerHTML;
                        if (idx > 0) combinedHTML += pageDivider;
                        combinedHTML += editorContent.replace(/ {2,}/g, match => '&nbsp;'.repeat(match
                            .length));
                    });
                    hiddenInput.value = combinedHTML;
                });

                // Modal Hapus Template Logic
                const deleteModalTemplate = document.getElementById('deleteModal');
                if (deleteModalTemplate) {
                    deleteModalTemplate.addEventListener('show.bs.modal', function(event) {
                        const button = event.relatedTarget;
                        const actionUrl = button.getAttribute('data-action');
                        const form = deleteModalTemplate.querySelector('#deleteForm');
                        form.setAttribute('action', actionUrl);
                    });
                }
            });

            // Copy to Clipboard Utils
            let toastTimeout;

            function copyToClipboard(text) {
                navigator.clipboard.writeText(text).then(() => {
                    const toast = document.getElementById("copy-toast");
                    toast.querySelector('span').innerText = text + " disalin!";
                    toast.className = "show";
                    clearTimeout(toastTimeout);
                    toastTimeout = setTimeout(() => toast.className = "", 3000);
                });
            }
        </script>
    @endpush
@endsection
