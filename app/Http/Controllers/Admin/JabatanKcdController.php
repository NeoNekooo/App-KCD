<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JabatanKcd;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Instansi;
use Illuminate\Support\Facades\Auth;

class JabatanKcdController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jabatans = JabatanKcd::latest()->paginate(10);
        
        // 🔥 Untuk Super Admin: Ambil list instansi buat drop-down
        $instansis = [];
        if (in_array(strtolower(trim(Auth::user()->role)), ['admin', 'administrator']) && empty(Auth::user()->instansi_id)) {
            $instansis = Instansi::orderBy('id', 'asc')->get();
        }

        return view('admin.kepegawaian_kcd.jabatan.index', compact('jabatans', 'instansis'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Tentukan Instansi ID yang akan dipake divalidasi & simpan
        $targetInstansiId = (in_array(strtolower(trim(Auth::user()->role)), ['admin', 'administrator']) && empty(Auth::user()->instansi_id))
            ? $request->instansi_id
            : Auth::user()->instansi_id;

        $request->validate([
            'nama' => [
                'required', 'string', 'max:255',
                Rule::unique('jabatan_kcd')->where(function ($query) use ($targetInstansiId) {
                    return $query->where('instansi_id', $targetInstansiId);
                })
            ],
            'role' => 'required|string|max:255',
            'instansi_id' => [
                Rule::requiredIf(function() {
                    return in_array(strtolower(trim(Auth::user()->role)), ['admin', 'administrator']) && empty(Auth::user()->instansi_id);
                }),
                'nullable', 'exists:instansis,id'
            ]
        ]);

        // 🛡️ SECURITY CHECK: Larang Admin Wilayah bikin jabatan "Admin/Administrator"
        $forbidden = ['admin', 'administrator'];
        if (in_array(strtolower(trim($request->nama)), $forbidden) && !empty(auth()->user()->instansi_id)) {
            return back()->with('error', 'Ops! Nama jabatan "' . $request->nama . '" dilarang digunakan di tingkat wilayah.')->withInput();
        }

        $data = $request->all();
        $data['instansi_id'] = $targetInstansiId;

        JabatanKcd::create($data);

        return back()->with('success', 'Jabatan berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Tentukan Instansi ID
        $targetInstansiId = (in_array(strtolower(trim(Auth::user()->role)), ['admin', 'administrator']) && empty(Auth::user()->instansi_id))
            ? $request->instansi_id
            : Auth::user()->instansi_id;

        $request->validate([
            'nama' => [
                'required', 'string', 'max:255',
                Rule::unique('jabatan_kcd')->where(function ($query) use ($targetInstansiId) {
                    return $query->where('instansi_id', $targetInstansiId);
                })->ignore($id)
            ],
            'role' => 'required|string|max:255',
            'instansi_id' => 'sometimes|nullable|exists:instansis,id'
        ]);

        // 🛡️ SECURITY CHECK
        $forbidden = ['admin', 'administrator'];
        if (in_array(strtolower(trim($request->nama)), $forbidden) && !empty(auth()->user()->instansi_id)) {
            return back()->with('error', 'Ops! Nama jabatan "' . $request->nama . '" dilarang digunakan di tingkat wilayah.')->withInput();
        }

        $jabatan = JabatanKcd::findOrFail($id);
        
        $data = $request->all();
        $data['instansi_id'] = $targetInstansiId;

        $jabatan->update($data);

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
