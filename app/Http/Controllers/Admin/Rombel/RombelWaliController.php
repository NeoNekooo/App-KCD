<?php

namespace App\Http\Controllers\Admin\Rombel;

use App\Http\Controllers\Controller;
use App\Models\Rombel;
use Illuminate\Http\Request;

class RombelWaliController extends Controller
{
    /**
     * Menampilkan form untuk menambah data wali kelas baru.
     */
    public function create()
    {
        // Method ini hanya menampilkan halaman form.
        return view('admin.rombel.wali.create');
    }

    /**
     * Menampilkan halaman utama dengan daftar rombel yang sudah memiliki wali kelas.
     */
    public function index()
    {
        // 1. Ambil data dari model Rombel.
        // 2. Gunakan whereNotNull('wali_id') untuk memfilter HANYA rombel yang sudah punya wali kelas.
        // 3. Gunakan with() untuk mengambil data relasi yang diperlukan saja ('wali' dan 'jurusan').
        $rombels = Rombel::whereNotNull('ptk_id')
                            ->with(['wali']) // <-- Tidak menggunakan tabel jurusan, gunakan kolom di rombels
                            ->latest()
                            ->paginate(10);

        // 4. Kirim data $rombels ke view.
        return view('admin.rombel.wali.index', compact('rombels'));
    }
}

