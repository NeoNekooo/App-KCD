<?php

namespace App\Http\Controllers\Admin\Kesiswaan\Ppdb;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TahunPelajaran;
use App\Models\CalonSiswa;
use App\Models\Rombel;

class PenempatanKelasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tahunAktif = TahunPelajaran::where('is_active', 1)->first();
    
        // Data untuk dropdown kelas tujuan (hanya kelas X)
        $kelas = Rombel::where('nama', 'LIKE', 'X %')
            ->orderBy('nama')
            ->get();
    
        // Ambil jurusan untuk filter
        $jurusans = Rombel::select('jurusan_id_str')
                    ->distinct()
                    ->orderBy('jurusan_id_str')
                    ->pluck('jurusan_id_str');
    
        // Query dasar
        $baseQuery = CalonSiswa::where('tahun_id', $tahunAktif->id)
            ->whereHas('syarat', function ($q) {
                $q->where('is_checked', true);
            });
        
        // Filter jurusan kalau ada
        if ($request->filled('jurusan')) {
            $baseQuery->where('jurusan', $request->jurusan);
        }
    
        // Pisahkan siswa
        $belumDitempatkan = (clone $baseQuery)->whereNull('kelas_tujuan')->get();
        $sudahDitempatkan = (clone $baseQuery)->whereNotNull('kelas_tujuan')->get();
    
        return view('admin.kesiswaan.ppdb.penempatan_kelas', compact(
            'belumDitempatkan',
            'sudahDitempatkan',
            'jurusans',
            'kelas',
            'tahunAktif'
        ));
    }


    public function updateKelas(Request $request)
    {
        $siswaIds = $request->input('siswa_id', []);
        $kelasTujuan = $request->input('kelas_tujuan');

        if (empty($siswaIds) || !$kelasTujuan) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak lengkap'
            ]);
        }

        \App\Models\CalonSiswa::whereIn('id', $siswaIds)
            ->update(['kelas_tujuan' => $kelasTujuan]);

        
    return redirect()->back()->with('success', 'Siswa berhasil ditempatkan!');
    }

}
