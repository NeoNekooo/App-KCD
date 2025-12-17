<?php

namespace App\Http\Controllers\Admin\Administrasi;

use App\Http\Controllers\Controller;
use App\Models\TipeSurat;
use Illuminate\Http\Request;

class TipeSuratController extends Controller
{
    /**
     * Halaman utama template surat
     */
    public function index(Request $request)
    {
        // 1. Ambil kategori dari URL, default 'siswa'
        $kategori = $request->get('kategori', 'siswa');

        // 2. Ambil data list sesuai kategori
        $templates = TipeSurat::where('kategori', $kategori)
                              ->latest()
                              ->get();

        // 3. Variabel $template kosong (mode create)
        $template = null;

        return view('admin.administrasi.tipe_surat.index', compact('templates', 'kategori', 'template'));
    }

    /**
     * Simpan template baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul_surat'   => 'required|string|max:255',
            // UPDATE: Tambahkan 'sk' pada validasi
            'kategori'      => 'required|in:siswa,guru,sk', 
            'template_isi'  => 'required',
            // UPDATE: Tambahkan validasi ukuran kertas
            'ukuran_kertas' => 'required',
        ]);

        TipeSurat::create([
            'judul_surat'   => $request->judul_surat,
            'kategori'      => $request->kategori,
            'template_isi'  => $request->template_isi,
            'ukuran_kertas' => $request->ukuran_kertas,
            // Font size dihapus sesuai permintaan migrasi
        ]);

        return redirect()
                ->route('admin.administrasi.tipe-surat.index', ['kategori' => $request->kategori])
                ->with('success', 'Template surat berhasil disimpan!');
    }

    /**
     * Edit template
     * Mengembalikan view index tapi dengan variabel $template terisi
     */
    public function edit($id)
    {
        $template = TipeSurat::findOrFail($id);
        $kategori = $template->kategori;

        // Ambil list untuk sidebar kanan
        $templates = TipeSurat::where('kategori', $kategori)->latest()->get();

        // Return ke view index, tapi bawa data $template untuk diedit
        return view('admin.administrasi.tipe_surat.index', compact('template', 'templates', 'kategori'));
    }

    /**
     * Update template
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'judul_surat'   => 'required|string|max:255',
            'template_isi'  => 'required',
            'ukuran_kertas' => 'required',
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
     * Hapus template
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