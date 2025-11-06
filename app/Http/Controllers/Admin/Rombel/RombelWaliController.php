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
        // 3. Gunakan with(['wali', 'jurusan']) untuk mengambil data relasi (nama wali dan nama jurusan) secara efisien.
        // 4. Urutkan dari yang terbaru dan buat paginasi.
        $rombels = Rombel::whereNotNull('wali_id')
                         ->with(['wali', 'jurusan'])
                         ->latest()
                         ->paginate(10);

        // 5. Kirim data $rombels ke view.
        return view('admin.rombel.wali.index', compact('rombels'));
    }
}
