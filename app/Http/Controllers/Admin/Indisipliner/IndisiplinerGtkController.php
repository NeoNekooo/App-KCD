<?php

namespace App\Http\Controllers\Admin\Indisipliner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Gtk;
use App\Models\PelanggaranKategoriGtk;
use App\Models\PelanggaranPoinGtk;
use App\Models\PelanggaranSanksiGtk; // Pastikan model ini ada
use App\Models\PelanggaranNilaiGtk;
use App\Models\Rombel;

class IndisiplinerGtkController extends Controller
{
    // ============================================================
    // PENGATURAN
    // ============================================================
    public function pengaturanIndex()
    {
        $kategoriList = PelanggaranKategoriGtk::with('pelanggaranPoinGtk')
            ->orderBy('nama', 'asc')
            ->get();

        $sanksiList = PelanggaranSanksiGtk::orderBy('poin_min', 'asc')->get();

        return view('admin.indisipliner.guru.pengaturan.index', compact('kategoriList', 'sanksiList'));
    }

    public function storeKategori(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:pelanggaran_kategori_gtk,nama'
        ]);

        PelanggaranKategoriGtk::create([
            'nama' => strtoupper($request->nama),
            'status' => 1
        ]);

        return back()->with('success', 'Kategori pelanggaran berhasil ditambahkan.');
    }

    public function updateKategori(Request $request, PelanggaranKategoriGtk $kategori)
    {
        $request->validate([
            'nama' => [
                'required',
                'string',
                'max:255',
                Rule::unique('pelanggaran_kategori_gtk')->ignore($kategori->ID, 'ID'),
            ],
        ]);

        $kategori->update(['nama' => strtoupper($request->nama)]);
        return back()->with('success', 'Kategori pelanggaran berhasil diperbarui.');
    }

    public function destroyKategori(PelanggaranKategoriGtk $kategori)
    {
        if ($kategori->pelanggaranPoinGtk()->exists()) {
            return back()->withErrors('Kategori tidak dapat dihapus karena masih memiliki jenis pelanggaran.');
        }
        $kategori->delete();
        return back()->with('success', 'Kategori pelanggaran berhasil ditambahkan.');
    }

    // ============================================================
    // POIN PELANGGARAN
    // ============================================================
    public function storePoin(Request $request)
    {
        $request->validate([
            'IDpelanggaran_kategori' => 'required|exists:pelanggaran_kategori_gtk,ID',
            'nama' => 'required|string|max:255',
            'poin' => 'required|integer',
            'tindakan' => 'nullable|string',
        ]);

        PelanggaranPoinGtk::create([
            'IDpelanggaran_kategori' => $request->IDpelanggaran_kategori,
            'nama' => strtoupper($request->nama),
            'poin' => $request->poin,
            'tindakan' => $request->tindakan,
        ]);

        return back()->with('success', 'Jenis pelanggaran berhasil ditambahkan.');
    }

    public function updatePoin(Request $request, $id)
    {
        $request->validate([
            'IDpelanggaran_kategori' => 'required|exists:pelanggaran_kategori_gtk,ID',
            'nama' => 'required|string|max:255',
            'poin' => 'required|integer',
            'tindakan' => 'nullable|string',
        ]);

        $poin = PelanggaranPoinGtk::findOrFail($id);
        $poin->update([
            'IDpelanggaran_kategori' => $request->IDpelanggaran_kategori,
            'nama' => strtoupper($request->nama),
            'poin' => $request->poin,
            'tindakan' => $request->tindakan,
        ]);

        return back()->with('success', 'Jenis pelanggaran berhasil diperbarui.');
    }

    public function destroyPoin($id)
    {
        $poin = PelanggaranPoinGtk::findOrFail($id);
        $poin->delete();

        return back()->with('success', 'Jenis pelanggaran berhasil dihapus.');
    }

    // ============================================================
    // SANKSI PELANGGARAN (INI YANG DIPERBAIKI)
    // ============================================================
    public function storeSanksi(Request $request)
    {
        // 1. Validasi
        $request->validate([
            'poin_min' => 'required|integer',
            'poin_max' => 'required|integer|gte:poin_min',
            'nama' => 'required|string|max:255', // Ini adalah 'name' dari input di form
            'penindak' => 'nullable|string|max:255', // Asumsi 'name' inputnya adalah 'penindak'
        ]);

        // 2. Buat data baru
        PelanggaranSanksiGtk::create([
            'poin_min' => $request->poin_min,
            'poin_max' => $request->poin_max,
            'nama' => $request->nama,
            'penindak' => $request->penindak,
        ]);

        // 3. Kembalikan ke halaman dengan hash untuk tab yang benar
        // PERBAIKAN: Menggunakan #tab-sanksi sesuai ID di HTML Anda
        return redirect(route('admin.indisipliner.guru.pengaturan.index') . '#tab-sanksi')
            ->with('success', 'Sanksi berhasil ditambahkan.');
    }

    // --- METHOD BARU DITAMBAHKAN DI SINI ---
    public function updateSanksi(Request $request, $id)
    {
        // 1. Validasi
        $request->validate([
            'poin_min' => 'required|integer',
            'poin_max' => 'required|integer|gte:poin_min',
            'nama' => 'required|string|max:255',
            'penindak' => 'nullable|string|max:255',
        ]);

        // 2. Cari dan update sanksi
        $sanksi = PelanggaranSanksiGtk::findOrFail($id);
        $sanksi->update([
            'poin_min' => $request->poin_min,
            'poin_max' => $request->poin_max,
            'nama' => $request->nama,
            'penindak' => $request->penindak,
        ]);

        // 3. Redirect kembali ke tab yang benar
        return redirect(route('admin.indisipliner.guru.pengaturan.index') . '#tab-sanksi')
            ->with('success', 'Sanksi berhasil diperbarui.');
    }

    // --- METHOD BARU DITAMBAHKAN DI SINI ---
    public function destroySanksi($id)
    {
        // 1. Cari dan hapus sanksi
        $sanksi = PelanggaranSanksiGtk::findOrFail($id);
        $sanksi->delete();

        // 2. Redirect kembali ke tab yang benar
        return redirect(route('admin.indisipliner.guru.pengaturan.index') . '#tab-sanksi')
            ->with('success', 'Sanksi berhasil dihapus.');
    }


    // ============================================================
    // DAFTAR INDISIPLINER GURU
    // ============================================================
    public function daftarIndex(Request $request)
    {
        $semesterList = Rombel::select('semester_id')->distinct()->get()->map(function ($item) {
            $tahun = substr($item->semester_id, 0, 4);
            $semester = substr($item->semester_id, 4, 1);
            return [
                'semester_id' => $item->semester_id,
                'tahun_pelajaran' => $tahun . '/' . ($tahun + 1),
                'semester' => $semester == '1' ? 'Ganjil' : 'Genap',
            ];
        });

        $semesterAktif = Rombel::orderByDesc('semester_id')->value('semester_id');

        $kategoriList = PelanggaranKategoriGtk::with('pelanggaranPoinGtk')->orderBy('nama')->get();
        $gurus = Gtk::orderBy('nama')->get();

        $query = PelanggaranNilaiGtk::with('detailPoinGtk')->latest('tanggal')->latest('jam');

        $semesterFilter = $request->filled('semester_id') ? $request->semester_id : $semesterAktif;
        $tahunReq = substr($semesterFilter, 0, 4);
        $semesterReq = substr($semesterFilter, 4, 1);
        $tapelReq = $tahunReq . '/' . ($tahunReq + 1);
        $semesterTeks = $semesterReq == '1' ? 'Ganjil' : 'Genap';

        $query->where('tapel', $tapelReq)->where('semester', $semesterTeks);

        if ($request->filled('nama_guru')) {
            $query->where('nama_guru', 'like', "%{$request->nama_guru}%");
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nama_guru', 'like', "%$search%");
        }

        $pelanggaranList = $query->paginate(10)->appends($request->query());

        return view('admin.indisipliner.guru.daftar.index', compact(
            'pelanggaranList',
            'semesterList',
            'kategoriList',
            'gurus',
            'semesterAktif'
        ));
    }

    // ============================================================
    // SIMPAN DATA
    // ============================================================
    public function store(Request $request)
    {
        $request->validate([
            'semester_id' => 'required|string',
            'tanggal' => 'required|date',
            'jam' => 'required',
            'nama_guru' => 'required|string|max:255',
            'IDpelanggaran_poin' => 'required|exists:pelanggaran_poin_gtk,ID',
        ]);

        $poin = PelanggaranPoinGtk::findOrFail($request->IDpelanggaran_poin);

        $tahun = substr($request->semester_id, 0, 4);
        $semesterAngka = substr($request->semester_id, 4, 1);
        $tapel = $tahun . '/' . ($tahun + 1);
        $semesterTeks = $semesterAngka == '1' ? 'Ganjil' : 'Genap';

        PelanggaranNilaiGtk::create([
            'nama_guru' => strtoupper($request->nama_guru),
            'IDpelanggaran_poin' => $request->IDpelanggaran_poin,
            'tanggal' => $request->tanggal,
            'jam' => $request->jam,
            'poin' => $poin->poin,
            'tapel' => $tapel,
            'semester' => $semesterTeks,
            'semester_id' => $request->semester_id,
        ]);

        return redirect()->route('admin.indisipliner.guru.daftar.index')
            ->with('success', 'Data pelanggaran guru berhasil disimpan.');
    }

    // ============================================================
    // HAPUS DATA
    // ============================================================
    public function destroy(PelanggaranNilaiGtk $pelanggaran)
    {
        $pelanggaran->delete();
        return back()->with('success', 'Data pelanggaran guru berhasil dihapus.');
    }

    // ============================================================
    // REKAPITULASI
    // ============================================================
    public function rekapitulasiIndex(Request $request)
    {
        $guruList = Gtk::orderBy('nama')->get();
        $pelanggaranGuru = null;
        $totalPoin = 0;
        $sanksiAktif = null;
        $namaGuru = $request->nama_guru ?? null;

        if ($namaGuru) {
            $pelanggaranGuru = PelanggaranNilaiGtk::where('nama_guru', 'like', "%$namaGuru%")
                ->with('detailPoinGtk')
                ->orderBy('tanggal', 'desc')
                ->get();

            $totalPoin = $pelanggaranGuru->sum('poin');

            $sanksiAktif = PelanggaranSanksiGtk::where('poin_min', '<=', $totalPoin)
                ->where('poin_max', '>=', $totalPoin)
                ->first();
        }

        return view('admin.indisipliner.guru.rekapitulasi.index', compact(
            'guruList',
            'namaGuru',
            'pelanggaranGuru',
            'totalPoin',
            'sanksiAktif'
        ));
    }

    // ============================================================
    // CETAK SEMUA GURU
    // ============================================================
    public function cetakSemua()
    {
        $pelanggaranList = PelanggaranNilaiGtk::with('detailPoinGtk')
            ->orderBy('nama_guru')
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('admin.indisipliner.guru.cetak.rekap-semua', compact('pelanggaranList'));
    }

    // ============================================================
    // CETAK SATU GURU
    // ============================================================
    public function cetakIndividu($nama)
    {
        $guru = Gtk::where('nama', $nama)->firstOrFail();

        $pelanggaranGuru = PelanggaranNilaiGtk::where('nama_guru', $guru->nama)
            ->with('detailPoinGtk')
            ->orderBy('tanggal', 'desc')
            ->get();

        $totalPoin = $pelanggaranGuru->sum('poin');

        $sanksiAktif = PelanggaranSanksiGtk::where('poin_min', '<=', $totalPoin)
            ->where('poin_max', '>=', $totalPoin)
            ->first();

        return view('admin.indisipliner.guru.cetak.rekap-individu', compact(
            'guru',
            'pelanggaranGuru',
            'totalPoin',
            'sanksiAktif'
        ));
    }
}