<?php

namespace App\Http\Controllers\Admin\Kesiswaan\Ppdb;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TahunPelajaran;

class TahunPpdbController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
    
        $tahunPpdb = TahunPelajaran::orderBy('tahun_pelajaran', 'asc')->get();
    
        return view('admin.kesiswaan.ppdb.tahun_pendaftaran_ppdb', compact('tahunPpdb'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
            $request->validate([
            'tahun_pelajaran' => 'required|string|max:20|unique:tahun_pelajarans,tahun_pelajaran',
            'keterangan' => 'nullable|string|max:255',
        ]);
    
        TahunPelajaran::create([
            'tahun_pelajaran' => $request->tahun_pelajaran,
            'keterangan' => $request->keterangan,
            'is_active' => false, // default non-aktif
        ]);
    
        return redirect()->back()->with('success', 'Tahun PPDB berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function toggleActive($id)
    {
        $tahun = TahunPelajaran::findOrFail($id);
    
        if (! $tahun->is_active) {
            // kalau belum aktif, set semua lain jadi nonaktif
            TahunPelajaran::query()->update(['is_active' => false]);
        
            $tahun->is_active = true;
            $tahun->save();
        
            $message = "Tahun {$tahun->tahun_pelajaran} berhasil dijadikan is_active";
        } else {
            // kalau sudah aktif, nonaktifkan saja
            $tahun->is_active = false;
            $tahun->save();
        
            $message = "Tahun {$tahun->tahun_pelajaran} berhasil di-nonaktifkan";
        }
    
        return redirect()->back()->with('success', $message);
    }


}
