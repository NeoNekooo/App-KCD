<?php

namespace App\Http\Controllers\Admin\Rombel;

use App\Http\Controllers\Controller;
use App\Models\Rombel;
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
        // Gunakan pencocokan case-insensitive dan trim untuk mengakomodasi impor data
        // Perubahan: gunakan kolom `jenis_rombel_str` (kolom string dari impor) dan terima beberapa varian
        $rombels = Rombel::with(['wali', 'jurusan', 'kurikulum', 'siswa'])
                           ->whereRaw("LOWER(TRIM(jenis_rombel_str)) IN (?, ?)", ['reguler', 'kelas'])
                           ->latest() // Mengurutkan dari yang terbaru
                           ->paginate(10);

        return view('admin.rombel.reguler.index', compact('rombels'));
    }


}
