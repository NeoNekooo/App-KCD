<?php

namespace App\Http\Controllers\Admin\Kesiswaan\Ppdb;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\QuotaPendaftaran;
use App\Models\TahunPelajaran;
use App\Models\Rombel;

class QuotaController extends Controller
{
    public function index()
    {
        $tahunPpdb = TahunPelajaran::where('is_active', 1)->first();
        $quotas = $tahunPpdb
            ? QuotaPendaftaran::where('tahunPelajaran_id', $tahunPpdb->id)->get()
            : collect(); // jika tidak ada tahun aktif, koleksi kosong

        
        // Ambil jurusan untuk filter
        $jurusans = Rombel::select('jurusan_id_str')
                    ->distinct()
                    ->orderBy('jurusan_id_str')
                    ->pluck('jurusan_id_str');

        return view('admin.kesiswaan.ppdb.quota_pendaftaran', compact('quotas', 'tahunPpdb','jurusans'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'tahun_id'      => 'required|exists:tahun_pelajarans,id',
                'keahlian'      => 'required|string',
                'jumlah_kelas'  => 'required|integer|min:1',
                'quota'         => 'required|integer|min:1',
            ]);

            QuotaPendaftaran::create([
                'tahunPelajaran_id' => $request->tahun_id,
                'keahlian'          => $request->keahlian,
                'jumlah_kelas'      => $request->jumlah_kelas,
                'quota'             => $request->quota,
            ]);

            return redirect()->back()->with('success', 'Quota berhasil ditambahkan.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Gagal menambahkan quota: ' . $th->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'keahlian'      => 'required|string',
                'jumlah_kelas'  => 'required|integer|min:1',
                'quota'         => 'required|integer|min:1',
            ]);

            $quota = QuotaPendaftaran::findOrFail($id);
            $quota->update([
                'keahlian'      => $request->keahlian,
                'jumlah_kelas'  => $request->jumlah_kelas,
                'quota'         => $request->quota,
            ]);

            return redirect()->back()->with('success', 'Quota berhasil diupdate.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Gagal mengupdate quota: ' . $th->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $quota = QuotaPendaftaran::findOrFail($id);
            $quota->delete();
        
            return redirect()->back()->with('success', 'Quota berhasil dihapus.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Gagal menghapus quota: ' . $th->getMessage());
        }
    }
}
