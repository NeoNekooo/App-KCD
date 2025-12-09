@extends('layouts.admin')

@section('content')

    {{-- CSS Tambahan --}}
    <style>
        /* FIX ALIGNMENT QUILL */
        #area_surat .ql-align-center { text-align: center !important; }
        #area_surat .ql-align-right { text-align: right !important; }
        #area_surat .ql-align-justify { text-align: justify !important; }

        #area_surat [style*="text-align: center"] { text-align: center !important; }
        #area_surat [style*="text-align: right"] { text-align: right !important; }
        #area_surat [style*="text-align: justify"] { text-align: justify !important; }

        #area_surat { 
            text-align: initial !important; 
            font-family: 'Times New Roman', Times, serif !important; /* Paksa Font di Preview */
        }

        /* Style untuk Spacer Visual di Preview */
        #visual_spacer {
            width: 100%;
            height: 3cm; /* Sesuaikan tinggi kop */
            background-color: #f8f9fa; /* Warna abu tipis agar terlihat areanya */
            border-bottom: 1px dashed #ccc; /* Garis putus-putus penanda batas */
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
            @page { size: auto; margin: 0mm; }
            body { margin: 0px; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            
            #area_surat { 
                width: 100% !important; 
                height: 100% !important; 
                box-shadow: none !important; 
                margin: 0 !important; 
                font-family: 'Times New Roman', Times, serif !important; /* Paksa Font saat Print */
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
                                value="{{ isset($tapelAktif) && $tapelAktif ? $tapelAktif->tahun_ajaran : 'Tidak ada tapel aktif' }}"
                                readonly>
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
                            <label class="form-label fw-bold">Semester / Jenis Surat</label>
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
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Siswa</label>
                            <select name="siswa_id" id="select_siswa" class="form-select" disabled required>
                                <option>- Pilih Kelas Terlebih Dahulu -</option>
                            </select>
                        </div>
                    </div>
                </form>

                <div class="alert alert-info mt-4 mb-0" style="background:#00BFF3;color:white;">
                    <i class="bx bx-info-circle me-1"></i>
                    Silahkan pilih **SURAT**, **KELAS** dan **NAMA SISWA** lalu klik tombol **Tampilkan**.
                </div>

                {{-- AREA PREVIEW SURAT --}}
                @if (session('preview_surat'))
                    <hr class="my-4">

                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <h5 class="fw-bold mb-0 text-primary">
                            <i class="bx bx-file"></i> Preview Surat
                        </h5>

                        <div class="d-flex align-items-center gap-3">
                            {{-- TOGGLE KOP SURAT --}}
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" id="toggleKop" checked style="cursor: pointer;">
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

                    {{-- TOOLBAR PENGATURAN KERTAS --}}
                    <div class="card bg-light border mb-3">
                        <div class="card-body py-2">
                            <div class="row align-items-center g-2">
                                <div class="col-auto fw-bold text-muted small"><i class="bx bx-cog"></i> Atur Kertas:
                                </div>
                                <div class="col-auto">
                                    <select id="paper_size" class="form-select form-select-sm" title="Ukuran Kertas">
                                        <option value="A4">A4 (210 x 297 mm)</option>
                                        <option value="F4">F4/Folio (215 x 330 mm)</option>
                                        <option value="Legal">Legal (216 x 356 mm)</option>
                                        <option value="Letter">Letter (216 x 279 mm)</option>
                                    </select>
                                </div>
                                <div class="col-auto">
                                    <div class="input-group input-group-sm" title="Warna Latar Kertas">
                                        <span class="input-group-text"><i class='bx bxs-color-fill'></i></span>
                                        <input type="color" id="paper_color" class="form-control form-control-color"
                                            value="#ffffff" style="max-width: 50px;">
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <div class="input-group input-group-sm" title="Margin Kertas">
                                        <span class="input-group-text">Margin (mm)</span>
                                        <input type="number" id="paper_margin" class="form-control" value="20"
                                            min="0" style="width: 70px;">
                                    </div>
                                </div>
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

                    {{-- Container Preview --}}
                    <div class="card bg-secondary bg-opacity-10 p-4 rounded-3 text-center overflow-auto">
                        <div id="area_surat"
                            style="
                        background: white;
                        width: 210mm; 
                        min-height: 297mm;
                        margin: 0 auto;
                        padding: 20mm;
                        text-align: left;
                        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
                        color: black;
                        font-family: 'Times New Roman', Times, serif; /* Font default preview */
                        font-size: 12pt;
                        line-height: 1.5;
                        transition: all 0.3s ease;
                    ">
                            {!! session('preview_surat') !!}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

{{-- SCRIPT JAVASCRIPT --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // 1. LOGIKA TOOLBAR PENGATURAN KERTAS
            function updatePaperSettings() {
                var size = $('#paper_size').val();
                var margin = $('#paper_margin').val() + 'mm';
                var color = $('#paper_color').val();
                var fontSize = $('#font_size').val() + 'pt';
                var width = '210mm';
                var height = '297mm';

                if (size === 'A4') { width = '210mm'; height = '297mm'; }
                else if (size === 'F4') { width = '215mm'; height = '330mm'; }
                else if (size === 'Legal') { width = '216mm'; height = '356mm'; }
                else if (size === 'Letter') { width = '216mm'; height = '279mm'; }

                $('#area_surat').css({
                    'width': width, 'min-height': height, 'padding': margin,
                    'background-color': color, 'font-size': fontSize
                });
            }
            $('#paper_size, #paper_color, #paper_margin, #font_size').on('input change', updatePaperSettings);
            updatePaperSettings();

            // 2. LOGIKA AJAX SISWA
            $('#select_kelas').change(function() {
                var namaRombel = $(this).val();
                var siswaSelect = $('#select_siswa');
                $('#kelas_old').val(namaRombel);

                if (namaRombel) {
                    siswaSelect.html('<option>Loading...</option>').prop('disabled', true);
                    var url = "{{ route('admin.administrasi.get-siswa-by-kelas', ':nama_rombel') }}";
                    url = url.replace(':nama_rombel', encodeURIComponent(namaRombel));

                    $.ajax({
                        url: url, type: 'GET', dataType: 'json',
                        success: function(data) {
                            siswaSelect.empty().append('<option value="">-- Pilih Siswa --</option>');
                            if (data.length > 0) {
                                $.each(data, function(key, value) {
                                    var selected = (value.id == "{{ old('siswa_id') }}") ? 'selected' : '';
                                    siswaSelect.append('<option value="' + value.id + '" ' + selected + '>' + value.nama + '</option>');
                                });
                                siswaSelect.prop('disabled', false);
                            } else {
                                siswaSelect.append('<option>Tidak ada siswa</option>');
                            }
                        },
                        error: function() { siswaSelect.empty().append('<option>Error mengambil data</option>'); }
                    });
                } else {
                    siswaSelect.empty().append('<option>- Pilih Kelas Terlebih Dahulu -</option>').prop('disabled', true);
                }
            });

            var oldKelas = "{{ old('kelas_old') }}";
            if (oldKelas && $('#select_kelas').val() == "") {
                $('#select_kelas').val(oldKelas).trigger('change');
            } else if ($('#select_kelas').val() != "") {
                $('#select_kelas').trigger('change');
            }

            // 3. LOGIKA LIVE PREVIEW KOP SPACER
            function updatePreviewSpacer() {
                var useKop = $('#toggleKop').is(':checked');
                var spacer = $('#visual_spacer');
                
                if (useKop) {
                    if (spacer.length === 0) {
                        $('#area_surat').prepend('<div id="visual_spacer">Area Kop Surat (3cm)</div>');
                    } else {
                        spacer.show(); 
                    }
                } else {
                    if (spacer.length > 0) {
                        spacer.hide(); 
                    }
                }
            }
            $('#toggleKop').on('change', updatePreviewSpacer);
            updatePreviewSpacer();
        });

        // FUNGSI CETAK PERBAIKAN (FIX CENTER ALIGNMENT)
        function printDiv(divId) {
            var contentClone = document.getElementById(divId).cloneNode(true);
            
            var useKop = document.getElementById('toggleKop').checked;
            var tinggiKop = '3cm';

            var existingVisualSpacer = contentClone.querySelector('#visual_spacer');
            if(existingVisualSpacer) {
                existingVisualSpacer.remove();
            }

            var spacerHtml = '';
            if (useKop) {
                spacerHtml = '<div style="width: 100%; height: ' + tinggiKop + '; display: block; background: transparent;"></div>';
            }

            var currentStyle = document.getElementById(divId).getAttribute('style');
            currentStyle = currentStyle.replace(/width:\s*[^;]+;?/gi, ''); 

            var finalAlignmentCss =
                '.ql-align-center { text-align: center !important; }' +
                '.ql-align-right { text-align: right !important; }' +
                '.ql-align-justify { text-align: justify !important; }' +
                
                'body, #area_surat, #area_surat * { font-family: "Times New Roman", Times, serif !important; }' +

                '@media print {' +
                '  @page { size: A4 portrait; margin: 0mm !important; }' +
                '  body { margin: 0px !important; padding: 0px !important; }' +
                // PERBAIKAN UTAMA: Tambahkan box-sizing: border-box !important
                '  #area_surat { box-sizing: border-box !important; box-shadow: none !important; margin: 0 !important; overflow: visible !important; width: 100% !important; }' +
                '}' +
                '#area_surat * { margin: 0; padding: 0; }';

            var w = window.open('', '', 'height=600,width=800');
            w.document.write('<html><head><title>Cetak Surat Siswa</title>');
            w.document.write('<style>' + finalAlignmentCss + '</style>');
            w.document.write('</head><body>');

            // Tambahkan box-sizing: border-box !important di sini juga
            w.document.write('<div id="area_surat" style="' + currentStyle + '; width: 100% !important; box-sizing: border-box !important; margin: 0 auto; box-shadow: none;">' +
                spacerHtml + contentClone.innerHTML +
                '</div>');

            w.document.write('</body></html>');
            w.document.close();
            w.focus();
            
            setTimeout(function() {
                w.print();
                w.close();
            }, 500);
        }
    </script>
@endsection