@extends('layouts.admin')

@section('content')
    <style>
        /* =========================================
                   1. DEFINISI FONT & STYLE (Sesuai Editor)
                ========================================= */
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

        /* Alignment */
        .ql-align-center {
            text-align: center;
        }

        .ql-align-right {
            text-align: right;
        }

        .ql-align-justify {
            text-align: justify;
        }

        /* =========================================
                   2. TABEL LAYOUT (INVISIBLE / TANPA GARIS)
                ========================================= */
        /* Kita hilangkan border tabel di preview & print */
        #area_surat table {
            width: 100%;
            border-collapse: collapse;
            border: none !important;
            /* Hapus garis luar */
        }

        #area_surat td {
            border: none !important;
            /* Hapus garis sel */
            padding: 2px 4px;
            /* Spasi dikit biar rapi */
            vertical-align: top;
        }

        /* =========================================
                   3. SETTING AREA KERTAS
                ========================================= */
        #area_surat {
            text-align: initial;
            box-sizing: border-box;
            white-space: pre-wrap !important;
            /* Agar spasi & enter terbaca */
            font-family: 'Times New Roman', serif;
            /* Default font */
            font-size: 12pt;
            /* Default size */
        }

        #area_surat p {
            margin: 0;
            /* Reset margin paragraf Quill */
            padding: 0;
        }

        /* Spacer Visual (Kop) */
        #visual_spacer {
            width: 100%;
            height: 3cm;
            background-color: #f8f9fa;
            border-bottom: 1px dashed #ccc;
            margin-bottom: 0px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #999;
            font-size: 10pt;
            font-style: italic;
            user-select: none;
        }

        /* Media Print */
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
        }
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">Formulir Cetak Surat Siswa</h5>
                <button type="submit" form="formSurat" class="btn btn-primary">
                    <i class="bx bx-search-alt"></i> Tampilkan
                </button>
            </div>

            <div class="card-body">
                {{-- FORM FILTER --}}
                <form id="formSurat" action="{{ route('admin.administrasi.surat-keluar-siswa.store') }}" method="POST">
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
                            <label class="form-label fw-bold">Kelas</label>
                            <select id="select_kelas" class="form-select">
                                <option value="">- Pilih -</option>
                                @foreach ($kelasList as $kelas)
                                    <option value="{{ $kelas }}" {{ old('kelas_old') == $kelas ? 'selected' : '' }}>
                                        {{ $kelas }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" name="kelas_old" id="kelas_old" value="{{ old('kelas_old') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Jenis Surat</label>
                            <select name="tipe_surat_id" class="form-select" required>
                                <option value="">- Pilih Surat -</option>
                                @foreach ($tipeSurats as $tipe)
                                    <option value="{{ $tipe->id }}"
                                        {{ old('tipe_surat_id') == $tipe->id ? 'selected' : '' }}>{{ $tipe->judul_surat }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Siswa</label>
                            <select name="siswa_id" id="select_siswa" class="form-select" disabled required>
                                <option>- Pilih Kelas Terlebih Dahulu -</option>
                            </select>
                        </div>
                    </div>
                </form>

                <div class="alert alert-info mt-4 mb-0" style="background:#00BFF3;color:white;">
                    <i class="bx bx-info-circle me-1"></i> Silahkan pilih data lalu klik tombol **Tampilkan**.
                </div>

                {{-- AREA PREVIEW SURAT --}}
                @if (session('preview_surat'))
                    @php
                        $setting = session('template_setting');
                        $kertasDB = $setting->ukuran_kertas ?? 'A4';

                        // Default Fallback
                        $width = '210mm';
                        $minHeight = '297mm';

                        if ($kertasDB == 'F4') {
                            $width = '215mm';
                            $minHeight = '330mm';
                        }
                        if ($kertasDB == 'Legal') {
                            $width = '216mm';
                            $minHeight = '356mm';
                        }
                        if ($kertasDB == 'Letter') {
                            $width = '216mm';
                            $minHeight = '279mm';
                        }
                    @endphp

                    <hr class="my-4">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0 text-primary"><i class="bx bx-file"></i> Preview Surat</h5>
                        <div class="d-flex align-items-center gap-3">
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" id="toggleKop" checked
                                    style="cursor: pointer;">
                                <label class="form-check-label fw-bold" for="toggleKop">Pakai Jarak Kop?</label>
                            </div>
                            <button onclick="printDiv('area_surat')" class="btn btn-success"><i class="bx bx-printer"></i>
                                Cetak Surat</button>
                        </div>
                    </div>

                    {{-- Info Kertas --}}
                    <div class="card bg-light border mb-3">
                        <div class="card-body py-2">
                            <div class="row align-items-center g-2">
                                <div class="col-auto">
                                    <span class="badge bg-label-primary">Kertas: {{ $kertasDB }}</span>
                                </div>
                                <div class="col-auto">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text fw-bold">Margin (mm)</span>
                                        <input type="number" id="paper_margin" class="form-control" value="20"
                                            min="0" style="width: 60px;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Container Preview --}}
                    <div class="card bg-secondary bg-opacity-10 p-4 rounded-3 text-center overflow-auto">
                        <div id="area_surat"
                            style="
                                background: white;
                                width: {{ $width }}; 
                                min-height: {{ $minHeight }};
                                height: auto; 
                                margin: 0 auto;
                                padding: 20mm;
                                text-align: left;
                                box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
                                color: black;
                                line-height: 1.5;
                            ">
                            {!! session('preview_surat') !!}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Logika AJAX Siswa (Sama seperti sebelumnya)
            $('#select_kelas').change(function() {
                var namaRombel = $(this).val();
                var siswaSelect = $('#select_siswa');
                $('#kelas_old').val(namaRombel);

                if (namaRombel) {
                    siswaSelect.html('<option>Loading...</option>').prop('disabled', true);
                    var url = "{{ route('admin.administrasi.get-siswa-by-kelas', ':nama_rombel') }}";
                    url = url.replace(':nama_rombel', encodeURIComponent(namaRombel));

                    $.ajax({
                        url: url,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            siswaSelect.empty().append(
                                '<option value="">-- Pilih Siswa --</option>');
                            if (data.length > 0) {
                                $.each(data, function(key, value) {
                                    var selected = (value.id ==
                                        "{{ old('siswa_id') }}") ? 'selected' : '';
                                    siswaSelect.append('<option value="' + value.id +
                                        '" ' + selected + '>' + value.nama +
                                        '</option>');
                                });
                                siswaSelect.prop('disabled', false);
                            } else {
                                siswaSelect.append('<option>Tidak ada siswa</option>');
                            }
                        }
                    });
                } else {
                    siswaSelect.empty().append('<option>- Pilih Kelas Terlebih Dahulu -</option>').prop(
                        'disabled', true);
                }
            });

            var oldKelas = "{{ old('kelas_old') }}";
            if (oldKelas && $('#select_kelas').val() == "") {
                $('#select_kelas').val(oldKelas).trigger('change');
            }

            // Update Margin Realtime
            $('#paper_margin').on('input change', function() {
                $('#area_surat').css('padding', $(this).val() + 'mm');
            });

            // Logika Spacer Kop
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
            if ($('#toggleKop').length > 0) {
                updatePreviewSpacer();
            }
        });

        // FUNGSI CETAK DENGAN STYLE LENGKAP
        function printDiv(divId) {
            var contentClone = document.getElementById(divId).cloneNode(true);
            var useKop = document.getElementById('toggleKop').checked;

            var currentWidth = $('#area_surat').css('width');
            var currentPadding = $('#area_surat').css('padding');

            var existingVisualSpacer = contentClone.querySelector('#visual_spacer');
            if (existingVisualSpacer) {
                existingVisualSpacer.remove();
            }

            // Ganti visual spacer dengan div kosong transparan untuk print
            var spacerHtml = useKop ? '<div style="width: 100%; height: 3cm; display: block;"></div>' : '';

            // Ambil CSS Framework (Bootstrap dll)
            var stylesHtml = '';
            $('link[rel="stylesheet"]').each(function() {
                stylesHtml += '<link rel="stylesheet" href="' + $(this).attr('href') + '">';
            });

            // CSS Khusus Print (Font, Tabel Invisible, Layout)
            var customCss = `
                <style>
                    body { background-color: white !important; -webkit-print-color-adjust: exact; }
                    
                    /* Wrapper Halaman */
                    #print-wrapper {
                        width: ${currentWidth} !important; 
                        margin: 0 auto;
                        padding: ${currentPadding} !important;
                        box-sizing: border-box;
                        overflow: hidden;
                        white-space: pre-wrap !important;
                        font-family: 'Times New Roman', serif; /* Font Default */
                        font-size: 12pt;
                        line-height: 1.5;
                    }
                    
                    /* Reset Margin Paragraph */
                    p { margin: 0; padding: 0; }

                    @page { size: auto; margin: 0mm; }
                    
                    /* --- STYLE FONT DARI EDITOR --- */
                    .ql-font-times-new-roman { font-family: 'Times New Roman', Times, serif !important; }
                    .ql-font-arial { font-family: Arial, Helvetica, sans-serif !important; }
                    .ql-font-courier-new { font-family: 'Courier New', Courier, monospace !important; }
                    .ql-font-calibri { font-family: 'Calibri', sans-serif !important; }
                    .ql-font-verdana { font-family: Verdana, Geneva, sans-serif !important; }

                    /* --- ALIGNMENT --- */
                    .ql-align-center { text-align: center !important; }
                    .ql-align-right { text-align: right !important; }
                    .ql-align-justify { text-align: justify !important; }

                    /* --- TABEL INVISIBLE (LAYOUTING) --- */
                    table { 
                        width: 100% !important; 
                        border-collapse: collapse !important; 
                        border: none !important; /* HILANGKAN BORDER */
                    }
                    td { 
                        border: none !important; /* HILANGKAN BORDER CELL */
                        padding: 2px 4px !important;
                        vertical-align: top;
                    }
                </style>
            `;

            var w = window.open('', '', 'height=800,width=1000');
            w.document.write('<html><head><title>Cetak Surat</title>' + stylesHtml + customCss + '</head><body>');
            w.document.write('<div id="print-wrapper">' + spacerHtml + contentClone.innerHTML + '</div>');
            w.document.write('</body></html>');
            w.document.close();
            w.focus();

            // Delay sedikit untuk memastikan style terload
            setTimeout(function() {
                w.print();
                w.close();
            }, 1000);
        }
    </script>
@endsection
