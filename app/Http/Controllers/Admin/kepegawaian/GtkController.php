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

class GtkController extends Controller
{
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

}