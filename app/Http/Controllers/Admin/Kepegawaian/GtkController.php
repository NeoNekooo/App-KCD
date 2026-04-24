<?php

namespace App\Http\Controllers\Admin\Kepegawaian;

use App\Models\Gtk;
use App\Models\Sekolah;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RekapitulasiGtkExport;

class GtkController extends Controller
{
    // --- 1. INDEX GURU ---
    public function indexGuru(Request $request)
    {
        $query = Gtk::with(['sekolah'])
                    ->where('status', 'Aktif')
                    ->where('jenis_ptk_id_str', 'LIKE', '%Guru%');
        
        $user = Auth::user();
        
        // Terapkan Filter Wilayah (Berjenjang)
        $this->applyGtkFilters($query, $request, $user);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('nuptk', 'like', "%{$search}%");
            });
        }

        // --- PAGINATION ---
        $perPage = $request->input('per_page', 15);
        if ($perPage === 'all') $perPage = $query->count() > 0 ? $query->count() : 15;

        $gurus = $query->latest('updated_at')->paginate($perPage)->withQueryString();

        // 🔥 FORCED DECRYPTION DI LEVEL CONTROLLER (GURU) 🔥
        $gurus->through(function ($gtk) {
            $cols = \App\Services\EncryptionService::getEncryptedColumns()['gtks'] ?? [];
            foreach ($cols as $col) {
                if (isset($gtk->$col)) {
                    $gtk->$col = \App\Services\EncryptionService::decrypt($gtk->$col);
                }
            }
            return $gtk;
        });

        // --- LOGIKA DROPDOWN BERJENJANG ---
        $listKabupaten = Sekolah::select('kabupaten_kota')->whereNotNull('kabupaten_kota')->distinct()->orderBy('kabupaten_kota')->pluck('kabupaten_kota');
        
        $listKecamatan = [];
        if ($request->filled('kabupaten_kota')) {
            $listKecamatan = Sekolah::where('kabupaten_kota', 'LIKE', '%' . $request->kabupaten_kota . '%')
                ->select('kecamatan')->whereNotNull('kecamatan')->distinct()->orderBy('kecamatan')->pluck('kecamatan');
        }

        $listSekolah = [];
        if ($request->filled('kecamatan')) {
            $sekolahQuery = Sekolah::where('kecamatan', 'LIKE', '%' . $request->kecamatan . '%');
            if ($request->filled('kabupaten_kota')) {
                $sekolahQuery->where('kabupaten_kota', 'LIKE', '%' . $request->kabupaten_kota . '%');
            }
            $listSekolah = $sekolahQuery->orderBy('nama')->pluck('nama', 'sekolah_id');
        }
        
        return view('admin.kepegawaian.gtk.index_guru', compact('gurus', 'listKabupaten', 'listKecamatan', 'listSekolah'));
    }

    // --- 2. INDEX TENDIK ---
    public function indexTendik(Request $request)
    {
        $query = Gtk::with(['sekolah'])
                    ->where('status', 'Aktif')
                    ->where('jenis_ptk_id_str', 'NOT LIKE', '%Guru%');
        
        $user = Auth::user();
        
        // Terapkan Filter Wilayah (Berjenjang)
        $this->applyGtkFilters($query, $request, $user);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('nuptk', 'like', "%{$search}%");
            });
        }

        // --- PAGINATION ---
        $perPage = $request->input('per_page', 15);
        if ($perPage === 'all') $perPage = $query->count() > 0 ? $query->count() : 15;

        $tendiks = $query->latest('updated_at')->paginate($perPage)->withQueryString();

        // 🔥 FORCED DECRYPTION DI LEVEL CONTROLLER (TENDIK) 🔥
        $tendiks->through(function ($gtk) {
            $cols = \App\Services\EncryptionService::getEncryptedColumns()['gtks'] ?? [];
            foreach ($cols as $col) {
                if (isset($gtk->$col)) {
                    $gtk->$col = \App\Services\EncryptionService::decrypt($gtk->$col);
                }
            }
            return $gtk;
        });
        
        // --- LOGIKA DROPDOWN BERJENJANG ---
        $listKabupaten = Sekolah::select('kabupaten_kota')->whereNotNull('kabupaten_kota')->distinct()->orderBy('kabupaten_kota')->pluck('kabupaten_kota');
        
        $listKecamatan = [];
        if ($request->filled('kabupaten_kota')) {
            $listKecamatan = Sekolah::where('kabupaten_kota', 'LIKE', '%' . $request->kabupaten_kota . '%')
                ->select('kecamatan')->whereNotNull('kecamatan')->distinct()->orderBy('kecamatan')->pluck('kecamatan');
        }

        $listSekolah = [];
        if ($request->filled('kecamatan')) {
            $sekolahQuery = Sekolah::where('kecamatan', 'LIKE', '%' . $request->kecamatan . '%');
            if ($request->filled('kabupaten_kota')) {
                $sekolahQuery->where('kabupaten_kota', 'LIKE', '%' . $request->kabupaten_kota . '%');
            }
            $listSekolah = $sekolahQuery->orderBy('nama')->pluck('nama', 'sekolah_id');
        }

        return view('admin.kepegawaian.gtk.index_tendik', compact('tendiks', 'listKabupaten', 'listKecamatan', 'listSekolah'));
    }

    // --- 2.1 INDEX NON-AKTIF ---
    public function indexNonaktif(Request $request)
    {
        $query = Gtk::with(['sekolah'])->where('status', '!=', 'Aktif');
        
        $user = Auth::user();
        $this->applyGtkFilters($query, $request, $user);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%");
            });
        }

        $perPage = $request->input('per_page', 15);
        $gtks = $query->latest('updated_at')->paginate($perPage)->withQueryString();

        // Decryption
        $gtks->through(function ($gtk) {
            $cols = \App\Services\EncryptionService::getEncryptedColumns()['gtks'] ?? [];
            foreach ($cols as $col) {
                if (isset($gtk->$col)) {
                    $gtk->$col = \App\Services\EncryptionService::decrypt($gtk->$col);
                }
            }
            return $gtk;
        });

        // Dropdowns
        $listKabupaten = Sekolah::select('kabupaten_kota')->distinct()->orderBy('kabupaten_kota')->pluck('kabupaten_kota');
        
        return view('admin.kepegawaian.gtk.index_nonaktif', compact('gtks', 'listKabupaten'));
    }

    // --- HELPER LOGIKA FILTER (LANGSUNG KE TABEL GTKS) ---
    private function applyGtkFilters($query, $request, $user)
    {
        if ($user && !empty($user->sekolah_id)) {
            $query->where('sekolah_id', $user->sekolah_id);
        } else {
            if ($request->filled('sekolah_id')) {
                $query->where('sekolah_id', $request->sekolah_id);
            } 
            else if ($request->filled('kabupaten_kota') || $request->filled('kecamatan')) {
                $sekolahIds = Sekolah::query();
                
                if ($request->filled('kabupaten_kota')) {
                    $sekolahIds->where('kabupaten_kota', 'LIKE', '%' . $request->kabupaten_kota . '%');
                }
                
                if ($request->filled('kecamatan')) {
                    $sekolahIds->where('kecamatan', 'LIKE', '%' . $request->kecamatan . '%');
                }

                $query->whereIn('sekolah_id', $sekolahIds->pluck('sekolah_id'));
            }
        }
    }

    // --- 3. SHOW DETAIL ---
    public function show($id)
    {
        $gtk = Gtk::with(['sekolah'])->findOrFail($id);

        // 🔥 FORCED DECRYPTION SHOW DETAIL 🔥
        $cols = \App\Services\EncryptionService::getEncryptedColumns()['gtks'] ?? [];
        foreach ($cols as $col) {
            if (isset($gtk->$col)) {
                $gtk->$col = \App\Services\EncryptionService::decrypt($gtk->$col);
            }
        }

        return view('admin.kepegawaian.gtk.show', compact('gtk'));
    }

    // --- 4. SHOW MULTIPLE ---
    public function showMultiple(Request $request) {
        $ids = explode(',', $request->input('ids'));
        $gtks = Gtk::with(['sekolah'])->whereIn('id', $ids)->get();
        return view('admin.kepegawaian.gtk.show_multiple', compact('gtks'));
    }

    public function updateData(Request $request, $id) {
        $gtk = Gtk::findOrFail($id);
        $gtk->update($request->except(['_token', '_method']));
        return back()->with('success', 'Data berhasil diperbarui!');
    }

    public function uploadMedia(Request $request, $id) {
        $request->validate(['foto' => 'image|max:5120']);
        $gtk = Gtk::findOrFail($id);
        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('gtk_media/foto', 'public');
            $gtk->foto = $path;
            $gtk->save();
        }
        return back()->with('success', 'Foto berhasil diupdate!');
    }

    // --- 5. REKAPITULASI GTK ---
    public function rekapitulasi(Request $request)
    {
        $kategoriPtk = $request->input('kategori_ptk', ''); // 'Guru', 'Tendik', atau kosong(Semua)
        $jenjangTerpilih = $request->input('jenjang', ''); // 'SMA', 'SMK', 'SLB', dll

        // 1. Query Agregasi: Join 'gtks' dengan 'sekolahs' agar kita tahu Wilayah dan Jenjang
        // Menggunakan Model Gtk agar FilterRegional Aktif
        $query = Gtk::query()
            ->join('sekolahs', 'gtks.sekolah_id', '=', 'sekolahs.sekolah_id')
            ->select(
                'sekolahs.kabupaten_kota',
                DB::raw("SUM(CASE WHEN sekolahs.status_sekolah_str LIKE '%Negeri%' THEN 1 ELSE 0 END) as total_negeri"),
                DB::raw("SUM(CASE WHEN sekolahs.status_sekolah_str LIKE '%Swasta%' THEN 1 ELSE 0 END) as total_swasta"),
                DB::raw("COUNT(gtks.id) as total_keseluruhan")
            )
            ->whereNotNull('sekolahs.kabupaten_kota');

        // ==== Filter Kondisional ====
        
        // A. Filter Jenjang Sekolah
        if (!empty($jenjangTerpilih)) {
            $query->where('sekolahs.bentuk_pendidikan_id_str', $jenjangTerpilih);
        }

        // B. Filter Kategori (Guru / Tendik)
        if ($kategoriPtk === 'Guru') {
            $query->where('gtks.jenis_ptk_id_str', 'LIKE', '%Guru%');
        } elseif ($kategoriPtk === 'Tendik') {
            $query->where('gtks.jenis_ptk_id_str', 'NOT LIKE', '%Guru%');
        }

        // ==== Ambil Data Rekap ====
        $rekapData = $query->groupBy('sekolahs.kabupaten_kota')
                           ->orderBy('sekolahs.kabupaten_kota', 'asc')
                           ->get();

        // 2. Kalkulasi Grand Total ke Bawah
        $grandTotalNegeri = $rekapData->sum('total_negeri');
        $grandTotalSwasta = $rekapData->sum('total_swasta');
        $grandTotalAkhir = $rekapData->sum('total_keseluruhan');

        // 3. Ambil List Dropdown Filter
        $listJenjang = Sekolah::whereNotNull('bentuk_pendidikan_id_str')
                              ->distinct()
                              ->pluck('bentuk_pendidikan_id_str')
                              ->filter(function($value) { return !empty(trim($value)); })
                              ->sort()
                              ->values();

        // 4. Return ke View
        return view('admin.kepegawaian.gtk.rekapitulasi', compact(
            'rekapData', 
            'grandTotalNegeri', 
            'grandTotalSwasta', 
            'grandTotalAkhir',
            'listJenjang',
            'jenjangTerpilih',
            'kategoriPtk'
        ));
    }

    public function exportRekapitulasi(Request $request)
    {
        $jenjangTerpilih = $request->input('jenjang', '');
        $kategoriPtk = $request->input('kategori_ptk', '');
        
        // Buat detail penamaan file
        $namaFile = 'Rekapitulasi_GTK';
        if (!empty($jenjangTerpilih)) {
            $namaFile .= '_' . preg_replace('/[^A-Za-z0-9\-]/', '', $jenjangTerpilih); // Hindari spasi/karakter aneh
        }
        if (!empty($kategoriPtk)) {
            $namaFile .= '_' . preg_replace('/[^A-Za-z0-9\-]/', '', $kategoriPtk);
        }
        $namaFile .= '.xlsx';

        return Excel::download(new RekapitulasiGtkExport($jenjangTerpilih, $kategoriPtk), $namaFile);
    }
}