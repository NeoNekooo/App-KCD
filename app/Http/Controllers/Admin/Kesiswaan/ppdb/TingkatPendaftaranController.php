<?php

namespace App\Http\Controllers\Admin\Kesiswaan\ppdb;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TingkatPendaftaran;

class TingkatPendaftaranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tingkats = TingkatPendaftaran::all();
        return view('admin.kesiswaan.ppdb.pengaturan_tingkat',compact('tingkats'));
    }

    public function toggleActive($id)
    {
        // Nonaktifkan semua dulu
        TingkatPendaftaran::query()->update(['is_active' => false]);

        // Aktifkan yang dipilih
        $tingkat = TingkatPendaftaran::findOrFail($id);
        $tingkat->is_active = true;
        $tingkat->save();

        return back()->with('success', "Tingkat {$tingkat->keterangan} berhasil diaktifkan.");
    }
}
