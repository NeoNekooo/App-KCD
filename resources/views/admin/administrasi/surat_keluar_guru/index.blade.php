@extends('layouts.admin')

@section('content')
    {{-- 1. CSS Quill & Styling Kertas --}}
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">

    <style>
        /* =========================================
               1. DEFINISI FONT HELPER
               ========================================= */
        .ql-font-times-new-roman {
            font-family: 'Times New Roman', Times, serif !important;
        }

        .ql-font-arial {
            font-family: Arial, Helvetica, sans-serif !important;
        }

        .ql-font-courier-new {
            font-family: 'Courier New', Courier, monospace !important;
        }

        .ql-font-calibri {
            font-family: 'Calibri', sans-serif !important;
        }

        .ql-font-verdana {
            font-family: Verdana, Geneva, sans-serif !important;
        }

        .ql-font-tahoma {
            font-family: Tahoma, Geneva, sans-serif !important;
        }

        .ql-font-georgia {
            font-family: Georgia, serif !important;
        }

        /* =========================================
               2. SETTING KERTAS UTAMA
               ========================================= */
        .ql-editor {
            /* Font Default */
            font-family: 'Times New Roman', Times, serif !important;
            font-size: 12px;
            line-height: 1.42;
            color: #000;

            /* Padding Kertas */
            padding: 0.5cm 2.54cm 0.5cm 2.54cm !important;

            background: white;
            height: 100%;

            /* PERBAIKAN SCROLLBAR: */
            border: none !important;
            overflow: hidden !important;
            /* HIDE SEMUA SCROLLBAR */
            width: 100% !important;
            box-sizing: border-box;
        }

        /* RESET FONT ELEMENT (Agar Inline Style Menang) */
        .ql-editor h1,
        .ql-editor h2,
        .ql-editor h3,
        .ql-editor h4,
        .ql-editor h5,
        .ql-editor h6,
        .ql-editor p,
        .ql-editor div,
        .ql-editor li {
            font-family: unset !important;
        }

        /* Pastikan strong/bold tetap tebal */
        .ql-editor strong,
        .ql-editor b {
            font-weight: bold !important;
        }

        /* =========================================
               3. SISTEM PREVIEW
               ========================================= */
        #preview-wrapper-bg {
            background-color: #525659;
            padding: 40px 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 30px;
            overflow-x: auto;
            /* Scroll hanya di background abu-abu, bukan di kertas */
            width: 100%;
            min-height: 600px;
            border-radius: 8px;
            border: 1px solid #444;
        }

        .page-instance {
            background: white;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            position: relative;
            display: flex;
            flex-direction: column;
            margin-bottom: 0;
            transition: all 0.3s;
            overflow: hidden !important;
            /* Potong konten yang keluar kertas */
            box-sizing: border-box;
        }

        /* UKURAN KERTAS */
        .page-instance.paper-A4 {
            width: 210mm;
            height: 297mm;
        }

        .page-instance.paper-F4 {
            width: 215mm;
            height: 330mm;
        }

        .page-instance.paper-Legal {
            width: 216mm;
            height: 356mm;
        }

        .page-instance.paper-Letter {
            width: 216mm;
            height: 279mm;
        }

        /* LOGIKA KOP SURAT */
        .page-instance.has-kop:first-child .ql-editor {
            padding-top: 3.5cm !important;
            padding-bottom: 0cm !important;
        }

        /* =========================================
               4. TABLE FIXES (ANTI-SCROLL & NO BORDER)
               ========================================= */
        .ql-editor table {
            table-layout: fixed !important;
            width: 100% !important;
            /* Paksa tabel selebar kertas, jangan lebih */
            max-width: 100% !important;
            border-collapse: collapse;
            margin-bottom: 1em;
            border: none !important;
        }

        .ql-editor td {
            padding: 2px 4px;
            vertical-align: top;
            border: none !important;
            /* Hapus border */
            word-wrap: break-word;
            /* Paksa teks turun ke bawah jika kepanjangan */
            white-space: normal !important;
            /* Jangan biarkan teks memanjang ke samping */
            overflow-wrap: break-word;
        }

        /* =========================================
               5. MEDIA PRINT (HILANGKAN SCROLLBAR SAAT CETAK)
               ========================================= */
        @media print {
            body * {
                visibility: hidden;
            }

            #preview-wrapper-bg,
            #preview-wrapper-bg * {
                visibility: visible;
            }

            #preview-wrapper-bg {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                margin: 0;
                padding: 0;
                background: white;
                border: none;
                z-index: 99999;
                overflow: hidden !important;
                /* KUNCI: Matikan scrollbar saat print */
            }

            .page-instance {
                box-shadow: none;
                margin: 0;
                width: 100% !important;
                height: 100% !important;
                page-break-after: always;
                break-after: page;
                border: none;
                overflow: hidden !important;
                /* KUNCI: Matikan scrollbar kertas */
            }

            .ql-editor {
                overflow: hidden !important;
                /* KUNCI: Matikan scrollbar editor */
                width: 100% !important;
            }

            .page-instance:last-child {
                page-break-after: auto;
            }

            @page {
                size: auto;
                margin: 0mm;
            }

            .btn,
            .card-header,
            .alert,
            form,
            .layout-navbar,
            .layout-menu,
            nav,
            header,
            footer {
                display: none !important;
            }

            /* Sembunyikan scrollbar browser (Chrome/Safari) */
            ::-webkit-scrollbar {
                display: none;
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
                {{-- 2. FORM FILTER --}}
                <form id="formSurat" action="{{ route('admin.administrasi.surat-keluar-guru.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="tanggal_surat" value="{{ date('Y-m-d') }}">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tahun Pelajaran</label>
                            <input type="text" class="form-control"
                                value="{{ isset($tapelAktif) && $tapelAktif ? $tapelAktif->tahun_ajaran : '-' }}" readonly>
                            <input type="hidden" name="tapel_id"
                                value="{{ isset($tapelAktif) && $tapelAktif ? $tapelAktif->id : '' }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Jenis Surat</label>
                            <select name="tipe_surat_id" class="form-select" required>
                                <option value="">- Pilih Surat -</option>
                                @foreach ($tipeSurats as $tipe)
                                    <option value="{{ $tipe->id }}"
                                        {{ old('tipe_surat_id') == $tipe->id ? 'selected' : '' }}>
                                        {{ $tipe->judul_surat }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold">Guru / GTK</label>
                            <select name="gtk_id" class="form-select" required>
                                <option value="">- Pilih Guru -</option>
                                @foreach ($guruList as $g)
                                    <option value="{{ $g->id }}" {{ old('gtk_id') == $g->id ? 'selected' : '' }}>
                                        {{ $g->nama }} {{ $g->nip ? '(' . $g->nip . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>

                <div class="alert alert-info mt-4 mb-0" style="background:#00BFF3;color:white;">
                    <i class="bx bx-info-circle me-1"></i> Silahkan pilih data lalu klik tombol **Tampilkan**.
                </div>

                {{-- 3. AREA PREVIEW SURAT --}}
                @if (session('preview_surat'))
                    @php
                        $setting = session('template_setting');
                        $kertasDB = $setting->ukuran_kertas ?? 'A4';
                        $useKop = $setting->use_kop ?? 0;
                        $isiSuratRaw = session('preview_surat');
                        // Bersihkan entities font
                        $isiSuratRaw = str_replace('&quot;', "'", $isiSuratRaw);
                    @endphp

                    <hr class="my-4">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="fw-bold mb-1 text-primary"><i class="bx bx-file"></i> Preview Surat</h5>
                            <small class="text-muted">
                                Kertas: <b>{{ $kertasDB }}</b> |
                                Mode Kop: <b>{{ $useKop == 1 ? 'Aktif (Jarak 3.5cm)' : 'Non-Aktif' }}</b>
                            </small>
                        </div>
                        <button onclick="window.print()" class="btn btn-success">
                            <i class="bx bx-printer me-1"></i> Cetak / PDF
                        </button>
                    </div>

                    {{-- Wrapper Visual --}}
                    <div id="preview-wrapper-bg"></div>

                    {{-- Data Hidden untuk JS --}}
                    <textarea id="raw_html_content" style="display:none;">{!! $isiSuratRaw !!}</textarea>
                    <input type="hidden" id="paper_size_val" value="{{ $kertasDB }}">
                    <input type="hidden" id="use_kop_val" value="{{ $useKop }}">
                @endif
            </div>
        </div>
    </div>

    {{-- 4. JAVASCRIPT --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>

    <script>
        $(document).ready(function() {
            const rawContent = $('#raw_html_content').val();
            const paperSize = $('#paper_size_val').val();
            const useKop = $('#use_kop_val').val();
            const wrapper = document.getElementById('preview-wrapper-bg');
            const pageDivider = '<div class="page-break-divider"></div>';

            if (rawContent) {
                let pages = [];
                if (rawContent.includes('page-break-divider')) {
                    pages = rawContent.split(pageDivider);
                } else {
                    pages = [rawContent];
                }

                pages.forEach((pageHtml, index) => {
                    if (pageHtml.trim() === '') return;

                    const pageDiv = document.createElement('div');
                    pageDiv.className = `page-instance paper-${paperSize}`;

                    if (index === 0 && useKop == '1') {
                        pageDiv.classList.add('has-kop');
                    }

                    const contentDiv = `<div class="ql-editor">${pageHtml}</div>`;
                    pageDiv.innerHTML = contentDiv;
                    wrapper.appendChild(pageDiv);

                    setTimeout(() => {
                        restoreTableWidthsInPreview(pageDiv);
                    }, 50);
                });
            }

            function restoreTableWidthsInPreview(container) {
                const tables = container.querySelectorAll('table');
                tables.forEach(table => {
                    table.style.tableLayout = 'fixed';
                    table.style.width = '100%'; // Pastikan tabel selalu 100%
                    const firstRow = table.querySelector('tr');
                    if (firstRow) {
                        Array.from(firstRow.cells).forEach(cell => {
                            const w = cell.style.width || cell.getAttribute('width');
                            if (w) cell.style.width = w;
                        });
                    }
                });
            }
        });
    </script>
@endsection
