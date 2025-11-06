<?php

namespace App\Http\Controllers\Admin\Rombel;

use App\Http\Controllers\Controller;
use App\Models\Rombel; // Menggunakan model Rombel yang sudah ada
use Illuminate\Http\Request;

class RombelPraktikController extends Controller
{
    /**
     * Menampilkan form untuk membuat data rombel praktik baru.
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
        // Pastikan nilai 'Praktik' sesuai dengan data di database.
        $rombels = Rombel::with(['wali', 'jurusan', 'kurikulum'])
                            ->where('jenis_rombel', 'Praktik')
                            ->latest()
                            ->paginate(10);
        
        return view('admin.rombel.praktik.index', compact('rombels'));
    }
}
