<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BeritaController extends Controller
{
    public function index()
    {
        $berita = Berita::orderBy('created_at', 'desc')->get();
        return view('admin.website.berita.index', compact('berita'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'ringkasan' => 'nullable|string|max:500',
            'isi' => 'required|string',
            'gambar' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,publish',
        ]);

        $data = $request->except('gambar');
        $data['penulis'] = auth()->user()->name ?? 'Admin';
        $data['slug'] = Str::slug($request->judul) . '-' . Str::random(5);

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('berita', 'public');
        }
        if ($request->status == 'publish') {
            $data['published_at'] = now();
        }

        Berita::create($data);
        return redirect()->back()->with('success', 'Berita berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $berita = Berita::findOrFail($id);
        $request->validate([
            'judul' => 'required|string|max:255',
            'ringkasan' => 'nullable|string|max:500',
            'isi' => 'required|string',
            'gambar' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,publish',
        ]);

        $data = $request->except('gambar');
        if ($request->hasFile('gambar')) {
            if ($berita->gambar && Storage::disk('public')->exists($berita->gambar)) {
                Storage::disk('public')->delete($berita->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('berita', 'public');
        }
        if ($request->status == 'publish' && !$berita->published_at) {
            $data['published_at'] = now();
        }

        $berita->update($data);
        return redirect()->back()->with('success', 'Berita berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $berita = Berita::findOrFail($id);
        if ($berita->gambar && Storage::disk('public')->exists($berita->gambar)) {
            Storage::disk('public')->delete($berita->gambar);
        }
        $berita->delete();
        return redirect()->back()->with('success', 'Berita berhasil dihapus.');
    }
}
