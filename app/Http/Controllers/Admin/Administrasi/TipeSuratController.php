<?php

namespace App\Http\Controllers\Admin\Administrasi;

use App\Http\Controllers\Controller;
use App\Models\TipeSurat;
use App\Models\Tapel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TipeSuratController extends Controller
{
    public function index(Request $request)
    {
        // Default ke 'siswa' jika kosong
        $kategori = $request->get('kategori', 'siswa');

        // Query data berdasarkan kategori
        $templates = TipeSurat::where('kategori', $kategori)
                             ->latest()
                             ->get();

        $template = null;
        if ($request->has('tipe_surat')) {
             $template = TipeSurat::find($request->tipe_surat);
        }

        return view('admin.administrasi.tipe_surat.index', compact('templates', 'kategori', 'template'));
    }

    public function store(Request $request)
    {
        $this->validateRequest($request);

        $tapelAktif = Tapel::where('is_active', 1)->first();
        $tapelId = $tapelAktif ? $tapelAktif->id : null;

        TipeSurat::create($this->prepareData($request, $tapelId));

        return redirect()
                ->route('admin.administrasi.tipe-surat.index', ['kategori' => $request->kategori])
                ->with('success', 'Template surat berhasil disimpan!');
    }

    public function update(Request $request, $id)
    {
        $this->validateRequest($request);
        $tipeSurat = TipeSurat::findOrFail($id);
        $tipeSurat->update($this->prepareData($request, $tipeSurat->tapel_id));

        return redirect()
            ->route('admin.administrasi.tipe-surat.index', ['kategori' => $tipeSurat->kategori])
            ->with('success', 'Template surat berhasil diperbarui!');
    }

    public function edit($id)
    {
        $template = TipeSurat::findOrFail($id);
        return redirect()->route('admin.administrasi.tipe-surat.index', [
            'kategori' => $template->kategori,
            'tipe_surat' => $id
        ]);
    }

    public function destroy($id)
    {
        $tipeSurat = TipeSurat::findOrFail($id);
        $kategori = $tipeSurat->kategori;
        $tipeSurat->delete();

        return redirect()
                ->route('admin.administrasi.tipe-surat.index', ['kategori' => $kategori])
                ->with('success', 'Template surat berhasil dihapus.');
    }

    public function duplicate($id)
    {
        $original = TipeSurat::findOrFail($id);
        $copy = $original->replicate();
        $copy->judul_surat = $original->judul_surat . ' (Salinan)';
        $copy->save();

        return back()->with('success', 'Template surat berhasil diduplikasi!');
    }

    private function validateRequest(Request $request)
    {
        $request->validate([
            'judul_surat'   => 'required|string|max:255',
            // 🔥 UPDATE: Tambahkan 'internal' di sini
            'kategori'      => 'required|in:siswa,guru,sk,layanan,internal', 
            'sub_kategori'  => 'nullable|string|max:100',
            'template_isi'  => 'required',
            'ukuran_kertas' => 'required',
            'margin_top'    => 'nullable|integer',
            'margin_right'  => 'nullable|integer',
            'margin_bottom' => 'nullable|integer',
            'margin_left'   => 'nullable|integer',
        ]);
    }

    private function prepareData(Request $request, $tapelId)
    {
        return [
            'judul_surat'   => $request->judul_surat,
            'kategori'      => $request->kategori,
            'sub_kategori'  => $request->sub_kategori,
            'template_isi'  => $request->template_isi,
            'ukuran_kertas' => $request->ukuran_kertas,
            'tapel_id'      => $tapelId,
            'use_kop'       => 0,
            'margin_top'    => $request->margin_top ?? 20,
            'margin_right'  => $request->margin_right ?? 25,
            'margin_bottom' => $request->margin_bottom ?? 20,
            'margin_left'   => $request->margin_left ?? 25,
        ];
    }
}