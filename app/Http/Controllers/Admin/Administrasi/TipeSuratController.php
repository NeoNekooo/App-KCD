<?php

namespace App\Http\Controllers\Admin\Administrasi;

use App\Http\Controllers\Controller;
use App\Models\TipeSurat;
use App\Models\Tapel; 
use Illuminate\Http\Request;

class TipeSuratController extends Controller
{
    /**
     * Halaman utama template surat
     */
    public function index(Request $request)
    {
        $kategori = $request->get('kategori', 'siswa');

        $templates = TipeSurat::where('kategori', $kategori)
                              ->latest()
                              ->get();

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
            'kategori'      => 'required|in:siswa,guru,sk', 
            'template_isi'  => 'required',
            'ukuran_kertas' => 'required',
        ]);

        // --- PERBAIKAN DI SINI ---
        // Menggunakan 'is_active' sesuai nama kolom di database kamu
        $tapelAktif = Tapel::where('is_active', 1)->first(); 
        $tapelId = $tapelAktif ? $tapelAktif->id : null;
        // -------------------------

        TipeSurat::create([
            'judul_surat'   => $request->judul_surat,
            'kategori'      => $request->kategori,
            'template_isi'  => $request->template_isi,
            'ukuran_kertas' => $request->ukuran_kertas,
            'use_kop'       => $request->has('use_kop') ? 1 : 0,
            'tapel_id'      => $tapelId, 
        ]);

        return redirect()
                ->route('admin.administrasi.tipe-surat.index', ['kategori' => $request->kategori])
                ->with('success', 'Template surat berhasil disimpan pada Tahun Pelajaran aktif!');
    }

    /**
     * Edit template
     */
    public function edit($id)
    {
        $template = TipeSurat::findOrFail($id);
        $kategori = $template->kategori;

        $templates = TipeSurat::where('kategori', $kategori)->latest()->get();

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
            'use_kop'       => $request->has('use_kop') ? 1 : 0,
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