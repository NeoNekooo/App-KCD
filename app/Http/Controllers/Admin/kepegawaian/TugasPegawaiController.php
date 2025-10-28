<?php

namespace App\Http\Controllers\Admin\Kepegawaian;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rombel;
// use App\Models\Ekstrakurikuler; // <-- DIKOMENTARI
use App\Models\Gtk;
use App\Models\TugasPegawai; 

class TugasPegawaiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // --- AMBIL DATA TUGAS POKOK ---
        
        // TODO: Ganti ini dengan logika untuk mengambil tahun & semester aktif
        $tahunAktif = '2024/2025';
        $semesterAktif = 'Ganjil';

        $tugasPokok = TugasPegawai::with('gtk')
            ->where('tahun_pelajaran', $tahunAktif)
            ->where('semester', $semesterAktif)
            ->get();


        // --- AMBIL DATA TUGAS TAMBAHAN ---
        
        // 1. Wali Kelas
        $waliKelas = Rombel::with('waliKelas') 
            ->whereNotNull('ptk_id')
            ->get();

        // 2. Pembina Ekskul (DIKOMENTARI)
        // $pembinaEkskul = Ekstrakurikuler::with('pembina')
        //     ->whereNotNull('pembina_id')
        //     ->get();

        // 3. Jabatan Struktural
        $jabatanStruktural = Gtk::whereIn('jabatan_ptk_id_str', [
            'Kepala Sekolah',
            'Wakil Kepala Sekolah Bidang Kurikulum',
            'Wakil Kepala Sekolah Bidang Kesiswaan',
            'Kepala Program Keahlian'
        ])->get();


        // 4. KIRIM SEMUA DATA KE VIEW
        return view('admin.kepegawaian.tugas-pegawai.index', compact(
            'tugasPokok',
            'waliKelas',
            // 'pembinaEkskul', // <-- DIKOMENTARI
            'jabatanStruktural',
            'tahunAktif',
            'semesterAktif'
        ));
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
        //
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