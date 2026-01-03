@extends('layouts.admin')

@section('content')
    {{-- === TINYMCE LOCAL === --}}
    <script src="{{ asset('js/tinymce.min.js') }}"></script>

    <style>
        /* UI Enhancements */
        .tox-promotion,
        .tox-statusbar__branding {
            display: none !important;
        }

        .tox-tinymce {
            border: 1px solid #d9dee3 !important;
            border-radius: 8px !important;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            width: 100% !important;
            margin-bottom: 0 !important;
        }

        .tox-statusbar {
            border-top: 1px solid #ccc !important;
        }

        .tox-toolbar__primary {
            position: sticky;
            top: 0;
            z-index: 10;
        }

        /* Chip Variabel */
        .var-chip {
            cursor: pointer;
            font-size: 0.8rem;
            border: 1px solid #d9dee3;
            background: #fff;
            color: #566a7f;
            padding: 4px 10px;
            border-radius: 50px;
            display: inline-flex;
            align-items: center;
            transition: 0.2s;
        }

        .var-chip:hover {
            background: #696cff;
            color: #fff;
            border-color: #696cff;
        }

        .var-code {
            font-family: monospace;
            background: #f5f5f9;
            padding: 2px 6px;
            border-radius: 4px;
            margin-left: 8px;
            font-size: 0.7rem;
            color: #696cff;
        }

        /* Wrapper per Halaman */
        .page-wrapper {
            background: #525659;
            padding: 30px;
            border-radius: 8px;
            margin-bottom: 30px;
            display: flex;
            flex-direction: column;
            align-items: center;
            border: 1px solid #444;
            position: relative;
        }

        .page-label {
            background: #fff;
            color: #333;
            padding: 5px 15px;
            border-radius: 4px;
            font-size: 0.85rem;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .btn-delete-page {
            position: absolute;
            top: 30px;
            right: 30px;
            background: #ff3e1d;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.8rem;
            z-index: 5;
        }

        .btn-delete-page:hover {
            background: #c51c00;
        }

        /* Toast */
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
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Kepegawaian /</span> Template SK</h4>

        @php
            $isEdit = isset($template) && $template !== null;
            $formAction = $isEdit
                ? route('admin.kepegawaian.TemplateSk.update', $template->id)
                : route('admin.kepegawaian.TemplateSk.store');

            $dbContent = $isEdit ? (string) $template->template_isi : '';
            $fullContent = (string) old('template_isi', $dbContent);
            $delimiter = '<div class="mce-pagebreak" contenteditable="false"></div>';

            if (strpos($fullContent, $delimiter) !== false) {
                $pages = explode($delimiter, $fullContent);
            } else {
                $pages = [$fullContent ?: ''];
            }

            // === VARIABEL SK ===
            $variableGroups = [
                'Identitas Pegawai' => [
                    ['code' => '{{nama}}', 'desc' => 'Nama Lengkap'],
                    ['code' => '{{nip}}', 'desc' => 'NIP'],
                    ['code' => '{{nik}}', 'desc' => 'NIK'],
                    ['code' => '{{nuptk}}', 'desc' => 'NUPTK'],
                    ['code' => '{{tempat_lahir}}', 'desc' => 'Tempat Lahir'],
                    ['code' => '{{tanggal_lahir}}', 'desc' => 'Tanggal Lahir'],
                    ['code' => '{{jabatan_gtk}}', 'desc' => 'Jabatan GTK'],
                ],
                'Kepegawaian' => [
                    ['code' => '{{status_pegawai}}', 'desc' => 'Status Pegawai'],
                    ['code' => '{{pangkat}}', 'desc' => 'Pangkat/Golongan'],
                    ['code' => '{{tmt}}', 'desc' => 'TMT Pengangkatan'],
                    ['code' => '{{pendidikan}}', 'desc' => 'Pendidikan Terakhir'],
                ],
                'Detail SK' => [
                    ['code' => '{{no_surat}}', 'desc' => 'Nomor SK Resmi'],
                    ['code' => '{{jabatan}}', 'desc' => 'Jabatan di SK'],
                    ['code' => '{{tanggal}}', 'desc' => 'Tanggal SK'],
                    ['code' => '{{tahun_ajaran}}', 'desc' => 'Tahun Pelajaran'],
                ],
            ];

            // Quick Access
            $quickAccess = array_merge(
                $variableGroups['Detail SK'],
                array_slice($variableGroups['Identitas Pegawai'], 0, 3)
            );
        @endphp

        <div class="row">
            <div class="col-md-9">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 fw-bold text-primary">{{ $isEdit ? 'Edit Template SK' : 'Buat Baru' }}</h5>
                    </div>
                    <div class="card-body mt-3">
                        <form id="formTemplate" action="{{ $formAction }}" method="POST">
                            @csrf
                            @if ($isEdit)
                                @method('PUT')
                            @endif
                            {{-- Hidden input for orientation, category, etc if needed --}}
                            <textarea name="template_isi" id="real_template_isi" style="display:none;"></textarea>

                            <div class="row g-3 mb-4 bg-light p-3 rounded mx-1">
                                <div class="col-md-8">
                                    <label class="form-label fw-bold">Judul SK</label>
                                    <input type="text" name="judul_surat" class="form-control"
                                        value="{{ old('judul_surat', $isEdit ? $template->judul_surat : '') }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Ukuran Kertas</label>
                                    <select name="ukuran_kertas" id="paperSizeSelect" class="form-select">
                                        @foreach (['A4', 'F4', 'Legal', 'Letter'] as $uk)
                                            <option value="{{ $uk }}"
                                                {{ old('ukuran_kertas', $isEdit ? $template->ukuran_kertas : 'A4') == $uk ? 'selected' : '' }}>
                                                {{ $uk }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- Input Margin --}}
                            <div class="card mb-3 border border-dashed shadow-none mx-1">
                                <div class="card-body p-2 bg-white rounded">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class='bx bx-ruler me-2 text-primary'></i>
                                        <label class="fw-bold m-0 small text-uppercase">Margin (mm)</label>
                                    </div>
                                    <div class="row g-2">
                                        @foreach (['top' => 'Atas', 'right' => 'Kanan', 'bottom' => 'Bawah', 'left' => 'Kiri'] as $pos => $label)
                                            <div class="col-3">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light px-2"
                                                        style="font-size: 0.7rem;">{{ $label }}</span>
                                                    <input type="number" id="margin{{ ucfirst($pos) }}"
                                                        name="margin_{{ $pos }}"
                                                        class="form-control margin-input px-1 text-center"
                                                        value="{{ old('margin_' . $pos, $isEdit ? $template->{'margin_' . $pos} ?? 20 : 20) }}">
                                                </div>
                                            </div>
                                        @endforeach
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
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill"
                                        data-bs-toggle="modal" data-bs-target="#variableModal">Lihat Semua</button>
                                </div>
                            </div>

                            <div id="pages-container">
                                @foreach ($pages as $index => $pageContent)
                                    <div class="page-wrapper" id="wrapper_page_{{ $index }}">
                                        <div class="d-flex justify-content-between w-100 mb-2 align-items-center">
                                            <span class="page-label">HALAMAN {{ $index + 1 }}</span>
                                            @if ($index > 0)
                                                <button type="button" class="btn-delete-page"
                                                    onclick="removePage({{ $index }})"><i
                                                        class='bx bx-trash'></i>
                                                    Hapus</button>
                                            @endif
                                        </div>
                                        <textarea id="editor_page_{{ $index }}" class="page-editor">{!! $pageContent !!}</textarea>
                                    </div>
                                @endforeach
                            </div>

                            <div class="text-center mb-4">
                                <button type="button" class="btn btn-outline-secondary btn-sm"
                                    onclick="createNewPage()"><i class='bx bx-plus'></i> Tambah Halaman Manual</button>
                            </div>

                            <div class="d-flex gap-2 pt-3 border-top">
                                <button type="submit" id="btnSimpan" class="btn btn-primary shadow-sm">Simpan
                                    Template SK</button>
                                <a href="{{ route('admin.kepegawaian.TemplateSk.index') }}"
                                    class="btn btn-outline-secondary">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Sidebar List --}}
            <div class="col-md-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 fw-bold text-secondary">Daftar Template SK</h6>
                    </div>
                    <ul class="list-group list-group-flush">
                        @foreach ($templates as $t)
                            <li class="list-group-item py-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="me-2 overflow-hidden">
                                        <h6 class="mb-1 text-truncate" style="max-width: 120px;">
                                            {{ $t->judul_surat }}</h6>
                                        <small class="badge bg-label-secondary">{{ $t->ukuran_kertas }}</small>
                                    </div>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.kepegawaian.TemplateSk.edit', $t->id) }}"
                                            class="btn btn-primary"><i class="bx bx-pencil"></i></a>
                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                            data-bs-target="#deleteModal"
                                            data-action="{{ route('admin.kepegawaian.TemplateSk.destroy', $t->id) }}"><i
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

    {{-- MODAL & TOAST --}}
    <div class="modal fade" id="variableModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white">Daftar Variabel SK</h5><button type="button"
                        class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body bg-light p-4">
                    @foreach ($variableGroups as $groupName => $vars)
                        @if (!empty($vars))
                            <div class="mb-4">
                                <h6 class="fw-bold border-bottom pb-2">{{ $groupName }}</h6>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach ($vars as $var)
                                        <div class="var-chip shadow-sm modal-var-chip"
                                            onclick="insertVariable('{{ $var['code'] }}')">
                                            <span>{{ $var['desc'] }}</span><span
                                                class="var-code">{{ $var['code'] }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Hapus?</h5><button type="button" class="btn-close"
                        data-bs-dismiss="modal"></button>
                </div>
                <form id="deleteForm" method="POST"> @csrf @method('DELETE')
                    <div class="modal-body">Hapus template SK ini?</div>
                    <div class="modal-footer"><button type="button" class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">Batal</button><button type="submit"
                            class="btn btn-danger">Hapus</button></div>
                </form>
            </div>
        </div>
    </div>
    <div id="action-toast"><span>Variabel disisipkan!</span></div>

    @push('scripts')
        <script>
            // === 1. CONFIG SIZE ===
            const paperConfig = {
                'A4': { width: '210mm', heightVal: 297 },
                'F4': { width: '215mm', heightVal: 330 },
                'Legal': { width: '216mm', heightVal: 356 },
                'Letter': { width: '216mm', heightVal: 279 }
            };

            let pageCount = {{ count($pages) }};
            window.activeEditorId = 'editor_page_0';

            document.addEventListener('DOMContentLoaded', function() {
                if (typeof tinymce === 'undefined') return;
                document.querySelectorAll('.page-editor').forEach(el => initTinyMCE(el.id));
            });

            function initTinyMCE(editorId) {
                tinymce.init({
                    selector: '#' + editorId,
                    license_key: 'gpl',
                    height: '800px',
                    plugins: 'preview searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media table nonbreaking anchor lists wordcount help',
                    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline | alignleft aligncenter alignright alignjustify | numlist bullist | table',
                    table_default_attributes: {
                        border: '1'
                    },
                    table_default_styles: {
                        'width': '100%',
                        'border-collapse': 'collapse'
                    },
                    // CSS EDITOR (PAPER STYLE)
                    content_style: `
                        html { background-color: #525659 !important; padding: 2rem 0; margin: 0; }
                        body { background-color: white !important; color: black; margin: 0 auto !important; box-shadow: 0 4px 15px rgba(0,0,0,0.3); box-sizing: border-box; display: block !important; font-family: 'Times New Roman', serif; font-size: 12pt; overflow-wrap: break-word; word-wrap: break-word; overflow-y: auto !important; }
                        table { width: 100% !important; max-width: 100% !important; table-layout: fixed; }
                        td, th { word-wrap: break-word; vertical-align: top; }
                    `,
                    setup: function(editor) {
                        editor.on('init', function() {
                            updateEditorVisuals();
                        });
                        editor.on('focus', function() {
                            window.activeEditorId = editor.id;
                        });
                        editor.on('keyup', function(e) {
                            checkPageOverflow(editor);
                            editor.selection.scrollIntoView();
                        });
                        editor.on('paste', function(e) {
                            setTimeout(() => checkPageOverflow(editor), 100);
                        });
                    }
                });
            }

            // === 2. LOGIKA OVERFLOW ===
            function checkPageOverflow(editor) {
                const body = editor.getBody();
                if (body.scrollHeight > body.clientHeight) {
                    const currentIdStr = editor.id;
                    const currentIndex = parseInt(currentIdStr.replace('editor_page_', ''));
                    if (!document.getElementById('editor_page_' + (currentIndex + 1))) {
                        showToast("Halaman Penuh! Membuat Halaman " + (currentIndex + 2) + "...");
                        createNewPage();
                        setTimeout(() => {
                            const nextEditor = tinymce.get('editor_page_' + (currentIndex + 1));
                            if (nextEditor) nextEditor.focus();
                        }, 500);
                    }
                }
            }

            function createNewPage() {
                const container = document.getElementById('pages-container');
                const newIndex = pageCount;
                const newHtml = `
                    <div class="page-wrapper" id="wrapper_page_${newIndex}">
                        <div class="d-flex justify-content-between w-100 mb-2 align-items-center">
                            <span class="page-label">HALAMAN ${newIndex + 1}</span>
                            <button type="button" class="btn-delete-page" onclick="removePage(${newIndex})"><i class='bx bx-trash'></i> Hapus</button>
                        </div>
                        <textarea id="editor_page_${newIndex}" class="page-editor"></textarea>
                    </div>`;
                container.insertAdjacentHTML('beforeend', newHtml);
                initTinyMCE('editor_page_' + newIndex);
                pageCount++;
            }

            window.removePage = function(index) {
                if (confirm('Hapus Halaman ' + (index + 1) + '?')) {
                    if (tinymce.get('editor_page_' + index)) tinymce.get('editor_page_' + index).remove();
                    const wrapper = document.getElementById('wrapper_page_' + index);
                    if (wrapper) wrapper.remove();
                }
            };

            document.getElementById('btnSimpan').addEventListener('click', function(e) {
                let combinedContent = "";
                let first = true;
                document.querySelectorAll('.page-editor').forEach((el) => {
                    const editorInstance = tinymce.get(el.id);
                    if (editorInstance) {
                        if (!first) combinedContent += '<div class="mce-pagebreak" contenteditable="false"></div>';
                        combinedContent += editorInstance.getContent();
                        first = false;
                    }
                });
                document.getElementById('real_template_isi').value = combinedContent;
            });

            // === 3. UPDATE VISUAL EDITOR ===
            function updateEditorVisuals() {
                const editors = tinymce.get();
                const sizeName = document.getElementById('paperSizeSelect').value;
                const top = document.getElementById('marginTop').value || 0;
                const right = document.getElementById('marginRight').value || 0;
                const bottom = document.getElementById('marginBottom').value || 0;
                const left = document.getElementById('marginLeft').value || 0;
                const config = paperConfig[sizeName] || paperConfig['A4'];

                for (let i = 0; i < editors.length; i++) {
                    const body = editors[i].dom.select('body')[0];
                    if (body) {
                        editors[i].dom.setStyle(body, 'width', config.width);
                        editors[i].dom.setStyle(body, 'min-height', config.heightVal + 'mm');
                        editors[i].dom.setStyle(body, 'height', config.heightVal + 'mm');
                        editors[i].dom.setStyle(body, 'max-height', config.heightVal + 'mm');
                        editors[i].dom.setStyle(body, 'box-sizing', 'border-box');
                        editors[i].dom.setStyle(body, 'padding', `${top}mm ${right}mm 0mm ${left}mm`);
                        if (bottom > 0) {
                            editors[i].dom.setStyle(body, 'border-bottom', `${bottom}mm solid #fff0f0`);
                        } else {
                            editors[i].dom.setStyle(body, 'border-bottom', '0px solid transparent');
                        }
                    }
                }
            }

            document.getElementById('paperSizeSelect').addEventListener('change', updateEditorVisuals);
            document.querySelectorAll('.margin-input').forEach(input => input.addEventListener('input', updateEditorVisuals));

            function insertVariable(code) {
                const editor = tinymce.get(window.activeEditorId);
                if (editor) {
                    editor.insertContent(code);
                    showToast("Variabel disisipkan!");
                } else {
                    alert("Klik dulu di kotak editor!");
                }
            }

            function showToast(msg) {
                const toast = document.getElementById("action-toast");
                toast.innerText = msg;
                toast.className = "show";
                setTimeout(() => toast.className = "", 3000);
            }

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