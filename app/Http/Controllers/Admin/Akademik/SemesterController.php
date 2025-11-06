<?php

namespace App\Http\Controllers\Admin\Akademik;

use App\Http\Controllers\Controller;
use App\Models\Semester;
use Illuminate\Http\Request;

class SemesterController extends Controller
{
    /**
     * Menampilkan daftar semua semester.
     */
    public function index()
    {
        $semesters = Semester::orderBy('id')->get();
        // Path View: resources/views/admin/akademik/semester/index.blade.php
        return view('admin.akademik.semester.index', compact('semesters'));
    }

    /**
     * Mengubah status 'is_active' dari semester (Toggle).
     */
    public function toggle(Semester $semester)
    {
        // Secara implisit menggunakan logic Model booted() untuk menonaktifkan yang lain
        $semester->is_active = true;
        $semester->save(); 

        return redirect()->route('admin.akademik.semester.index')
                         ->with('success', 'Semester ' . $semester->nama . ' berhasil diaktifkan!');
    }
    
    // Metode resource lainnya (create, store, edit, update, show, destroy)
    // dihapus karena route hanya menggunakan 'index' dan 'toggle'
}