<?php

namespace App\Http\Controllers\Admin\Kepegawaian;

use App\Models\Gtk;
use App\Models\Sekolah;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;

// --- IMPORT BARU UNTUK EXCEL ---
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\GtkExport;
// ---------------------------------

class GtkController extends Controller
{
    public function cetakPdfMultiple(Request $request)
    {
        // 1. Validasi
        $request->validate(['ids' => 'required|string']);
        
        // 2. Explode IDs
        $ids = explode(',', $request->input('ids'));

        // 3. Fetch data GTK
        // Kita pakai 'find' dengan array 'ids' untuk menjaga urutan
        // Jika urutan tidak penting, bisa ganti Gtk::whereIn('id', $ids)->get();
        $gtks = Gtk::find($ids); 

        // 4. Fetch data Sekolah (untuk kop surat)
        $sekolah = Sekolah::first();

        // 5. Load view PDF baru
        $pdf = Pdf::loadView('admin.kepegawaian.gtk.gtk_pdf_multiple', compact('gtks', 'sekolah'));
        
        // 6. Atur nama file
        $fileName = 'Kumpulan_Profil_GTK.pdf';

        // 7. Stream PDF
        return $pdf->stream($fileName);
    }
    /**
     * Menampilkan daftar Guru.
     */
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

    /**
     * Menampilkan daftar Tenaga Kependidikan.
     */
    public function indexTendik(Request $request)
    {
        $query = Gtk::query()->where('jenis_ptk_id_str', 'Tenaga Kependidikan');
        
        $query->when($request->search, function ($q, $search) {
        return $q->where(function ($sub) use ($search) {
            $sub->where('nama', 'like', "%{$search}%")
                ->orWhere('nip', 'like', "%{$search}%")
                ->orWhere('nik', 'like', "%{$search}%"); // <-- SUDAH DIPERBAIKI
        });
    });
        $tendiks = $query->latest()->paginate(15);
        
        return view('admin.kepegawaian.gtk.index_tendik', compact('tendiks'));
    }

    /**
     * Menampilkan detail untuk satu atau lebih GTK yang dipilih.
     */
    public function showMultiple(Request $request)
    {
        $request->validate(['ids' => 'required|string']);
        
        $ids = explode(',', $request->input('ids'));

        $gtks = Gtk::whereIn('id', $ids)->get();

        return view('admin.kepegawaian.gtk.show_multiple', compact('gtks'));
    }

    /**
     * ====================================================================
     * FUNGSI BARU UNTUK CETAK PDF DITAMBAHKAN DI SINI
     * ====================================================================
     */
    public function cetakPdf($id)
    {
        // Ambil data GTK yang akan dicetak
        $gtk = Gtk::findOrFail($id);
        
        // Ambil data sekolah untuk kop surat
        $sekolah = Sekolah::first();

        // Data untuk QR Code (contoh: NUPTK atau NIK)
        $qrCodeData = "Nama: " . $gtk->nama . "\nNUPTK: " . ($gtk->nuptk ?? '-');

        // Buat PDF dari view 'gtk_pdf.blade.php'
        $pdf = Pdf::loadView('admin.kepegawaian.gtk.gtk_pdf', compact('gtk', 'sekolah', 'qrCodeData'));
        
        // Atur nama file saat di-download
        $fileName = 'Profil GTK - ' . $gtk->nama . '.pdf';

        // Tampilkan PDF di browser (stream)
        return $pdf->stream($fileName);
    }

    /**
     * Menangani export data Guru ke Excel.
     * --- SUDAH DIPERBARUI ---
     */
    public function exportGuruExcel(Request $request)
    {
        $query = Gtk::query()->where('jenis_ptk_id_str', 'Guru');

        // Terapkan filter berdasarkan ID atau pencarian
        if ($request->has('ids')) {
            $ids = explode(',', $request->input('ids'));
            $query->whereIn('id', $ids);
        }
        elseif ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($sub) use ($search) {
                $sub->where('nama', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        // Tambahkan sorting
        $query->latest(); 
        
        // Tentukan nama file .xlsx
        $fileName = 'Data_Guru_Sekull.xlsx';

        // Panggil class export baru
        return Excel::download(new GtkExport($query), $fileName);
    }

    /**
     * Menangani export data Tenaga Kependidikan ke Excel.
     * --- SUDAH DIPERBARUI ---
     */
    public function exportTendikExcel(Request $request)
    {
        $query = Gtk::query()->where('jenis_ptk_id_str', 'Tenaga Kependidikan');

        // Terapkan filter berdasarkan ID atau pencarian
        if ($request->has('ids')) {
            $ids = explode(',', $request->input('ids'));
            $query->whereIn('id', $ids);
        }
        elseif ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($sub) use ($search) {
                $sub->where('nama', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            });
        }
        
        // Tambahkan sorting
        $query->latest();

        // Tentukan nama file .xlsx
        $fileName = 'Data_Tendik_Sekull.xlsx';

        // Panggil class export baru
        return Excel::download(new GtkExport($query), $fileName);
    }

    /**
     * --- FUNGSI generateExport LAMA SUDAH DIHAPUS ---
     * --- KARENA LOGIKANYA PINDAH KE GtkExport.php ---
     */
}