@extends('layouts.admin')

@section('content')
    {{-- === TINYMCE LOKAL === --}}
    <script src="{{ asset('js/tinymce.min.js') }}"></script>

    <style>
        /* Sembunyikan Iklan & Statusbar Branding */
        .tox-promotion,
        .tox-statusbar__branding {
            display: none !important;
        }

        /* Container Editor */
        .tox-tinymce {
            border: 1px solid #ddd !important;
            border-radius: 4px !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .tox-statusbar {
            display: flex !important;
            visibility: visible !important;
            border-top: 1px solid #ccc !important;
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
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
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
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Administrasi /</span> Template Surat SK</h4>

        @php
            // === DATA VARIABEL DIKELOMPOKKAN (UNTUK MODAL) ===
            $variableGroups = [
                'Identitas Pegawai' => [
                    ['code' => '{{nama}}', 'desc' => 'Nama Lengkap'],
                    ['code' => '{{nip}}', 'desc' => 'NIP'],
                    ['code' => '{{nik}}', 'desc' => 'NIK'],
                    ['code' => '{{nuptk}}', 'desc' => 'NUPTK'],
                    ['code' => '{{jenis_kelamin}}', 'desc' => 'Jenis Kelamin'],
                    ['code' => '{{agama}}', 'desc' => 'Agama'],
                ],
                'Biodata & Alamat' => [
                    ['code' => '{{ttl}}', 'desc' => 'Tempat, Tgl Lahir'],
                    ['code' => '{{tempat_lahir}}', 'desc' => 'Tempat Lahir'],
                    ['code' => '{{tanggal_lahir}}', 'desc' => 'Tgl Lahir'],
                    ['code' => '{{pendidikan}}', 'desc' => 'Pendidikan Terakhir'],
                    ['code' => '{{alamat}}', 'desc' => 'Alamat Lengkap'],
                ],
                'Data Kepegawaian' => [
                    ['code' => '{{jabatan}}', 'desc' => 'Jabatan (Di SK)'],
                    ['code' => '{{jabatan_gtk}}', 'desc' => 'Jabatan GTK'],
                    ['code' => '{{status_pegawai}}', 'desc' => 'Status Pegawai'],
                    ['code' => '{{pangkat}}', 'desc' => 'Pangkat'],
                    ['code' => '{{golongan}}', 'desc' => 'Golongan'],
                    ['code' => '{{tmt}}', 'desc' => 'TMT Pengangkatan'],
                ],
                'Detail SK & Sekolah' => [
                    ['code' => '{{nomor_surat}}', 'desc' => 'Nomor Surat'],
                    ['code' => '{{mata_pelajaran}}', 'desc' => 'Mata Pelajaran'],
                    ['code' => '{{jumlah_jam}}', 'desc' => 'Jumlah Jam'],
                    ['code' => '{{tahun_ajaran}}', 'desc' => 'Tahun Ajaran'],
                    ['code' => '{{semester}}', 'desc' => 'Semester'],
                    ['code' => '{{tanggal}}', 'desc' => 'Tanggal Cetak'],
                    // ['code' => '{{perihal}}', 'desc' => 'Perihal Surat'],
                ],
            ];

            // Variabel Cepat (shortcut di halaman utama)
            $quickAccess = $variableGroups['Identitas Pegawai']; 
        @endphp

        <div class="row">
            <div class="col-md-9">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-primary">
                            {{ isset($template) ? 'Edit Template SK' : 'Buat Template SK Baru' }}
                        </h5>
                    </div>

                    <div class="card-body mt-3">
                        <form id="formTemplate"
                            action="{{ isset($template) ? route('admin.kepegawaian.TemplateSk.update', $template->id) : route('admin.kepegawaian.TemplateSk.store') }}"
                            method="POST">
                            @csrf
                            @if (isset($template)) @method('PUT') @endif

                            {{-- PENGATURAN SURAT --}}
                            <div class="row g-3 mb-4 bg-light p-3 rounded mx-1">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Judul Template</label>
                                    <input type="text" name="judul_surat" class="form-control"
                                        placeholder="Contoh: SK Pengangkatan Guru"
                                        value="{{ old('judul_surat', $template->judul_surat ?? '') }}" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Ukuran Kertas</label>
                                    <select name="ukuran_kertas" id="paperSizeSelect" class="form-select">
                                        @foreach (['A4', 'F4', 'Legal', 'Letter'] as $uk)
                                            <option value="{{ $uk }}" {{ old('ukuran_kertas', $template->ukuran_kertas ?? 'A4') == $uk ? 'selected' : '' }}>
                                                {{ $uk }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 d-flex align-items-center pt-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="use_kop" id="toggleKop"
                                            value="1" {{ old('use_kop', $template->use_kop ?? 0) == 1 ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold" for="toggleKop">Gunakan Kop</label>
                                    </div>
                                </div>
                            </div>

                            {{-- SHORTCUT VARIABEL (Hanya Sedikit) --}}
                            <div class="mb-3 px-1">
                                <label class="form-label fw-bold d-block mb-2"><i class="bx bx-bolt-circle"></i> Variabel Cepat:</label>
                                <div class="d-flex flex-wrap gap-2 align-items-center">
                                    @foreach ($quickAccess as $var)
                                        <span class="var-chip shadow-sm" onclick="insertVariable('{{ $var['code'] }}')">
                                            {{ $var['desc'] }}
                                        </span>
                                    @endforeach
                                    
                                    {{-- TOMBOL PEMICU MODAL --}}
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill ms-2" 
                                            data-bs-toggle="modal" data-bs-target="#variableModal">
                                        <i class="bx bx-list-ul me-1"></i> Lihat Semua Variabel
                                    </button>
                                </div>
                            </div>

                            {{-- EDITOR --}}
                            <div class="mb-4">
                                <textarea name="template_isi" id="my-editor">{{ old('template_isi', $template->template_isi ?? '') }}</textarea>
                            </div>

                            <div class="d-flex gap-2 pt-3 border-top">
                                <button type="submit" class="btn btn-primary btn-lg"><i class="bx bx-save me-1"></i> Simpan Template</button>
                                <a href="{{ route('admin.kepegawaian.TemplateSk.index') }}" class="btn btn-outline-secondary btn-lg">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- SIDEBAR LIST --}}
            <div class="col-md-3">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 fw-bold text-secondary">Daftar Template SK</h6>
                    </div>
                    <ul class="list-group list-group-flush">
                        @foreach ($templates as $t)
                            <li class="list-group-item list-group-item-action py-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="me-2 overflow-hidden">
                                        <h6 class="mb-1 text-truncate">{{ $t->judul_surat }}</h6>
                                        <small class="badge bg-label-secondary">{{ $t->ukuran_kertas }}</small>
                                    </div>
                                    <div class="btn-group btn-group-sm shadow-sm">
                                        <a href="{{ route('admin.kepegawaian.TemplateSk.edit', $t->id) }}" class="btn btn-primary"><i class="bx bx-pencil"></i></a>
                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" data-action="{{ route('admin.kepegawaian.TemplateSk.destroy', $t->id) }}">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- === MODAL DAFTAR VARIABEL === --}}
    <div class="modal fade" id="variableModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white"><i class="bx bx-data me-2"></i> Daftar Variabel SK</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                {{-- Search Bar di Modal --}}
                <div class="modal-header border-bottom bg-light">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bx bx-search"></i></span>
                        <input type="text" id="searchVariable" class="form-control border-start-0 ps-0" placeholder="Cari variabel (misal: nama, alamat, pangkat)...">
                    </div>
                </div>

                <div class="modal-body bg-light p-4">
                    {{-- Loop Kategori --}}
                    @foreach ($variableGroups as $groupName => $vars)
                        <div class="variable-group mb-4">
                            <h6 class="fw-bold text-uppercase text-muted mb-3 border-bottom pb-2">
                                <i class="bx bx-folder me-1"></i> {{ $groupName }}
                            </h6>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach ($vars as $var)
                                    <div class="var-chip shadow-sm modal-var-chip" 
                                         onclick="insertVariable('{{ $var['code'] }}')"
                                         data-search="{{ strtolower($var['desc'] . ' ' . $var['code']) }}">
                                        <span>{{ $var['desc'] }}</span>
                                        <span class="var-code">{{ $var['code'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                    
                    {{-- Pesan jika tidak ditemukan --}}
                    <div id="noResultMsg" class="text-center py-5 d-none">
                        <i class="bx bx-search-alt text-muted fs-1 mb-2"></i>
                        <p class="text-muted">Variabel tidak ditemukan.</p>
                    </div>
                </div>
                
                <div class="modal-footer bg-white">
                    <small class="text-muted me-auto">* Klik tombol variabel untuk menyisipkannya ke editor.</small>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL DELETE --}}
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="deleteForm" method="POST" action="">@csrf @method('DELETE')
                    <div class="modal-body">
                        <p class="mb-0">Yakin hapus template ini?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="action-toast"><i class='bx bx-check-circle'></i> <span>Variabel disisipkan!</span></div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

        <script>
            // === 1. KONFIGURASI KERTAS ===
            const paperConfig = {
                'A4': { width: '210mm', height: 297, gap: 10 },
                'F4': { width: '215mm', height: 330, gap: 10 },
                'Legal': { width: '216mm', height: 356, gap: 10 },
                'Letter': { width: '216mm', height: 279, gap: 10 }
            };

            let currentPaper = '{{ old('ukuran_kertas', $template->ukuran_kertas ?? 'A4') }}';
            let useKop = {{ old('use_kop', $template->use_kop ?? 0) }};

            // === 2. INIT TINYMCE ===
            tinymce.init({
                selector: '#my-editor',
                license_key: 'gpl',
                height: '700px', 
                toolbar_sticky: true,
                toolbar_sticky_offset: 80, 
                promotion: false,
                branding: false,
                statusbar: true,   

                plugins: 'preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars emoticons accordion',
                toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | numlist bullist | table | lineheight outdent indent | removeformat | charmap | code pagebreak',

                font_family_formats: 'Times New Roman=times new roman,times,serif;Arial=arial,helvetica,sans-serif;Courier New=courier new,courier,monospace;Tahoma=tahoma,arial,helvetica,sans-serif;Verdana=verdana,geneva;',
                font_size_formats: '8pt 10pt 11pt 12pt 14pt 16pt 18pt 24pt 36pt',

                content_style: `
                    html { background: #f0f2f5; padding: 20px 0; display: flex; justify-content: center; overflow-y: auto; }
                    body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; margin: 0; padding: 2.54cm; background-color: white; background-image: linear-gradient(#ffffff calc(100% - 10px), #d0d0d0 calc(100% - 10px), #d0d0d0 100%); background-repeat: repeat-y; box-shadow: 0 0 10px rgba(0,0,0,0.2); overflow-x: auto; overflow-y: visible; }
                    p { margin-bottom: 0.5rem; line-height: 1.5; }
                    table { border-collapse: collapse; width: 100%; }
                    table td, table th { border: 1px solid #000; padding: 4px; }
                    table[border="0"] td, table[style*="border: none"] td, table[style*="border: 0px"] td { border: 1px dashed #bbb !important; }
                `,

                setup: function(editor) {
                    editor.on('init', function() {
                        applyPaperSettings(editor, currentPaper);
                        applyKopPadding(editor, useKop);
                    });
                }
            });

            // === FUNGSI UTAMA ===
            function applyPaperSettings(editor, sizeName) {
                const config = paperConfig[sizeName] || paperConfig['A4'];
                const body = editor.dom.select('body')[0];
                editor.dom.setStyle(body, 'width', config.width);
                editor.dom.setStyle(body, 'max-width', config.width);
                const totalHeightMm = (config.height + config.gap) + 'mm';
                const paperHeightMm = config.height + 'mm';
                const gradient = `linear-gradient(to bottom, #ffffff ${paperHeightMm}, #e0e0e0 ${paperHeightMm}, #e0e0e0 ${totalHeightMm})`;
                editor.dom.setStyle(body, 'background-image', gradient);
                editor.dom.setStyle(body, 'background-size', `100% ${totalHeightMm}`);
                editor.dom.setStyle(body, 'min-height', paperHeightMm);
            }

            function applyKopPadding(editor, hasKop) {
                const paddingTop = hasKop ? '5cm' : '2.54cm';
                editor.dom.setStyle(editor.getBody(), 'padding-top', paddingTop);
            }

            function insertVariable(code) {
                if (tinymce.activeEditor) {
                    tinymce.activeEditor.insertContent(code);
                    showToast(code + ' disisipkan!');
                    
                    // Opsional: Tutup modal setelah klik
                    // const modalEl = document.getElementById('variableModal');
                    // const modal = bootstrap.Modal.getInstance(modalEl);
                    // if(modal) modal.hide();
                }
            }

            function showToast(message) {
                const toast = document.getElementById("action-toast");
                toast.querySelector('span').innerText = message;
                toast.className = "show";
                setTimeout(() => toast.className = "", 2000);
            }

            // === EVENT LISTENERS ===
            document.getElementById('paperSizeSelect').addEventListener('change', function() {
                if (tinymce.activeEditor) applyPaperSettings(tinymce.activeEditor, this.value);
            });

            document.getElementById('toggleKop').addEventListener('change', function() {
                if (tinymce.activeEditor) applyKopPadding(tinymce.activeEditor, this.checked);
            });

            // LOGIKA SEARCH DI MODAL
            document.getElementById('searchVariable').addEventListener('keyup', function() {
                const value = this.value.toLowerCase();
                const chips = document.querySelectorAll('.modal-var-chip');
                let foundAny = false;

                chips.forEach(chip => {
                    const text = chip.getAttribute('data-search');
                    if (text.includes(value)) {
                        chip.style.display = "inline-flex";
                        foundAny = true;
                    } else {
                        chip.style.display = "none";
                    }
                });

                // Tampilkan pesan jika tidak ada hasil
                const noResult = document.getElementById('noResultMsg');
                noResult.className = foundAny ? "d-none" : "text-center py-5";
            });

            // Modal Delete
            const deleteModalEl = document.getElementById('deleteModal');
            if (deleteModalEl) {
                deleteModalEl.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const actionUrl = button.getAttribute('data-action');
                    deleteModalEl.querySelector('#deleteForm').setAttribute('action', actionUrl);
                });
            }
        </script>
    @endpush
@endsection