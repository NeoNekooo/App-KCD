<?php

namespace App\Http\Controllers\Admin\Landing;

use App\Http\Controllers\Controller;
use App\Models\AlumniTestimoni; // <--- UBAH INI (Dulunya App\Models\Testimoni)
use Illuminate\Http\Request;

class TestimoniController extends Controller
{
    public function index()
    {
        // Gunakan AlumniTestimoni, bukan Testimoni
        $testimonis = AlumniTestimoni::with('siswa')->latest()->get();
        
        return view('admin.landing.testimoni.index', compact('testimonis'));
    }

    public function update(Request $request, $id)
    {
        // Gunakan AlumniTestimoni
        $testimoni = AlumniTestimoni::findOrFail($id);

        $request->validate([
            'status' => 'required|in:Approved,Rejected,Pending'
        ]);

        $testimoni->update([
            'status' => $request->status
        ]);

        return redirect()->back()->with('success', 'Status testimoni berhasil diperbarui!');
    }

    public function destroy($id)
    {
        // Gunakan AlumniTestimoni
        $testimoni = AlumniTestimoni::findOrFail($id);
        $testimoni->delete();

        return redirect()->back()->with('success', 'Testimoni berhasil dihapus!');
    }
}