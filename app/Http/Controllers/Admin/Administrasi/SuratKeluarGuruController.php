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
        
        $isiSurat = $this->generateSuratHtml($template, $guru, $request, $previewNomor);

        return back()
            ->withInput()
            ->with('preview_surat', $isiSurat)
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
        
        // 1. Generate Nomor Surat Resmi
        $keteranganLog = "Cetak surat a.n " . $guru->nama . " (" . ($guru->jenis_ptk ?? 'Guru') . ")";
        $hasilNomor = NomorSuratSettingController::generateNomor('guru', $keteranganLog, $template->template_isi);

        if ($hasilNomor['status'] == 'error') {
            return back()->with('error', $hasilNomor['pesan']);
        }
        
        $nomorResmi = $hasilNomor['hasil'];

        // 2. AMBIL HTML DARI INPUT PREVIEW
        if ($request->has('html_content') && !empty($request->html_content)) {
            $finalContent = $request->html_content;

            // Ganti Placeholder dengan Nomor Resmi
            $previewNomorDefault = NomorSuratSettingController::getPreviewNomor('guru');
            $finalContent = str_replace($previewNomorDefault, $nomorResmi, $finalContent);
            $finalContent = str_replace('[Nomor Resmi]', $nomorResmi, $finalContent);
            
            // HAPUS TEKS VISUAL "BATAS HALAMAN"
            $finalContent = str_replace('--- BATAS HALAMAN BARU (PAGE BREAK) ---', '', $finalContent);
            
        } else {
            // Fallback
            $template->template_isi = $nomorResmi;
            $finalContent = $this->generateSuratHtml($template, $guru, $request, $nomorResmi);
        }

        // 3. SIAPKAN MARGIN
        $margins = [
            'top'    => $template->margin_top ?? 20,
            'right'  => $template->margin_right ?? 25,
            'bottom' => $template->margin_bottom ?? 20,
            'left'   => $template->margin_left ?? 25,
            'paper'  => $template->ukuran_kertas ?? 'A4'
        ];

        // PENTING: Hapus 'success' agar tidak ada toast hijau
        return back()
            ->withInput()
            ->with('preview_surat', $finalContent)      
            ->with('template_setting', $template)
            ->with('auto_print_content', $finalContent) // Trigger JS Print
            ->with('print_margins', $margins);
    }

    // ==========================================================
    // HELPERS
    // ==========================================================
    private function generateSuratHtml($template, $guru, $request, $customNomor = null)
    {
        $rawContent = $template->template_isi;
        $tapelAktif = Tapel::where('is_active', 1)->first();
        
        $tglLahir = $guru->tanggal_lahir ? Carbon::parse($guru->tanggal_lahir)->translatedFormat('d F Y') : '-';
        $tglCetak = Carbon::parse($request->tanggal_surat)->translatedFormat('d F Y');

        $alamatLengkap = collect([
            $guru->alamat_jalan, 
            $guru->rt ? 'RT '.$guru->rt : null,
            $guru->rw ? 'RW '.$guru->rw : null,
            $guru->desa_kelurahan ? 'Desa ' . $guru->desa_kelurahan : null,
            $guru->kecamatan ? 'Kec. ' . $guru->kecamatan : null
        ])->filter()->implode(', ');

        $dataMap = [
            'nama'            => $guru->nama,
            'nip'             => $guru->nip ?? '-',
            'nuptk'           => $guru->nuptk ?? '-',
            'nik'             => $guru->nik ?? '-',
            'jenis_ptk'       => $guru->jenis_ptk ?? '-',
            'jabatan'         => $guru->tugas_tambahan ?? $guru->jenis_ptk ?? '-',
            'tempat_lahir'    => $guru->tempat_lahir ?? '-',
            'tanggal_lahir'   => $tglLahir,
            'ttl'             => ($guru->tempat_lahir ?? '-') . ', ' . $tglLahir,
            'jenis_kelamin'   => ($guru->jenis_kelamin == 'L') ? 'Laki-laki' : 'Perempuan',
            'agama'           => $guru->agama ?? '-',
            'alamat'          => $alamatLengkap ?: '-',
            'unit_kerja'      => $guru->unit_kerja ?? config('app.name'),
            'no_hp'           => $guru->no_hp ?? '-',
            'email'           => $guru->email ?? '-',
            'pangkat'         => $guru->pangkat_golongan ?? '-',
            'pendidikan'      => $guru->pendidikan_terakhir ?? '-',
            'tahun_pelajaran' => $tapelAktif->tahun_ajaran ?? date('Y/Y+1'),
            'tanggal'         => $tglCetak,
            'no_surat'        => $customNomor ?? '[Nomor Resmi]',
        ];

        foreach ($dataMap as $key => $val) {
            $pattern = '/\{\{\s*' . preg_quote($key, '/') . '\s*\}\}/i';
            $rawContent = preg_replace($pattern, $val, $rawContent);
        }

        return $rawContent;
    }
}