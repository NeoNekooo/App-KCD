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
            ->orderBy('tahun', 'desc')
            ->orderBy('jenjang', 'asc')
            ->get();
        return view('admin.pkks.instrumen.index', compact('instrumens'));
    }

    public function create()
    {
        // Ambil Jenjang yang ada di tabel sekolah (Dinamis Bre!)
        $jenjangs = \App\Models\Sekolah::distinct()->pluck('bentuk_pendidikan_id_str')->filter()->sort();
        return view('admin.pkks.instrumen.create', compact('jenjangs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'jenjang' => 'required',
            'tahun' => 'required|integer',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after:start_at',
            'skor_maks' => 'required|integer'
        ]);

        $instrumen = PkksInstrumen::create($request->all());

        return redirect()->route('admin.pkks.instrumen.manage', $instrumen->id)
            ->with('success', 'Paket instrumen berhasil dibuat! Silakan tambah kompetensi.');
    }

    public function manage($id)
    {
        $instrumen = PkksInstrumen::with(['kompetensis.indikators'])->findOrFail($id);
        return view('admin.pkks.instrumen.manage', compact('instrumen'));
    }

    // Tambah Kompetensi Manual
    public function storeKompetensi(Request $request, $id)
    {
        $request->validate(['nama' => 'required|string|max:255']);
        
        PkksKompetensi::create([
            'pkks_instrumen_id' => $id,
            'nama' => $request->nama,
            'urutan' => PkksKompetensi::where('pkks_instrumen_id', $id)->count() + 1
        ]);

        return back()->with('success', 'Kompetensi berhasil ditambah!');
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

    public function destroy($id)
    {
        PkksInstrumen::findOrFail($id)->delete();
        return back()->with('success', 'Instrumen berhasil dihapus!');
    }
}
