<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DokumenLayanan; // Import the DokumenLayanan model
use App\Models\PengajuanSekolah; // Import the PengajuanSekolah model

class DokumenLayananController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get distinct categories for the filter dropdown
        $kategoris = PengajuanSekolah::query()
            ->select('kategori')
            ->whereNotNull('kategori')
            ->distinct()
            ->orderBy('kategori')
            ->pluck('kategori');

        $query = PengajuanSekolah::has('dokumenLayanan')
            ->with('dokumenLayanan')
            ->withCount('dokumenLayanan')
            ->latest();

        // Filter by search query
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('nama_sekolah', 'like', "%{$search}%")
                  ->orWhere('nama_guru', 'like', "%{$search}%")
                  ->orWhere('judul', 'like', "%{$search}%");
            });
        }
        
        // Filter by category
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        $pengajuans = $query->paginate(15)->withQueryString();

        return view('admin.dokumen_layanan.index', compact('pengajuans', 'kategoris'));
    }
}
