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
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Administrasi /</span> Template SK Kepegawaian</h4>

        @php
            $variableGroups = [
                'Identitas Pegawai' => [
                    ['code' => '{{ nama }}', 'desc' => 'Nama Lengkap'],
                    ['code' => '{{ nip }}', 'desc' => 'NIP'],
                    ['code' => '{{ nik }}', 'desc' => 'NIK'],
                    ['code' => '{{ nuptk }}', 'desc' => 'NUPTK'],
                    ['code' => '{{ jabatan_gtk }}', 'desc' => 'Jabatan GTK'],
                ],
                'Kepegawaian' => [
                    ['code' => '{{ status_pegawai }}', 'desc' => 'Status Pegawai'],
                    ['code' => '{{ pangkat }}', 'desc' => 'Pangkat/Golongan'],
                    ['code' => '{{ tmt }}', 'desc' => 'TMT Pengangkatan'],
                    ['code' => '{{ pendidikan }}', 'desc' => 'Pendidikan'],
                ],
                'Detail SK' => [
                    // PERBAIKAN: nomor_surat diganti menjadi no_surat agar sinkron
                    ['code' => '{{ no_surat }}', 'desc' => 'Nomor SK Resmi'],
                    ['code' => '{{ jabatan }}', 'desc' => 'Jabatan di SK'],
                    ['code' => '{{ tanggal }}', 'desc' => 'Tanggal SK'],
                    ['code' => '{{ tahun_ajaran }}', 'desc' => 'Tahun Pelajaran'],
                ],
            ];
            $quickAccess = [
                ['code' => '{{ no_surat }}', 'desc' => 'Nomor SK'],
                ['code' => '{{ nama }}', 'desc' => 'Nama'],
                ['code' => '{{ tanggal }}', 'desc' => 'Tanggal'],
            ];
        @endphp

        <div class="row">
            <div class="col-md-9">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 fw-bold text-primary">{{ isset($template) ? 'Edit Template SK' : 'Buat Baru' }}</h5>
                    </div>

                    <div class="card-body mt-3">
                        <form id="formTemplate"
                            action="{{ isset($template) ? route('admin.kepegawaian.TemplateSk.update', $template->id) : route('admin.kepegawaian.TemplateSk.store') }}"
                            method="POST">
                            @csrf
                            @if (isset($template))
                                @method('PUT')
                            @endif

                            <input type="hidden" name="margin_bottom" value="{{ $template->margin_bottom ?? 20 }}">

                            <div class="row g-3 mb-4 bg-light p-3 rounded mx-1">
                                <div class="col-md-8">
                                    <label class="form-label fw-bold">Judul SK</label>
                                    <input type="text" name="judul_surat" class="form-control"
                                        value="{{ old('judul_surat', $template->judul_surat ?? '') }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Ukuran Kertas</label>
                                    <select name="ukuran_kertas" id="paperSizeSelect" class="form-select">
                                        @foreach (['A4', 'F4', 'Legal'] as $uk)
                                            <option value="{{ $uk }}"
                                                {{ old('ukuran_kertas', $template->ukuran_kertas ?? 'A4') == $uk ? 'selected' : '' }}>
                                                {{ $uk }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- PANEL MARGIN SETTINGS --}}
                            <div class="card mb-3 border border-dashed shadow-none mx-1">
                                <div class="card-body p-2 bg-white rounded">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class='bx bx-ruler me-2 text-primary'></i>
                                        <label class="fw-bold m-0 small text-uppercase">Margin Kertas (mm)</label>
                                    </div>
                                    <div class="row g-2">
                                        <div class="col-4">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text bg-light">Atas</span>
                                                <input type="number" name="margin_top" id="marginTop"
                                                    class="form-control margin-input"
                                                    value="{{ old('margin_top', $template->margin_top ?? 20) }}">
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text bg-light">Kiri</span>
                                                <input type="number" name="margin_left" id="marginLeft"
                                                    class="form-control margin-input"
                                                    value="{{ old('margin_left', $template->margin_left ?? 25) }}">
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text bg-light">Kanan</span>
                                                <input type="number" name="margin_right" id="marginRight"
                                                    class="form-control margin-input"
                                                    value="{{ old('margin_right', $template->margin_right ?? 25) }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3 px-1">
                                <label class="form-label fw-bold d-block mb-2">Variabel SK:</label>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach ($quickAccess as $var)
                                        <span class="var-chip shadow-sm"
                                            onclick="insertVariable('{{ $var['code'] }}')">{{ $var['desc'] }}</span>
                                    @endforeach
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill ms-2"
                                        data-bs-toggle="modal" data-bs-target="#variableModal">Semua Variabel</button>
                                </div>
                            </div>

                            <div class="mb-4">
                                <textarea name="template_isi" id="my-editor">{{ old('template_isi', $template->template_isi ?? '') }}</textarea>
                            </div>

                            <div class="d-flex gap-2 pt-3 border-top">
                                <button type="submit" class="btn btn-primary btn-lg shadow-sm">Simpan Template SK</button>
                                <a href="{{ route('admin.kepegawaian.TemplateSk.index') }}"
                                    class="btn btn-outline-secondary btn-lg">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 fw-bold text-secondary">Daftar Template SK</h6>
                    </div>
                    <ul class="list-group list-group-flush">
                        @foreach ($templates as $t)
                            <li class="list-group-item py-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="me-2 overflow-hidden">
                                        <h6 class="mb-1 text-truncate" style="max-width: 120px;">{{ $t->judul_surat }}</h6>
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

    {{-- MODAL VARIABEL --}}
    <div class="modal fade" id="variableModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white">Variabel Template SK</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body bg-light p-4">
                    @foreach ($variableGroups as $groupName => $vars)
                        <div class="variable-group mb-4">
                            <h6 class="fw-bold text-uppercase text-muted mb-3 border-bottom pb-2">{{ $groupName }}</h6>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach ($vars as $var)
                                    <div class="var-chip shadow-sm" onclick="insertVariable('{{ $var['code'] }}')">
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

    {{-- MODAL DELETE --}}
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <form id="deleteForm" method="POST">
                    @csrf @method('DELETE')
                    <div class="modal-body text-center p-4">
                        <i class="bx bx-trash text-danger display-4 mb-3"></i>
                        <h5>Hapus Template?</h5>
                        <p class="text-muted">Data yang dihapus tidak bisa dikembalikan.</p>
                        <div class="d-flex gap-2 justify-content-center mt-4">
                            <button type="button" class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="action-toast"><span>Variabel disisipkan!</span></div>

    @push('scripts')
        <script>
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
                }
            };

            tinymce.init({
                selector: '#my-editor',
                license_key: 'gpl',
                height: '800px',
                toolbar_sticky: true,
                toolbar_sticky_offset: 70,
                plugins: 'preview searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media table charmap pagebreak nonbreaking anchor lists wordcount help',
                toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline | alignleft aligncenter alignright alignjustify | numlist bullist | table | pagebreak',
                font_size_formats: '8pt 9pt 10pt 11pt 12pt 14pt 16pt 18pt 24pt 36pt 48pt',
                content_style: `
                    html { background-color: #525659 !important; padding: 2rem 0; display: flex; justify-content: center; }
                    body { background-color: white !important; font-family: 'Times New Roman', serif; font-size: 12pt; line-height: 1.5; color: #000; width: 210mm; min-height: 297mm; margin: 0 auto !important; border: 1px solid #c7c7c7; box-shadow: 0 0 10px rgba(0,0,0,0.5); padding: 2cm; box-sizing: border-box !important; word-wrap: break-word; }
                    .mce-pagebreak { border-top: 2px dashed #999; background: #eee; height: 15px; margin: 10px -2cm; display: flex; align-items: center; justify-content: center; color: #777; font-size: 10px; page-break-after: always; }
                    .mce-pagebreak::after { content: "BATAS HALAMAN MANUAL"; }
                    table { border-collapse: collapse; width: 100%; margin-bottom: 10px; }
                    table td, table th { border: 1px solid #000; padding: 4px; }
                    table[border="0"] td { border: 1px dashed #ccc !important; }
                `,
                setup: function(editor) {
                    editor.on('init', function() {
                        updateEditorVisuals();
                    });
                }
            });

            function updateEditorVisuals() {
                if (!tinymce.activeEditor) return;
                const sizeName = document.getElementById('paperSizeSelect').value;
                const top = document.getElementById('marginTop').value;
                const right = document.getElementById('marginRight').value;
                const left = document.getElementById('marginLeft').value;
                const config = paperConfig[sizeName] || paperConfig['A4'];
                const body = tinymce.activeEditor.dom.select('body')[0];

                tinymce.activeEditor.dom.setStyle(body, 'width', config.width);
                tinymce.activeEditor.dom.setStyle(body, 'min-height', config.heightVal + 'mm');

                body.style.paddingTop = top + 'mm';
                body.style.paddingRight = right + 'mm';
                body.style.paddingLeft = left + 'mm';

                const pageH = config.heightVal;
                const gradientStyle =
                    `repeating-linear-gradient(to bottom, #ffffff 0mm, #ffffff ${pageH}mm, #525659 ${pageH}mm, #525659 ${pageH + 10}mm)`;
                tinymce.activeEditor.dom.setStyle(body, 'background-image', gradientStyle);
            }

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
