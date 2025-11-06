<?php

namespace App\Http\Controllers\Admin\Rombel;

use App\Http\Controllers\Controller;
use App\Models\Ekstrakurikuler; // <-- Import model yang benar
use Illuminate\Http\Request;

class RombelEkstrakurikulerController extends Controller
{
    /**
     * Menampilkan form untuk membuat data ekskul baru.
     */
    public function create()
    {
        return view('admin.rombel.ekstrakurikuler.create');
    }

    /**
     * Menampilkan halaman utama dengan daftar ekskul.
     */
    public function index()
    {
        // Mengambil data dari tabel 'ekstrakurikulers' beserta data relasi 'pembina'
        $ekskul = Ekstrakurikuler::with('pembina')
                                ->latest()
                                ->paginate(10);
        
        return view('admin.rombel.ekstrakurikuler.index', compact('ekskul'));
    }
}
