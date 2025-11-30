<?php

namespace App\Http\Controllers\Admin\Ppdb;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TahunPelajaran;
use App\Models\KompetensiPendaftaran;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class KompetensiPendaftaranController extends Controller
{
    public function index()
    {
        $tahunPpdb = TahunPelajaran::where('is_active', 1)->first();

        $kompetensiPendaftaran = $tahunPpdb
            ? KompetensiPendaftaran::where('tahunPelajaran_id', $tahunPpdb->id)->get()
            : collect();

        return view('admin.ppdb.kompetensi_pendaftaran', compact('kompetensiPendaftaran', 'tahunPpdb'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'tahun_id'   => 'required|exists:tahun_pelajarans,id',
                'kode'       => [
                    'required',
                    'string',
                    Rule::unique('kompetensi_pendaftarans', 'kode')
                        ->where('tahunPelajaran_id', $request->tahun_id),
                ],
                'kompetensi' => 'required|string',
            ]);

            KompetensiPendaftaran::create([
                'tahunPelajaran_id' => $request->tahun_id,
                'kode'              => $request->kode,
                'kompetensi'        => $request->kompetensi,
            ]);

            return redirect()->route('admin.ppdb.kompetensi-ppdb.index')
                             ->with('success', 'Kompetensi berhasil ditambahkan.');
        } catch (ValidationException $e) {
            return redirect()->back()->with('danger', 'Kode ' . $request->kode . ' sudah ada')->withInput();
        } catch (\Throwable $e) {
            return redirect()->route('admin.ppdb.kompetensi-ppdb.index')
                             ->with('error', 'Gagal menambahkan kompetensi: ' . $e->getMessage());
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $request->validate([
                'tahun_id'   => 'required|exists:tahun_pelajarans,id',
                'kode'       => [
                    'required',
                    'string',
                    Rule::unique('kompetensi_pendaftarans', 'kode')
                        ->where(fn ($query) => $query->where('tahunPelajaran_id', $request->tahun_id))
                        ->ignore($id, 'id'),
                ],
                'kompetensi' => 'required|string',
            ]);

            $kompetensi = KompetensiPendaftaran::findOrFail((int)$id);
            $kompetensi->update([
                'tahunPelajaran_id' => $request->tahun_id,
                'kode'              => $request->kode,
                'kompetensi'        => $request->kompetensi,
            ]);

            return redirect()->route('admin.ppdb.kompetensi-ppdb.index')
                             ->with('success', 'Kompetensi berhasil diperbarui.');
        } catch (ValidationException $e) {
            return redirect()->back()->with('danger', 'Kode ' . $request->kode . ' sudah ada')->withInput();
        } catch (\Throwable $e) {
            return redirect()->route('admin.ppdb.kompetensi-ppdb.index')
                             ->with('error', 'Gagal memperbarui kompetensi: ' . $e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        try {
            $kompetensi = KompetensiPendaftaran::findOrFail((int)$id);
            $kompetensi->delete();

            return redirect()->route('admin.ppdb.kompetensi-ppdb.index')
                             ->with('success', "{$kompetensi->kompetensi} berhasil dihapus.");
        } catch (\Throwable $e) {
            return redirect()->route('admin.ppdb.kompetensi-ppdb.index')
                             ->with('error', "Gagal menghapus data: " . $e->getMessage());
        }
    }
}
