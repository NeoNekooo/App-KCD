@extends('layouts.admin')

@section('content')
    {{-- === TINYMCE LOKAL === --}}
    <script src="{{ asset('js/tinymce.min.js') }}"></script>

    <style>
        /* UI Enhancements */
        .tox-promotion,
        .tox-statusbar__branding {
            display: none !important;
        }

        .tox-tinymce {
            border: 1px solid #ddd !important;
            border-radius: 8px !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .tox-statusbar {
            display: flex !important;
            visibility: visible !important;
            border-top: 1px solid #ccc !important;
        }

        /* Toolbar agar lebih rapi & Sticky */
        .tox-toolbar__primary {
            background-color: #f8f9fa !important;
            border-bottom: 1px solid #eee !important;
            flex-wrap: wrap !important;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        /* Chip Variabel */
        .var-chip {
            cursor: pointer;
            transition: all 0.2s;
            font-size: 0.85rem;
            border: 1px solid #e0e0e0;
            user-select: none;
            display: inline-flex;
            align-items: center;
            padding: 5px 10px;
            border-radius: 50px;
            background: #fff;
            color: #566a7f;
        }

        .var-chip:hover {
            background-color: #e7e7ff !important;
            color: #696cff !important;
            border-color: #696cff;
            transform: translateY(-1px);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .var-code {
            font-family: monospace;
            background: #f5f5f9;
            padding: 2px 6px;
            border-radius: 4px;
            margin-left: 8px;
            font-size: 0.75rem;
            color: #696cff;
        }

        /* Toast Notification */
        #action-toast {
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

        #action-toast.show {
            visibility: visible;
            transform: translateX(-50%) translateY(0);
            opacity: 1;
        }
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Administrasi /</span> Template Surat</h4>

        @php
            $reqKategori = request()->kategori;
            if (empty($reqKategori) && isset($template)) {
                $reqKategori = $template->kategori;
            }
            $kategoriAktif = $reqKategori ?? 'siswa';

            $variableGroups = [];
            $variableGroups['Data Umum (Wajib)'] = [
                ['code' => '{{no_surat}}', 'desc' => 'Nomor Surat'],
                ['code' => '{{tanggal}}', 'desc' => 'Tanggal Cetak'],
                ['code' => '{{tahun_ajaran}}', 'desc' => 'Tahun Ajaran'],
            ];

            if ($kategoriAktif == 'siswa') {
                $variableGroups['Identitas Siswa'] = [
                    ['code' => '{{nama}}', 'desc' => 'Nama Lengkap'],
                    ['code' => '{{nipd}}', 'desc' => 'NIPD'],
                    ['code' => '{{nisn}}', 'desc' => 'NISN'],
                    ['code' => '{{kelas}}', 'desc' => 'Kelas'],
                    ['code' => '{{ttl}}', 'desc' => 'Tempat, Tgl Lahir'],
                    ['code' => '{{alamat}}', 'desc' => 'Alamat'],
                ];
            } else {
                $variableGroups['Identitas Guru'] = [
                    ['code' => '{{nama}}', 'desc' => 'Nama Lengkap'],
                    ['code' => '{{nip}}', 'desc' => 'NIP'],
                    ['code' => '{{jabatan}}', 'desc' => 'Jabatan'],
                ];
            }
            $quickAccess = array_merge($variableGroups['Data Umum (Wajib)'], array_slice(end($variableGroups), 0, 3));
        @endphp

        <ul class="nav nav-pills mb-3">
            <li class="nav-item">
                <a class="nav-link {{ $kategoriAktif == 'siswa' ? 'active' : '' }}"
                    href="{{ route('admin.administrasi.tipe-surat.index', ['kategori' => 'siswa']) }}"><i
                        class='bx bx-user me-1'></i> Siswa</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $kategoriAktif == 'guru' ? 'active' : '' }}"
                    href="{{ route('admin.administrasi.tipe-surat.index', ['kategori' => 'guru']) }}"><i
                        class='bx bx-briefcase-alt-2 me-1'></i> Guru</a>
            </li>
        </ul>

        <div class="row">
            <div class="col-md-9">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 fw-bold text-primary">{{ isset($template) ? 'Edit Template' : 'Buat Baru' }}</h5>
                    </div>

                    <div class="card-body mt-3">
                        <form id="formTemplate"
                            action="{{ isset($template) ? route('admin.administrasi.tipe-surat.update', $template->id) : route('admin.administrasi.tipe-surat.store') }}"
                            method="POST">
                            @csrf
                            @if (isset($template))
                                @method('PUT')
                            @endif
                            <input type="hidden" name="kategori" value="{{ $kategoriAktif }}">

                            {{-- Hidden Margin Bottom (Default 20 agar tidak error di DB) --}}
                            <input type="hidden" name="margin_bottom" value="{{ $template->margin_bottom ?? 20 }}">

                            <div class="row g-3 mb-4 bg-light p-3 rounded mx-1">
                                <div class="col-md-8">
                                    <label class="form-label fw-bold">Judul Surat</label>
                                    <input type="text" name="judul_surat" class="form-control"
                                        value="{{ old('judul_surat', $template->judul_surat ?? '') }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Ukuran Kertas</label>
                                    <select name="ukuran_kertas" id="paperSizeSelect" class="form-select">
                                        @foreach (['A4', 'F4', 'Legal', 'Letter'] as $uk)
                                            <option value="{{ $uk }}"
                                                {{ old('ukuran_kertas', $template->ukuran_kertas ?? 'A4') == $uk ? 'selected' : '' }}>
                                                {{ $uk }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- PANEL MARGIN SETTINGS (TANPA MARGIN BAWAH) --}}
                            <div class="card mb-3 border border-dashed shadow-none mx-1">
                                <div class="card-body p-2 bg-white rounded">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class='bx bx-ruler me-2 text-primary'></i>
                                        <label class="fw-bold m-0 small text-uppercase">Margin Kertas (mm)</label>
                                    </div>
                                    <div class="row g-2">
                                        {{-- 1. ATAS --}}
                                        <div class="col-4 col-md-4">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text bg-light">Atas</span>
                                                <input type="number" name="margin_top" id="marginTop"
                                                    class="form-control margin-input"
                                                    value="{{ old('margin_top', $template->margin_top ?? 20) }}">
                                            </div>
                                        </div>
                                        {{-- 2. KIRI --}}
                                        <div class="col-4 col-md-4">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text bg-light">Kiri</span>
                                                <input type="number" name="margin_left" id="marginLeft"
                                                    class="form-control margin-input"
                                                    value="{{ old('margin_left', $template->margin_left ?? 25) }}">
                                            </div>
                                        </div>
                                        {{-- 3. KANAN --}}
                                        <div class="col-4 col-md-4">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text bg-light">Kanan</span>
                                                <input type="number" name="margin_right" id="marginRight"
                                                    class="form-control margin-input"
                                                    value="{{ old('margin_right', $template->margin_right ?? 25) }}">
                                            </div>
                                        </div>
                                        {{-- MARGIN BAWAH DIHAPUS DARI TAMPILAN --}}
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3 px-1">
                                <label class="form-label fw-bold d-block mb-2">Variabel Cepat:</label>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach ($quickAccess as $var)
                                        <span class="var-chip shadow-sm"
                                            onclick="insertVariable('{{ $var['code'] }}')">{{ $var['desc'] }}</span>
                                    @endforeach
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill ms-2"
                                        data-bs-toggle="modal" data-bs-target="#variableModal">Lihat Semua</button>
                                </div>
                            </div>

                            <div class="mb-4">
                                {{-- EDITOR TINYMCE --}}
                                <textarea name="template_isi" id="my-editor">{{ old('template_isi', $template->template_isi ?? '') }}</textarea>
                            </div>

                            <div class="d-flex gap-2 pt-3 border-top">
                                <button type="submit" class="btn btn-primary btn-lg shadow-sm">Simpan Template</button>
                                <a href="{{ route('admin.administrasi.tipe-surat.index', ['kategori' => $kategoriAktif]) }}"
                                    class="btn btn-outline-secondary btn-lg">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- SIDEBAR LIST --}}
            <div class="col-md-3">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 fw-bold text-secondary">Daftar Template</h6>
                    </div>
                    <ul class="list-group list-group-flush">
                        @foreach ($templates as $t)
                            <li class="list-group-item py-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="me-2 overflow-hidden">
                                        <h6 class="mb-1 text-truncate" style="max-width: 120px;">{{ $t->judul_surat }}
                                        </h6>
                                        <small class="badge bg-label-secondary">{{ $t->ukuran_kertas }}</small>
                                    </div>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.administrasi.tipe-surat.edit', ['tipe_surat' => $t->id, 'kategori' => $kategoriAktif]) }}"
                                            class="btn btn-primary"><i class="bx bx-pencil"></i></a>
                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                            data-bs-target="#deleteModal"
                                            data-action="{{ route('admin.administrasi.tipe-surat.destroy', $t->id) }}"><i
                                                class="bx bx-trash"></i></button>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL VARIABEL & DELETE --}}
    <div class="modal fade" id="variableModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white">Daftar Variabel</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-header border-bottom bg-light">
                    <input type="text" id="searchVariable" class="form-control" placeholder="Cari variabel...">
                </div>
                <div class="modal-body bg-light p-4">
                    @foreach ($variableGroups as $groupName => $vars)
                        <div class="variable-group mb-4">
                            <h6 class="fw-bold text-uppercase text-muted mb-3 border-bottom pb-2">{{ $groupName }}</h6>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach ($vars as $var)
                                    <div class="var-chip shadow-sm modal-var-chip"
                                        onclick="insertVariable('{{ $var['code'] }}')"
                                        data-search="{{ strtolower($var['desc'] . ' ' . $var['code']) }}">
                                        <span>{{ $var['desc'] }}</span><span class="var-code">{{ $var['code'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Hapus Template?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="deleteForm" method="POST">
                    @csrf @method('DELETE')
                    <div class="modal-body">Data yang dihapus tidak bisa dikembalikan.</div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Hapus Sekarang</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="action-toast"><span>Variabel disisipkan!</span></div>

    @push('scripts')
        <script>
            // === 1. CONFIG UKURAN KERTAS ===
            const paperConfig = {
                'A4': {
                    width: '210mm',
                    heightVal: 297
                },
                'F4': {
                    width: '215mm',
                    heightVal: 330
                },
                'Legal': {
                    width: '216mm',
                    heightVal: 356
                },
                'Letter': {
                    width: '216mm',
                    heightVal: 279
                }
            };

            // === 2. INIT TINYMCE ===
            tinymce.init({
                selector: '#my-editor',
                license_key: 'gpl',
                height: '800px',
                table_resizing: 'proportional',
                table_column_resizing: 'resizer',

                // FITUR STICKY TOOLBAR
                toolbar_sticky: true,
                toolbar_sticky_offset: 70,

                // PLUGINS
                plugins: 'preview searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media table charmap pagebreak nonbreaking anchor lists wordcount help',

                // === TOOLBAR ===
                toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline | alignleft aligncenter alignright alignjustify | numlist bullist | table | pagebreak',

                block_formats: 'Paragraph=p;Heading 1=h1;Heading 2=h2;Heading 3=h3;Heading 4=h4;Heading 5=h5;Heading 6=h6;Preformatted=pre',
                font_size_formats: '8pt 9pt 10pt 11pt 12pt 14pt 16pt 18pt 24pt 36pt 48pt',
                font_family_formats: 'Andale Mono=andale mono,times; Arial=arial,helvetica,sans-serif; Arial Black=arial black,avant garde; Book Antiqua=book antiqua,palatino; Comic Sans MS=comic sans ms,sans-serif; Courier New=courier new,courier; Georgia=georgia,palatino; Helvetica=helvetica; Impact=impact,chicago; Symbol=symbol; Tahoma=tahoma,arial,helvetica,sans-serif; Terminal=terminal,monaco; Times New Roman=times new roman,times; Trebuchet MS=trebuchet ms,geneva; Verdana=verdana,geneva; Webdings=webdings; Wingdings=wingdings,zapf dingbats',

                // === STYLE EDITOR ===
                content_style: `
                    html {
                        background-color: #525659 !important;
                        padding: 2rem 0; 
                        overflow-y: auto !important; 
                        display: flex;
                        justify-content: center;
                    }
                    body {
                        background-color: white !important;
                        font-family: 'Times New Roman', serif;
                        font-size: 12pt;
                        line-height: 1.5;
                        color: #000;
                        width: 210mm;
                        min-height: 297mm;
                        margin: 0 auto !important; 
                        display: block !important;
                        border: 1px solid #c7c7c7;
                        box-shadow: 0 0 10px rgba(0,0,0,0.5);
                        padding: 2cm;
                        box-sizing: border-box !important; 
                        word-wrap: break-word;
                        overflow-wrap: break-word;
                        overflow-x: hidden; 

                        /* GARIS PEMBATAS HALAMAN */
                        background-image: repeating-linear-gradient(to bottom, 
                            #ffffff 0mm, 
                            #ffffff 297mm, 
                            #525659 297mm, 
                            #525659 307mm
                        );
                    }
                    .mce-pagebreak { 
                        border-top: 2px dashed #999; 
                        background: #eee; 
                        height: 15px; 
                        margin: 10px -2cm; 
                        display: flex; align-items: center; justify-content: center;
                        color: #777; font-size: 10px;
                        page-break-after: always;
                    }
                    .mce-pagebreak::after { content: "BATAS HALAMAN MANUAL"; }
                    table { border-collapse: collapse; width: 100%; margin-bottom: 10px; }
                    table td, table th { border: 1px solid #000; padding: 4px; }
                    table[border="0"] td { border: 1px dashed #ccc !important; }
                `,

                setup: function(editor) {
                    editor.on('init', function() {
                        updateEditorVisuals(); // Update visual saat load
                    });
                }
            });

            // === 3. LOGIKA UPDATE VISUAL ===
            function updateEditorVisuals() {
                if (!tinymce.activeEditor) return;

                const sizeName = document.getElementById('paperSizeSelect').value;
                const top = document.getElementById('marginTop').value;
                const right = document.getElementById('marginRight').value;
                const left = document.getElementById('marginLeft').value;
                // Margin Bottom dihapus dari logika visual agar tidak merusak tampilan

                const config = paperConfig[sizeName] || paperConfig['A4'];
                const body = tinymce.activeEditor.dom.select('body')[0];

                // Update Lebar & Tinggi
                tinymce.activeEditor.dom.setStyle(body, 'width', config.width);
                tinymce.activeEditor.dom.setStyle(body, 'min-height', config.heightVal + 'mm');

                // Update Padding (Margin)
                body.style.paddingTop = top + 'mm';
                body.style.paddingRight = right + 'mm';
                body.style.paddingLeft = left + 'mm';
                // Tidak ada paddingBottom yang diset, biarkan default agar tidak aneh

                // Update Gradient Halaman
                const pageH = config.heightVal;
                const gapH = 10;
                const cycleH = pageH + gapH;

                const gradientStyle = `repeating-linear-gradient(to bottom, 
                    #ffffff 0mm, 
                    #ffffff ${pageH}mm, 
                    #525659 ${pageH}mm, 
                    #525659 ${cycleH}mm
                )`;
                tinymce.activeEditor.dom.setStyle(body, 'background-image', gradientStyle);
            }

            // === 4. EVENT LISTENERS ===
            document.getElementById('paperSizeSelect').addEventListener('change', updateEditorVisuals);
            document.querySelectorAll('.margin-input').forEach(input => {
                input.addEventListener('input', updateEditorVisuals);
            });

            function insertVariable(code) {
                if (tinymce.activeEditor) {
                    tinymce.activeEditor.insertContent(code);
                    const toast = document.getElementById("action-toast");
                    toast.className = "show";
                    setTimeout(() => toast.className = "", 2000);
                }
            }

            document.getElementById('searchVariable').addEventListener('keyup', function() {
                const val = this.value.toLowerCase();
                document.querySelectorAll('.modal-var-chip').forEach(chip => {
                    const text = chip.getAttribute('data-search');
                    chip.style.display = text.includes(val) ? "inline-flex" : "none";
                });
            });

            const deleteModalEl = document.getElementById('deleteModal');
            if (deleteModalEl) {
                deleteModalEl.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    document.getElementById('deleteForm').setAttribute('action', button.getAttribute('data-action'));
                });
            }
        </script>
    @endpush
@endsection