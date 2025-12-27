<?php

namespace App\Http\Controllers\Admin\Alumni;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AlumniData;
use App\Models\Siswa; 
use Illuminate\Support\Facades\DB;

class AdminAlumniController extends Controller
{
    /**
     * 1. HALAMAN MANAJEMEN TESTIMONI
     */
    public function indexTestimoni()
    {
        $data = AlumniData::with('siswa')
                    ->whereNotNull('testimoni')
                    ->where('testimoni', '!=', '')
                    ->latest()
                    ->get();

        return view('admin.alumni.testimoni', compact('data'));
    }

    public function toggleTestimoni($id)
    {
        $alumni = AlumniData::findOrFail($id);
        $alumni->tampilkan_testimoni = !$alumni->tampilkan_testimoni;
        $alumni->save();

        $status = $alumni->tampilkan_testimoni ? 'DITAYANGKAN' : 'DISEMBUNYIKAN';
        return redirect()->back()->with('success', "Testimoni berhasil $status.");
    }

    /**
     * 2. HALAMAN PENELUSURAN KERJA (TRACER STUDY)
     */
    public function indexTracer()
    {
        $data = AlumniData::with('siswa')
                    ->whereNotNull('status_kegiatan')
                    ->latest()
                    ->get();

        $stats = [
            'bekerja'   => $data->where('status_kegiatan', 'Bekerja')->count(),
            'kuliah'    => $data->where('status_kegiatan', 'Kuliah')->count(),
            'wirausaha' => $data->where('status_kegiatan', 'Wirausaha')->count(),
            'mencari'   => $data->where('status_kegiatan', 'Mencari Kerja')->count(),
        ];

        return view('admin.alumni.tracer', compact('data', 'stats'));
    }

    /**
     * ==========================================
     * 3. TAMBAHAN: INPUT DATA MANUAL (OLEH ADMIN)
     * ==========================================
     */

    public function create()
{
    if (!auth()->check() || !session()->has('peserta_didik_id')) {
        abort(403);
    }

    $siswa = Siswa::where(
        'peserta_didik_id',
        session('peserta_didik_id')
    )->firstOrFail();

    $alumni = AlumniData::where(
        'peserta_didik_id',
        $siswa->peserta_didik_id
    )->first();

    return view('admin.personal.siswa.create', [
        'siswas' => $siswa,
        'alumni' => $alumni
    ]);
}


    public function store(Request $request)
    {
        $request->validate([
            'peserta_didik_id' => 'required|exists:siswas,peserta_didik_id',
        ]);

        AlumniData::updateOrCreate(
            ['peserta_didik_id' => $request->peserta_didik_id],
            [
                // Testimoni
                'testimoni' => $request->testimoni,
                'tampilkan_testimoni' => $request->has('tampilkan_testimoni') ? 1 : 0,

                // Tracer
                'status_kegiatan' => $request->status_kegiatan,
                'nama_instansi'   => $request->nama_instansi,
                'bidang_jabatan'  => $request->bidang_jabatan, 
                'tahun_mulai'     => $request->tahun_mulai,
                'pendapatan'      => $request->pendapatan,
                'linieritas'      => $request->linieritas,
            ]
        );

        return redirect()->back()->with('success', 'Data Alumni berhasil disimpan.');
    }
}