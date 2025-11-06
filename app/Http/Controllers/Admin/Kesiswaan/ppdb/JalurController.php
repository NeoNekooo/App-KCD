<?php

namespace App\Http\Controllers\Admin\Kesiswaan\Ppdb;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TahunPelajaran;
use App\Models\JalurPendaftaran;
use Illuminate\Validation\Rule;


class JalurController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tahunPpdb = TahunPelajaran::where('is_active', 1)->first();

        $jalurPendaftaran = $tahunPpdb
            ? JalurPendaftaran::where('tahunPelajaran_id', $tahunPpdb->id)->get()
            : collect(); // jika tidak ada tahun aktif, koleksi kosong

        return view('admin.kesiswaan.ppdb.jalur_pendaftaran', compact('jalurPendaftaran', 'tahunPpdb'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'tahun_id'   => 'required|exists:tahun_pelajarans,id',
                'kode'       => [
                    'required',
                    'string',
                    Rule::unique('jalur_pendaftarans', 'kode')
                        ->where('tahunPelajaran_id', $request->tahun_id),
                ],
                'jalur'      => 'required|string',
                'keterangan' => 'required|string',
            ]);

            JalurPendaftaran::create([
                'tahunPelajaran_id' => $request->tahun_id,
                'kode'              => $request->kode,
                'jalur'             => $request->jalur,
                'keterangan'        => $request->keterangan,
                'is_active'         => 0,
            ]);

            return redirect()->route('admin.kesiswaan.ppdb.jalur-ppdb.index')
                             ->with('success', 'Jalur berhasil ditambahkan.');
        } catch (\Throwable $e) {
            return redirect()->route('admin.kesiswaan.ppdb.jalur-ppdb.index')
                             ->with('error', 'Gagal menambahkan jalur: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $request->validate([
                'tahun_id'   => 'required|exists:tahun_pelajarans,id',
                'kode'       => [
                    'required',
                    'string',
                    Rule::unique('jalur_pendaftarans', 'kode')
                        ->where(fn ($query) => $query->where('tahunPelajaran_id', $request->tahun_id))
                        ->ignore($id), // abaikan data yg sedang diupdate
                ],
                'jalur'      => 'required|string',
                'keterangan' => 'required|string',
            ]);

            $jalur = JalurPendaftaran::findOrFail($id);
            $jalur->update([
                'tahunPelajaran_id' => $request->tahun_id,
                'kode'       => $request->kode,
                'jalur'      => $request->jalur,
                'keterangan' => $request->keterangan,
            ]);

            return redirect()->route('admin.kesiswaan.ppdb.jalur-ppdb.index')
                             ->with('success', 'Jalur berhasil diperbarui.');
        } catch (\Throwable $e) {
            return redirect()->route('admin.kesiswaan.ppdb.jalur-ppdb.index')
                             ->with('error', 'Gagal memperbarui jalur: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $jalur = JalurPendaftaran::with('syarats')->findOrFail($id);

            // Cek apakah masih ada syarat
            if ($jalur->syarats->count() > 0) {
                return redirect()->route('admin.kesiswaan.ppdb.jalur-ppdb.index')
                                 ->with('error', "Tidak bisa menghapus {$jalur->jalur}, masih ada syarat terkait.");
            }

            $jalur->delete();

            return redirect()->route('admin.kesiswaan.ppdb.jalur-ppdb.index')
                             ->with('success', "{$jalur->jalur} berhasil dihapus.");
        } catch (\Throwable $e) {
            return redirect()->route('admin.kesiswaan.ppdb.jalur-ppdb.index')
                             ->with('error', "Terjadi kesalahan: " . $e->getMessage());
        }
    }

    /**
     * Toggle Active status
     */
    public function toggleActive($id)
    {
        try {
            $jalur = JalurPendaftaran::findOrFail($id);

            // Hanya toggle jalur yang dipilih, tanpa memengaruhi jalur lain
            $jalur->is_active = ! $jalur->is_active;
            $jalur->save();

            $message = $jalur->is_active
                ? "{$jalur->jalur} berhasil dijadikan Active"
                : "{$jalur->jalur} berhasil di-nonaktifkan";

            return redirect()->back()->with('success', $message);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal mengubah status jalur: ' . $e->getMessage());
        }
    }

}
