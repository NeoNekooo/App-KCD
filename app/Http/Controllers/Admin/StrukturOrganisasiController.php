<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StrukturOrganisasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StrukturOrganisasiController extends Controller
{
    public function index()
    {
        $struktur = StrukturOrganisasi::orderBy('urutan', 'asc')->get();
        $daftar_jabatan = \App\Models\JabatanKcd::orderBy('nama', 'asc')->get();
        
        return view('admin.website.struktur.index', compact('struktur', 'daftar_jabatan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jabatan' => 'required|string',
            'jenis_hubungan' => 'required|string|in:struktural,asisten_kiri,asisten_kanan',
            'nama_pejabat' => 'nullable|string',
            'parent_id' => 'nullable|exists:struktur_organisasis,id',
            'foto_pejabat' => 'nullable|image|max:2048'
        ]);

        $data = $request->except('foto_pejabat');
        if ($request->hasFile('foto_pejabat')) {
            $data['foto_pejabat'] = $request->file('foto_pejabat')->store('struktur', 'public');
        }

        StrukturOrganisasi::create($data);
        return redirect()->back()->with('success', 'Struktur berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $org = StrukturOrganisasi::findOrFail($id);
        $request->validate([
            'jabatan' => 'required|string',
            'jenis_hubungan' => 'required|string|in:struktural,asisten_kiri,asisten_kanan',
            'nama_pejabat' => 'nullable|string',
            'parent_id' => 'nullable|exists:struktur_organisasis,id',
            'foto_pejabat' => 'nullable|image|max:2048'
        ]);

        $data = $request->except('foto_pejabat');
        if ($request->hasFile('foto_pejabat')) {
            if ($org->foto_pejabat && Storage::disk('public')->exists($org->foto_pejabat)) {
                Storage::disk('public')->delete($org->foto_pejabat);
            }
            $data['foto_pejabat'] = $request->file('foto_pejabat')->store('struktur', 'public');
        }

        $org->update($data);
        return redirect()->back()->with('success', 'Struktur berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $org = StrukturOrganisasi::findOrFail($id);
        if ($org->foto_pejabat && Storage::disk('public')->exists($org->foto_pejabat)) {
            Storage::disk('public')->delete($org->foto_pejabat);
        }
        $org->delete();
        return redirect()->back()->with('success', 'Struktur berhasil dihapus.');
    }
}
