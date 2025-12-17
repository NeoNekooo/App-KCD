<?php

namespace App\Http\Controllers\Admin\Kesiswaan;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Rombel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\PelanggaranSanksi; // Pastikan model ini ada
use App\Models\PelanggaranNilai;

class SiswaController extends Controller
{
    public function index(Request $request)
    {
        // Memulai query dengan eager loading relasi rombel untuk efisiensi
        $query = Siswa::with('rombel');

        // =================================================================
        // [PERUBAHAN LOGIKA FILTER KELAS]
        // Kita adopsi logika dari controller Anda yang BERHASIL.
        // =================================================================
        if ($request->filled('rombel_id')) {
            $rombel = Rombel::find($request->rombel_id);

            if ($rombel && !empty($rombel->anggota_rombel)) {
                // 1. Decode JSON anggota rombel
                $anggotaData = json_decode($rombel->anggota_rombel, true);
                
                if (is_array($anggotaData) && !empty($anggotaData)) {
                    // 2. Ambil semua ID peserta didik dari JSON
                    $anggotaPdIds = array_column($anggotaData, 'peserta_didik_id');
                    
                    // 3. Cari Siswa yang memiliki peserta_didik_id tersebut
                    //    Kita ambil 'id' siswanya untuk memfilter query utama
                    $siswaIdsInRombel = Siswa::whereIn('peserta_didik_id', $anggotaPdIds)->pluck('id');
                    
                    // 4. Terapkan filter ke query utama
                    $query->whereIn('id', $siswaIdsInRombel);
                } else {
                    // Jika kelas ada tapi anggotanya kosong, jangan tampilkan siapa-siapa
                    $query->whereRaw('1 = 0');
                }
            } else {
                // Jika Rombel ID tidak ditemukan, jangan tampilkan siapa-siapa
                 $query->whereRaw('1 = 0');
            }
        }
        // =================================================================
        // [AKHIR PERUBAHAN]
        // =================================================================

        // 2. Logika untuk Pencarian (Ini sudah benar, tidak perlu diubah)
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('nama', 'like', "%{$searchTerm}%")
                  ->orWhere('nisn', 'like', "%{$searchTerm}%")
                  ->orWhere('nik', 'like', "%{$searchTerm}%");
            });
        }

        // 3. Logika Paginasi (Sudah benar)
        $siswas = $query->orderBy('nama', 'asc')->paginate(15); 

        // Ambil data rombel untuk filter dropdown
        // [PENTING] Pastikan Anda mengambil 'id'
        $rombels = Rombel::select('id', 'nama') // <-- Tambahkan select()
                         ->orderBy('nama', 'asc')
                         ->get()
                         ->unique('nama');

        return view('admin.kesiswaan.siswa.index', compact('siswas', 'rombels'));
    }

    public function create()
    {
        return view('admin.kesiswaan.siswa.create');
    }

    public function store(Request $request)
    {
        $request->validate(['nama_lengkap' => 'required|string|max:255']);

        $data = $request->except(['foto']);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('siswa/foto', 'public');
        }

        Siswa::create($data);

        return redirect()->route('admin.kesiswaan.siswa.index')->with('success', 'Data siswa berhasil ditambahkan.');
    }

    public function show(Siswa $siswa)
    {
        return view('admin.kesiswaan.siswa.show', compact('siswa'));
    }

    public function edit(Siswa $siswa)
    {
        return view('admin.kesiswaan.siswa.edit', compact('siswa'));
    }

    /**
     * Memperbarui data siswa di database, termasuk semua kolom dan foto.
     */

    public function update(Request $request, Siswa $siswa)
{
    // 1. Validasi hanya untuk input yang ada di database dan form
    $validatedData = $request->validate([
        'nama' => 'required|string|max:255',
        'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        'nipd' => 'nullable|string', // Menggunakan 'nipd'
        'nisn' => 'nullable|string',
        'nik' => 'nullable|string',
        'jenis_kelamin' => 'required|in:L,P',
        'tempat_lahir' => 'nullable|string',
        'tanggal_lahir' => 'nullable|date',
        'agama_id_str' => 'nullable|string',
        'nomor_telepon_seluler' => 'nullable|string', // Menggunakan 'nomor_telepon_seluler'
        'email' => 'nullable|email',
        'nama_ayah' => 'nullable|string',
        'pekerjaan_ayah_id_str' => 'nullable|string',
        'nama_ibu' => 'nullable|string', // Menggunakan 'nama_ibu'
        'pekerjaan_ibu_id_str' => 'nullable|string',
        'nama_wali' => 'nullable|string',
        'pekerjaan_wali_id_str' => 'nullable|string',
    ]);

    // 2. Tangani upload file foto
    if ($request->hasFile('foto')) {
        if ($siswa->foto) {
            Storage::disk('public')->delete($siswa->foto);
        }
        $path = $request->file('foto')->store('siswa/foto', 'public');
        $validatedData['foto'] = $path;
    }

    // 3. Update data siswa
    $siswa->update($validatedData);

    return redirect()->route('admin.kesiswaan.siswa.index')->with('success', 'Data siswa berhasil diperbarui.');
}

    public function destroy(Siswa $siswa)
    {
        if ($siswa->foto) {
            Storage::disk('public')->delete($siswa->foto);
        }
        
        $siswa->delete();

        return redirect()->route('admin.kesiswaan.siswa.index')->with('success', 'Data siswa berhasil dihapus.');
    }

    public function cetakKartu(Siswa $siswa)
    {
        // Cek jika siswa belum punya token, buatkan dulu
        if (empty($siswa->qr_token)) {
            $siswa->qr_token = Str::uuid()->toString();
            $siswa->save();
        }

        return view('admin.kesiswaan.siswa.kartu', compact('siswa'));
    }

    public function showCetakMassalIndex()
    {
        $rombels = Rombel::orderBy('nama', 'asc')->get()->unique('nama');
        return view('admin.kesiswaan.siswa.cetak_massal_index', compact('rombels'));
    }

    /**
     * Menampilkan halaman cetak yang berisi semua kartu siswa dari satu rombel.
     */
   public function cetakKartuMassal(Rombel $rombel)
    {
        // [CATATAN]
        // Method ini menggunakan 'rombongan_belajar_id' (UUID)
        // Jika method ini JUGA GAGAL, Anda harus mengubahnya
        // untuk menggunakan logika JSON 'anggota_rombel' juga.
        
        // Coba kita ubah agar konsisten dengan logika filter index
        $anggotaData = json_decode($rombel->anggota_rombel, true);
        $siswaIds = [];
        if (is_array($anggotaData) && !empty($anggotaData)) {
             $anggotaPdIds = array_column($anggotaData, 'peserta_didik_id');
             $siswaIds = Siswa::whereIn('peserta_didik_id', $anggotaPdIds)->pluck('id');
        }

        $siswas = Siswa::whereIn('id', $siswaIds)
                        ->orderBy('nama', 'asc')
                        ->get();

        // Pastikan semua siswa punya token sebelum dicetak
        foreach ($siswas as $siswa) {
            if (empty($siswa->qr_token)) {
                $siswa->qr_token = Str::uuid()->toString();
                $siswa->save();
            }
        }

        return view('admin.kesiswaan.siswa.kartu_massal', compact('siswas', 'rombel'));
    }

    public function pelanggaran()
{
    // ===============================
    // VALIDASI LOGIN SISWA
    // ===============================
    if (!session()->has('peserta_didik_id')) {
        abort(403, 'Akses ditolak');
    }

    // ===============================
    // AMBIL DATA SISWA LOGIN
    // ===============================
    $siswa = Siswa::where('peserta_didik_id', session('peserta_didik_id'))
                  ->with('rombel')
                  ->firstOrFail();

    // ===============================
    // AMBIL DATA PELANGGARAN SISWA
    // ===============================
    $pelanggaranSiswa = PelanggaranNilai::where('nipd', $siswa->nipd)
        ->with('detailPoinSiswa')
        ->orderBy('tanggal', 'desc')
        ->get();

    // ===============================
    // HITUNG TOTAL POIN
    // ===============================
    $totalPoin = $pelanggaranSiswa->sum('poin');

    // ===============================
    // AMBIL SANKSI AKTIF
    // ===============================
    $sanksiAktif = PelanggaranSanksi::where('poin_min', '<=', $totalPoin)
        ->where('poin_max', '>=', $totalPoin)
        ->first();

    // ===============================
    // KIRIM KE VIEW
    // ===============================
    return view('admin.personal.siswa.pelanggaran', [
        'siswa'             => $siswa,
        'pelanggaranSiswa'  => $pelanggaranSiswa,
        'totalPoin'         => $totalPoin,
        'sanksiAktif'       => $sanksiAktif,

        // dummy biar blade lama gak error
        'tingkatList' => collect(),
        'rombelList'  => collect(),
        'siswaList'   => collect(),
    ]);
}

}

