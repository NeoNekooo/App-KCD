<?php

namespace App\Http\Controllers\Admin\Landing;

use App\Http\Controllers\Controller;
use App\Models\Prestasi;
use App\Models\PrestasiItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PrestasiController extends Controller
{
    public function index(Request $request)
    {
        // Load count items
        $prestasis = Prestasi::withCount('items')->latest()->paginate(9);
        return view('admin.landing.prestasi.index', compact('prestasis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul'         => 'required|string|max:255',
            'nama_pemenang' => 'required|string|max:255',
            'tingkat'       => 'required',
            'foto'          => 'required|image|mimes:jpeg,png,jpg,webp|max:2048', // Cover
            'tanggal'       => 'nullable|date',
        ]);

        $data = $request->except(['_token']);

        if ($request->hasFile('foto')) {
            $image = $request->file('foto');
            $image->storeAs('prestasis/covers', $image->hashName(), 'public');
            $data['foto'] = $image->hashName();
        }

        Prestasi::create($data);

        return redirect()->back()->with('success', 'Prestasi berhasil ditambahkan!');
    }

    // Halaman Detail untuk Upload Banyak Foto
    public function show($id)
    {
        $prestasi = Prestasi::with('items')->findOrFail($id);
        return view('admin.landing.prestasi.show', compact('prestasi'));
    }

    public function update(Request $request, $id)
    {
        $prestasi = Prestasi::findOrFail($id);

        $request->validate([
            'judul'         => 'required|string|max:255',
            'nama_pemenang' => 'required|string|max:255',
            'tingkat'       => 'required',
            'foto'          => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = $request->except(['_token', '_method']);

        if ($request->hasFile('foto')) {
            // Hapus cover lama
            if ($prestasi->foto && Storage::disk('public')->exists('prestasis/covers/' . $prestasi->foto)) {
                Storage::disk('public')->delete('prestasis/covers/' . $prestasi->foto);
            }
            $image = $request->file('foto');
            $image->storeAs('prestasis/covers', $image->hashName(), 'public');
            $data['foto'] = $image->hashName();
        }

        $prestasi->update($data);

        return redirect()->back()->with('success', 'Data prestasi diperbarui!');
    }

    // Method Upload Item (Banyak Foto)
    public function storeItem(Request $request, $id)
    {
        $prestasi = Prestasi::findOrFail($id);

        $request->validate([
            'files.*' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $file->storeAs('prestasis/items', $file->hashName(), 'public');

                PrestasiItem::create([
                    'prestasi_id' => $prestasi->id,
                    'file'        => $file->hashName(),
                    'caption'     => $file->getClientOriginalName(),
                ]);
            }
        }

        return redirect()->back()->with('success', 'Foto dokumentasi berhasil ditambahkan!');
    }

    // Hapus Item Foto
    public function destroyItem($id)
    {
        $item = PrestasiItem::findOrFail($id);

        if (Storage::disk('public')->exists('prestasis/items/' . $item->file)) {
            Storage::disk('public')->delete('prestasis/items/' . $item->file);
        }

        $item->delete();

        return redirect()->back()->with('success', 'Foto dihapus!');
    }

    public function destroy($id)
    {
        $prestasi = Prestasi::with('items')->findOrFail($id);
        
        // Hapus Cover
        if ($prestasi->foto && Storage::disk('public')->exists('prestasis/covers/' . $prestasi->foto)) {
            Storage::disk('public')->delete('prestasis/covers/' . $prestasi->foto);
        }

        // Hapus Semua Item Foto Fisik
        foreach ($prestasi->items as $item) {
            if (Storage::disk('public')->exists('prestasis/items/' . $item->file)) {
                Storage::disk('public')->delete('prestasis/items/' . $item->file);
            }
        }

        $prestasi->delete();

        return redirect()->route('admin.landing.prestasi.index')->with('success', 'Prestasi dan dokumentasinya dihapus!');
    }
}