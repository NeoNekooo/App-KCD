<?php

namespace App\Http\Controllers\Admin\Administrasi;

use App\Http\Controllers\Controller;
use App\Models\TipeSurat;
use App\Models\Tapel;
use Illuminate\Http\Request;

class TipeSuratController extends Controller
{
    /**
     * Halaman utama template surat (List Data)
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
            // Validasi Margin (Boleh kosong/null, tapi harus angka)
            'margin_top'    => 'nullable|integer',
            'margin_right'  => 'nullable|integer',
            'margin_bottom' => 'nullable|integer',
            'margin_left'   => 'nullable|integer',
        ]);

        // Ambil Tahun Pelajaran Aktif
        $tapelAktif = Tapel::where('is_active', 1)->first();
        $tapelId = $tapelAktif ? $tapelAktif->id : null;

        TipeSurat::create([
            'judul_surat'   => $request->judul_surat,
            'kategori'      => $request->kategori,
            'template_isi'  => $request->template_isi,
            'ukuran_kertas' => $request->ukuran_kertas,
            'tapel_id'      => $tapelId,
            
            // Logic Checkbox Use Kop (Jika dicentang = 1, jika tidak = 0)
            'use_kop'       => $request->has('use_kop') ? 1 : 0,

            // Simpan Margin (Default ke 20/25 jika input kosong)
            'margin_top'    => $request->margin_top ?? 20,
            'margin_right'  => $request->margin_right ?? 25,
            'margin_bottom' => $request->margin_bottom ?? 20,
            'margin_left'   => $request->margin_left ?? 25,
        ]);

        return redirect()
                ->route('admin.administrasi.tipe-surat.index', ['kategori' => $request->kategori])
                ->with('success', 'Template surat berhasil disimpan!');
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
            // Validasi Margin
            'margin_top'    => 'nullable|integer',
            'margin_right'  => 'nullable|integer',
            'margin_bottom' => 'nullable|integer',
            'margin_left'   => 'nullable|integer',
        ]);

        $tipeSurat = TipeSurat::findOrFail($id);

        $tipeSurat->update([
            'judul_surat'   => $request->judul_surat,
            'template_isi'  => $request->template_isi,
            'ukuran_kertas' => $request->ukuran_kertas,
            
            // Update Kop (PENTING: Gunakan has() untuk checkbox)
            'use_kop'       => $request->has('use_kop') ? 1 : 0,

            // Update Margin
            'margin_top'    => $request->margin_top ?? 20,
            'margin_right'  => $request->margin_right ?? 25,
            'margin_bottom' => $request->margin_bottom ?? 0,
            'margin_left'   => $request->margin_left ?? 25,
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