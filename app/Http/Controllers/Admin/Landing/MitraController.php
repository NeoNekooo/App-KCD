<?php

namespace App\Http\Controllers\Admin\Landing;

use App\Http\Controllers\Controller;
use App\Models\Mitra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MitraController extends Controller
{
    public function index()
    {
        $mitras = Mitra::latest()->get(); // Tidak perlu paginate karena biasanya logo ditampilkan semua
        return view('admin.landing.mitra.index', compact('mitras'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_mitra' => 'required|string|max:255',
            'logo'       => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = $request->except(['_token']);

        if ($request->hasFile('logo')) {
            $image = $request->file('logo');
            
            // PERBAIKAN: 
            // 1. Path jadi 'mitras' (tanpa 'public/')
            // 2. Tambah parameter 'public' di belakang
            $image->storeAs('mitras', $image->hashName(), 'public');
            
            $data['logo'] = $image->hashName();
        }

        Mitra::create($data);

        return redirect()->back()->with('success', 'Mitra industri berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $mitra = Mitra::findOrFail($id);

        $request->validate([
            'nama_mitra' => 'required|string|max:255',
            'logo'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = $request->except(['_token', '_method']);

        if ($request->hasFile('logo')) {
            // PERBAIKAN: Hapus logo lama pakai disk public
            if ($mitra->logo && Storage::disk('public')->exists('mitras/' . $mitra->logo)) {
                Storage::disk('public')->delete('mitras/' . $mitra->logo);
            }

            // Upload logo baru
            $image = $request->file('logo');
            $image->storeAs('mitras', $image->hashName(), 'public');
            $data['logo'] = $image->hashName();
        }

        $mitra->update($data);

        return redirect()->back()->with('success', 'Mitra industri berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $mitra = Mitra::findOrFail($id);
        
        // PERBAIKAN: Hapus logo fisik pakai disk public
        if ($mitra->logo && Storage::disk('public')->exists('mitras/' . $mitra->logo)) {
            Storage::disk('public')->delete('mitras/' . $mitra->logo);
        }

        $mitra->delete();

        return redirect()->back()->with('success', 'Mitra industri berhasil dihapus!');
    }
}