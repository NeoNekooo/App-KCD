<?php

namespace App\Http\Controllers\Admin\Ppdb;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TahunPelajaran;
use App\Models\CalonSiswa;
use App\Models\KelasPendaftaran;
use App\Models\KompetensiPendaftaran;
use App\Models\TingkatPendaftaran;


class PenempatanKelasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tahunAktif = TahunPelajaran::where('is_active', 1)->first();

        $tingkatAktif = TingkatPendaftaran::where('is_active', 1)->first();
    
        $kelas = ($tahunAktif && $tingkatAktif)
                ? KelasPendaftaran::where('tahunPelajaran_id', $tahunAktif->id)
                    ->where('tingkat', $tingkatAktif->tingkat)
                    ->get()
                : collect();

        // Ambil jurusan untuk filter
        $jurusans = KompetensiPendaftaran::all();
    
        // Query dasar
        $baseQuery = CalonSiswa::where('tahun_id', $tahunAktif->id)
            ->whereHas('syarat', function ($q) {
                $q->where('is_checked', true);
            });
        
        // Filter berdasarkan tingkat aktif
        if ($tingkatAktif) {
            $baseQuery->where('tingkat', $tingkatAktif->tingkat);
        }
        
        // Filter jurusan kalau ada (hanya berlaku untuk tingkat 10)
        if ($request->filled('jurusan')) {
            $baseQuery->where('jurusan', $request->jurusan);
        }
    
        // Pisahkan siswa
        $belumDitempatkan = (clone $baseQuery)->whereNull('kelas_tujuan')->get();
        $sudahDitempatkan = (clone $baseQuery)->whereNotNull('kelas_tujuan')->get();
    
        return view('admin.ppdb.penempatan_kelas', compact(
            'belumDitempatkan',
            'sudahDitempatkan',
            'jurusans',
            'kelas',
            'tahunAktif',
            'tingkatAktif'
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
