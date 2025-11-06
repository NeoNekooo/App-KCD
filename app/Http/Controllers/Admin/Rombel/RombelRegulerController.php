<?php

namespace App\Http\Controllers\Admin\Rombel;

use App\Http\Controllers\Controller;
use App\Models\Rombel; // Menggunakan model Rombel
use Illuminate\Http\Request;

class RombelRegulerController extends Controller
{
    /**
     * Menampilkan form untuk membuat data rombel baru.
     */
    public function create()
    {
        return view('admin.rombel.reguler.create');
    }

    /**
     * Menampilkan halaman utama dengan daftar rombel reguler.
     */
    public function index()
    {
        // Mengambil data dari tabel 'rombels' dan memfilternya
        // hanya untuk yang berjenis 'Reguler'.
        // 'with()' digunakan untuk mengambil data relasi (wali, jurusan, kurikulum)
        // agar lebih efisien dan tidak terjadi N+1 problem.
        $rombels = Rombel::with(['wali', 'jurusan', 'kurikulum'])
                         ->where('jenis_rombel', 'Reguler')
                         ->latest()
                         ->paginate(10);
        
        return view('admin.rombel.reguler.index', compact('rombels'));
    }
}
