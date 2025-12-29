@extends('layouts.admin')

@section('content')
    <style id="preview-styles">
        /* === STYLE PREVIEW LAYAR === */
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
            width: 210mm;
            min-height: 297mm;
            padding: 2cm;
            margin-bottom: 30px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
            color: #000;
            font-family: 'Times New Roman', serif;
            font-size: 12pt;
            line-height: 1.5;
            text-align: justify;
            box-sizing: border-box;
            position: relative;
            background-image: repeating-linear-gradient(to bottom, #ffffff 0mm, #ffffff 297mm, #525659 297mm, #525659 307mm);
        }

        .paper-sheet p {
            margin-top: 0;
            margin-bottom: 1rem;
        }

        .paper-sheet table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 10px;
        }

        .paper-sheet td,
        .paper-sheet th {
            padding: 3px;
            border: 1px solid #000;
            vertical-align: top;
        }

        .paper-sheet table[border="0"] td,
        .paper-sheet table[style*="border-width: 0"] td {
            border: 1px dotted #ccc;
        }

        .paper-sheet .mce-pagebreak {
            display: block;
            border-top: 1px dashed #999;
            background: #525659;
            height: 10mm;
            margin: 20px -2cm;
            page-break-after: always;
            position: relative;
            color: #fff;
            text-align: center;
            line-height: 10mm;
            font-size: 10px;
        }

        .paper-sheet .mce-pagebreak::after {
            content: "--- BATAS HALAMAN BARU (PAGE BREAK) ---";
        }
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Administrasi /</span> Cetak Surat Siswa</h4>

        {{-- 1. FORM FILTER (Hanya Tombol Preview) --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="mb-0 fw-bold text-primary"><i class="bx bx-filter-alt me-2"></i> Filter Data Surat</h5>
            </div>
            <div class="card-body mt-4">
                <form id="formSuratSiswa" action="{{ route('admin.administrasi.surat-keluar-siswa.store') }}"
                    method="POST">
                    @csrf
                    <input type="hidden" name="tanggal_surat" value="{{ date('Y-m-d') }}">
                    <textarea name="html_content" style="display:none;"></textarea>

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
                                        {{ old('tipe_surat_id') == $tipe->id ? 'selected' : '' }}>{{ $tipe->judul_surat }}
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

        {{-- 2. AREA PREVIEW & TOMBOL CETAK RESMI --}}
        @if (session('preview_surat'))
            @php
                $setting = session('template_setting');
                $mt = $setting->margin_top ?? 20;
                $mr = $setting->margin_right ?? 25;
                $mb = $setting->margin_bottom ?? 20;
                $ml = $setting->margin_left ?? 25;

                $w = '210mm';
                $h = 297;
                if (($setting->ukuran_kertas ?? 'A4') == 'F4') {
                    $w = '215mm';
                    $h = 330;
                }
                if (($setting->ukuran_kertas ?? 'A4') == 'Legal') {
                    $w = '216mm';
                    $h = 356;
                }
                if (($setting->ukuran_kertas ?? 'A4') == 'Letter') {
                    $w = '216mm';
                    $h = 279;
                }
            @endphp

            <div class="card shadow-sm border-0" id="previewSection">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center sticky-top"
                    style="top: 70px; z-index: 100">
                    <div>
                        <h5 class="fw-bold mb-0 text-primary">Hasil Preview Surat</h5>
                        <small class="text-muted">Klik Cetak Resmi untuk mendaftarkan nomor surat.</small>
                    </div>

                    <form action="{{ route('admin.administrasi.surat-keluar-siswa.cetak') }}" method="POST"
                        onsubmit="return copyPreview(this)">
                        @csrf
                        <input type="hidden" name="tipe_surat_id" value="{{ old('tipe_surat_id') }}">
                        <input type="hidden" name="siswa_id" value="{{ old('siswa_id') }}">
                        <input type="hidden" name="tanggal_surat" value="{{ old('tanggal_surat') }}">
                        <textarea name="html_content" style="display:none;"></textarea>

                        <button type="submit" class="btn btn-success shadow-sm px-4">
                            <i class='bx bx-printer me-1'></i> Cetak Resmi
                        </button>
                    </form>
                </div>

                <div class="card-body p-0">
                    <div class="preview-desk">
                        <div class="paper-sheet" id="paperContent"
                            style="padding: {{ $mt }}mm {{ $mr }}mm {{ $mb }}mm {{ $ml }}mm; width: {{ $w }}; min-height: {{ $h }}mm;">
                            {!! session('preview_surat') !!}
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            function copyPreview(formEl) {
                const paperDiv = document.getElementById('paperContent');
                const textarea = formEl.querySelector('textarea[name="html_content"]');
                if (paperDiv && textarea) {
                    textarea.value = paperDiv.innerHTML;
                }
                return true;
            }

            @if (session('auto_print_content') && session('print_margins'))
                window.onload = function() {
                    var rawContent = {!! json_encode(session('auto_print_content')) !!};
                    var margins = {!! json_encode(session('print_margins')) !!};
                    var marginHalamanDua = '5mm';

                    var fullHtml = `
                        <!DOCTYPE html>
                        <html>
                        <head>
                            <title>Cetak Surat</title>
                            <style>
                                * { margin: 0; padding: 0; box-sizing: border-box; }
                                @media print {
                                    @page {
                                        size: ` + margins.paper + `; 
                                        margin-top: ` + marginHalamanDua + `;
                                        margin-bottom: ` + marginHalamanDua + `;
                                        margin-left: ` + margins.left + `mm;
                                        margin-right: ` + margins.right + `mm;
                                    }
                                    @page :first {
                                        margin-top: ` + margins.top + `mm;
                                        margin-bottom: ` + margins.bottom + `mm;
                                    }
                                    html, body { margin: 0 !important; padding: 0 !important; width: 100%; height: 100%; }
                                }
                                body { font-family: 'Times New Roman', serif; font-size: 12pt; line-height: 1.5; color: #000; }
                                p { margin-top: 0; margin-bottom: 1rem; }
                                table { width: 100%; border-collapse: collapse; margin-bottom: 5px; }
                                td, th { border: 1px solid black; padding: 3px; vertical-align: top; }
                                table[border="0"] td, table[border="0"] th, 
                                table[style*="border: 0"] td, table[style*="border: 0"] th,
                                table[style*="border-width: 0"] td, table[style*="border-width: 0"] th,
                                td[style*="border-width: 0"], th[style*="border-width: 0"] { 
                                    border: none !important; 
                                }
                                .mce-pagebreak { 
                                    page-break-after: always; height: 0 !important; margin: 0 !important; 
                                    padding: 0 !important; line-height: 0 !important; display: block; 
                                    visibility: hidden; border: none;
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

            $(document).ready(function() {
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

            @if (session('preview_surat'))
                document.getElementById("previewSection").scrollIntoView({
                    behavior: "smooth"
                });
            @endif
        </script>
    @endpush
@endsection
