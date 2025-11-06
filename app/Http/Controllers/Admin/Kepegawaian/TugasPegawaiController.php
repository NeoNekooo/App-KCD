<?php

namespace App\Http\Controllers\Admin\Kepegawaian;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rombel; // <- IMPORT MODEL ROMBEL
use App\Models\Ekstrakurikuler; // <- IMPORT MODEL EKSTRAKURIKULER
use App\Models\Gtk; // <- IMPORT MODEL GTK/PTK (Sesuaikan nama model Anda)

class TugasPegawaiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // 1. Mengambil data Wali Kelas dari tabel 'rombels'
        // Kita gunakan 'wali' sebagai nama relasi
        $rombels = Rombel::with('wali')
            ->whereNotNull('ptk_id') // Hanya ambil rombel yang punya wali kelas
            ->get();

        // 2. Mengambil data Pembina dari tabel 'ekstrakurikulers'
        // Kita gunakan 'pembina' sebagai nama relasi
        $ekskuls = Ekstrakurikuler::with('pembina')
            ->whereNotNull('pembina_id') // Hanya ambil eskul yang punya pembina
            ->get();

        // 3. Mengambil data Tugas Tambahan dari tabel 'gtks'
        // Ganti string '...' dengan nama jabatan yang Anda anggap tugas tambahan
        $tugasTambahan = Gtk::whereIn('jabatan_ptk_id_str', [
            'Kepala Sekolah',
            'Wakil Kepala Sekolah Bidang Kurikulum',
            'Wakil Kepala Sekolah Bidang Kesiswaan',
            'Kepala Program Keahlian'
            // Tambahkan jabatan lain jika perlu
        ])->get();


        return view('admin.kepegawaian.tugas-pegawai.index', compact(
            'rombels',
            'ekskuls',
            'tugasTambahan'
        ));
    }

    /**
     * Show the form for creating a new resource.
     * (Dilewati oleh route Anda)
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
        // Logika untuk menyimpan data (jika Anda menambahkannya via modal)
        // return back()->with('success', 'Tugas berhasil disimpan');
    }

    /**
     * Display the specified resource.
     * (Dilewati oleh route Anda)
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     * (Dilewati oleh route Anda)
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
        // Logika untuk update data (jika Anda mengeditnya via modal)
        // return back()->with('success', 'Tugas berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Logika untuk menghapus data
        // return back()->with('success', 'Tugas berhasil dihapus');
    }
}