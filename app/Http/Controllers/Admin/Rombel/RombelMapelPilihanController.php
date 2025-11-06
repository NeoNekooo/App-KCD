<?php

namespace App\Http\Controllers\Admin\Rombel;

use App\Http\Controllers\Controller;
use App\Models\Rombel; // Menggunakan model Rombel yang sudah ada
use Illuminate\Http\Request;

class RombelMapelPilihanController extends Controller
{
    /**
     * Menampilkan form untuk membuat data mapel pilihan baru.
     */
    public function create()
    {
        // Logika untuk menampilkan form tambah data
        return view('admin.rombel.mapel-pilihan.create');
    }

    /**
     * Menampilkan halaman utama dengan daftar rombel mapel pilihan.
     */
    public function index()
    {
        // Mengambil data dari tabel 'rombels' dan memfilternya
        // hanya untuk yang jenisnya 'Mapel Pilihan'.
        $rombels = Rombel::with(['wali', 'jurusan', 'kurikulum'])
                         ->where('jenis_rombel', 'Mapel Pilihan')
                         ->latest()
                         ->paginate(10);
        
        return view('admin.rombel.mapel-pilihan.index', compact('rombels'));
    }
}
