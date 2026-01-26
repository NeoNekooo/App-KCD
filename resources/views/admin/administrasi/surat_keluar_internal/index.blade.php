@extends('layouts.admin')

@section('content')
    {{-- === TINYMCE LOKAL === --}}
    <script src="{{ asset('js/tinymce.min.js') }}"></script>

    <style>
        /* UI Enhancements */
        .tox-promotion, .tox-statusbar__branding { display: none !important; }
        .tox-tinymce { border: 1px solid #d9dee3 !important; border-radius: 8px !important; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05); width: 100% !important; margin-bottom: 0 !important; }
        .tox-statusbar { border-top: 1px solid #ccc !important; }
        .tox-toolbar__primary { position: sticky; top: 0; z-index: 10; }
        
        /* Preview Desk Style */
        .preview-desk { background-color: #525659; padding: 50px 0; display: flex; flex-direction: column; align-items: center; min-height: 100vh; overflow-y: auto; }
        .paper-sheet { background-color: white; margin-bottom: 30px; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5); color: #000; font-family: 'Times New Roman', serif; font-size: 12pt; line-height: 1.5; text-align: justify; box-sizing: border-box; position: relative; overflow: hidden; }
        .paper-sheet p { margin-top: 0; margin-bottom: 1rem; }
        .paper-sheet table { border-collapse: collapse; width: 100%; margin-bottom: 10px; table-layout: fixed; }
        .paper-sheet td, .paper-sheet th { padding: 3px; border: 1px solid #000; vertical-align: top; word-wrap: break-word; }
        .paper-sheet table[border="0"] td { border: 1px dotted #e0e0e0; }
        
        .alert-custom-hidden { display: none !important; }
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Administrasi /</span> Cetak Surat Internal</h4>

        {{-- 🔥 TAMPILKAN ERROR VALIDASI 🔥 --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class='bx bx-error-circle fs-4 me-2'></i>
                    <div>
                        <strong>Mohon Maaf, ada data yang kurang:</strong>
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- FORM FILTER --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="mb-0 fw-bold text-primary"><i class="bx bx-filter-alt me-2"></i> Filter & Cetak Surat Internal</h5>
            </div>
            <div class="card-body mt-4">
                <form id="formSuratInternal" action="{{ route('admin.administrasi.surat-keluar-internal.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="tanggal_surat" value="{{ date('Y-m-d') }}">

                    <div class="row g-3 mb-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Jenis Template Surat <span class="text-danger">*</span></label>
                            <select name="tipe_surat_id" class="form-select @error('tipe_surat_id') is-invalid @enderror" required>
                                <option value="">- Pilih Template Internal -</option>
                                @foreach ($tipeSurats as $tipe)
                                    <option value="{{ $tipe->id }}" {{ old('tipe_surat_id') == $tipe->id ? 'selected' : '' }}>
                                        {{ $tipe->judul_surat }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- PILIH TARGET (PEGAWAI / INSTANSI) --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold d-block">Tujuan Surat:</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="target_type" id="radioPegawai" value="pegawai" 
                                {{ old('target_type', 'pegawai') == 'pegawai' ? 'checked' : '' }} onchange="toggleTarget()">
                            <label class="form-check-label" for="radioPegawai">Pegawai KCD</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="target_type" id="radioInstansi" value="instansi" 
                                {{ old('target_type') == 'instansi' ? 'checked' : '' }} onchange="toggleTarget()">
                            <label class="form-check-label" for="radioInstansi">Instansi Luar</label>
                        </div>
                    </div>

                    <div class="row g-3">
                        {{-- SELECT PEGAWAI --}}
                        <div class="col-md-12" id="divPegawai">
                            <label class="form-label fw-bold">Pilih Pegawai <span class="text-danger">*</span></label>
                            {{-- name="target_id" langsung di sini --}}
                            <select name="target_id" id="selectPegawai" class="form-select select2">
                                <option value="">- Cari Nama Pegawai -</option>
                                @foreach ($pegawaiList as $p)
                                    <option value="{{ $p->id }}" {{ old('target_id') == $p->id ? 'selected' : '' }}>
                                        {{ $p->nama }} {{ $p->nip ? '(' . $p->nip . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- SELECT INSTANSI --}}
                        <div class="col-md-12 d-none" id="divInstansi">
                            <label class="form-label fw-bold">Pilih Instansi <span class="text-danger">*</span></label>
                            {{-- name="target_id" juga, tapi nanti di-disable kalau ga dipake --}}
                            <select name="target_id" id="selectInstansi" class="form-select select2" disabled>
                                <option value="">- Cari Nama Instansi -</option>
                                @foreach ($instansiList as $i)
                                    <option value="{{ $i->id }}" {{ old('target_id') == $i->id ? 'selected' : '' }}>
                                        {{ $i->nama_instansi }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mt-4 text-end">
                        <button type="submit" class="btn btn-primary shadow-sm px-4">
                            <i class='bx bx-show me-1'></i> Tampilkan Preview
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- 2. AREA PREVIEW --}}
        @if (session('preview_pages'))
            @php
                $setting = session('template_setting');
                $mt = $setting->margin_top ?? 20;
                $mr = $setting->margin_right ?? 25;
                $mb = $setting->margin_bottom ?? 20;
                $ml = $setting->margin_left ?? 25;
                
                $paperMap = [
                    'A4' => ['w' => '210mm', 'h' => 297],
                    'F4' => ['w' => '215mm', 'h' => 330],
                    'Legal' => ['w' => '216mm', 'h' => 356],
                    'Letter' => ['w' => '216mm', 'h' => 279],
                ];
                $uk = $setting->ukuran_kertas ?? 'A4';
                $pW = $paperMap[$uk]['w'] ?? '210mm';
                $pH = $paperMap[$uk]['h'] ?? 297;
            @endphp

            <div class="card shadow-sm border-0" id="previewSection">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center sticky-top"
                    style="top: 70px; z-index: 100">
                    <div>
                        <h5 class="fw-bold mb-0 text-primary">Preview Dokumen</h5>
                        <small class="text-muted">Total: {{ count(session('preview_pages')) }} Halaman | Kertas: {{ $uk }}</small>
                    </div>

                    <form method="POST">
                        @csrf
                        <input type="hidden" name="tipe_surat_id" value="{{ old('tipe_surat_id', request('tipe_surat_id')) }}">
                        <input type="hidden" name="target_type" value="{{ old('target_type', request('target_type')) }}">
                        <input type="hidden" name="target_id" value="{{ old('target_id', request('target_id')) }}">
                        <input type="hidden" name="tanggal_surat" value="{{ old('tanggal_surat', request('tanggal_surat')) }}">
                        <textarea name="html_content" style="display:none;">{{ session('full_content_raw') }}</textarea>

                        {{-- 🔥 HANYA SATU TOMBOL CETAK (PDF) 🔥 --}}
                        <button type="submit" formaction="{{ route('admin.administrasi.surat-keluar-internal.pdf') }}"
                            formtarget="_blank" class="btn btn-danger px-4 rounded-pill shadow-sm">
                            <i class='bx bxs-file-pdf me-1'></i> Cetak Surat (PDF)
                        </button>
                    </form>
                </div>

                <div class="card-body p-0">
                    <div class="preview-desk">
                        @foreach (session('preview_pages') as $index => $pageContent)
                            <div style="position: relative;">
                                <div class="paper-sheet"
                                    style="width: {{ $pW }}; height: {{ $pH }}mm; padding: {{ $mt }}mm {{ $mr }}mm {{ $mb }}mm {{ $ml }}mm;">
                                    {!! $pageContent !!}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            // === LOGIC GANTI TARGET (TANPA JS SINKRONISASI RIBET) ===
            function toggleTarget() {
                var type = document.querySelector('input[name="target_type"]:checked').value;
                var divPegawai = document.getElementById('divPegawai');
                var divInstansi = document.getElementById('divInstansi');
                var selPegawai = $('#selectPegawai');
                var selInstansi = $('#selectInstansi');

                if (type === 'pegawai') {
                    // Tampilkan Pegawai
                    divPegawai.classList.remove('d-none');
                    divInstansi.classList.add('d-none');
                    
                    // Enable Pegawai (Biar dikirim ke server)
                    selPegawai.prop('disabled', false);
                    
                    // Disable Instansi (Biar GAK dikirim ke server)
                    selInstansi.prop('disabled', true);
                    
                    // Reset Instansi value
                    selInstansi.val("").trigger('change');
                } else {
                    // Tampilkan Instansi
                    divPegawai.classList.add('d-none');
                    divInstansi.classList.remove('d-none');
                    
                    // Disable Pegawai
                    selPegawai.prop('disabled', true);
                    
                    // Enable Instansi
                    selInstansi.prop('disabled', false);
                    
                    // Reset Pegawai value
                    selPegawai.val("").trigger('change');
                }
            }

            $(document).ready(function() {
                // Init select2
                if (typeof jQuery !== 'undefined' && jQuery('.select2').length) {
                    jQuery('.select2').select2({ theme: 'bootstrap-5', width: '100%' });
                }

                // Inisialisasi awal (Penting saat reload/back)
                var initialType = "{{ old('target_type', 'pegawai') }}";
                var initialId = "{{ old('target_id') }}"; // Nilai lama dari input

                if(initialType === 'instansi') {
                    $('#radioInstansi').prop('checked', true);
                    // Set value dulu sebelum toggle
                    $('#selectInstansi').val(initialId); 
                } else {
                    $('#radioPegawai').prop('checked', true);
                    $('#selectPegawai').val(initialId);
                }
                
                // Jalankan logika toggle untuk disable/enable yang benar
                toggleTarget();

                // Trigger change select2 biar UI nya update sesuai value
                if(initialType === 'instansi') {
                    $('#selectInstansi').trigger('change');
                } else {
                    $('#selectPegawai').trigger('change');
                }

                // AUTO SCROLL KE PREVIEW
                @if (session('preview_pages'))
                    setTimeout(function() {
                        var previewEl = document.getElementById("previewSection");
                        if (previewEl) {
                            previewEl.scrollIntoView({ behavior: "smooth", block: "start" });
                        }
                    }, 500);
                @endif
            });
        </script>
    @endpush
@endsection