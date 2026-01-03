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

        // Jika ada parameter tipe_surat (dari redirect edit), ambil datanya
        if ($request->has('tipe_surat')) {
             $template = TipeSurat::find($request->tipe_surat);
        }

        return view('admin.administrasi.tipe_surat.index', compact('templates', 'kategori', 'template'));
    }

    /**
     * Simpan template baru
     */
    public function store(Request $request)
    {
        $this->validateRequest($request);

        // Ambil Tahun Pelajaran Aktif
        $tapelAktif = Tapel::where('is_active', 1)->first();
        $tapelId = $tapelAktif ? $tapelAktif->id : null;

        TipeSurat::create($this->prepareData($request, $tapelId));

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
        
        return redirect()->route('admin.administrasi.tipe-surat.index', [
            'kategori' => $template->kategori,
            'tipe_surat' => $id
        ]);
    }

    /**
     * Update template
     */
    public function update(Request $request, $id)
    {
        $this->validateRequest($request);

        $tipeSurat = TipeSurat::findOrFail($id);

        $tipeSurat->update($this->prepareData($request, $tipeSurat->tapel_id));

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

    /**
     * Validasi Request
     */
    private function validateRequest(Request $request)
    {
        $request->validate([
            'judul_surat'   => 'required|string|max:255',
            'kategori'      => 'required|in:siswa,guru,sk',
            'template_isi'  => 'required',
            'ukuran_kertas' => 'required',
            // 'orientasi' dihapus karena tidak ada di DB
            'margin_top'    => 'nullable|integer',
            'margin_right'  => 'nullable|integer',
            'margin_bottom' => 'nullable|integer',
            'margin_left'   => 'nullable|integer',
        ]);
    }

    /**
     * Prepare Data untuk Simpan/Update
     */
    private function prepareData(Request $request, $tapelId)
    {
        return [
            'judul_surat'   => $request->judul_surat,
            'kategori'      => $request->kategori,
            'template_isi'  => $request->template_isi,
            'ukuran_kertas' => $request->ukuran_kertas,
            'tapel_id'      => $tapelId,
            
            // 'orientasi' dihapus agar tidak error SQL 'Column not found'
            
            'use_kop'       => 0, // Default 0 (Fitur dimatikan)

            // Margin
            'margin_top'    => $request->margin_top ?? 20,
            'margin_right'  => $request->margin_right ?? 25,
            
            // UPDATE: Default Margin Bottom diset 20 jika kosong, agar ada jarak aman
            'margin_bottom' => $request->margin_bottom ?? 20, 
            
            'margin_left'   => $request->margin_left ?? 25,
        ];
    }
}