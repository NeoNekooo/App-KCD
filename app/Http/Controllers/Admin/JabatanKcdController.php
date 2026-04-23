<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JabatanKcd;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
            'nama' => [
                'required', 'string', 'max:255',
                Rule::unique('jabatan_kcd')->where(function ($query) {
                    return $query->where('instansi_id', auth()->user()->instansi_id);
                })
            ],
            'role' => 'required|string|max:255',
        ]);

        // 🛡️ SECURITY CHECK: Larang Admin Wilayah bikin jabatan "Admin/Administrator"
        $forbidden = ['admin', 'administrator'];
        if (in_array(strtolower(trim($request->nama)), $forbidden) && !empty(auth()->user()->instansi_id)) {
            return back()->with('error', 'Ops! Nama jabatan "' . $request->nama . '" dilarang digunakan di tingkat wilayah.')->withInput();
        }

        $data = $request->all();
        $data['instansi_id'] = auth()->user()->instansi_id;

        JabatanKcd::create($data);

        return back()->with('success', 'Jabatan berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'nama' => [
                'required', 'string', 'max:255',
                Rule::unique('jabatan_kcd')->where(function ($query) {
                    return $query->where('instansi_id', auth()->user()->instansi_id);
                })->ignore($id)
            ],
            'role' => 'required|string|max:255',
        ]);

        // 🛡️ SECURITY CHECK
        $forbidden = ['admin', 'administrator'];
        if (in_array(strtolower(trim($request->nama)), $forbidden) && !empty(auth()->user()->instansi_id)) {
            return back()->with('error', 'Ops! Nama jabatan "' . $request->nama . '" dilarang digunakan di tingkat wilayah.')->withInput();
        }

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
