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

class SuratKeluarSiswaController extends Controller
{
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
        $isiSurat = $this->generateSuratHtml($template, $siswa, $request, $previewNomor);

        return back()
            ->withInput()
            ->with('preview_surat', $isiSurat)
            ->with('template_setting', $template)
            ->with('preview_nomor_raw', $previewNomor);
    }

    public function getSiswaByKelas($nama_rombel)
    {
        $siswas = Siswa::where('nama_rombel', urldecode($nama_rombel))
            ->orderBy('nama', 'asc')
            ->select('id', 'nama', 'nisn')
            ->get();
        return response()->json($siswas);
    }

    // ==========================================================
    // FUNGSI CETAK RESMI (MENGANDALKAN PREVIEW)
    // ==========================================================
    public function cetak(Request $request)
    {
        $template = TipeSurat::findOrFail($request->tipe_surat_id);
        $siswa    = Siswa::findOrFail($request->siswa_id);
        
        // 1. Generate Nomor Surat Resmi & Simpan Log
        $keteranganLog = "Cetak surat a.n " . $siswa->nama;
        $hasilNomor = NomorSuratSettingController::generateNomor('siswa', $keteranganLog, $template->template_isi);

        if ($hasilNomor['status'] == 'error') {
            return back()->with('error', $hasilNomor['pesan']);
        }
        
        $nomorResmi = $hasilNomor['hasil'];

        // 2. AMBIL HTML DARI INPUT PREVIEW (Agar 100% Sama dengan Tampilan)
        if ($request->has('html_content') && !empty($request->html_content)) {
            $finalContent = $request->html_content;

            // Replace Placeholder dengan Nomor Resmi
            $previewNomorDefault = NomorSuratSettingController::getPreviewNomor('siswa');
            $finalContent = str_replace($previewNomorDefault, $nomorResmi, $finalContent);
            $finalContent = str_replace('[Nomor Resmi]', $nomorResmi, $finalContent);
            
            // HAPUS TEKS VISUAL "BATAS HALAMAN" (Agar bersih saat diprint)
            $finalContent = str_replace('--- BATAS HALAMAN BARU (PAGE BREAK) ---', '', $finalContent);
            
        } else {
            // Fallback
            $template->template_isi = $nomorResmi;
            $finalContent = $this->generateSuratHtml($template, $siswa, $request, $nomorResmi);
        }

        // 3. SIAPKAN MARGIN (Untuk JS)
        $margins = [
            'top'    => $template->margin_top ?? 20,
            'right'  => $template->margin_right ?? 25,
            'bottom' => $template->margin_bottom ?? 20,
            'left'   => $template->margin_left ?? 25,
            'paper'  => $template->ukuran_kertas ?? 'A4'
        ];

        return back()
            ->withInput()
            ->with('preview_surat', $finalContent)
            ->with('template_setting', $template)
            ->with('auto_print_content', $finalContent) 
            ->with('print_margins', $margins);
    }

    private function generateSuratHtml($template, $siswa, $request, $customNomor = null)
    {
        $rawContent = $template->template_isi;
        $tglLahir = $siswa->tanggal_lahir ? Carbon::parse($siswa->tanggal_lahir)->translatedFormat('d F Y') : '-';
        $tglCetak = Carbon::parse($request->tanggal_surat)->translatedFormat('d F Y');

        $alamatLengkap = collect([
            $siswa->alamat_jalan, 
            $siswa->desa_kelurahan ? 'Desa ' . $siswa->desa_kelurahan : null,
            $siswa->kecamatan ? 'Kec. ' . $siswa->kecamatan : null, 
            $siswa->kabupaten ? 'Kab. ' . $siswa->kabupaten : null
        ])->filter()->implode(', ');

        $dataMap = [
            'nama'         => Str::title(strtolower($siswa->nama)),
            'nipd'         => $siswa->nipd ?? '-',
            'nisn'         => $siswa->nisn ?? '-',
            'kelas'        => $siswa->nama_rombel ?? '-',
            'ttl'          => ($siswa->tempat_lahir ?? '-') . ', ' . $tglLahir,
            'alamat'       => $alamatLengkap ?: '-',
            'tanggal'      => $tglCetak,
            'no_surat'     => $customNomor ?? '[Nomor Resmi]',
            'tahun_ajaran' => Tapel::where('is_active', 1)->first()->tahun_ajaran ?? '-',
        ];

        foreach ($dataMap as $key => $val) {
            $rawContent = preg_replace('/\{\{\s*' . preg_quote($key, '/') . '\s*\}\}/i', $val, $rawContent);
        }

        return $rawContent;
    }
}