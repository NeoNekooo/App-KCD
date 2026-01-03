<?php

namespace App\Http\Controllers\Admin\Administrasi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TipeSurat;
use App\Models\Gtk;
use App\Models\Tapel;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Http\Controllers\Admin\Administrasi\NomorSuratSettingController;
use Barryvdh\DomPDF\Facade\Pdf; 

class SuratKeluarGuruController extends Controller
{
    public function index()
    {
        $tapelAktif = Tapel::where('is_active', 1)->first();
        $tipeSurats = TipeSurat::where('kategori', 'guru')->get();
        $guruList = Gtk::orderBy('nama', 'asc')->get();

        return view('admin.administrasi.surat_keluar_guru.index', compact('tapelAktif', 'tipeSurats', 'guruList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipe_surat_id' => 'required',
            'gtk_id'        => 'required',
            'tanggal_surat' => 'required|date',
        ]);

        $template = TipeSurat::findOrFail($request->tipe_surat_id);
        $guru     = Gtk::findOrFail($request->gtk_id);

        $previewNomor = NomorSuratSettingController::getPreviewNomor('guru');
        
        $fullContent = $this->generateSuratHtml($template, $guru, $request, $previewNomor);

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

    public function cetak(Request $request)
    {
        $request->validate([
            'tipe_surat_id' => 'required',
            'gtk_id'        => 'required',
            'tanggal_surat' => 'required|date',
        ]);

        $template = TipeSurat::findOrFail($request->tipe_surat_id);
        $guru     = Gtk::findOrFail($request->gtk_id);
        
        $keteranganLog = "Cetak surat a.n " . $guru->nama;
        
        // === UPDATE: MENGIRIM DATA ARSIP ===
        $hasilNomor = NomorSuratSettingController::generateNomor(
            'guru',                   // Kategori
            $keteranganLog,           // Info Log
            $template->template_isi,  // Isi HTML Template
            $template->id,            // ID Template
            $guru->id,                // ID Target (Guru)
            'App\Models\Gtk'          // Model Target
        );

        if ($hasilNomor['status'] == 'error') {
            return back()->with('error', $hasilNomor['pesan']);
        }
        
        $nomorResmi = $hasilNomor['nomor_saja'];

        if ($request->has('html_content') && !empty($request->html_content)) {
            $finalContent = $request->html_content;
            $previewNomorDefault = NomorSuratSettingController::getPreviewNomor('guru');
            $finalContent = str_replace($previewNomorDefault, $nomorResmi, $finalContent);
            $finalContent = str_replace('[Nomor Resmi]', $nomorResmi, $finalContent);
        } else {
            $finalContent = $this->generateSuratHtml($template, $guru, $request, $nomorResmi);
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
            ->with('success', 'Nomor surat berhasil digenerate: ' . $nomorResmi);
    }

    // === DOWNLOAD PDF (PREVIEW + CLEANUP) ===
    public function downloadPdf(Request $request)
    {
        $template = TipeSurat::findOrFail($request->tipe_surat_id);
        
        if ($request->has('html_content') && !empty($request->html_content)) {
            $finalContent = $request->html_content;
        } else {
            $guru = Gtk::findOrFail($request->gtk_id);
            $finalContent = $this->generateSuratHtml($template, $guru, $request, '[Nomor Resmi]');
        }

        // Cleanup Sampah Enter
        $finalContent = preg_replace('/^(<p>(&nbsp;|\s|<br>)*<\/p>\s*)+/i', '', $finalContent);
        $finalContent = preg_replace('/(<p>(&nbsp;|\s|<br>)*<\/p>\s*)+$/i', '', $finalContent);

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

        return $pdf->stream('Surat_Guru_' . date('YmdHis') . '.pdf');
    }

    private function generateSuratHtml($template, $guru, $request, $customNomor = null)
    {
        $rawContent = $template->template_isi;
        $tapelAktif = Tapel::where('is_active', 1)->first();
        
        $tglLahir = $guru->tanggal_lahir ? Carbon::parse($guru->tanggal_lahir)->translatedFormat('d F Y') : '-';
        $tglCetak = Carbon::parse($request->tanggal_surat)->translatedFormat('d F Y');

        $dataMap = [
            'nama'            => $guru->nama,
            'nip'             => $guru->nip ?? '-',
            'nuptk'           => $guru->nuptk ?? '-',
            'nik'             => $guru->nik ?? '-',
            'jenis_ptk'       => $guru->jenis_ptk ?? '-',
            'jabatan'         => $guru->jenis_ptk ?? '-',
            'tempat_lahir'    => $guru->tempat_lahir ?? '-',
            'tanggal_lahir'   => $tglLahir,
            'ttl'             => ($guru->tempat_lahir ?? '-') . ', ' . $tglLahir,
            'jk'              => ($guru->jenis_kelamin == 'L') ? 'Laki-laki' : 'Perempuan',
            'alamat'          => $guru->alamat_jalan ?? '-',
            'no_hp'           => $guru->no_hp ?? '-',
            'email'           => $guru->email ?? '-',
            'tahun_ajaran'    => $tapelAktif->tahun_ajaran ?? date('Y/Y+1'),
            'tanggal'         => $tglCetak,
            'no_surat'        => $customNomor ?? '[Nomor Resmi]',
        ];

        foreach ($dataMap as $key => $val) {
            $rawContent = preg_replace('/\{\{\s*' . preg_quote($key, '/') . '\s*\}\}/i', $val, $rawContent);
        }

        return $rawContent;
    }
}