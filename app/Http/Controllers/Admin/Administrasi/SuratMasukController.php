<?php

namespace App\Http\Controllers\Admin\Administrasi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SuratMasuk;
use Illuminate\Support\Facades\Storage;

class SuratMasukController extends Controller
{
    /**
     * TAMPILKAN DAFTAR SURAT MASUK (dengan search & filter tanggal)
     */
    public function index(Request $request)
    {
        $query = SuratMasuk::query();

        // Search global (no_surat, asal_surat, perihal)
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('no_surat', 'like', "%{$request->search}%")
                  ->orWhere('asal_surat', 'like', "%{$request->search}%")
                  ->orWhere('perihal', 'like', "%{$request->search}%");
            });
        }

        // Filter tanggal diterima
        if ($request->filled('from')) {
            $query->whereDate('tanggal_diterima', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('tanggal_diterima', '<=', $request->to);
        }

        $suratMasuks = $query->orderBy('tanggal_diterima', 'desc')
                             ->paginate(10)
                             ->appends($request->except('page'));

        return view('admin.administrasi.surat_masuk.index', compact('suratMasuks'));
    }

    /**
     * SIMPAN DATA SURAT MASUK BARU
     */
    public function store(Request $request)
    {
        $request->validate([
            'no_agenda'         => 'nullable|string',
            'no_surat'          => 'required|string',
            'tanggal_surat'     => 'required|date',
            'tanggal_diterima'  => 'required|date',
            'asal_surat'        => 'required|string',
            'perihal'           => 'required|string',
            'tujuan_disposisi'  => 'nullable|string',
            'keterangan'        => 'nullable|string',
            'file_surat'        => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $filePath = null;
        if ($request->hasFile('file_surat')) {
            // simpan di storage/app/public/surat_masuk
            $filePath = $request->file('file_surat')->store('surat-masuk', 'public');
        }

        SuratMasuk::create([
            'no_agenda' => $request->no_agenda,
            'no_surat' => $request->no_surat,
            'tanggal_surat' => $request->tanggal_surat,
            'tanggal_diterima' => $request->tanggal_diterima,
            'asal_surat' => $request->asal_surat,
            'perihal' => $request->perihal,
            'tujuan_disposisi' => $request->tujuan_disposisi,
            'file_surat' => $filePath, // path relatif untuk disk public
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('admin.administrasi.surat-masuk.index')
                         ->with('success', 'Surat Masuk berhasil dicatat!');
    }

    /**
     * TAMPILKAN HALAMAN EDIT (opsional jika pakai modal di index)
     */
    public function edit($id)
    {
        $item = SuratMasuk::findOrFail($id);
        return view('admin.administrasi.surat_masuk.edit', compact('item'));
    }

    /**
     * UPDATE DATA SURAT
     */
    public function update(Request $request, $id)
    {
        $item = SuratMasuk::findOrFail($id);

        $request->validate([
            'no_agenda'         => 'nullable|string',
            'no_surat'          => 'required|string',
            'tanggal_surat'     => 'required|date',
            'tanggal_diterima'  => 'required|date',
            'asal_surat'        => 'required|string',
            'perihal'           => 'required|string',
            'tujuan_disposisi'  => 'nullable|string',
            'keterangan'        => 'nullable|string',
            'file_surat'        => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        // jika ada file baru, hapus file lama (jika ada) lalu simpan file baru
        if ($request->hasFile('file_surat')) {
            if ($item->file_surat && Storage::disk('public')->exists($item->file_surat)) {
                Storage::disk('public')->delete($item->file_surat);
            }
            $newPath = $request->file('file_surat')->store('surat_masuk', 'public');
            $item->file_surat = $newPath;
        }

        $item->update([
            'no_agenda' => $request->no_agenda,
            'no_surat' => $request->no_surat,
            'tanggal_surat' => $request->tanggal_surat,
            'tanggal_diterima' => $request->tanggal_diterima,
            'asal_surat' => $request->asal_surat,
            'perihal' => $request->perihal,
            'tujuan_disposisi' => $request->tujuan_disposisi,
            'keterangan' => $request->keterangan,
            'file_surat' => $item->file_surat, // sudah di-set jika ada upload baru
        ]);

        return redirect()->route('admin.administrasi.surat-masuk.index')
                         ->with('success', 'Data surat berhasil diperbarui!');
    }

    /**
     * HAPUS DATA SURAT
     */
    public function destroy($id)
    {
        $item = SuratMasuk::findOrFail($id);

        if ($item->file_surat && Storage::disk('public')->exists($item->file_surat)) {
            Storage::disk('public')->delete($item->file_surat);
        }

        $item->delete();

        return redirect()->route('admin.administrasi.surat-masuk.index')
                         ->with('success', 'Surat masuk berhasil dihapus.');
    }
}
