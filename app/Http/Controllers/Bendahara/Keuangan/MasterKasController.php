<?php

namespace App\Http\Controllers\Bendahara\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\MasterKas;
use Illuminate\Http\Request;

class MasterKasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   public function index()
    {
        $masterKas = MasterKas::orderBy('nama_kas')->get();
        return view('bendahara.keuangan.master_kas.index', compact('masterKas'));
    }

    // Menampilkan form tambah Kas
    public function create()
    {
        return view('bendahara.keuangan.master_kas.create');
    }

    // Menyimpan Kas baru
    public function store(Request $request)
    {
        $request->validate([
            'nama_kas' => 'required|string|max:100|unique:master_kas,nama_kas',
            'saldo_awal' => 'nullable|numeric|min:0',
        ]);

        MasterKas::create($request->all());

        return redirect()->route('bendahara.keuangan.kas-master.index')->with('success', 'Kas/Bank berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
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
}
