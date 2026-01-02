<?php

namespace App\Http\Controllers\Admin\Landing;

use App\Http\Controllers\Controller;
use App\Models\Ekskul;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EkskulController extends Controller
{
    public function index()
    {
        $ekskuls = Ekskul::latest()->paginate(9);
        return view('admin.landing.ekstrakurikuler.index', compact('ekskuls'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_ekskul' => 'required|string|max:255',
            'pembina'     => 'nullable|string|max:255',
            'jadwal'      => 'nullable|string|max:255',
            'tempat'      => 'nullable|string|max:255', // Validasi baru
            'status'      => 'required|string|in:Aktif,Buka Pendaftaran,Penuh,Vakum', // Validasi baru
            'foto'        => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = $request->except(['_token']);

        if ($request->hasFile('foto')) {
            $image = $request->file('foto');
            $image->storeAs('ekstrakurikulers', $image->hashName(), 'public');
            $data['foto'] = $image->hashName();
        }

        Ekskul::create($data);

        return redirect()->back()->with('success', 'Ekstrakurikuler berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $ekskul = Ekskul::findOrFail($id);

        $request->validate([
            'nama_ekskul' => 'required|string|max:255',
            'pembina'     => 'nullable|string|max:255',
            'jadwal'      => 'nullable|string|max:255',
            'tempat'      => 'nullable|string|max:255',
            'status'      => 'required|string|in:Aktif,Buka Pendaftaran,Penuh,Vakum',
            'foto'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = $request->except(['_token', '_method']);

        if ($request->hasFile('foto')) {
            if ($ekskul->foto && Storage::disk('public')->exists('ekstrakurikulers/' . $ekskul->foto)) {
                Storage::disk('public')->delete('ekstrakurikulers/' . $ekskul->foto);
            }
            $image = $request->file('foto');
            $image->storeAs('ekstrakurikulers', $image->hashName(), 'public');
            $data['foto'] = $image->hashName();
        }

        $ekskul->update($data);

        return redirect()->back()->with('success', 'Ekstrakurikuler berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $ekskul = Ekskul::findOrFail($id);
        
        if ($ekskul->foto && Storage::disk('public')->exists('ekstrakurikulers/' . $ekskul->foto)) {
            Storage::disk('public')->delete('ekstrakurikulers/' . $ekskul->foto);
        }

        $ekskul->delete();

        return redirect()->back()->with('success', 'Ekstrakurikuler berhasil dihapus!');
    }
}