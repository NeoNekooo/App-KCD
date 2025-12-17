<?php

namespace App\Http\Controllers\Admin\Administrasi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TipeSurat;
use App\Models\Gtk; 
use App\Models\Tapel;
use Carbon\Carbon;

class SuratKeluarGuruController extends Controller
{
    public function index()
    {
        $tapelAktif = Tapel::getAktif();
        $tipeSurats = TipeSurat::where('kategori', 'guru')->get();

        // Ambil data dari model Gtk
        $guruList = Gtk::orderBy('nama', 'asc')->get();

        return view('admin.administrasi.surat_keluar_guru.index', compact(
            'tapelAktif',
            'tipeSurats',
            'guruList'
        ));
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
        
        // Ambil isi template
        $isiSurat = $template->template_isi;

        // Replace Placeholder
        $isiSurat = str_replace('{{nama}}', $guru->nama ?? '-', $isiSurat);
        $isiSurat = str_replace('{{nuptk}}', $guru->nuptk ?? '-', $isiSurat);
        $isiSurat = str_replace('{{nip}}', $guru->nip ?? '-', $isiSurat);
        $isiSurat = str_replace('{{mapel}}', $guru->mapel ?? $guru->jenis_ptk ?? '-', $isiSurat);
        $isiSurat = str_replace('{{jabatan}}', $guru->jabatan ?? '-', $isiSurat);
        $isiSurat = str_replace('{{unit_kerja}}', $guru->unit_kerja ?? '-', $isiSurat); // Tambahan umum
        $isiSurat = str_replace('{{alamat}}', $guru->alamat ?? '-', $isiSurat);
        $isiSurat = str_replace('{{tahun_pelajaran}}', $tapelAktif->tahun_ajaran ?? '-', $isiSurat); // Tambahan umum

        $isiSurat = str_replace(
            '{{tanggal}}',
            Carbon::parse($request->tanggal_surat)->locale('id')->isoFormat('D MMMM Y'),
            $isiSurat
        );

        // PENTING: Kirim 'template_setting' ke View
        return back()
            ->withInput()
            ->with('preview_surat', $isiSurat)
            ->with('template_setting', $template); 
    }
}