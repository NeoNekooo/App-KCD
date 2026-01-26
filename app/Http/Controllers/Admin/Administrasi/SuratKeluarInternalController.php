<?php

namespace App\Http\Controllers\Admin\Administrasi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TipeSurat;
use App\Models\PegawaiKcd; 
use App\Models\Instansi;   
use App\Models\Tapel;
use Carbon\Carbon;
use App\Http\Controllers\Admin\Administrasi\NomorSuratSettingController;
use Barryvdh\DomPDF\Facade\Pdf;

class SuratKeluarInternalController extends Controller
{
    public function index()
    {
        $tapelAktif = Tapel::where('is_active', 1)->first();
        
        // Ambil template surat kategori 'internal'
        $tipeSurats = TipeSurat::where('kategori', 'internal')->get();
        
        // Data Pegawai KCD (Urutkan nama)
        $pegawaiList = PegawaiKcd::orderBy('nama', 'asc')->get();
        
        // Data Instansi (Urutkan nama)
        $instansiList = Instansi::orderBy('nama_instansi', 'asc')->get();

        return view('admin.administrasi.surat_keluar_internal.index', compact('tapelAktif', 'tipeSurats', 'pegawaiList', 'instansiList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipe_surat_id' => 'required',
            'target_type'   => 'required|in:pegawai,instansi',
            'target_id'     => 'required',
            'tanggal_surat' => 'required|date',
        ]);

        $template = TipeSurat::findOrFail($request->tipe_surat_id);
        
        if ($request->target_type == 'pegawai') {
            $targetData = PegawaiKcd::findOrFail($request->target_id);
        } else {
            $targetData = Instansi::findOrFail($request->target_id);
        }

        $previewNomor = NomorSuratSettingController::getPreviewNomor('internal');
        
        $fullContent = $this->generateSuratHtml($template, $targetData, $request->target_type, $request, $previewNomor);

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
            'target_type'   => 'required|in:pegawai,instansi',
            'target_id'     => 'required',
            'tanggal_surat' => 'required|date',
        ]);

        $template = TipeSurat::findOrFail($request->tipe_surat_id);

        if ($request->target_type == 'pegawai') {
            $targetData = PegawaiKcd::findOrFail($request->target_id);
            $namaTarget = $targetData->nama;
            $modelTarget = 'App\Models\PegawaiKcd';
        } else {
            $targetData = Instansi::findOrFail($request->target_id);
            $namaTarget = $targetData->nama_instansi;
            $modelTarget = 'App\Models\Instansi';
        }
        
        $keteranganLog = "Cetak surat internal ke " . $namaTarget;
        
        // Generate Nomor
        $hasilNomor = NomorSuratSettingController::generateNomor(
            'internal',           
            $keteranganLog,       
            $template->template_isi, 
            $template->id,        
            $targetData->id,      
            $modelTarget          
        );

        if ($hasilNomor['status'] == 'error') {
            return back()->with('error', $hasilNomor['pesan']);
        }
        
        $nomorResmi = $hasilNomor['nomor_saja'];

        if ($request->has('html_content') && !empty($request->html_content)) {
            $finalContent = $request->html_content;
            $previewNomorDefault = NomorSuratSettingController::getPreviewNomor('internal');
            $finalContent = str_replace($previewNomorDefault, $nomorResmi, $finalContent);
            $finalContent = str_replace('[Nomor Resmi]', $nomorResmi, $finalContent);
        } else {
            $finalContent = $this->generateSuratHtml($template, $targetData, $request->target_type, $request, $nomorResmi);
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

    public function downloadPdf(Request $request)
    {
        $template = TipeSurat::findOrFail($request->tipe_surat_id);
        
        if ($request->has('html_content') && !empty($request->html_content)) {
            $finalContent = $request->html_content;
        } else {
            if ($request->target_type == 'pegawai') {
                $targetData = PegawaiKcd::findOrFail($request->target_id);
            } else {
                $targetData = Instansi::findOrFail($request->target_id);
            }
            $finalContent = $this->generateSuratHtml($template, $targetData, $request->target_type, $request, '[Nomor Resmi]');
        }

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

        return $pdf->stream('Surat_Internal_' . date('YmdHis') . '.pdf');
    }

    private function generateSuratHtml($template, $data, $type, $request, $customNomor = null)
    {
        $rawContent = $template->template_isi;
        $tapelAktif = Tapel::where('is_active', 1)->first();
        
        $tglCetak = Carbon::parse($request->tanggal_surat)->translatedFormat('d F Y');

        // Mapping Data Umum
        $commonMap = [
            'tahun_ajaran'    => $tapelAktif->tahun_ajaran ?? date('Y/Y+1'),
            'tanggal'         => $tglCetak,
            'no_surat'        => $customNomor ?? '[Nomor Resmi]',
        ];

        // Mapping Data Khusus
        $specificMap = [];

        if ($type == 'pegawai') {
            // === MAPPING PEGAWAI KCD (Sesuai pegawai_kcds.sql) ===
            $tglLahir = $data->tanggal_lahir ? Carbon::parse($data->tanggal_lahir)->translatedFormat('d F Y') : '-';
            
            $specificMap = [
                'nama'            => $data->nama,
                'nip'             => $data->nip ?? '-',
                'nik'             => $data->nik ?? '-',
                'jabatan'         => $data->jabatan ?? '-',
                'tempat_lahir'    => $data->tempat_lahir ?? '-',
                'tanggal_lahir'   => $tglLahir,
                'ttl'             => ($data->tempat_lahir ?? '-') . ', ' . $tglLahir,
                'jk'              => ($data->jenis_kelamin == 'L') ? 'Laki-laki' : 'Perempuan',
                'alamat'          => $data->alamat ?? '-',
                'no_hp'           => $data->no_hp ?? '-',
                // Di database kolomnya 'email_pribadi'
                'email'           => $data->email_pribadi ?? '-', 
                
                // Variabel Instansi dikosongkan agar tidak error di template
                'nama_instansi'        => '-',
                'nama_brand'           => '-',
                'nama_kepala_instansi' => '-',
                'nip_kepala_instansi'  => '-',
                'alamat_instansi'      => '-',
                'telepon_instansi'     => '-',
                'email_instansi'       => '-',
                'website_instansi'     => '-',
                'logo_instansi'        => '',
                'tanda_tangan_instansi'=> '',
            ];
        } else {
            // === MAPPING INSTANSI (Sesuai instansis.sql) ===
            
            // Logic Gambar Logo
            $logoHtml = '';
            if (!empty($data->logo)) {
                $logoUrl = asset('storage/' . $data->logo);
                $logoHtml = '<img src="'.$logoUrl.'" alt="Logo" style="width: 80px; height: auto;">';
            }

            // Logic Gambar Tanda Tangan
            $ttdHtml = '';
            if (!empty($data->tanda_tangan)) {
                $ttdUrl = asset('storage/' . $data->tanda_tangan);
                $ttdHtml = '<img src="'.$ttdUrl.'" alt="TTD" style="width: 100px; height: auto;">';
            }

            $specificMap = [
                // Fallback variabel umum agar tidak error
                'nama'            => $data->nama_instansi, 
                'alamat'          => $data->alamat ?? '-',
                'email'           => $data->email ?? '-',
                'no_hp'           => $data->telepon ?? '-',
                'nip'             => '-',
                'nik'             => '-',
                'jabatan'         => '-',
                'ttl'             => '-',
                'jk'              => '-',

                // Variabel Khusus Instansi
                'nama_instansi'        => $data->nama_instansi,
                'nama_brand'           => $data->nama_brand ?? '-',
                'nama_kepala_instansi' => $data->nama_kepala ?? '-',
                'nip_kepala_instansi'  => $data->nip_kepala ?? '-',
                'alamat_instansi'      => $data->alamat ?? '-',
                'telepon_instansi'     => $data->telepon ?? '-',
                'email_instansi'       => $data->email ?? '-',
                'website_instansi'     => $data->website ?? '-',
                
                // Variabel Gambar
                'logo_instansi'        => $logoHtml,
                'tanda_tangan_instansi'=> $ttdHtml,
            ];
        }

        $dataMap = array_merge($commonMap, $specificMap);

        foreach ($dataMap as $key => $val) {
            $rawContent = preg_replace('/\{\{\s*' . preg_quote($key, '/') . '\s*\}\}/i', $val, $rawContent);
        }

        return $rawContent;
    }
}