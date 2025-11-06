<?php

namespace App\Http\Controllers\Admin\Indisipliner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

// Import Model yang dibutuhkan
use App\Models\PelanggaranKategori;
use App\Models\PelanggaranPoin;
use App\Models\PelanggaranSanksi;
use App\Models\PelanggaranNilai;
use App\Models\Siswa;
use App\Models\Rombel;
use App\Models\TahunPelajaran as Tapel;
use App\Models\Semester;

class IndisiplinerSiswaController extends Controller
{
    // =================================================================================
    // PENGATURAN
    // =================================================================================
    
    public function pengaturanIndex()
    {
        $kategoriList = PelanggaranKategori::with('pelanggaranPoin')->orderBy('nama', 'asc')->get();
        $sanksiList = PelanggaranSanksi::orderBy('poin_min', 'asc')->get();
        return view('admin.indisipliner.siswa.pengaturan.index', compact('kategoriList', 'sanksiList'));
    }

    // --- Method untuk Kategori ---
    public function storeKategori(Request $request)
    {
        $request->validate(['nama' => 'required|string|max:255|unique:pelanggaran_kategori,nama']);
        PelanggaranKategori::create(['nama' => strtoupper($request->nama), 'status' => 1]);
        return back()->with('success', 'Kategori pelanggaran berhasil ditambahkan.');
    }

    public function updateKategori(Request $request, PelanggaranKategori $pelanggaranKategori)
    {
        $request->validate([
            'nama' => ['required','string','max:255', Rule::unique('pelanggaran_kategori')->ignore($pelanggaranKategori->ID, 'ID')]
        ]);
        $pelanggaranKategori->update(['nama' => strtoupper($request->nama)]);
        return back()->with('success', 'Kategori pelanggaran berhasil diperbarui.');
    }

    public function destroyKategori(PelanggaranKategori $pelanggaranKategori)
    {
        if ($pelanggaranKategori->pelanggaranPoin()->exists()) {
            return back()->withErrors('Kategori tidak dapat dihapus karena masih memiliki jenis pelanggaran.');
        }
        $pelanggaranKategori->delete();
        return back()->with('success', 'Kategori pelanggaran berhasil dihapus.');
    }

    // --- Method untuk Poin Pelanggaran ---
    public function storePoin(Request $request)
    {
        $request->validate([
            'IDpelanggaran_kategori' => 'required|exists:pelanggaran_kategori,ID',
            'nama' => 'required|string',
            'poin' => 'required|integer|min:0',
            'tindakan' => 'nullable|string',
        ]);
        PelanggaranPoin::create($request->all());
        return back()->with('success', 'Jenis pelanggaran berhasil ditambahkan.');
    }

    public function updatePoin(Request $request, PelanggaranPoin $pelanggaranPoin)
    {
        $request->validate([
            'IDpelanggaran_kategori' => 'required|exists:pelanggaran_kategori,ID',
            'nama' => 'required|string',
            'poin' => 'required|integer|min:0',
            'tindakan' => 'nullable|string',
        ]);
        $pelanggaranPoin->update($request->all());
        return back()->with('success', 'Jenis pelanggaran berhasil diperbarui.');
    }

    public function destroyPoin(PelanggaranPoin $pelanggaranPoin)
    {
        $pelanggaranPoin->delete();
        return back()->with('success', 'Jenis pelanggaran berhasil dihapus.');
    }

    // --- Method untuk Sanksi ---
    public function storeSanksi(Request $request)
    {
        $request->validate([
            'poin_min' => 'required|integer|min:0',
            'poin_max' => 'required|integer|gte:poin_min',
            'nama' => 'required|string|max:255',
            'penindak' => 'nullable|string',
        ]);
        PelanggaranSanksi::create($request->all());
        return back()->with('success', 'Sanksi pelanggaran berhasil ditambahkan.');
    }

    public function updateSanksi(Request $request, PelanggaranSanksi $pelanggaranSanksi)
    {
        $request->validate([
            'poin_min' => 'required|integer|min:0',
            'poin_max' => 'required|integer|gte:poin_min',
            'nama' => 'required|string|max:255',
            'penindak' => 'nullable|string',
        ]);
        $pelanggaranSanksi->update($request->all());
        return back()->with('success', 'Sanksi pelanggaran berhasil diperbarui.');
    }

    public function destroySanksi(PelanggaranSanksi $pelanggaranSanksi)
    {
        $pelanggaranSanksi->delete();
        return back()->with('success', 'Sanksi pelanggaran berhasil dihapus.');
    }


    // =================================================================================
    // DAFTAR INDISIPLINER
    // =================================================================================

    public function daftarIndex(Request $request)
    {
        // Data untuk filter dan form modal
        $tapels = Tapel::orderBy('tahun_pelajaran', 'desc')->get();
        $semesters = Semester::all();
        $rombels = Rombel::select('id', 'nama')->orderBy('nama')->get()->unique('nama');
        $kategoriList = PelanggaranKategori::with('pelanggaranPoin')->orderBy('nama')->get();
        $siswaList = collect();

        if ($request->filled('rombel_id')) {
            $siswaList = Siswa::where('anggota_rombel_id', $request->rombel_id)->orderBy('nama')->get();
        }

        $query = PelanggaranNilai::with(['siswa', 'detailPoin.kategori', 'rombel'])->latest('tanggal')->latest('jam');
        
        if ($request->filled('tapel_id')) {
            $query->where('IDtapel', $request->tapel_id);
        }
        if ($request->filled('semester_id')) {
            $query->where('IDsemester', $request->semester_id);
        }
        if ($request->filled('rombel_id')) {
            $query->where('IDkelas', $request->rombel_id);
        }
        if ($request->filled('nis')) {
            $query->where('NIS', $request->nis);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('NIS', 'like', "%{$search}%")
                  ->orWhereHas('siswa', function ($subq) use ($search) {
                        $subq->where('nama', 'like', "%{$search}%");
                    });
            });
        }

        $pelanggaranList = $query->paginate(10)->appends($request->query());

        return view('admin.indisipliner.siswa.daftar.index', compact('pelanggaranList', 'rombels', 'siswaList', 'tapels', 'semesters', 'kategoriList'));
    }

    public function storePelanggaran(Request $request)
    {
        $request->validate([
            'IDtapel' => 'required|exists:tahun_pelajarans,id',
            'IDsemester' => 'required|exists:semesters,id',
            'tanggal' => 'required|date',
            'jam' => 'required',
            'IDkelas' => 'required|exists:rombels,id',
            'NIS' => 'required|exists:siswas,nipd',
            'IDpelanggaran_poin' => 'required|exists:pelanggaran_poin,ID',
            'IDmapel' => 'nullable'
        ]);

        $poin = PelanggaranPoin::findOrFail($request->IDpelanggaran_poin);

        PelanggaranNilai::create([
            'NIS' => $request->NIS,
            'IDkelas' => $request->IDkelas,
            'IDpelanggaran_poin' => $poin->ID,
            'poin' => $poin->poin,
            'tanggal' => $request->tanggal,
            'jam' => $request->jam,
            'IDtapel' => $request->IDtapel,
            'IDsemester' => $request->IDsemester,
            'IDmapel' => $request->IDmapel,
        ]);

        return back()->with('success', 'Data pelanggaran berhasil disimpan.');
    }

    public function destroyPelanggaran(PelanggaranNilai $pelanggaranNilai)
    {
        $pelanggaranNilai->delete();
        return back()->with('success', 'Data pelanggaran berhasil dihapus.');
    }

    // =================================================================================
    // ==> BAGIAN YANG DIPERBAIKI ADA DI SINI <==
    // =================================================================================

    public function getSiswaByRombel(Rombel $rombel)
    {
        // Panggil relasi 'siswa()' yang sudah kita buat di model Rombel.
        // Laravel akan otomatis melakukan query yang benar menggunakan 'anggota_rombel_id'.
        $siswa = $rombel->siswa()
                        ->orderBy('nama', 'asc')
                        ->get(['nipd', 'nama']); // Ambil kolom nipd dan nama

        return response()->json($siswa);
    }
    
    // =================================================================================
    // REKAPITULASI
    // =================================================================================

    // public function rekapitulasiIndex(Request $request)
    // {
    //     $siswaList = Siswa::orderBy('nama')->get();
    //     $siswa = null;
    //     $pelanggaranSiswa = null;
    //     $totalPoin = 0;
    //     $sanksiAktif = null;

    //     if ($request->filled('nis')) {
    //         $siswa = Siswa::with('rombel')->where('nipd', $request->nis)->first();

    //         if ($siswa) {
    //             $pelanggaranSiswa = PelanggaranNilai::where('NIS', $siswa->nipd)
    //                                     ->with('detailPoin')
    //                                     ->orderBy('tanggal', 'desc')
    //                                     ->get();
                
    //             $totalPoin = $pelanggaranSiswa->sum('poin');

    //             $sanksiAktif = PelanggaranSanksi::where('poin_min', '<=', $totalPoin)
    //                                     ->where('poin_max', '>=', $totalPoin)
    //                                     ->first();
    //         } else {
    //             return back()->withErrors(['nis' => 'Siswa dengan NIPD tersebut tidak ditemukan.'])->withInput();
    //         }
    //     }

    //     return view('admin.indisipliner.siswa.rekapitulasi.index', compact('siswaList', 'siswa', 'pelanggaranSiswa', 'totalPoin', 'sanksiAktif'));
    // }


      // =================================================================================
    // REKAPITULASI
    // =================================================================================

    public function rekapitulasiIndex(Request $request)
    {
        $siswaList = Siswa::orderBy('nama')->get();
        $siswa = null;
        $pelanggaranSiswa = null;
        $totalPoin = 0;
        $sanksiAktif = null;

        if ($request->filled('nis')) {
            $siswa = Siswa::with('rombel')->where('nipd', $request->nis)->first();

            if ($siswa) {
                $pelanggaranSiswa = PelanggaranNilai::where('NIS', $siswa->nipd)
                                    ->with('detailPoin')
                                    ->orderBy('tanggal', 'desc')
                                    ->get();
                
                $totalPoin = $pelanggaranSiswa->sum('poin');

                $sanksiAktif = PelanggaranSanksi::where('poin_min', '<=', $totalPoin)
                                ->where('poin_max', '>=', $totalPoin)
                                ->first();
            } else {
                return back()->withErrors(['nis' => 'Siswa dengan NIPD tersebut tidak ditemukan.'])->withInput();
            }
        }

        return view('admin.indisipliner.siswa.rekapitulasi.index', compact('siswaList', 'siswa', 'pelanggaranSiswa', 'totalPoin', 'sanksiAktif'));
    }
}
