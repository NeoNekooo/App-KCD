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

        /* Preview Desk Style */
        .preview-desk {
            background-color: #525659;
            padding: 50px 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            overflow-y: auto;
        }

        .paper-sheet {
            background-color: white;
            margin-bottom: 30px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
            color: #000;
            font-family: 'Times New Roman', serif;
            font-size: 12pt;
            line-height: 1.5;
            text-align: justify;
            box-sizing: border-box;
            position: relative;
            overflow: hidden;
        }

        .paper-sheet p {
            margin-top: 0;
            margin-bottom: 1rem;
        }

        .paper-sheet table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 10px;
            table-layout: fixed;
        }

        .paper-sheet td,
        .paper-sheet th {
            padding: 3px;
            border: 1px solid #000;
            vertical-align: top;
            word-wrap: break-word;
        }

        .paper-sheet table[border="0"] td {
            border: 1px dotted #e0e0e0;
        }

        /* Sembunyikan elemen alert/toast bawaan */
        .alert,
        .toast {
            display: none !important;
        }
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Administrasi /</span> Cetak Surat Siswa</h4>

        {{-- 1. FORM FILTER --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="mb-0 fw-bold text-primary"><i class="bx bx-filter-alt me-2"></i> Filter & Cetak Surat</h5>
            </div>
            <div class="card-body mt-4">
                <form id="formSuratSiswa" action="{{ route('admin.administrasi.surat-keluar-siswa.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="tanggal_surat" value="{{ date('Y-m-d') }}">

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tahun Pelajaran</label>
                            <input type="text" class="form-control bg-light"
                                value="{{ $tapelAktif ? $tapelAktif->tahun_ajaran : '-' }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Jenis Template Surat</label>
                            <select name="tipe_surat_id" class="form-select" required>
                                <option value="">- Pilih Template -</option>
                                @foreach ($tipeSurats as $tipe)
                                    <option value="{{ $tipe->id }}"
                                        {{ old('tipe_surat_id') == $tipe->id ? 'selected' : '' }}>
                                        {{ $tipe->judul_surat }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Kelas</label>
                            <select id="select_kelas" class="form-select">
                                <option value="">- Pilih Kelas -</option>
                                @foreach ($kelasList as $kelas)
                                    <option value="{{ $kelas }}" {{ old('kelas_old') == $kelas ? 'selected' : '' }}>
                                        {{ $kelas }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" name="kelas_old" id="kelas_old" value="{{ old('kelas_old') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nama Siswa</label>
                            <select name="siswa_id" id="select_siswa" class="form-select" disabled required>
                                <option value="">- Pilih Kelas Dulu -</option>
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

                // Config Margin & Kertas
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
                        <small class="text-muted">
                            Total: {{ count(session('preview_pages')) }} Halaman |
                            Kertas: {{ $uk }}
                        </small>
                    </div>

                    {{-- FORM ACTION GANDA (PDF / CETAK RESMI) --}}
                    <form method="POST">
                        @csrf
                        <input type="hidden" name="tipe_surat_id" value="{{ old('tipe_surat_id') }}">
                        <input type="hidden" name="siswa_id" value="{{ old('siswa_id') }}">
                        <input type="hidden" name="tanggal_surat" value="{{ old('tanggal_surat') }}">

                        {{-- Raw content --}}
                        <textarea name="html_content" style="display:none;">{{ session('full_content_raw') }}</textarea>

                        <div class="btn-group shadow-sm">
                            {{-- TOMBOL PDF (PREVIEW) --}}
                            <button type="submit" formaction="{{ route('admin.administrasi.surat-keluar-siswa.pdf') }}"
                                formtarget="_blank" class="btn btn-danger px-4">
                                <i class='bx bxs-file-pdf me-1'></i> PDF Preview
                            </button>

                            {{-- TOMBOL CETAK RESMI --}}
                            <button type="submit" formaction="{{ route('admin.administrasi.surat-keluar-siswa.cetak') }}"
                                class="btn btn-success px-4">
                                <i class='bx bx-printer me-1'></i> Cetak Resmi
                            </button>
                        </div>
                    </form>
                </div>

                <div class="card-body p-0">
                    <div class="preview-desk">
                        @foreach (session('preview_pages') as $index => $pageContent)
                            <div style="position: relative;">
                                <div class="paper-sheet"
                                    style="
                                        width: {{ $pW }}; 
                                        height: {{ $pH }}mm;
                                        padding: {{ $mt }}mm {{ $mr }}mm {{ $mb }}mm {{ $ml }}mm;
                                     ">
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
            // HAPUS ELEMEN TOAST/ALERT JIKA MASIH MUNCUL
            $(document).ready(function() {
                $('.alert').remove();
                $('#action-toast').remove();

                // AJAX Siswa
                $('#select_kelas').change(function() {
                    var namaRombel = $(this).val();
                    var siswaSelect = $('#select_siswa');
                    $('#kelas_old').val(namaRombel);

                    if (namaRombel) {
                        siswaSelect.prop('disabled', true).html('<option>Memuat...</option>');
                        $.get("{{ url('admin/administrasi/get-siswa-by-kelas') }}/" + encodeURIComponent(
                            namaRombel), function(data) {
                            siswaSelect.empty().append('<option value="">-- Pilih Siswa --</option>');
                            $.each(data, function(k, v) {
                                var sel = (v.id == "{{ old('siswa_id') }}") ? 'selected' : '';
                                siswaSelect.append('<option value="' + v.id + '" ' + sel + '>' +
                                    v.nama + '</option>');
                            });
                            siswaSelect.prop('disabled', false);
                        });
                    }
                });
                if ($('#select_kelas').val()) $('#select_kelas').trigger('change');
            });

            // 2. AUTO SCROLL KE PREVIEW
            @if (session('preview_pages'))
                document.getElementById("previewSection").scrollIntoView({
                    behavior: "smooth"
                });
            @endif

            // 3. POPUP PRINT OTOMATIS (FIX PAGE BREAK + CLEANUP)
            @if (session('auto_print_content') && session('print_margins'))
                window.onload = function() {
                    var rawContent = {!! json_encode(session('auto_print_content')) !!};
                    var margins = {!! json_encode(session('print_margins')) !!};

                    // === CLEANUP LOGIC ===
                    rawContent = rawContent.replace(/^(<p>(&nbsp;|\s|<br>)*<\/p>\s*)+/gi, '');
                    rawContent = rawContent.replace(/(<p>(&nbsp;|\s|<br>)*<\/p>\s*)+$/gi, '');

                    var fullHtml = `
                        <!DOCTYPE html>
                        <html>
                        <head>
                            <title>Cetak Surat</title>
                            <style>
                                * { box-sizing: border-box; }
                                html, body { margin: 0; padding: 0; width: 100%; height: auto !important; font-family: 'Times New Roman', serif; font-size: 12pt; line-height: 1.5; color: #000; }
                                p { margin-top: 0; margin-bottom: 0.8rem; }
                                @media print {
                                    @page {
                                        size: ` + margins.paper + `; 
                                        margin: ` + margins.top + `mm ` + margins.right + `mm ` + margins.bottom +
                        `mm ` + margins.left + `mm;
                                    }
                                    .mce-pagebreak { display: block !important; page-break-before: always !important; break-before: page !important; height: 0px !important; width: 100% !important; border: none !important; margin: 0 !important; padding: 0 !important; visibility: hidden; }
                                    table { width: 100%; border-collapse: collapse; }
                                    td, th { border: 1px solid black; padding: 3px; vertical-align: top; }
                                    table[border="0"] td { border: none !important; }
                                }
                            </style>
                        </head>
                        <body>` + rawContent + `</body>
                        </html>
                    `;

                    var printWindow = window.open('', '_blank', 'height=800,width=1000');
                    printWindow.document.write(fullHtml);
                    printWindow.document.close();

                    setTimeout(function() {
                        printWindow.focus();
                        printWindow.print();
                    }, 1000);
                };
            @endif
        </script>
    @endpush
@endsection
