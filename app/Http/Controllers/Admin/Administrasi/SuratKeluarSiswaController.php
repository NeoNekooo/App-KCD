<?php

namespace App\Http\Controllers\Admin\Administrasi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TipeSurat;
use App\Models\Siswa;
use App\Models\Tapel;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Http\Controllers\Admin\Administrasi\NomorSuratSettingController;
use Barryvdh\DomPDF\Facade\Pdf; // Import Library PDF

class SuratKeluarSiswaController extends Controller
{
    /**
     * Halaman Utama
     */
    public function index()
    {
        $tapelAktif = Tapel::where('is_active', 1)->first();
        $tipeSurats = TipeSurat::where('kategori', 'siswa')->get();
        
        $kelasList  = Siswa::whereNotNull('nama_rombel')
            ->select('nama_rombel')
            ->distinct()
            ->orderBy('nama_rombel')
            ->pluck('nama_rombel');

        return view('admin.administrasi.surat_keluar.index', compact('tapelAktif', 'tipeSurats', 'kelasList'));
    }

    /**
     * Preview HTML di Halaman Admin
     */
    public function store(Request $request)
    {
        $request->validate([
            'tipe_surat_id' => 'required',
            'siswa_id'      => 'required',
            'tanggal_surat' => 'required|date',
        ]);

        $template = TipeSurat::findOrFail($request->tipe_surat_id);
        $siswa    = Siswa::findOrFail($request->siswa_id);

        $previewNomor = NomorSuratSettingController::getPreviewNomor('siswa');
        
        $fullContent = $this->generateSuratHtml($template, $siswa, $request, $previewNomor);

        $delimiter = '<div class="mce-pagebreak" contenteditable="false"></div>';
        if (strpos($fullContent, $delimiter) !== false) {
            $pages = explode($delimiter, $fullContent);
        } else {
            $pages = [$fullContent];
        }

        return back()
            ->withInput()
            ->with('preview_pages', $pages) 
            ->with('full_content_raw', $fullContent) 
            ->with('template_setting', $template)
            ->with('preview_nomor_raw', $previewNomor);
    }

    /**
     * AJAX Helper
     */
    public function getSiswaByKelas($nama_rombel)
    {
        $siswas = Siswa::where('nama_rombel', urldecode($nama_rombel))
            ->orderBy('nama', 'asc')
            ->select('id', 'nama', 'nisn')
            ->get();
        return response()->json($siswas);
    }

    /**
     * PROSES CETAK RESMI (Generate Nomor & Simpan Log Arsip)
     */
    public function cetak(Request $request)
    {
        $request->validate([
            'tipe_surat_id' => 'required',
            'siswa_id'      => 'required',
            'tanggal_surat' => 'required|date',
        ]);

        $template = TipeSurat::findOrFail($request->tipe_surat_id);
        $siswa    = Siswa::findOrFail($request->siswa_id);
        
        $keteranganLog = "Cetak surat a.n " . $siswa->nama;

        // === UPDATE PENTING: MENGIRIM DATA UNTUK ARSIP DIGITAL ===
        // Pastikan NomorSuratSettingController->generateNomor sudah menerima 6 parameter
        $hasilNomor = NomorSuratSettingController::generateNomor(
            'siswa',                  // Kategori
            $keteranganLog,           // Log Info
            $template->template_isi,  // Isi Template Raw
            $template->id,            // ID Template (Untuk Arsip)
            $siswa->id,               // ID Target (Untuk Arsip)
            'App\Models\Siswa'        // Model Target (Untuk Arsip)
        );

        if ($hasilNomor['status'] == 'error') {
            return back()->with('error', $hasilNomor['pesan']);
        }
        
        $nomorResmi = $hasilNomor['hasil']; // HTML Hasil Replace

        // Jika user melakukan edit manual di preview, gunakan itu. Jika tidak, gunakan hasil generate baru.
        if ($request->has('html_content') && !empty($request->html_content)) {
            $finalContent = $request->html_content;
            $previewNomorDefault = NomorSuratSettingController::getPreviewNomor('siswa');
            $finalContent = str_replace($previewNomorDefault, $hasilNomor['nomor_saja'], $finalContent);
            $finalContent = str_replace('[Nomor Resmi]', $hasilNomor['nomor_saja'], $finalContent);
        } else {
            $finalContent = $this->generateSuratHtml($template, $siswa, $request, $hasilNomor['nomor_saja']);
        }

        $margins = [
            'top'    => $template->margin_top ?? 20,
            'right'  => $template->margin_right ?? 25,
            'bottom' => $template->margin_bottom ?? 20,
            'left'   => $template->margin_left ?? 25,
            'paper'  => $template->ukuran_kertas ?? 'A4'
        ];

        return back()
            ->withInput()
            ->with('preview_pages', explode('<div class="mce-pagebreak" contenteditable="false"></div>', $finalContent))
            ->with('full_content_raw', $finalContent)
            ->with('template_setting', $template)
            ->with('auto_print_content', $finalContent) 
            ->with('print_margins', $margins)
            ->with('success', 'Nomor surat berhasil digenerate: ' . $hasilNomor['nomor_saja']);
    }

    /**
     * DOWNLOAD / PREVIEW PDF (Tanpa Generate Nomor Baru)
     */
    public function downloadPdf(Request $request)
    {
        $template = TipeSurat::findOrFail($request->tipe_surat_id);
        
        // 1. Ambil Konten
        if ($request->has('html_content') && !empty($request->html_content)) {
            $finalContent = $request->html_content;
        } else {
            $siswa = Siswa::findOrFail($request->siswa_id);
            $finalContent = $this->generateSuratHtml($template, $siswa, $request, '[Nomor Resmi]');
        }

        // 2. Cleanup Sampah Enter
        $finalContent = preg_replace('/^(<p>(&nbsp;|\s|<br>)*<\/p>\s*)+/i', '', $finalContent);
        $finalContent = preg_replace('/(<p>(&nbsp;|\s|<br>)*<\/p>\s*)+$/i', '', $finalContent);

        // 3. Config Kertas
        $paperMap = [
            'A4'     => [0, 0, 595.28, 841.89],
            'F4'     => [0, 0, 609.45, 935.43],
            'Legal'  => [0, 0, 612.00, 1008.00],
            'Letter' => [0, 0, 612.00, 792.00],
        ];
        $uk = $template->ukuran_kertas ?? 'A4';
        $paperSize = $paperMap[$uk] ?? $paperMap['A4'];

        $mt = ($template->margin_top ?? 20) . 'mm';
        $mr = ($template->margin_right ?? 25) . 'mm';
        $mb = ($template->margin_bottom ?? 20) . 'mm';
        $ml = ($template->margin_left ?? 25) . 'mm';

        // 4. Render HTML Wrapper
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                @page { margin: 0px; }
                body { 
                    margin-top: '.$mt.'; margin-right: '.$mr.'; margin-bottom: '.$mb.'; margin-left: '.$ml.'; 
                    font-family: "Times New Roman", serif; font-size: 12pt; line-height: 1.5; color: #000;
                }
                
                /* Fix Page Break di PDF */
                .mce-pagebreak { 
                    page-break-before: always !important; 
                    display: block !important;
                    height: 0px !important; 
                    margin: 0 !important; 
                    padding: 0 !important; 
                    border: none !important; 
                    visibility: hidden;
                }
                .mce-pagebreak:first-child { page-break-before: avoid !important; }

                table { width: 100%; border-collapse: collapse; }
                td, th { vertical-align: top; padding: 2px; }
                p { margin-top: 0; margin-bottom: 0.8rem; }
            </style>
        </head>
        <body>'.$finalContent.'</body>
        </html>';

        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper($paperSize, 'portrait');

        return $pdf->stream('Surat_Siswa_' . date('YmdHis') . '.pdf');
    }

    private function generateSuratHtml($template, $siswa, $request, $customNomor = null)
    {
        $rawContent = $template->template_isi;
        
        $tglLahir = $siswa->tanggal_lahir ? Carbon::parse($siswa->tanggal_lahir)->translatedFormat('d F Y') : '-';
        $tglCetak = Carbon::parse($request->tanggal_surat)->translatedFormat('d F Y');

        $alamatParts = [];
        if ($siswa->alamat_jalan) $alamatParts[] = $siswa->alamat_jalan;
        if ($siswa->desa_kelurahan) $alamatParts[] = "Desa " . $siswa->desa_kelurahan;
        if ($siswa->kecamatan) $alamatParts[] = "Kec. " . $siswa->kecamatan;
        if ($siswa->kabupaten) $alamatParts[] = "Kab. " . $siswa->kabupaten;
        $alamatLengkap = !empty($alamatParts) ? implode(', ', $alamatParts) : '-';

        $dataMap = [
            'no_surat'      => $customNomor ?? '[Nomor Resmi]',
            'tanggal'       => $tglCetak,
            'tahun_ajaran'  => Tapel::where('is_active', 1)->first()->tahun_ajaran ?? '-',
            
            'nama'          => Str::title($siswa->nama),
            'nipd'          => $siswa->nipd ?? '-',
            'nisn'          => $siswa->nisn ?? '-',
            'nik'           => $siswa->nik ?? '-',
            'kelas'         => $siswa->nama_rombel ?? '-',
            'ttl'           => ($siswa->tempat_lahir ?? '-') . ', ' . $tglLahir,
            'jk'            => $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan',
            'agama'         => $siswa->agama ?? '-',
            'alamat'        => $alamatLengkap,
            
            'nama_ayah'     => $siswa->nama_ayah ?? '-',
            'nama_ibu'      => $siswa->nama_ibu ?? '-',
            'pekerjaan_ayah'=> $siswa->pekerjaan_ayah ?? '-',
            'nama_wali'     => $siswa->nama_wali ?? '-',
        ];

        foreach ($dataMap as $key => $val) {
            $rawContent = preg_replace('/\{\{\s*' . preg_quote($key, '/') . '\s*\}\}/i', $val, $rawContent);
        }

        return $rawContent;
    }
}