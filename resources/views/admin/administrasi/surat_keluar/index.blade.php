@extends('layouts.admin')

@section('content')

    {{-- CSS Tambahan (Disamakan dengan Guru) --}}
    <style>
        /* FIX ALIGNMENT QUILL */
        #area_surat .ql-align-center { text-align: center !important; }
        #area_surat .ql-align-right { text-align: right !important; }
        #area_surat .ql-align-justify { text-align: justify !important; }

        #area_surat [style*="text-align: center"] { text-align: center !important; }
        #area_surat [style*="text-align: right"] { text-align: right !important; }
        #area_surat [style*="text-align: justify"] { text-align: justify !important; }

        /* Reset Style Container */
        #area_surat { 
            text-align: initial !important; 
            box-sizing: border-box;
        }

        /* Style untuk Spacer Visual di Preview (Tanda area Kop Surat) */
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

        /* CSS ini hanya berlaku saat tombol print ditekan (native browser), 
           tapi kita akan menimpa ini dengan JS agar lebih presisi */
        @media print {
            @page { size: auto; margin: 0mm; }
            body { margin: 0px; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
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

                {{-- FORM FILTER SISWA (Tetap mempertahankan logika input Siswa) --}}
                <form id="formSurat" action="{{ route('admin.administrasi.surat-keluar-siswa.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="tanggal_surat" value="{{ date('Y-m-d') }}">

                    <div class="row g-3">
                        {{-- Tahun Pelajaran --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tahun Pelajaran</label>
                            <input type="text" class="form-control"
                                value="{{ isset($tapelAktif) && $tapelAktif ? $tapelAktif->tahun_ajaran : 'Tidak ada tapel aktif' }}"
                                readonly>
                            <input type="hidden" name="tapel_id"
                                value="{{ isset($tapelAktif) && $tapelAktif ? $tapelAktif->id : '' }}">
                        </div>

                        {{-- Kelas (Pemicu AJAX) --}}
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

                        {{-- Jenis Surat --}}
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

                        {{-- Pilih Siswa (Hasil AJAX) --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Siswa</label>
                            <select name="siswa_id" id="select_siswa" class="form-select" disabled required>
                                <option>- Pilih Kelas Terlebih Dahulu -</option>
                            </select>
                        </div>
                    </div>
                </form>

                {{-- BOX INFORMASI (Disamakan dengan Guru) --}}
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

                    {{-- TOOLBAR PENGATURAN KERTAS (Disamakan dengan Guru) --}}
                    <div class="card bg-light border mb-3">
                        <div class="card-body py-2">
                            <div class="row align-items-center g-2">
                                
                                {{-- 1. Ukuran Kertas --}}
                                <div class="col-auto">
                                    <div class="input-group input-group-sm" title="Ukuran Kertas">
                                        <span class="input-group-text"><i class="bx bx-paper"></i></span>
                                        <select id="paper_size" class="form-select form-select-sm">
                                            <option value="A4">A4 (210 x 297 mm)</option>
                                            <option value="F4">F4/Folio (215 x 330 mm)</option>
                                            <option value="Legal">Legal (216 x 356 mm)</option>
                                            <option value="Letter">Letter (216 x 279 mm)</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- 2. Jenis Font --}}
                                <div class="col-auto">
                                    <div class="input-group input-group-sm" title="Jenis Font">
                                        <span class="input-group-text"><i class="bx bx-font-family"></i></span>
                                        <select id="font_family" class="form-select form-select-sm" style="max-width: 150px;">
                                            <option value="'Times New Roman', serif" selected>Times New Roman</option>
                                            <option value="Arial, Helvetica, sans-serif">Arial</option>
                                            <option value="'Courier New', monospace">Courier New</option>
                                            <option value="'Calibri', sans-serif">Calibri</option>
                                            <option value="Tahoma, sans-serif">Tahoma</option>
                                            <option value="Verdana, sans-serif">Verdana</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- 3. Ukuran Font (Diaktifkan agar tidak error) --}}
                                {{-- <div class="col-auto">
                                    <div class="input-group input-group-sm" title="Ukuran Font">
                                        <span class="input-group-text">Size (pt)</span>
                                        <input type="number" id="font_size" class="form-control" value="12" min="8" max="72" style="width: 60px;">
                                    </div>
                                </div> --}}

                                {{-- 4. Margin --}}
                                <div class="col-auto">
                                    <div class="input-group input-group-sm" title="Margin Kertas">
                                        <span class="input-group-text">Margin (mm)</span>
                                        <input type="number" id="paper_margin" class="form-control" value="20" min="0" style="width: 60px;">
                                    </div>
                                </div>

                                {{-- 5. Warna Kertas --}}
                                <div class="col-auto">
                                    <div class="input-group input-group-sm" title="Warna Latar Kertas">
                                        <span class="input-group-text"><i class='bx bxs-color-fill'></i></span>
                                        <input type="color" id="paper_color" class="form-control form-control-color" value="#ffffff" style="max-width: 50px;">
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
                        font-family: 'Times New Roman', serif;
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

            // --- BAGIAN 1: LOGIKA AJAX SISWA (KHUSUS HALAMAN SISWA) ---
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
                            siswaSelect.empty().append('<option value="">-- Pilih Siswa --</option>');
                            if (data.length > 0) {
                                $.each(data, function(key, value) {
                                    var selected = (value.id == "{{ old('siswa_id') }}") ?
                                        'selected' : '';
                                    siswaSelect.append('<option value="' + value.id + '" ' +
                                        selected + '>' + value.nama + '</option>');
                                });
                                siswaSelect.prop('disabled', false);
                            } else {
                                siswaSelect.append('<option>Tidak ada siswa</option>');
                            }
                        },
                        error: function() {
                            siswaSelect.empty().append('<option>Error mengambil data</option>');
                        }
                    });
                } else {
                    siswaSelect.empty().append('<option>- Pilih Kelas Terlebih Dahulu -</option>').prop(
                        'disabled', true);
                }
            });

            // Trigger perubahan kelas jika ada old input
            var oldKelas = "{{ old('kelas_old') }}";
            if (oldKelas && $('#select_kelas').val() == "") {
                $('#select_kelas').val(oldKelas).trigger('change');
            } else if ($('#select_kelas').val() != "") {
                $('#select_kelas').trigger('change');
            }


            // --- BAGIAN 2: LOGIKA UPDATE TAMPILAN PREVIEW (DARI GURU) ---
            function updatePaperSettings() {
                var size = $('#paper_size').val();
                var margin = $('#paper_margin').val() + 'mm';
                var color = $('#paper_color').val();
                var fontSize = $('#font_size').val() + 'pt';
                var fontFamily = $('#font_family').val();

                var width = '210mm';
                var height = '297mm';

                if (size === 'A4') { width = '210mm'; height = '297mm'; }
                else if (size === 'F4') { width = '215mm'; height = '330mm'; }
                else if (size === 'Legal') { width = '216mm'; height = '356mm'; }
                else if (size === 'Letter') { width = '216mm'; height = '279mm'; }

                $('#area_surat').css({
                    'width': width,
                    'min-height': height,
                    'padding': margin,
                    'background-color': color,
                    'font-size': fontSize,
                    'font-family': fontFamily
                });
            }

            // Jalankan update setiap user mengubah input
            $('#paper_size, #paper_color, #paper_margin, #font_size, #font_family').on('input change', updatePaperSettings);
            updatePaperSettings(); // Jalankan sekali saat loading


            // --- BAGIAN 3: LOGIKA SPACER KOP SURAT ---
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
            if ($('#toggleKop').length > 0) { updatePreviewSpacer(); }
        });

        // --- BAGIAN 4: FUNGSI CETAK KHUSUS (DARI GURU - LEBIH STABIL) ---
        function printDiv(divId) {
            
            // 1. Clone konten surat
            var contentClone = document.getElementById(divId).cloneNode(true);
            var useKop = document.getElementById('toggleKop').checked;
            
            // 2. Ambil Settingan Font & Ukuran Kertas 'Live' dari elemen Preview
            var currentFontFamily = $('#font_family').val();
            var currentFontSize = $('#font_size').val() + 'pt';
            
            // Kita ambil Width dan Padding langsung dari CSS elemen preview agar Presisi
            var currentWidth = $('#area_surat').css('width'); 
            var currentPadding = $('#area_surat').css('padding');

            // 3. Bersihkan elemen visual (garis putus-putus) dari clone
            var existingVisualSpacer = contentClone.querySelector('#visual_spacer');
            if(existingVisualSpacer) { existingVisualSpacer.remove(); }

            // 4. Buat Spacer Bening untuk Kop (jika dicentang)
            var spacerHtml = '';
            if (useKop) {
                spacerHtml = '<div style="width: 100%; height: 3cm; display: block; background: transparent;"></div>';
            }

            // 5. AMBIL SEMUA CSS FRAMEWORK (Bootstrap, Sneat, dll) dari Halaman Induk
            var stylesHtml = '';
            $('link[rel="stylesheet"]').each(function() {
                stylesHtml += '<link rel="stylesheet" href="' + $(this).attr('href') + '">';
            });

            // 6. BUAT CSS KHUSUS PRINT
            var customCss = `
                <style>
                    body { 
                        background-color: white !important; 
                        -webkit-print-color-adjust: exact; 
                    }
                    /* Wrapper Print memaksa ukuran sesuai kertas yang dipilih */
                    #print-wrapper {
                        width: ${currentWidth} !important; 
                        margin: 0 auto;
                        padding: ${currentPadding} !important;
                        font-family: ${currentFontFamily} !important;
                        font-size: ${currentFontSize} !important;
                        box-sizing: border-box; /* Penting agar padding tidak melebarkan kertas */
                        overflow: hidden;
                    }
                    /* Reset Margin Browser */
                    @page { 
                        size: auto; 
                        margin: 0mm; 
                    }
                    /* Helper Alignment */
                    .ql-align-center { text-align: center !important; }
                    .ql-align-right { text-align: right !important; }
                    .ql-align-justify { text-align: justify !important; }
                </style>
            `;

            // 7. MEMBUKA JENDELA PRINT
            var w = window.open('', '', 'height=800,width=1000');
            w.document.write('<html><head><title>Cetak Surat Siswa</title>');
            
            w.document.write(stylesHtml); 
            w.document.write(customCss);
            
            w.document.write('</head><body>');

            // Bungkus konten
            w.document.write('<div id="print-wrapper">');
            w.document.write(spacerHtml); // Spacer Kop
            w.document.write(contentClone.innerHTML); // Isi Surat
            w.document.write('</div>');

            w.document.write('</body></html>');
            w.document.close();
            w.focus();

            // 8. DELAY PRINT (PENTING)
            setTimeout(function() {
                w.print();
                w.close();
            }, 1000);
        }
    </script>
@endsection