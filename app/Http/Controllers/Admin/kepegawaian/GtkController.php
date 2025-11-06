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
use Illuminate\Support\Facades\Storage; // <-- TAMBAHKAN INI

class GtkController extends Controller
{
    public function cetakPdfMultiple(Request $request)
    {
        // ... (fungsi lama, biarkan saja) ...
        $request->validate(['ids' => 'required|string']);
        
        $ids = explode(',', $request->input('ids'));

        $gtks = Gtk::find($ids); 

        $sekolah = Sekolah::first();

        $pdf = Pdf::loadView('admin.kepegawaian.gtk.gtk_pdf_multiple', compact('gtks', 'sekolah'));
        
        $fileName = 'Kumpulan_Profil_GTK.pdf';

        return $pdf->stream($fileName);
    }
    
    public function indexGuru(Request $request)
    {
        // ... (fungsi lama, biarkan saja) ...
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
        // ... (fungsi lama, biarkan saja) ...
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

    public function showMultiple(Request $request)
    {
        // ... (fungsi lama, biarkan saja) ...
        $request->validate(['ids' => 'required|string']);
        
        $ids = explode(',', $request->input('ids'));

        $gtks = Gtk::whereIn('id', $ids)->get();

        return view('admin.kepegawaian.gtk.show_multiple', compact('gtks'));
    }

    public function cetakPdf($id)
    {
        // ... (fungsi lama, biarkan saja) ...
        $gtk = Gtk::findOrFail($id);
        
        $sekolah = Sekolah::first();

        $qrCodeData = "Nama: " . $gtk->nama . "\nNUPTK: " . ($gtk->nuptk ?? '-');

        $rombelWali = Rombel::where('ptk_id', $gtk->ptk_id)->first();

        $rombelMengajar = Rombel::whereJsonContains('pembelajaran', ['ptk_id' => $gtk->ptk_id])->get();

        $tugasTerbaru = TugasPegawai::where('pegawai_id', $gtk->ptk_id)->orderBy('tmt', 'desc')->first();

        $pdf = Pdf::loadView('admin.kepegawaian.gtk.gtk_pdf', compact(
            'gtk', 
            'sekolah', 
            'qrCodeData',
            'rombelWali',
            'rombelMengajar',
            'tugasTerbaru'
        ));
        
        $fileName = 'Profil GTK - ' . $gtk->nama . '.pdf';

        return $pdf->stream($fileName);
    }

    public function exportGuruExcel(Request $request)
    {
        // ... (fungsi lama, biarkan saja) ...
        $query = Gtk::query()->where('jenis_ptk_id_str', 'Guru');

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

        $query->latest(); 
        
        $fileName = 'Data_Guru_Sekull.xlsx';

        return Excel::download(new GtkExport($query), $fileName);
    }

    public function exportTendikExcel(Request $request)
    {
        // ... (fungsi lama, biarkan saja) ...
        $query = Gtk::query()->where('jenis_ptk_id_str', 'Tenaga Kependidikan');

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
        
        $query->latest();

        $fileName = 'Data_Tendik_Sekull.xlsx';

        return Excel::download(new GtkExport($query), $fileName);
    }

    // --- TAMBAHAN FUNGSI UNTUK UPLOAD MEDIA ---
    public function uploadMedia(Request $request, $id)
    {
        // 1. Validasi input
        $request->validate([
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Maks 2MB
            'tandatangan' => 'nullable|image|mimes:png|max:1024', // Maks 1MB, disarankan PNG
        ]);

        // 2. Cari GTK
        $gtk = Gtk::findOrFail($id);

        // 3. Proses upload foto
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($gtk->foto && Storage::disk('public')->exists($gtk->foto)) {
                Storage::disk('public')->delete($gtk->foto);
            }
            // Simpan foto baru
            $path = $request->file('foto')->store('gtk_media/foto', 'public');
            $gtk->foto = $path;
        }

        // 4. Proses upload tanda tangan
        if ($request->hasFile('tandatangan')) {
            // Hapus ttd lama jika ada
            if ($gtk->tandatangan && Storage::disk('public')->exists($gtk->tandatangan)) {
                Storage::disk('public')->delete($gtk->tandatangan);
            }
            // Simpan ttd baru
            $path = $request->file('tandatangan')->store('gtk_media/tandatangan', 'public');
            $gtk->tandatangan = $path;
        }

        // 5. Simpan perubahan ke database
        $gtk->save();

        // 6. Kembali ke halaman sebelumnya dengan pesan sukses
        return back()->with('success', 'Media GTK berhasil diperbarui!');
    }
}