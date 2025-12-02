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

        $gurus = $query->latest()->paginate(15);
        
        return view('admin.kepegawaian.gtk.index_guru', compact('gurus'));
    }

    public function indexTendik(Request $request)
    {
        $query = Gtk::query()->where('jenis_ptk_id_str', 'Tenaga Kependidikan');
        
        $query->when($request->search, function ($q, $search) {
            return $q->where(function ($sub) use ($search) {
                $sub->where('nama', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%"); 
            });
        });

        $tendiks = $query->latest()->paginate(15);
        
        return view('admin.kepegawaian.gtk.index_tendik', compact('tendiks'));
    }

    // --- FUNGSI EXPORT & PDF BAWAAN (Lama) ---

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
        $query = Gtk::query()->where('jenis_ptk_id_str', 'Tenaga Kependidikan');

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
        $pdf = Pdf::loadView('admin.kepegawaian.gtk.gtk_pdf_multiple', compact('gtks', 'sekolah'));
        $fileName = 'Kumpulan_Profil_GTK.pdf';
        return $pdf->stream($fileName);
    }

    // --- FUNGSI UPLOAD MEDIA (FOTO & TTD) ---

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

    // 1. Halaman List Pencarian (Yang ada tombol Cetaknya)
    public function indexCetakKartu(Request $request)
    {
        $query = Gtk::query();

        // 1. Filter Pencarian (Nama/NIP)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('nuptk', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
             $query->where('jenis_ptk_id_str', $request->status);
        }

        $gtks = $query->orderBy('nama', 'asc')->paginate(10);
        
        // TAMBAHKAN INI: Ambil data sekolah untuk preview gambar di view
        $sekolah = Sekolah::first(); 

        return view('admin.kepegawaian.gtk.index_cetak_kartu', compact('gtks', 'sekolah'));
    }

    
    public function cetakSemua(Request $request)
    {
        $query = Gtk::query();

        // 1. Terapkan Filter Pencarian (Copy dari index)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('nuptk', 'like', "%{$search}%");
            });
        }

        // 2. Terapkan Filter Status (Copy dari index)
        if ($request->filled('status')) {
            $query->where('jenis_ptk_id_str', $request->status);
        }

        // 3. AMBIL SEMUA DATA (Tanpa Pagination)
        $gtks = $query->orderBy('nama', 'asc')->get();
        $sekolah = Sekolah::first();

        // 4. Pakai view cetak massal yang sama
        return view('admin.kepegawaian.gtk.cetak_kartu_massal', compact('gtks', 'sekolah'));
    }
    public function cetakKartu($id)
    {
        // 1. Ambil data pegawai
        $gtk = Gtk::findOrFail($id);
        
        // 2. Ambil data sekolah
        $sekolah = Sekolah::first();

        // 3. TRIK: Kita bungkus data satu orang ini menjadi 'koleksi' 
        // supaya bisa memakai view 'cetak_kartu_massal' yang desainnya sudah bagus.
        $gtks = collect([$gtk]);

        // 4. Tampilkan view
        return view('admin.kepegawaian.gtk.cetak_kartu_massal', compact('gtks', 'sekolah'));
    }

    public function uploadBackgroundKartu(Request $request)
    {
        $request->validate([
            'background_kartu' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Max 2MB
        ]);

        // Ambil data sekolah (asumsi ID 1)
        $sekolah = Sekolah::firstOrCreate(['id' => 1]);

        if ($request->hasFile('background_kartu')) {
            // Hapus background lama jika ada
            if ($sekolah->background_kartu && Storage::disk('public')->exists($sekolah->background_kartu)) {
                Storage::disk('public')->delete($sekolah->background_kartu);
            }
            
            // Simpan background baru
            $path = $request->file('background_kartu')->store('sekolah_media/background', 'public');
            $sekolah->background_kartu = $path;
            $sekolah->save();
        }

        return back()->with('success', 'Background kartu berhasil diupdate!');
    }
}