<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JabatanKcd;
use Illuminate\Http\Request;

class JabatanKcdController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jabatans = JabatanKcd::latest()->paginate(10);
        return view('admin.kepegawaian_kcd.jabatan.index', compact('jabatans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:jabatan_kcd,nama',
            'role' => 'required|string|max:255',
        ]);

        JabatanKcd::create($request->all());

        return back()->with('success', 'Jabatan berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:jabatan_kcd,nama,' . $id,
            'role' => 'required|string|max:255',
        ]);

        $jabatan = JabatanKcd::findOrFail($id);
        $jabatan->update($request->all());

        return back()->with('success', 'Jabatan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $jabatan = JabatanKcd::findOrFail($id);
        $jabatan->delete();

        return back()->with('success', 'Jabatan berhasil dihapus.');
    }
}
