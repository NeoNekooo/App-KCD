<?php

namespace App\Http\Controllers\Admin\Rombel;

use App\Http\Controllers\Controller;
use App\Models\Rombel; // Menggunakan model Rombel yang sudah ada
use Illuminate\Http\Request;

class RombelPraktikController extends Controller
{
    /**
     * Menampilkan form untuk membuat data rombel praktik baru.
     * (Kita biarkan method create ini, mungkin akan terpakai)
     */
    public function create()
    {
        return view('admin.rombel.praktik.create');
    }

    /**
     * Menampilkan halaman utama dengan daftar rombel praktik.
     */
    public function index()
    {
        // Mengambil data dari tabel 'rombels' dan memfilternya
        // hanya untuk yang jenisnya 'Praktik'.
        
        // PERBAIKAN: Menambahkan 'siswa' ke with() untuk Eager Loading
        // Ini agar kita bisa menampilkan "Jumlah Siswa" di view tanpa N+1 problem.
        $rombels = Rombel::with(['wali', 'jurusan', 'kurikulum', 'siswa'])
                           ->where('jenis_rombel', 'Praktik') // Filter untuk 'Praktik'
                           ->latest()
                           ->paginate(10);
        
        return view('admin.rombel.praktik.index', compact('rombels'));
    }

  
}
