<?php

namespace App\Http\Controllers\Admin\Kepegawaian;

use App\Models\Gtk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GtkController extends Controller
{
    public function index(Request $request)
    {
        $query = Gtk::query();

        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%')
                  ->orWhere('nip', 'like', '%' . $request->search . '%')
                  ->orWhere('nik', 'like', '%' . $request->search . '%');
        }

        $gtks = $query->latest()->paginate(15);
        
        return view('admin.kepegawaian.gtk.index', compact('gtks'));
    }

    /**
     * Menangani permintaan untuk export data ke Excel (CSV) dengan SEMUA kolom.
     */
    public function exportExcel(Request $request)
    {
        $query = Gtk::query();

        // PERUBAHAN DI SINI: Cek apakah ada ID yang dikirim untuk di-export
        if ($request->has('ids')) {
            $ids = explode(',', $request->input('ids'));
            $query->whereIn('id', $ids);
        }
        
        $gtks = $query->latest()->get();

        $fileName = 'Data_Lengkap_GTK_Sekull.csv';
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($gtks) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, [
                'Nama Lengkap', 'Jenis Kelamin', 'Tempat Lahir', 'Tanggal Lahir', 'Agama', 'NIK',
                'Status Kepegawaian', 'NIP', 'NUPTK', 'Jenis PTK', 'Jabatan', 'Tanggal Surat Tugas', 'Status Induk',
                'Pendidikan Terakhir', 'Bidang Studi Terakhir', 'Pangkat/Golongan Terakhir',
                'Riwayat Pendidikan Formal', 'Riwayat Kepangkatan'
            ]);

            foreach ($gtks as $gtk) {
                fputcsv($file, [
                    $gtk->nama,
                    $gtk->jenis_kelamin,
                    $gtk->tempat_lahir,
                    $gtk->tanggal_lahir,
                    $gtk->agama_id_str,
                    $gtk->nik,
                    $gtk->status_kepegawaian_id_str,
                    $gtk->nip,
                    $gtk->nuptk,
                    $gtk->jenis_ptk_id_str,
                    $gtk->jabatan_ptk_id_str,
                    $gtk->tanggal_surat_tugas,
                    $gtk->ptk_induk == 1 ? 'Induk' : 'Non-Induk',
                    $gtk->pendidikan_terakhir,
                    $gtk->bidang_studi_terakhir,
                    $gtk->pangkat_golongan_terakhir,
                    $gtk->rwy_pend_formal,
                    $gtk->rwy_kepangkatan
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function show(Gtk $gtk)
    {
        return view('admin.kepegawaian.gtk.show', compact('gtk'));
    }
}

