<?php

namespace App\Http\Controllers\Admin\Pkks;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PkksInstrumen;
use App\Models\PkksKompetensi;
use App\Models\PkksIndikator;
use App\Imports\PkksInstrumenImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class PkksInstrumenController extends Controller
{
    public function index()
    {
        $instrumens = PkksInstrumen::withCount(['kompetensis', 'penilaians'])
        $user = auth()->user();
        $query = PkksInstrumen::orderBy('tahun', 'desc')->orderBy('created_at', 'desc');

        // 🔥 Jika Pengawas, hanya tampilkan soal sesuai jenjangnya
        if (str_contains(strtolower($user->role), 'pengawas') && $user->jenjang) {
            $query->where('jenjang', $user->jenjang);
        }

        $instrumens = $query->get();
        return view('admin.pkks.instrumen.index', compact('instrumens'));
    }

    public function create()
    {
        $user = auth()->user();
        // Jika pengawas sudah punya jenjang, kirim ke view buat di-lock
        $fixedJenjang = (str_contains(strtolower($user->role), 'pengawas')) ? $user->jenjang : null;
        
        return view('admin.pkks.instrumen.create', compact('fixedJenjang'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $data = $request->all();

        // 🔥 Force Jenjang dari profil kalau dia Pengawas
        if (str_contains(strtolower($user->role), 'pengawas') && $user->jenjang) {
            $data['jenjang'] = $user->jenjang;
        }

        $request->validate([
            'nama' => 'required|string|max:255',
            'jenjang' => 'required|string',
            'tahun' => 'required|integer',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after:start_at',
            'skor_maks' => 'required|integer'
        ]);

        $instrumen = PkksInstrumen::create($data);

        return redirect()->route('admin.pkks.instrumen.manage', $instrumen->id)
            ->with('success', 'Paket instrumen berhasil dibuat! Silakan tambah kompetensi.');
    }

    public function manage($id)
    {
        $instrumen = PkksInstrumen::findOrFail($id);
        // Ambil hanya yang level atas (Parent)
        $kompetensis = PkksKompetensi::with(['children.indikators', 'indikators'])
            ->where('pkks_instrumen_id', $id)
            ->whereNull('parent_id')
            ->orderBy('urutan')
            ->get();

        return view('admin.pkks.instrumen.manage', compact('instrumen', 'kompetensis'));
    }

    // Tambah Kompetensi Manual
    public function storeKompetensi(Request $request, $id)
    {
        $request->validate(['nama' => 'required|string|max:255']);
        
        PkksKompetensi::create([
            'pkks_instrumen_id' => $id,
            'parent_id' => $request->parent_id, // Tambahan ini Bre
            'nama' => $request->nama,
            'urutan' => PkksKompetensi::where('pkks_instrumen_id', $id)->count() + 1
        ]);

        return back()->with('success', 'Kategori berhasil ditambah!');
    }

    // Update Kompetensi
    public function updateKompetensi(Request $request, $id)
    {
        $request->validate(['nama' => 'required|string|max:255']);
        PkksKompetensi::findOrFail($id)->update($request->all());
        return back()->with('success', 'Kompetensi berhasil diperbarui!');
    }

    // Hapus Kompetensi
    public function destroyKompetensi($id)
    {
        PkksKompetensi::findOrFail($id)->delete();
        return back()->with('success', 'Kompetensi berhasil dihapus!');
    }

    // Tambah Indikator Manual
    public function storeIndikator(Request $request, $kompetensiId)
    {
        $request->validate([
            'nomor' => 'required',
            'kriteria' => 'required',
        ]);

        PkksIndikator::create([
            'pkks_kompetensi_id' => $kompetensiId,
            'nomor' => $request->nomor,
            'kriteria' => $request->kriteria,
            'bukti_identifikasi' => $request->bukti_identifikasi
        ]);

        return back()->with('success', 'Indikator berhasil ditambah!');
    }

    // Update Indikator
    public function updateIndikator(Request $request, $id)
    {
        $request->validate([
            'nomor' => 'required',
            'kriteria' => 'required',
        ]);
        PkksIndikator::findOrFail($id)->update($request->all());
        return back()->with('success', 'Indikator berhasil diperbarui!');
    }

    // Hapus Indikator
    public function destroyIndikator($id)
    {
        PkksIndikator::findOrFail($id)->delete();
        return back()->with('success', 'Indikator berhasil dihapus!');
    }

    // Import Excel
    public function import(Request $request, $id)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);

        Excel::import(new PkksInstrumenImport($id), $request->file('file'));

        return back()->with('success', 'Soal-soal berhasil di-import dari Excel!');
    }

    public function toggleActive($id)
    {
        $instrumen = PkksInstrumen::findOrFail($id);
        $instrumen->update(['is_active' => !$instrumen->is_active]);

        $status = $instrumen->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Instrumen berhasil $status!");
    }

    public function destroy($id)
    {
        PkksInstrumen::findOrFail($id)->delete();
        return back()->with('success', 'Instrumen berhasil dihapus!');
    }
}
