@extends('layouts.admin')

@section('content')

    {{-- CSS & Library Quill --}}
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/quill-html-edit-button@2.2.11/dist/quill-html-edit-button.min.css" rel="stylesheet">

    <style>
        /* === Gaya Editor (Kertas A4 Look) === */
        .ql-editor {
            min-height: 450px;
            position: relative;
            z-index: 1;
            padding: 2cm 2cm !important;
            padding-top: calc(2cm + 55px) !important;
            border: 1px solid #ddd !important;
            background-color: white !important;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        }

        #editor-container {
            border: 1px solid #ddd;
            border-radius: 0.375rem;
            position: relative;
            background: #fff;
        }

        .ql-toolbar {
            position: absolute;
            width: 100%;
            top: 0;
            z-index: 10;
            background-color: #f8f9fa;
            border-bottom: 1px solid #ddd !important;
            border-top-left-radius: 0.375rem;
            border-top-right-radius: 0.375rem;
            border: none;
        }

        #editor-container .ql-container.ql-snow { border: none; }

        /* Garis Tengah Guide */
        .ql-editor.show-guide::after {
            content: "";
            position: absolute;
            top: 2cm; bottom: 0; left: 50%; width: 1px;
            background-image: linear-gradient(to bottom, rgba(255, 0, 0, 0.3) 5px, transparent 5px);
            background-size: 1px 10px;
            background-repeat: repeat-y;
            transform: translateX(-50%);
            pointer-events: none;
            z-index: 999;
            display: block;
        }

        /* === Variable Chips Styling === */
        .var-chip {
            cursor: pointer;
            transition: all 0.2s;
            user-select: none;
            font-size: 0.8rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            border: 1px solid #e0e0e0;
        }
        .var-chip:hover {
            background-color: #e7e7ff !important;
            color: #696cff !important;
            border-color: #696cff;
            transform: translateY(-1px);
        }
        .var-chip:active { transform: translateY(0); }
        .var-chip i { font-size: 14px; }

        /* Toast Copy Notification */
        #copy-toast {
            visibility: hidden;
            min-width: 250px;
            background-color: #333;
            color: #fff;
            text-align: center;
            border-radius: 4px;
            padding: 10px;
            position: fixed;
            z-index: 9999;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 14px;
        }
        #copy-toast.show { visibility: visible; -webkit-animation: fadein 0.5s, fadeout 0.5s 2.5s; animation: fadein 0.5s, fadeout 0.5s 2.5s; }
        
        @keyframes fadein { from {bottom: 0; opacity: 0;} to {bottom: 30px; opacity: 1;} }
        @keyframes fadeout { from {bottom: 30px; opacity: 1;} to {bottom: 0; opacity: 0;} }
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Administrasi /</span> Template Surat</h4>

        {{-- =======================================================================
             ⭐ PHP LOGIC: DEFINE VARIABLE (TANPA @)
             ======================================================================= --}}
        @php
            $kategoriAktif = request()->kategori ?? 'siswa';
            
            // Definisikan Variabel sesuai Kategori (TANPA @)
            if ($kategoriAktif == 'siswa') {
                $variables = [
                    ['code' => '{{nama}}', 'desc' => 'Nama Siswa'],
                    ['code' => '{{nisn}}', 'desc' => 'NISN'],
                    ['code' => '{{nipd}}', 'desc' => 'NIPD'],
                    ['code' => '{{kelas}}', 'desc' => 'Kelas/Rombel'],
                    ['code' => '{{tempat_lahir}}', 'desc' => 'Tempat Lahir'],
                    ['code' => '{{tanggal_lahir}}', 'desc' => 'Tanggal Lahir'],
                    ['code' => '{{alamat}}', 'desc' => 'Alamat Siswa'],
                    ['code' => '{{nama_wali}}', 'desc' => 'Nama Wali'],
                    ['code' => '{{tanggal}}', 'desc' => 'Tanggal Surat'],
                ];
            } else {
                $variables = [
                    ['code' => '{{nama}}', 'desc' => 'Nama Guru'],
                    ['code' => '{{nuptk}}', 'desc' => 'NUPTK'],
                    ['code' => '{{nip}}', 'desc' => 'NIP'],
                    ['code' => '{{mapel}}', 'desc' => 'Mata Pelajaran'],
                    ['code' => '{{jabatan}}', 'desc' => 'Jabatan'],
                    ['code' => '{{pangkat}}', 'desc' => 'Pangkat/Golongan'],
                    ['code' => '{{unit_kerja}}', 'desc' => 'Unit Kerja'],
                    ['code' => '{{alamat}}', 'desc' => 'Alamat Guru'],
                    ['code' => '{{tanggal}}', 'desc' => 'Tanggal Surat'],
                    ['code' => '{{tahun_pelajaran}}', 'desc' => 'Thn. Pelajaran'],
                ];
            }
        @endphp

        {{-- Navigation Tabs --}}
        <ul class="nav nav-pills mb-3">
            <li class="nav-item">
                <a class="nav-link {{ $kategoriAktif == 'siswa' ? 'active' : '' }}" href="{{ route('admin.administrasi.tipe-surat.index', ['kategori' => 'siswa']) }}">
                    <i class='bx bx-user me-1'></i> Siswa
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $kategoriAktif == 'guru' ? 'active' : '' }}" href="{{ route('admin.administrasi.tipe-surat.index', ['kategori' => 'guru']) }}">
                    <i class='bx bx-briefcase-alt-2 me-1'></i> Guru
                </a>
            </li>
        </ul>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <strong>Gagal Menyimpan!</strong> Silakan periksa inputan Anda.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            {{-- KOLOM KIRI — EDITOR FORM --}}
            <div class="col-md-8">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header d-flex justify-content-between align-items-center bg-white border-bottom">
                        <h5 class="mb-0 fw-bold text-primary">
                            <i class='bx bx-edit-alt me-1'></i>
                            {{ isset($template) ? 'Edit Template' : 'Buat Template Baru' }}
                        </h5>
                        <span class="badge bg-label-primary">{{ strtoupper($kategoriAktif) }}</span>
                    </div>

                    <div class="card-body mt-3">
                        <form action="{{ isset($template) ? route('admin.administrasi.tipe-surat.update', $template->id) : route('admin.administrasi.tipe-surat.store') }}" method="POST">
                            @csrf
                            @if (isset($template)) @method('PUT') @endif
                            <input type="hidden" name="kategori" value="{{ $kategoriAktif }}">

                            {{-- Judul & Ukuran Kertas --}}
                            <div class="row g-3 mb-4">
                                <div class="col-md-8">
                                    <label class="form-label fw-bold">Perihal / Judul Surat</label>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="bx bx-file"></i></span>
                                        <input type="text" name="judul_surat" class="form-control" placeholder="Contoh: Surat Keterangan Aktif" value="{{ old('judul_surat', $template->judul_surat ?? '') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Ukuran Kertas</label>
                                    <select name="ukuran_kertas" class="form-select">
                                        @foreach (['A4', 'F4', 'Legal', 'Letter'] as $uk)
                                            <option value="{{ $uk }}" {{ old('ukuran_kertas', $template->ukuran_kertas ?? '') == $uk ? 'selected' : '' }}>{{ $uk }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- =========================================================
                                 ⭐ VARIABLE PALETTE (UPDATED)
                                 ========================================================= --}}
                            <div class="mb-2">
                                <label class="form-label fw-bold d-block mb-1">
                                    <i class='bx bx-code-curly'></i> Variabel Otomatis
                                    <small class="text-muted fw-normal fst-italic ms-1">(Klik untuk menyalin)</small>
                                </label>
                                
                                <div class="d-flex flex-wrap gap-2 align-items-center p-3 bg-light rounded border">
                                    {{-- Tampilkan 5 Variabel Pertama --}}
                                    @foreach(array_slice($variables, 0, 5) as $var)
                                        <span class="badge bg-white text-secondary border var-chip shadow-sm" onclick="copyToClipboard('{{ $var['code'] }}')">
                                            <i class='bx bx-copy'></i> {{ $var['desc'] }}
                                        </span>
                                    @endforeach

                                    {{-- Tombol Lihat Semua --}}
                                    <button type="button" class="btn btn-xs btn-primary rounded-pill ms-auto" data-bs-toggle="modal" data-bs-target="#variableModal">
                                        <i class="bx bx-grid-alt me-1"></i> Lihat Semua Variabel
                                    </button>
                                </div>
                            </div>

                            {{-- Editor Area --}}
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label fw-bold mb-0">Isi Dokumen</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="toggleCenterGuide">
                                        <label class="form-check-label small text-muted" for="toggleCenterGuide">
                                            <i class="bx bx-ruler"></i> Garis Tengah
                                        </label>
                                    </div>
                                </div>

                                <div id="editor-container">
                                    <div id="editor-toolbar"></div>
                                    <div id="editor_konten_quill"></div>
                                </div>
                                
                                <textarea name="template_isi" id="template_isi_hidden" style="display:none;">{{ old('template_isi', $template->template_isi ?? '') }}</textarea>
                            </div>

                            <div class="d-flex gap-2 pt-2 border-top">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-save me-1"></i> {{ isset($template) ? 'Update' : 'Simpan' }}
                                </button>
                                @if (isset($template))
                                    <a href="{{ route('admin.administrasi.tipe-surat.index', ['kategori' => $kategoriAktif]) }}" class="btn btn-outline-secondary">Batal</a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN — LIST --}}
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header bg-light border-bottom">
                        <h6 class="mb-0 fw-bold text-secondary">
                            <i class='bx bx-list-ul me-1'></i> Daftar Template
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            @forelse($templates as $t)
                                <li class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3">
                                    <div class="overflow-hidden">
                                        <h6 class="mb-1 text-truncate" title="{{ $t->judul_surat }}">{{ $t->judul_surat }}</h6>
                                        <small class="text-muted d-block">
                                            <span class="badge bg-label-secondary me-1">{{ $t->ukuran_kertas }}</span>
                                            {{ $t->created_at->format('d M Y') }}
                                        </small>
                                    </div>
                                    <div class="flex-shrink-0 ms-2">
                                        <div class="btn-group">
                                            <a href="{{ route('admin.administrasi.tipe-surat.edit', $t->id) }}?kategori={{ $kategoriAktif }}" class="btn btn-sm btn-outline-primary"><i class="bx bx-pencil"></i></a>
                                            <form action="{{ route('admin.administrasi.tipe-surat.destroy', $t->id) }}" method="POST" onsubmit="return confirm('Hapus template ini?')" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger" style="border-top-left-radius: 0; border-bottom-left-radius: 0;"><i class="bx bx-trash"></i></button>
                                            </form>
                                        </div>
                                    </div>
                                </li>
                            @empty
                                <li class="list-group-item text-center py-5 text-muted">
                                    <div class="mb-2"><i class="bx bx-file-blank fs-1 text-light"></i></div>
                                    <small>Belum ada template.</small>
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- =======================================================================
         MODAL: SEMUA VARIABEL
         ======================================================================= --}}
    <div class="modal fade" id="variableModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white" id="modalTitle">
                        <i class='bx bx-code-curly me-2'></i>Daftar Variabel Otomatis
                    </h5>
                    <button type="button" class="btn-close btn-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body bg-light">
                    <p class="text-muted mb-3">Klik untuk menyalin kode, lalu paste ke dalam editor.</p>
                    
                    <div class="row g-3">
                        @foreach($variables as $var)
                            <div class="col-md-4 col-sm-6">
                                <div class="card h-100 border shadow-none var-chip w-100 justify-content-between p-3" onclick="copyToClipboard('{{ $var['code'] }}')">
                                    <span class="fw-semibold text-dark">{{ $var['desc'] }}</span>
                                    <code class="text-primary bg-label-primary px-2 py-1 rounded small">{{ $var['code'] }}</code>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Toast Notification --}}
    <div id="copy-toast">Kode disalin ke clipboard!</div>

    {{-- SCRIPTS --}}
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/quill-html-edit-button@2.2.11/dist/quill-html-edit-button.min.js"></script>

        <script>
            function copyToClipboard(text) {
                navigator.clipboard.writeText(text).then(function() {
                    var x = document.getElementById("copy-toast");
                    x.innerText = "Disalin: " + text;
                    x.className = "show";
                    setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);
                }, function(err) {
                    console.error('Async: Could not copy text: ', err);
                });
            }

            document.addEventListener("DOMContentLoaded", () => {
                const editorDiv = document.querySelector('#editor_konten_quill');
                const guideToggle = document.querySelector('#toggleCenterGuide');
                const hiddenInput = document.querySelector('#template_isi_hidden');
                const form = document.querySelector('form');
                const initialHtml = hiddenInput.value.trim();

                const toolbarOptions = [
                    [{ 'header': [1, 2, 3, false] }],
                    // [{ 'font': [] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'color': [] }, { 'background': [] }],
                    [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                    [{ 'indent': '-1' }, { 'indent': '+1' }],
                    [{ 'align': [] }],
                    ['link', 'image', 'table', 'code-block'],
                    ['clean', 'htmlEditButton']
                ];

                const quill = new Quill(editorDiv, {
                    theme: 'snow',
                    modules: {
                        htmlEditButton: { msg: "Edit HTML", okText: "Simpan", cancelText: "Batal", buttonHTML: "&lt;&gt;" },
                        toolbar: { container: toolbarOptions }
                    }
                });

                if (initialHtml) quill.clipboard.dangerouslyPasteHTML(0, initialHtml);

                form.addEventListener('submit', function() {
                    hiddenInput.value = editorDiv.querySelector('.ql-editor').innerHTML;
                });

                guideToggle.addEventListener('change', function() {
                    const editorElement = editorDiv.querySelector('.ql-editor');
                    if (editorElement) editorElement.classList.toggle('show-guide', this.checked);
                });
                
                if (guideToggle.checked) {
                    editorDiv.querySelector('.ql-editor').classList.add('show-guide');
                }
            });
        </script>
    @endpush

@endsection