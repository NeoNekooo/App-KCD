<?php

namespace App\Http\Controllers\Admin\Kepegawaian;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TipeSurat;
use App\Models\Tapel;

class TempalateSuratSkController extends Controller
{
    public function index()
    {
        $templates = TipeSurat::where('kategori', 'sk')
            ->latest()
            ->get();

        $tapels = Tapel::latest()->get();
        $template = null;

        return view('admin.kepegawaian.template-sk.index', compact('templates', 'template', 'tapels'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul_surat'   => 'required|string|max:255',
            'template_isi'  => 'required',
            'ukuran_kertas' => 'required',
        ]);

        $tapelAktif = Tapel::where('is_active', 1)->first();
        $tapelId = $tapelAktif ? $tapelAktif->id : null;

        TipeSurat::create([
            'judul_surat'   => $request->judul_surat,
            'tapel_id'      => $tapelId,
            'kategori'      => 'sk',
            'template_isi'  => $request->template_isi,
            'ukuran_kertas' => $request->ukuran_kertas,
            'use_kop'       => $request->has('use_kop') ? 1 : 0,
            // Tambahkan baris di bawah ini agar margin tersimpan
            'margin_top'    => $request->margin_top ?? 20,
            'margin_left'   => $request->margin_left ?? 25,
            'margin_right'  => $request->margin_right ?? 25,
            'margin_bottom' => $request->margin_bottom ?? 20,
        ]);

        return redirect()
            ->route('admin.kepegawaian.TemplateSk.index')
            ->with('success', 'Template SK berhasil disimpan!');
    }

    public function edit($id)
    {
        $template = TipeSurat::findOrFail($id);

        if ($template->kategori !== 'sk') {
            return redirect()->route('admin.kepegawaian.TemplateSk.index');
        }

        $templates = TipeSurat::where('kategori', 'sk')->latest()->get();
        $tapels = Tapel::latest()->get();

        return view('admin.kepegawaian.template-sk.index', compact('template', 'templates', 'tapels'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'judul_surat'   => 'required|string|max:255',
            'template_isi'  => 'required',
            'ukuran_kertas' => 'required',
        ]);

        $tipeSurat = TipeSurat::findOrFail($id);
        $tapelAktif = Tapel::where('is_active', 1)->first();

        $tipeSurat->update([
            'judul_surat'   => $request->judul_surat,
            'tapel_id'      => $tapelAktif ? $tapelAktif->id : $tipeSurat->tapel_id,
            'template_isi'  => $request->template_isi,
            'ukuran_kertas' => $request->ukuran_kertas,
            'use_kop'       => $request->has('use_kop') ? 1 : 0,
            // Tambahkan baris di bawah ini agar margin terupdate
            'margin_top'    => $request->margin_top,
            'margin_left'   => $request->margin_left,
            'margin_right'  => $request->margin_right,
            'margin_bottom' => $request->margin_bottom,
        ]);

        return redirect()
            ->route('admin.kepegawaian.TemplateSk.index')
            ->with('success', 'Template SK berhasil diperbarui!');
    }

    public function destroy($id)
    {
        TipeSurat::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Template SK berhasil dihapus.');
    }
}