<?php

namespace App\Http\Controllers\Admin\Kepegawaian;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TugasPegawaiKcd;
use App\Models\PegawaiKcd; 
use Illuminate\Support\Facades\Validator;

class TugasPegawaiKcdController extends Controller
{
    public function index()
    {
        // 1. Ambil data tugas + relasi pegawai
        $tugas = TugasPegawaiKcd::with('pegawai')->latest()->get();
        
        // 2. Ambil data pegawai buat dropdown
        $pegawais = PegawaiKcd::orderBy('nama', 'asc')->get();

        // 3. DAFTAR KATEGORI LAYANAN (Sesuai config sidebar_menu.php kamu)
        $listKategori = [
            'kenaikan-pangkat' => 'Kenaikan Pangkat',
            'kgb'              => 'KGB (Gaji Berkala)',
            'mutasi'           => 'Mutasi',
            'relokasi'         => 'Relokasi / Penempatan',
            'satya-lencana'    => 'Satya Lencana',
            'hukuman-disiplin' => 'Hukuman Disiplin',
            'verifikasi-surat' => 'Verifikasi Surat Lainnya',
        ];

        return view('admin.kepegawaian.tugas_kcd.index', compact('tugas', 'pegawais', 'listKategori'));
    }

    public function store(Request $request)
    {
        // Validasi
        $validator = Validator::make($request->all(), [
            'pegawai_kcd_id'   => 'required|exists:pegawai_kcds,id',
            'nama_tugas'       => 'required|string|max:255',
            'kategori_layanan' => 'nullable|string', // Boleh kosong
            'no_sk'            => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->with('error', 'Gagal menyimpan. Cek kelengkapan data.');
        }

        try {
            TugasPegawaiKcd::create([
                'pegawai_kcd_id'   => $request->pegawai_kcd_id,
                'nama_tugas'       => $request->nama_tugas,
                'kategori_layanan' => $request->kategori_layanan,
                'no_sk'            => $request->no_sk,
                'deskripsi'        => $request->deskripsi,
                'is_active'        => 1
            ]);

            return back()->with('success', 'Penugasan berhasil disimpan!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error Sistem: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $data = TugasPegawaiKcd::findOrFail($id);
        $data->delete();
        return back()->with('success', 'Data penugasan berhasil dihapus.');
    }
}