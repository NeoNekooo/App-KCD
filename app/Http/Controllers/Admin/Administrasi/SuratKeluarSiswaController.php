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
    /**
     * MENAMPILKAN FORMULIR FILTER CETAK
     */
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

    /**
     * PROSES GENERATE SURAT
     * Mengembalikan ke halaman yang sama dengan data preview
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'tipe_surat_id' => 'required',
            'siswa_id'      => 'required',
            'tanggal_surat' => 'required|date',
        ]);

        // 2. Ambil Data Master
        $template = TipeSurat::findOrFail($request->tipe_surat_id);
        $siswa    = Siswa::findOrFail($request->siswa_id);
        
        // 3. LOGIKA REPLACE (Mengganti {{kode}} dengan data asli)
        // Pastikan menggunakan strip_tags jika ingin menghapus tag HTML yang tidak diinginkan, 
        // tapi karena ini CKEditor biasanya kita butuh format HTML-nya.
        $isiSurat = $template->template_isi;

        // Replace Placeholder dengan Data Siswa
        // Menggunakan operator null coalescing (??) untuk menghindari error jika data kosong
        $isiSurat = str_replace('{{nama}}', $siswa->nama ?? '-', $isiSurat);
        $isiSurat = str_replace('{{nisn}}', $siswa->nisn ?? '-', $isiSurat);
        $isiSurat = str_replace('{{nipd}}', $siswa->nipd ?? '-', $isiSurat); 
        $isiSurat = str_replace('{{kelas}}', $siswa->nama_rombel ?? '-', $isiSurat);
        
        // Format Tanggal Surat (Indonesia)
        $tanggalIndo = Carbon::parse($request->tanggal_surat)->locale('id')->isoFormat('D MMMM Y');
        $isiSurat = str_replace('{{tanggal}}', $tanggalIndo, $isiSurat);

        // 4. KEMBALI KE HALAMAN SEBELUMNYA DENGAN DATA
        // withInput() -> Agar pilihan dropdown (kelas, siswa, tanggal) tidak hilang/reset
        // with('preview_surat') -> Mengirim hasil surat ke View untuk ditampilkan
        return back()
            ->withInput()
            ->with('preview_surat', $isiSurat);
    }

    /**
     * AJAX: AMBIL SISWA PER KELAS
     */
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