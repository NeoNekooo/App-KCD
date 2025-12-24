<?php

namespace App\Http\Controllers\Admin\Landing;

use App\Http\Controllers\Controller;
use App\Models\Fasilitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FasilitasController extends Controller
{
    public function index(Request $request)
{
    // Ambil data dengan pencarian dan pagination (8 item per halaman)
    $query = Fasilitas::latest();

    if ($request->has('q') && $request->q != '') {
        $query->where('nama_fasilitas', 'like', '%' . $request->q . '%');
    }

    $fasilitas = $query->paginate(8);

    return view('admin.landing.fasilitas.index', compact('fasilitas'));
}

    public function store(Request $request)
    {
        $request->validate([
            'nama_fasilitas' => 'required|string|max:255',
            'deskripsi'      => 'nullable|string',
            'foto'           => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->except(['_token']);

        if ($request->hasFile('foto')) {
            $image = $request->file('foto');
            
            // PERBAIKAN 1:
            // - Hapus 'public/' di path (jadi 'fasilitas' saja)
            // - Tambahkan parameter ke-3 'public'
            $image->storeAs('fasilitas', $image->hashName(), 'public');
            
            $data['foto'] = $image->hashName();
        }

        Fasilitas::create($data);

        return redirect()->back()->with('success', 'Fasilitas berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $fasilitas = Fasilitas::findOrFail($id);

        $request->validate([
            'nama_fasilitas' => 'required|string|max:255',
            'deskripsi'      => 'nullable|string',
            'foto'           => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->except(['_token', '_method']);

        if ($request->hasFile('foto')) {
            // PERBAIKAN 2: Hapus foto lama dengan disk 'public'
            if ($fasilitas->foto && Storage::disk('public')->exists('fasilitas/' . $fasilitas->foto)) {
                Storage::disk('public')->delete('fasilitas/' . $fasilitas->foto);
            }

            // Upload foto baru
            $image = $request->file('foto');
            // PERBAIKAN 3: Simpan dengan parameter 'public'
            $image->storeAs('fasilitas', $image->hashName(), 'public');
            
            $data['foto'] = $image->hashName();
        }

        $fasilitas->update($data);

        return redirect()->back()->with('success', 'Fasilitas berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $fasilitas = Fasilitas::findOrFail($id);
        
        // PERBAIKAN 4: Hapus foto fisik dengan disk 'public'
        if ($fasilitas->foto && Storage::disk('public')->exists('fasilitas/' . $fasilitas->foto)) {
            Storage::disk('public')->delete('fasilitas/' . $fasilitas->foto);
        }

        $fasilitas->delete();

        return redirect()->back()->with('success', 'Fasilitas berhasil dihapus!');
    }
}