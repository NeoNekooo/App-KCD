<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Galeri;
use App\Models\GaleriItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GaleriController extends Controller
{
    public function index()
    {
        $galeri = Galeri::withCount('items')->orderBy('created_at', 'desc')->get();
        return view('admin.website.galeri.index', compact('galeri'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'tanggal' => 'nullable|date',
            'deskripsi' => 'nullable|string',
            'foto' => 'nullable|image|max:2048',
            'items.*' => 'nullable|image|max:4096',
        ]);

        $data = $request->only('judul', 'tanggal', 'deskripsi');
        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('galeri', 'public');
        }

        $galeri = Galeri::create($data);

        if ($request->hasFile('items')) {
            foreach ($request->file('items') as $file) {
                GaleriItem::create([
                    'galeri_id' => $galeri->id,
                    'file' => $file->store('galeri/items', 'public'),
                    'jenis' => 'foto',
                ]);
            }
        }

        return redirect()->back()->with('success', 'Album galeri berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $galeri = Galeri::findOrFail($id);
        $request->validate([
            'judul' => 'required|string|max:255',
            'tanggal' => 'nullable|date',
            'deskripsi' => 'nullable|string',
            'foto' => 'nullable|image|max:2048',
            'items.*' => 'nullable|image|max:4096',
        ]);

        $data = $request->only('judul', 'tanggal', 'deskripsi');
        if ($request->hasFile('foto')) {
            if ($galeri->foto && Storage::disk('public')->exists($galeri->foto)) {
                Storage::disk('public')->delete($galeri->foto);
            }
            $data['foto'] = $request->file('foto')->store('galeri', 'public');
        }
        $galeri->update($data);

        if ($request->hasFile('items')) {
            foreach ($request->file('items') as $file) {
                GaleriItem::create([
                    'galeri_id' => $galeri->id,
                    'file' => $file->store('galeri/items', 'public'),
                    'jenis' => 'foto',
                ]);
            }
        }

        return redirect()->back()->with('success', 'Album galeri berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $galeri = Galeri::findOrFail($id);
        foreach ($galeri->items as $item) {
            if (Storage::disk('public')->exists($item->file)) {
                Storage::disk('public')->delete($item->file);
            }
            $item->delete();
        }
        if ($galeri->foto && Storage::disk('public')->exists($galeri->foto)) {
            Storage::disk('public')->delete($galeri->foto);
        }
        $galeri->delete();
        return redirect()->back()->with('success', 'Album galeri berhasil dihapus.');
    }

    public function destroyItem($id)
    {
        $item = GaleriItem::findOrFail($id);
        if (Storage::disk('public')->exists($item->file)) {
            Storage::disk('public')->delete($item->file);
        }
        $item->delete();
        return redirect()->back()->with('success', 'Foto berhasil dihapus.');
    }
}
