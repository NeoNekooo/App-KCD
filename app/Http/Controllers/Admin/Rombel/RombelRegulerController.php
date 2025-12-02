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
        
        // KITA TAMBAHKAN 'siswa' di sini
        $rombels = Rombel::with(['wali', 'jurusan', 'kurikulum', 'siswa'])
                           ->where('jenis_rombel', 'Reguler')
                           ->latest() // Mengurutkan dari yang terbaru
                           ->paginate(10);
        
        return view('admin.rombel.reguler.index', compact('rombels'));
    }

   
}
