<?php

namespace App\Http\Controllers\Admin\Alumni;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa;

class AlumniController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
{
    $kelas = Siswa::where('nama_rombel', 'LIKE', 'XII%')
                ->select('nama_rombel')
                ->distinct()
                ->orderBy('nama_rombel')
                ->get();

    $query = Siswa::where('nama_rombel', 'LIKE', 'XII%');

    if ($request->rombel_id) {
        $query->where('nama_rombel', $request->rombel_id);
    }

    if ($request->search) {
        $query->where(function($q) use ($request) {
            $q->where('nama', 'like', '%'.$request->search.'%')
              ->orWhere('nisn', 'like', '%'.$request->search.'%')
              ->orWhere('nik', 'like', '%'.$request->search.'%');
        });
    }

    $siswas = $query->orderBy('nama')->paginate(10);

    return view('admin.alumni.index', compact('siswas', 'kelas'));
}


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function lulus(Request $request)
    {
        // Ambil kelas XII, urutkan
        $kelas = Siswa::where('nama_rombel', 'LIKE', 'XII%')
                    ->select('nama_rombel')
                    ->distinct()
                    ->orderBy('nama_rombel')
                    ->get();

        // Ambil siswa berdasarkan kelas + urutkan nama A-Z
        $siswa = collect();

        if ($request->kelas) {
            $siswa = Siswa::where('nama_rombel', $request->kelas)
                        ->orderBy('nama')   // ← ini yang wajib
                        ->get();
        }

        return view('admin.alumni.lulus', compact('kelas', 'siswa'));
    }



    public function process(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|array',
        ]);

        Siswa::whereIn('id', $request->siswa_id)->update([
            'status' => 'lulus',
        ]);

        return back()->with('success', 'Siswa berhasil diluluskan.');
    }

}
