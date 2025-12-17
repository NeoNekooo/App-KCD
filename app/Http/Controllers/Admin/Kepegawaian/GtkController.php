<?php

namespace App\Http\Controllers\Admin\Kepegawaian;

use App\Models\Gtk;
use App\Models\Sekolah;
use App\Models\Rombel;
use App\Models\TugasPegawai;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\GtkExport;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\PelanggaranNilaiGtk;
use App\Models\PelanggaranSanksiGtk;

class GtkController extends Controller
{
    // --- FUNGSI DAFTAR PEGAWAI (Data Guru & Tendik) ---

    public function indexGuru(Request $request)
    {
        $query = Gtk::query()->where('jenis_ptk_id_str', 'Guru');

        $query->when($request->search, function ($q, $search) {
            return $q->where(function ($sub) use ($search) {
                $sub->where('nama', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            });
        });

        // --- LOGIKA PAGINATION DINAMIS ---
        $perPage = $request->input('per_page', 15); 

        if ($perPage === 'all') {
            $perPage = $query->count(); 
            if ($perPage == 0) $perPage = 15; 
        }

        $gurus = $query->latest()->paginate($perPage)->appends($request->all());
        
        return view('admin.kepegawaian.gtk.index_guru', compact('gurus'));
    }

    public function indexTendik(Request $request)
    {
        // 1. Filter Data (Gabungkan Kepsek (91) & Tendik (93))
        $query = Gtk::query()->whereIn('jenis_ptk_id', ['91', '93']);
        
        $query->when($request->search, function ($q, $search) {
            return $q->where(function ($sub) use ($search) {
                $sub->where('nama', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%"); 
            });
        });

        // 2. LOGIKA PAGINATION DINAMIS
        $perPage = $request->input('per_page', 15); 

        if ($perPage === 'all') {
            $perPage = $query->count(); 
            if ($perPage == 0) $perPage = 15;
        }

        $tendiks = $query->latest()->paginate($perPage)->appends($request->all());
        
        return view('admin.kepegawaian.gtk.index_tendik', compact('tendiks'));
    }

    // --- FUNGSI EXPORT & PDF BAWAAN ---

    public function exportGuruExcel(Request $request)
    {
        $query = Gtk::query()->where('jenis_ptk_id_str', 'Guru');

        if ($request->has('ids')) {
            $ids = explode(',', $request->input('ids'));
            $query->whereIn('id', $ids);
        } elseif ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($sub) use ($search) {
                $sub->where('nama', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        $query->latest(); 
        $fileName = 'Data_Guru_Sekull.xlsx';
        return Excel::download(new GtkExport($query), $fileName);
    }

    public function exportTendikExcel(Request $request)
    {
        $query = Gtk::query()->whereIn('jenis_ptk_id', ['91', '93']);

        if ($request->has('ids')) {
            $ids = explode(',', $request->input('ids'));
            $query->whereIn('id', $ids);
        } elseif ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($sub) use ($search) {
                $sub->where('nama', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            });
        }
        
        $query->latest();
        $fileName = 'Data_Tendik_Sekull.xlsx';
        return Excel::download(new GtkExport($query), $fileName);
    }

    public function showMultiple(Request $request)
    {
        $request->validate(['ids' => 'required|string']);
        $ids = explode(',', $request->input('ids'));
        $gtks = Gtk::whereIn('id', $ids)->get();
        return view('admin.kepegawaian.gtk.show_multiple', compact('gtks'));
    }

    public function cetakPdf($id)
    {
        $gtk = Gtk::findOrFail($id);
        $sekolah = Sekolah::first();
        $qrCodeData = "Nama: " . $gtk->nama . "\nNUPTK: " . ($gtk->nuptk ?? '-');
        $rombelWali = Rombel::where('ptk_id', $gtk->ptk_id)->first();
        $rombelMengajar = Rombel::whereJsonContains('pembelajaran', ['ptk_id' => $gtk->ptk_id])->get();
        
        $tugasTerbaru = TugasPegawai::where('pegawai_id', $gtk->ptk_id)->orderBy('tmt', 'desc')->first();

        $pdf = Pdf::loadView('admin.kepegawaian.gtk.gtk_pdf', compact(
            'gtk', 'sekolah', 'qrCodeData', 'rombelWali', 'rombelMengajar', 'tugasTerbaru'
        ));
        
        $fileName = 'Profil GTK - ' . $gtk->nama . '.pdf';
        return $pdf->stream($fileName);
    }

    public function cetakPdfMultiple(Request $request)
    {
        $request->validate(['ids' => 'required|string']);
        $ids = explode(',', $request->input('ids'));
        $gtks = Gtk::find($ids); 
        $sekolah = Sekolah::first();
        
        $rombelMengajar = Rombel::all(); 

        $pdf = Pdf::loadView('admin.kepegawaian.gtk.gtk_pdf_multiple', compact('gtks', 'sekolah', 'rombelMengajar'));
        $fileName = 'Kumpulan_Profil_GTK.pdf';
        return $pdf->stream($fileName);
    }

    // --- FUNGSI UPDATE DATA & UPLOAD MEDIA ---

    public function updateData(Request $request, $id)
    {
        $gtk = Gtk::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255',
            'nik' => 'nullable|numeric',
            'email' => 'nullable|email',
            'tanggal_lahir' => 'nullable|date',
        ]);

        try {
            $data = $request->except(['_token', '_method']);
            $gtk->update($data);

            return redirect()->back()->with('success', 'Data lengkap ' . $gtk->nama . ' berhasil diperbarui!');

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['msg' => 'Gagal menyimpan data: ' . $e->getMessage()])->withInput();
        }
    }

    public function uploadMedia(Request $request, $id)
    {
        $request->validate([
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', 
            'tandatangan' => 'nullable|image|mimes:png|max:1024',
        ]);

        $gtk = Gtk::findOrFail($id);

        if ($request->hasFile('foto')) {
            if ($gtk->foto && Storage::disk('public')->exists($gtk->foto)) {
                Storage::disk('public')->delete($gtk->foto);
            }
            $path = $request->file('foto')->store('gtk_media/foto', 'public');
            $gtk->foto = $path;
        }

        if ($request->hasFile('tandatangan')) {
            if ($gtk->tandatangan && Storage::disk('public')->exists($gtk->tandatangan)) {
                Storage::disk('public')->delete($gtk->tandatangan);
            }
            $path = $request->file('tandatangan')->store('gtk_media/tandatangan', 'public');
            $gtk->tandatangan = $path;
        }

        $gtk->save();
        return back()->with('success', 'Media GTK berhasil diperbarui!');
    }

    // --- FITUR BARU: CETAK KARTU ID ---

    public function indexCetakKartu(Request $request)
    {
        $query = Gtk::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('nuptk', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%"); // <--- UPDATE: Tambahkan pencarian NIK
            });
        }

        if ($request->filled('status')) {
             $query->where('jenis_ptk_id_str', $request->status);
        }

        $gtks = $query->orderBy('nama', 'asc')->paginate(10);
        $sekolah = Sekolah::first(); 

        return view('admin.kepegawaian.gtk.index_cetak_kartu', compact('gtks', 'sekolah'));
    }

    public function cetakSemua(Request $request)
    {
        $query = Gtk::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('nuptk', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%"); // <--- UPDATE: Tambahkan pencarian NIK
            });
        }

        if ($request->filled('status')) {
            $query->where('jenis_ptk_id_str', $request->status);
        }

        $gtks = $query->orderBy('nama', 'asc')->get();
        $sekolah = Sekolah::first();

        return view('admin.kepegawaian.gtk.cetak_kartu_massal', compact('gtks', 'sekolah'));
    }

    public function cetakKartu($id)
    {
        $gtk = Gtk::findOrFail($id);
        $sekolah = Sekolah::first();
        $gtks = collect([$gtk]);

        return view('admin.kepegawaian.gtk.cetak_kartu_massal', compact('gtks', 'sekolah'));
    }

    public function uploadBackgroundKartu(Request $request)
    {
        $request->validate([
            'background_kartu' => 'required|image|mimes:jpeg,png,jpg|max:2048', 
        ]);

        $sekolah = Sekolah::firstOrCreate(['id' => 1]);

        if ($request->hasFile('background_kartu')) {
            if ($sekolah->background_kartu && Storage::disk('public')->exists($sekolah->background_kartu)) {
                Storage::disk('public')->delete($sekolah->background_kartu);
            }
            $path = $request->file('background_kartu')->store('sekolah_media/background', 'public');
            $sekolah->background_kartu = $path;
            $sekolah->save();
        }

        return back()->with('success', 'Background kartu berhasil diupdate!');
    }

    // Personal
    public function profil()
    {
        if (!auth()->check() || !session()->has('ptk_id')) {
            abort(403);
        }

        $gtk = Gtk::where('ptk_id', session('ptk_id'))->firstOrFail();
        $gtks = collect([$gtk]);

        return view('admin.personal.guru.profil', compact('gtks'));
    }

    public function pelanggaran()
    {
        // ===============================
        // AMBIL GTK BERDASARKAN LOGIN
        // ===============================
        if (!session()->has('ptk_id')) {
            abort(403, 'Akses ditolak');
        }

        $gtk = Gtk::where('ptk_id', session('ptk_id'))->firstOrFail();

        // ===============================
        // AMBIL DATA PELANGGARAN
        // ===============================
        $pelanggaranGuru = PelanggaranNilaiGtk::where('nama_guru', $gtk->nama)
            ->with('detailPoinGtk')
            ->orderBy('tanggal', 'desc')
            ->get();

        // ===============================
        // HITUNG TOTAL POIN
        // ===============================
        $totalPoin = $pelanggaranGuru->sum('poin');

        // ===============================
        // AMBIL SANKSI AKTIF
        // ===============================
        $sanksiAktif = PelanggaranSanksiGtk::where('poin_min', '<=', $totalPoin)
            ->where('poin_max', '>=', $totalPoin)
            ->first();

        return view('admin.personal.guru.pelanggaran', [
            'namaGuru'        => $gtk->nama,
            'pelanggaranGuru' => $pelanggaranGuru,
            'totalPoin'       => $totalPoin,
            'sanksiAktif'     => $sanksiAktif,
            'guruList'        => collect(), // biar blade gak error
        ]);
    }

}