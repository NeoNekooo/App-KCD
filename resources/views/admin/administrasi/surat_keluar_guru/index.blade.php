@extends('layouts.admin')

@section('content')
    {{-- CSS Tambahan --}}
    <style>
        /* FIX ALIGNMENT QUILL */
        #area_surat .ql-align-center {
            text-align: center !important;
        }

        #area_surat .ql-align-right {
            text-align: right !important;
        }

        #area_surat .ql-align-justify {
            text-align: justify !important;
        }

        #area_surat [style*="text-align: center"] {
            text-align: center !important;
        }

        #area_surat [style*="text-align: right"] {
            text-align: right !important;
        }

        #area_surat [style*="text-align: justify"] {
            text-align: justify !important;
        }

        #area_surat {
            text-align: initial !important;
            /* Font Family dihandle oleh Javascript agar dinamis */
        }

        /* Style untuk Spacer Visual di Preview */
        #visual_spacer {
            width: 100%;
            height: 3cm;
            /* Sesuaikan tinggi kop */
            background-color: #f8f9fa;
            /* Warna abu tipis agar terlihat areanya */
            border-bottom: 1px dashed #ccc;
            /* Garis putus-putus penanda batas */
            margin-bottom: 0px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #999;
            font-size: 10pt;
            font-style: italic;
            user-select: none;
        }

        /* CSS Khusus Cetak */
        @media print {
            @page {
                size: auto;
                margin: 0mm;
            }

            body {
                margin: 0px;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            #area_surat {
                width: 100% !important;
                height: 100% !important;
                box-shadow: none !important;
                margin: 0 !important;
                /* Perbaikan agar pas tengah */
                box-sizing: border-box !important;
                overflow: visible !important;
                /* Font Family diurus JS saat window print dibuka */
            }
        }
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">Formulir Cetak Surat Guru</h5>
                <button type="submit" form="formSurat" class="btn btn-primary">
                    <i class="bx bx-search-alt"></i> Tampilkan
                </button>
            </div>

            <div class="card-body">

                {{-- FORM --}}
                <form id="formSurat" action="{{ route('admin.administrasi.surat-keluar-guru.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="tanggal_surat" value="{{ date('Y-m-d') }}">

                    <div class="row g-3">
                        {{-- Tahun Pelajaran --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tahun Pelajaran</label>
                            <input type="text" class="form-control"
                                value="{{ $tapelAktif->tahun_ajaran ?? 'Tidak ada tapel aktif' }}" readonly>
                            <input type="hidden" name="tapel_id" value="{{ $tapelAktif->id ?? '' }}">
                        </div>

                        {{-- Jenis Surat --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Jenis Surat</label>
                            <select name="tipe_surat_id" class="form-select" required>
                                <option value="">- Pilih Surat -</option>
                                @foreach ($tipeSurats as $tipe)
                                    <option value="{{ $tipe->id }}">
                                        {{ $tipe->judul_surat }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Guru --}}
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Guru / GTK</label>
                            <select name="gtk_id" class="form-select" required>
                                <option value="">- Pilih Guru -</option>
                                @foreach ($guruList as $g)
                                    <option value="{{ $g->id }}">
                                        {{ $g->nama }} @if ($g->nip)
                                            ({{ $g->nip }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>

                {{-- BOX INFORMASI --}}
                <div class="alert alert-info mt-4 mb-0" style="background:#00BFF3;color:white;">
                    <i class="bx bx-info-circle me-1"></i>
                    Silahkan pilih **JENIS SURAT** dan **NAMA GURU** lalu klik tombol **Tampilkan**.
                </div>

                {{-- PREVIEW --}}
                @if (session('preview_surat'))
                    <hr class="my-4">

                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <h5 class="fw-bold mb-0 text-primary">
                            <i class="bx bx-file"></i> Preview Surat
                        </h5>

                        <div class="d-flex align-items-center gap-3">
                            {{-- TOGGLE KOP SURAT --}}
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" id="toggleKop" checked
                                    style="cursor: pointer;">
                                <label class="form-check-label fw-bold" for="toggleKop" style="cursor: pointer;">
                                    Pakai Jarak Kop?
                                </label>
                            </div>

                            {{-- Tombol Cetak --}}
                            <button onclick="printDiv('area_surat')" class="btn btn-success">
                                <i class="bx bx-printer"></i> Cetak Surat
                            </button>
                        </div>
                    </div>

                    {{-- Toolbar Kertas --}}
                    <div class="card bg-light border mb-3">
                        <div class="card-body py-2">
                            <div class="row align-items-center g-2">
                                <div class="col-auto fw-bold text-muted small"><i class="bx bx-cog"></i> Atur Kertas:</div>

                                {{-- 1. Ukuran Kertas --}}
                                <div class="col-auto">
                                    <select id="paper_size" class="form-select form-select-sm" title="Ukuran Kertas">
                                        <option value="A4">A4 (210 x 297 mm)</option>
                                        <option value="F4">F4/Folio (215 x 330 mm)</option>
                                        <option value="Legal">Legal (216 x 356 mm)</option>
                                        <option value="Letter">Letter (216 x 279 mm)</option>
                                    </select>
                                </div>

                                {{-- 2. Warna Kertas --}}
                                <div class="col-auto">
                                    <div class="input-group input-group-sm" title="Warna Latar Kertas">
                                        <span class="input-group-text"><i class='bx bxs-color-fill'></i></span>
                                        <input type="color" id="paper_color" class="form-control form-control-color"
                                            value="#ffffff" style="max-width: 50px;">
                                    </div>
                                </div>

                                {{-- 3. Margin --}}
                                <div class="col-auto">
                                    <div class="input-group input-group-sm" title="Margin Kertas">
                                        <span class="input-group-text">Margin (mm)</span>
                                        <input type="number" id="paper_margin" class="form-control" value="20"
                                            min="0" style="width: 70px;">
                                    </div>
                                </div>

                                {{-- 4. Jenis Font (NEW) --}}
                                <div class="col-auto">
                                    <div class="input-group input-group-sm" title="Jenis Font">
                                        <span class="input-group-text"><i class='bx bx-font-family'></i></span>
                                        <select id="font_family" class="form-select form-select-sm" style="width: 140px;">
                                            <option value="'Times New Roman', Times, serif" selected>Times New Roman
                                            </option>
                                            <option value="Arial, Helvetica, sans-serif">Arial</option>
                                            <option value="Tahoma, Verdana, sans-serif">Tahoma</option>
                                            <option value="'Courier New', Courier, monospace">Courier New</option>
                                            <option value="'Calibri', sans-serif">Calibri</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- 5. Ukuran Font --}}
                                <div class="col-auto">
                                    <div class="input-group input-group-sm" title="Ukuran Font">
                                        <span class="input-group-text">Font (pt)</span>
                                        <input type="number" id="font_size" class="form-control" value="12"
                                            min="8" max="72" style="width: 70px;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- SURAT --}}
                    <div class="card bg-secondary bg-opacity-10 p-4 rounded-3 text-center overflow-auto">
                        <div id="area_surat"
                            style="
                        background: white;
                        width: 210mm;
                        min-height: 297mm;
                        margin: 0 auto;
                        padding: 20mm;
                        text-align: left;
                        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
                        color: black;
                        /* font-family dihapus disini, pakai JS */
                        font-size: 12pt;
                        line-height: 1.5;
                        transition: all .3s ease;
                    ">
                            {!! session('preview_surat') !!}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- SCRIPT --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // 1. LOGIKA TOOLBAR (Sama seperti sebelumnya)
            function updatePaperSettings() {
                var size = $('#paper_size').val();
                var margin = $('#paper_margin').val() + 'mm';
                var color = $('#paper_color').val();
                var fontSize = $('#font_size').val() + 'pt';
                var fontFamily = $('#font_family').val();

                var width = '210mm';
                var height = '297mm';

                if (size === 'A4') {
                    width = '210mm';
                    height = '297mm';
                } else if (size === 'F4') {
                    width = '215mm';
                    height = '330mm';
                } else if (size === 'Legal') {
                    width = '216mm';
                    height = '356mm';
                } else if (size === 'Letter') {
                    width = '216mm';
                    height = '279mm';
                }

                $('#area_surat').css({
                    'width': width,
                    'min-height': height,
                    'padding': margin,
                    'background-color': color,
                    'font-size': fontSize,
                    'font-family': fontFamily
                });

                // Simpan lebar saat ini ke atribut data agar bisa dibaca saat print
                $('#area_surat').attr('data-print-width', width);
            }

            $('#paper_size, #paper_color, #paper_margin, #font_size, #font_family').on('input change',
                updatePaperSettings);
            updatePaperSettings();

            // 2. PREVIEW SPACER (Sama seperti sebelumnya)
            function updatePreviewSpacer() {
                var useKop = $('#toggleKop').is(':checked');
                var spacer = $('#visual_spacer');
                if (useKop) {
                    if (spacer.length === 0) $('#area_surat').prepend(
                        '<div id="visual_spacer">Area Kop Surat (3cm)</div>');
                    else spacer.show();
                } else {
                    if (spacer.length > 0) spacer.hide();
                }
            }
            $('#toggleKop').on('change', updatePreviewSpacer);
            if ($('#toggleKop').length > 0) updatePreviewSpacer();
        });

        // === FUNGSI CETAK YANG SUDAH DIPERBAIKI ===
        function printDiv(divId) {
            var contentClone = document.getElementById(divId).cloneNode(true);
            var useKop = document.getElementById('toggleKop').checked;

            var selectedFont = $('#font_family').val();
            var selectedFontSize = $('#font_size').val() + 'pt';
            // Ambil lebar kertas yang sedang aktif (misal: 210mm)
            var currentWidth = $('#area_surat').attr('data-print-width') || '210mm';

            // Bersihkan elemen visual spacer (garis putus-putus) dari hasil clone
            var existingVisualSpacer = contentClone.querySelector('#visual_spacer');
            if (existingVisualSpacer) existingVisualSpacer.remove();

            // Buat spacer transparan untuk print (jika pakai kop)
            var spacerHtml = '';
            if (useKop) {
                spacerHtml = '<div style="width: 100%; height: 3cm; display: block; background: transparent;"></div>';
            }

            // CSS Khusus Popup Print
            var finalCss = `
                /* Reset CSS Dasar */
                * { box-sizing: border-box; margin: 0; padding: 0; }
                
                body { 
                    background-color: white; 
                    -webkit-print-color-adjust: exact !important; 
                    print-color-adjust: exact !important;
                }

                /* Force Font & Size sesuai pilihan */
                body, .print-area, .print-area * { 
                    font-family: ${selectedFont} !important; 
                    font-size: ${selectedFontSize} !important; 
                }

                /* Area Cetak */
                .print-area {
                    width: ${currentWidth} !important; /* Paksa lebar sesuai kertas (misal A4) */
                    margin: 0 auto; 
                    padding: 0;
                    overflow: hidden; /* Cegah scrollbar memicu shrink */
                }

                /* Penanganan Quill Alignment */
                .ql-align-center { text-align: center !important; }
                .ql-align-right { text-align: right !important; }
                .ql-align-justify { text-align: justify !important; }

                /* Media Query Print */
                @media print {
                    @page { 
                        size: auto; 
                        margin: 0mm !important; 
                    }
                    body { margin: 0px !important; }
                    .print-area {
                        width: ${currentWidth} !important;
                        box-shadow: none !important;
                    }
                }
            `;

            // Buka Window Baru
            var w = window.open('', '', 'height=800,width=1000');

            // Tulis HTML Lengkap dengan DOCTYPE (PENTING!)
            w.document.write('<!DOCTYPE html>');
            w.document.write('<html><head><title>Cetak Surat</title>');
            w.document.write('<style>' + finalCss + '</style>');
            w.document.write('</head><body>');

            // Masukkan Konten
            // Kita ambil style padding dari elemen asli, tapi width kita atur lewat class .print-area
            var originalPadding = $('#area_surat').css('padding');

            w.document.write(`
                <div class="print-area" style="padding: ${originalPadding};">
                    ${spacerHtml}
                    ${contentClone.innerHTML}
                </div>
            `);

            w.document.write('</body></html>');
            w.document.close();
            w.focus();

            // Tunggu sebentar agar render font selesai
            setTimeout(function() {
                w.print();
                w.close();
            }, 500);
        }
    </script>
@endsection
