<?php

namespace App\Http\Controllers\Admin\Kesiswaan\Ppdb;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SyaratPendaftaran;
use App\Models\TahunPelajaran;
use App\Models\JalurPendaftaran;

class SyaratController extends Controller
{
    public function index()
    {
        // cari tahun aktif
        $tahunPpdb = TahunPelajaran::where('is_active', 1)
            ->with([
                'jalurs' => fn($q) => $q->where('is_active', true), // jalur aktif
                'syarats.jalurPendaftaran' // syarat beserta jalurnya
            ])
            ->first();
            
        // kalau ada tahun aktif, ambil data relasi
        $syaratPendaftaran = $tahunPpdb ? $tahunPpdb->syarats : collect();
        $jalursAktif = $tahunPpdb ? $tahunPpdb->jalurs : collect();
            
        return view('admin.kesiswaan.ppdb.syarat_pendaftaran', compact(
            'tahunPpdb',
            'syaratPendaftaran',
            'jalursAktif'
        ));
    }


    public function store(Request $request)
    {
        try {
            $request->validate([
                'tahun_id' => 'required|exists:tahun_pelajarans,id',
                'jalur_id' => 'required|exists:jalur_pendaftarans,id',
                'syarat'   => 'required|string',
            ]);

            SyaratPendaftaran::create([
                'tahunPelajaran_id'   => $request->tahun_id,
                'jalurPendaftaran_id' => $request->jalur_id,
                'syarat'              => $request->syarat,
                'is_active'           => 0,
            ]);

            return redirect()->route('admin.kesiswaan.ppdb.syarat-ppdb.index')
                             ->with('success', 'Syarat berhasil ditambahkan.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menambahkan syarat.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'jalur_id' => 'required|exists:jalur_pendaftarans,id',
                'syarat'   => 'required|string',
            ]);

            $syarat = SyaratPendaftaran::findOrFail($id);
            $syarat->update([
                'jalurPendaftaran_id' => $request->jalur_id,
                'syarat'              => $request->syarat,
            ]);

            return redirect()->route('admin.kesiswaan.ppdb.syarat-ppdb.index')
                             ->with('success', 'Syarat berhasil diperbarui.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui syarat.');
        }
    }

    public function destroy($id)
    {
        try {
            $syarat = SyaratPendaftaran::findOrFail($id);
            $syarat->delete();

            return redirect()->route('admin.kesiswaan.ppdb.syarat-ppdb.index')
                             ->with('success', 'Syarat berhasil dihapus.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus syarat.');
        }
    }

    public function toggleActive($id)
    {
        try {
            $syarat = SyaratPendaftaran::findOrFail($id);

            if (! $syarat->is_active) {
                $syarat->is_active = true;
                $syarat->save();
                $message = "{$syarat->syarat} berhasil dijadikan Active";
            } else {
                $syarat->is_active = false;
                $syarat->save();
                $message = "{$syarat->syarat} berhasil di-nonaktifkan";
            }

            return redirect()->back()->with('success', $message);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengubah status syarat.');
        }
    }
    
    
    public function getByJalur($jalurId)
{
    // Cari tahun pelajaran yang aktif
    $tahunAktif = TahunPelajaran::where('is_active', 1)->first();

    if (! $tahunAktif) {
        return response()->json([]);
    }

    // Ambil syarat berdasarkan tahun aktif + jalur
    $syarat = SyaratPendaftaran::where('tahunPelajaran_id', $tahunAktif->id)
                ->where('jalurPendaftaran_id', $jalurId)
                ->where('is_active', true) // kalau mau hanya syarat aktif
                ->get(['id', 'syarat']);

    return response()->json($syarat);
}



}
