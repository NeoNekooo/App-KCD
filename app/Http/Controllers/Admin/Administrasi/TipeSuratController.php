<?php

namespace App\Http\Controllers\Admin\Administrasi;

use App\Http\Controllers\Controller;
use App\Models\TipeSurat;
use Illuminate\Http\Request;

class TipeSuratController extends Controller
{
    /**
     * Halaman utama template surat (Siswa/Guru)
     */
    public function index(Request $request)
    {
        // Tentukan kategori dari query string, default = siswa
        $kategori = $request->get('kategori', 'siswa');

        // Ambil list template berdasarkan kategori
        $templates = TipeSurat::where('kategori', $kategori)
                              ->latest()
                              ->get();

        return view('admin.administrasi.tipe_surat.index', compact('templates', 'kategori'));
    }

    /**
     * Simpan template baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul_surat'  => 'required|string|max:255',
            'kategori'     => 'required|in:siswa,guru',
            'template_isi' => 'required',
        ]);

        TipeSurat::create([
            'judul_surat'  => $request->judul_surat,
            'kategori'     => $request->kategori,
            'template_isi' => $request->template_isi,
            'ukuran_kertas' => $request->ukuran_kertas,
        ]);

        return redirect()
                ->route('admin.administrasi.tipe-surat.index', ['kategori' => $request->kategori])
                ->with('success', 'Template surat berhasil disimpan!');
    }

    /**
     * Edit template.
     */
    public function edit($id, Request $request)
    {
        $template = TipeSurat::findOrFail($id);

        $kategori = $template->kategori;

        $templates = TipeSurat::where('kategori', $kategori)->latest()->get();

        return view('admin.administrasi.tipe_surat.index', compact('template','templates','kategori'));
    }

    /**
     * Update template.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'judul_surat'  => 'required|string|max:255',
            'template_isi' => 'required',
            'ukuran_kertas' => 'nullable|string',
        ]);

        $tipeSurat = TipeSurat::findOrFail($id);

        $tipeSurat->update([
            'judul_surat'   => $request->judul_surat,
            'template_isi'  => $request->template_isi,
            'ukuran_kertas' => $request->ukuran_kertas,
        ]);

        return redirect()
            ->route('admin.administrasi.tipe-surat.index', ['kategori' => $tipeSurat->kategori])
            ->with('success', 'Template surat berhasil diperbarui!');
    }

    /**
     * Hapus template.
     */
    public function destroy($id)
    {
        $tipeSurat = TipeSurat::findOrFail($id);
        $kategori = $tipeSurat->kategori;
        $tipeSurat->delete();

        return redirect()
                ->route('admin.administrasi.tipe-surat.index', ['kategori' => $kategori])
                ->with('success', 'Template surat berhasil dihapus.');
    }
}
