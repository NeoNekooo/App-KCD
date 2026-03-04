<?php

namespace App\Http\Controllers\Admin\Kepegawaian;

use App\Models\Gtk;
use App\Models\Sekolah;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class GtkController extends Controller
{
    // --- 1. INDEX GURU ---
    public function indexGuru(Request $request)
    {
        $query = Gtk::with(['sekolah'])->where('jenis_ptk_id_str', 'LIKE', '%Guru%');
        
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

        $perPage = $request->input('per_page', 15);
        if ($perPage === 'all') $perPage = $query->count() > 0 ? $query->count() : 15;
        
        $gurus = $query->latest('updated_at')->paginate($perPage)->withQueryString();

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
        $query = Gtk::with(['sekolah'])->where('jenis_ptk_id_str', 'NOT LIKE', '%Guru%');
        
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

        $perPage = $request->input('per_page', 15);
        if ($perPage === 'all') $perPage = $query->count() > 0 ? $query->count() : 15;
        
        $tendiks = $query->latest('updated_at')->paginate($perPage)->withQueryString();
        
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
}