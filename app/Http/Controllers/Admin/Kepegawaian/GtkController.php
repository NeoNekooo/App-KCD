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
        // Filter GTK: Jabatan Guru
        $query = Gtk::query()->where('jenis_ptk_id_str', 'LIKE', '%Guru%');
        
        $user = Auth::user();
        $listKabupaten = [];
        $listKecamatan = [];
        $listSekolah = [];

        // --- LOGIKA FILTER WILAYAH ---
        if ($user && !empty($user->sekolah_id)) {
            // Jika user adalah Sekolah, kunci ke sekolah tersebut
            $query->whereHas('pengguna', function($q) use ($user) {
                $q->where('sekolah_id', $user->sekolah_id);
            });
        } else {
            // Jika Admin/KCD, load list filter
            $listKabupaten = Sekolah::select('kabupaten_kota')->distinct()->whereNotNull('kabupaten_kota')->orderBy('kabupaten_kota')->pluck('kabupaten_kota');

            if ($request->filled('kabupaten_kota')) {
                $listKecamatan = Sekolah::where('kabupaten_kota', $request->kabupaten_kota)->select('kecamatan')->distinct()->whereNotNull('kecamatan')->orderBy('kecamatan')->pluck('kecamatan');
            }
            if ($request->filled('kabupaten_kota') && $request->filled('kecamatan')) {
                $listSekolah = Sekolah::where('kabupaten_kota', $request->kabupaten_kota)->where('kecamatan', $request->kecamatan)->orderBy('nama')->pluck('nama', 'sekolah_id');
            }

            // Terapkan Filter
            if ($request->filled('sekolah_id')) {
                $query->whereHas('pengguna', function($q) use ($request) {
                    $q->where('sekolah_id', $request->sekolah_id);
                });
            } elseif ($request->filled('kecamatan')) {
                $query->whereHas('pengguna.sekolah', function($q) use ($request) {
                    $q->where('kecamatan', $request->kecamatan)->where('kabupaten_kota', $request->kabupaten_kota);
                });
            } elseif ($request->filled('kabupaten_kota')) {
                $query->whereHas('pengguna.sekolah', function($q) use ($request) {
                    $q->where('kabupaten_kota', $request->kabupaten_kota);
                });
            }
        }

        // --- PENCARIAN ---
        $query->when($request->search, function ($q, $search) {
            return $q->where(function ($sub) use ($search) {
                $sub->where('nama', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%")
                    ->orWhere('nuptk', 'like', "%{$search}%")
                    ->orWhere('alamat_jalan', 'like', "%{$search}%");
            });
        });

        // --- PAGINATION ---
        $perPage = $request->input('per_page', 15);
        if ($perPage === 'all') $perPage = $query->count() > 0 ? $query->count() : 15;
        
        $gurus = $query->latest()->paginate($perPage)->appends($request->all());
        
        return view('admin.kepegawaian.gtk.index_guru', compact('gurus', 'listKabupaten', 'listKecamatan', 'listSekolah'));
    }

    // --- 2. INDEX TENDIK ---
    public function indexTendik(Request $request)
    {
        // Filter GTK: BUKAN Guru (Mengambil semua jenis PTK selain Guru)
        $query = Gtk::query()->where('jenis_ptk_id_str', 'NOT LIKE', '%Guru%');
        
        $user = Auth::user();
        $listKabupaten = [];
        $listKecamatan = [];
        $listSekolah = [];

        // --- LOGIKA FILTER WILAYAH (SAMA DENGAN GURU) ---
        if ($user && !empty($user->sekolah_id)) {
            $query->whereHas('pengguna', function($q) use ($user) {
                $q->where('sekolah_id', $user->sekolah_id);
            });
        } else {
            $listKabupaten = Sekolah::select('kabupaten_kota')->distinct()->whereNotNull('kabupaten_kota')->orderBy('kabupaten_kota')->pluck('kabupaten_kota');

            if ($request->filled('kabupaten_kota')) {
                $listKecamatan = Sekolah::where('kabupaten_kota', $request->kabupaten_kota)->select('kecamatan')->distinct()->whereNotNull('kecamatan')->orderBy('kecamatan')->pluck('kecamatan');
            }
            if ($request->filled('kabupaten_kota') && $request->filled('kecamatan')) {
                $listSekolah = Sekolah::where('kabupaten_kota', $request->kabupaten_kota)->where('kecamatan', $request->kecamatan)->orderBy('nama')->pluck('nama', 'sekolah_id');
            }

            if ($request->filled('sekolah_id')) {
                $query->whereHas('pengguna', function($q) use ($request) {
                    $q->where('sekolah_id', $request->sekolah_id);
                });
            } elseif ($request->filled('kecamatan')) {
                $query->whereHas('pengguna.sekolah', function($q) use ($request) {
                    $q->where('kecamatan', $request->kecamatan)->where('kabupaten_kota', $request->kabupaten_kota);
                });
            } elseif ($request->filled('kabupaten_kota')) {
                $query->whereHas('pengguna.sekolah', function($q) use ($request) {
                    $q->where('kabupaten_kota', $request->kabupaten_kota);
                });
            }
        }

        // --- PENCARIAN ---
        $query->when($request->search, function ($q, $search) {
            return $q->where(function ($sub) use ($search) {
                $sub->where('nama', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%")
                    ->orWhere('nuptk', 'like', "%{$search}%")
                    ->orWhere('alamat_jalan', 'like', "%{$search}%");
            });
        });

        // --- PAGINATION ---
        $perPage = $request->input('per_page', 15);
        if ($perPage === 'all') $perPage = $query->count() > 0 ? $query->count() : 15;
        
        $tendiks = $query->latest()->paginate($perPage)->appends($request->all());
        
        return view('admin.kepegawaian.gtk.index_tendik', compact('tendiks', 'listKabupaten', 'listKecamatan', 'listSekolah'));
    }

    // --- 3. SHOW DETAIL (PROFIL) ---
    public function show($id)
    {
        $gtk = Gtk::with(['pengguna.sekolah'])->findOrFail($id);
        return view('admin.kepegawaian.gtk.show', compact('gtk'));
    }

    // --- 4. SHOW MULTIPLE (PERBANDINGAN) ---
    public function showMultiple(Request $request) {
        $ids = explode(',', $request->input('ids'));
        $gtks = Gtk::whereIn('id', $ids)->get();
        return view('admin.kepegawaian.gtk.show_multiple', compact('gtks'));
    }

    // --- FUNGSI UPDATE DATA ---
    public function updateData(Request $request, $id) {
        $gtk = Gtk::findOrFail($id);
        $gtk->update($request->except(['_token', '_method']));
        return back()->with('success', 'Data berhasil diperbarui!');
    }

    // --- FUNGSI UPLOAD FOTO ---
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