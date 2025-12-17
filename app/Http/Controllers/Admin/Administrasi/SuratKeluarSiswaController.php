<?php

namespace App\Http\Controllers\Admin\Administrasi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TipeSurat;
use App\Models\Siswa;
use App\Models\Tapel; 
use Carbon\Carbon;

class SuratKeluarSiswaController extends Controller
{
    public function index()
    {
        $tapelAktif = Tapel::getAktif(); 
        $tipeSurats = TipeSurat::where('kategori', 'siswa')->get();

        $kelasList = Siswa::whereNotNull('nama_rombel')
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
        
        $isiSurat = $template->template_isi;

        // Replace Placeholder
        $isiSurat = str_replace('{{nama}}', $siswa->nama ?? '-', $isiSurat);
        $isiSurat = str_replace('{{nisn}}', $siswa->nisn ?? '-', $isiSurat);
        $isiSurat = str_replace('{{nipd}}', $siswa->nipd ?? '-', $isiSurat); 
        $isiSurat = str_replace('{{kelas}}', $siswa->nama_rombel ?? '-', $isiSurat);
        $isiSurat = str_replace('{{tempat_lahir}}', $siswa->tempat_lahir ?? '-', $isiSurat);
        $isiSurat = str_replace('{{tanggal_lahir}}', $siswa->tanggal_lahir ?? '-', $isiSurat);
        $isiSurat = str_replace('{{alamat}}', $siswa->alamat ?? '-', $isiSurat);
        $isiSurat = str_replace('{{nama_wali}}', $siswa->nama_wali ?? '-', $isiSurat);
        
        $tanggalIndo = Carbon::parse($request->tanggal_surat)->locale('id')->isoFormat('D MMMM Y');
        $isiSurat = str_replace('{{tanggal}}', $tanggalIndo, $isiSurat);

        // KIRIM DATA SETTING (UKURAN KERTAS, FONT, DLL) KE VIEW
        return back()
            ->withInput()
            ->with('preview_surat', $isiSurat)
            ->with('template_setting', $template); 
    }

    public function getSiswaByKelas($nama_rombel)
    {
        $nama_rombel = urldecode($nama_rombel);
        $siswas = Siswa::where('nama_rombel', $nama_rombel)
                        ->orderBy('nama', 'asc')
                        ->select('id', 'nama', 'nisn')
                        ->get();
        return response()->json($siswas);
    }
}