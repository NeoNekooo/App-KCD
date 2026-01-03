<?php

namespace App\Http\Controllers\Admin\Kesiswaan;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Rombel;
use App\Models\Sekolah;
use App\Models\PelanggaranNilai;
use App\Models\PelanggaranSanksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SiswaExport;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class SiswaController extends Controller
{
    /**
     * Menampilkan Daftar Siswa (Tabel)
     * UPDATE: Filter Rombel diganti Filter Daerah (Kab/Kota & Kecamatan)
     */
    public function index(Request $request)
    {
        $query = Siswa::with('rombel')->where('status', 'Aktif');

        // --- 1. FILTER WILAYAH (Baru) ---
        
        // Filter Kabupaten/Kota
        if ($request->filled('kabupaten_kota')) {
            $query->where('kabupaten_kota', $request->kabupaten_kota);
        }

        // Filter Kecamatan
        if ($request->filled('kecamatan')) {
            $query->where('kecamatan', $request->kecamatan);
        }

        // --- 2. PENCARIAN ---
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('nama', 'like', "%{$searchTerm}%")
                  ->orWhere('nisn', 'like', "%{$searchTerm}%")
                  ->orWhere('nik', 'like', "%{$searchTerm}%");
            });
        }

        // --- 3. PAGINASI ---
        $perPage = $request->input('per_page', 15);
        $siswas = ($perPage == 'all')
            ? $query->orderBy('nama', 'asc')->paginate(9999)
            : $query->orderBy('nama', 'asc')->paginate($perPage);

        // --- 4. DATA UNTUK DROPDOWN FILTER (Mengambil data unik dari DB) ---
        $listKabupaten = Siswa::where('status', 'Aktif')
            ->whereNotNull('kabupaten_kota')
            ->where('kabupaten_kota', '!=', '')
            ->distinct()
            ->pluck('kabupaten_kota');

        $listKecamatan = Siswa::where('status', 'Aktif')
            ->whereNotNull('kecamatan')
            ->where('kecamatan', '!=', '')
            ->distinct()
            ->pluck('kecamatan');

        // --- 5. MAPPING KELAS MANUAL (Agar Badge Kelas di Tabel tetap muncul) ---
        // Kita tetap butuh data rombel untuk sekadar menampilkan nama kelas siswa
        $rombels = Rombel::select('id', 'nama', 'anggota_rombel')->get();

        $siswaRombelMap = [];
        foreach($rombels as $r) {
            $members = is_array($r->anggota_rombel) ? $r->anggota_rombel : json_decode($r->anggota_rombel, true);
            if(is_array($members)) {
                foreach($members as $m) {
                    if(isset($m['peserta_didik_id'])) {
                        $siswaRombelMap[$m['peserta_didik_id']] = $r->nama;
                    }
                }
            }
        }

        // Kirim variabel baru ke view
        return view('admin.kesiswaan.siswa.index', compact('siswas', 'siswaRombelMap', 'listKabupaten', 'listKecamatan'));
    }

    public function create()
    {
        return view('admin.kesiswaan.siswa.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'no_wa' => 'nullable|string|max:32',
        ]);
        $data = $request->except(['foto']);

        if ($request->hasFile('foto')) {
            $request->validate(['foto' => 'image|mimes:jpeg,png,jpg|max:5120']); // 5MB

            $file = $request->file('foto');
            $fileName = time() . '_' . Str::slug($request->nama_lengkap) . '.jpg';
            $path = 'siswa/foto/' . $fileName;

            // Compress using Intervention Image and save as jpg with quality 70
            $manager = new ImageManager(new Driver());
            $image = $manager->read($file);
            $image->scale(width: 400);
            $encoded = $image->toJpeg(70);
            Storage::disk('public')->put($path, (string) $encoded);

            $data['foto'] = $path;
        }

        Siswa::create($data);
        return redirect()->route('admin.kesiswaan.siswa.index')->with('success', 'Data siswa berhasil ditambahkan.');
    }

    /**
     * SHOW SINGLE
     */
    public function show($id)
    {
        $siswas = Siswa::with('rombel')->where('id', $id)->get();
        return view('admin.kesiswaan.siswa.show', compact('siswas'));
    }

    /**
     * SHOW MULTIPLE
     */
    public function showMultiple(Request $request)
    {
        $idsStr = $request->query('ids', '');
        if (empty($idsStr)) {
            return redirect()->route('admin.kesiswaan.siswa.index');
        }

        $idsArray = explode(',', $idsStr);

        $siswas = Siswa::with('rombel')
                        ->whereIn('id', $idsArray)
                        ->orderBy('nama', 'asc')
                        ->get();

        return view('admin.kesiswaan.siswa.show', compact('siswas'));
    }

    /**
     * Halaman Buku Induk
     */
    public function bukuIndukIndex(Request $request)
    {
        $rombels = Rombel::select('id','nama','anggota_rombel')
                         ->orderBy('nama', 'asc')
                         ->get()
                         ->unique('nama');

        $siswas = collect();
        $selectedRombel = null;

        if ($request->filled('rombel_id')) {
            $rombel = Rombel::find($request->rombel_id);
            if ($rombel) {
                $anggotaData = is_array($rombel->anggota_rombel)
                    ? $rombel->anggota_rombel
                    : json_decode($rombel->anggota_rombel, true);

                $pesertaIds = !empty($anggotaData) ? array_column($anggotaData, 'peserta_didik_id') : [];

                $siswas = Siswa::whereIn('peserta_didik_id', $pesertaIds)
                                ->orderBy('nama', 'asc')
                                ->get();
                $selectedRombel = $rombel;
            }
        }

        return view('admin.kesiswaan.siswa.buku_induk_index', compact('rombels','siswas','selectedRombel'));
    }

    public function bukuIndukRombel(Rombel $rombel)
    {
        $anggotaData = is_array($rombel->anggota_rombel)
            ? $rombel->anggota_rombel
            : json_decode($rombel->anggota_rombel, true);

        $pesertaIds = !empty($anggotaData) ? array_column($anggotaData, 'peserta_didik_id') : [];

        $siswas = Siswa::whereIn('peserta_didik_id', $pesertaIds)
                        ->orderBy('nama', 'asc')
                        ->get();

        $rombels = Rombel::select('id','nama','anggota_rombel')
                         ->orderBy('nama', 'asc')
                         ->get()
                         ->unique('nama');

        return view('admin.kesiswaan.siswa.buku_induk_index', compact('rombels','siswas','rombel'));
    }

    public function cetakBukuInduk($id)
    {
        return $this->cetakPdf($id);
    }

    public function edit($id)
    {
        return $this->show($id);
    }

    public function update(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);

        $dataToUpdate = $request->only([
            'alamat_jalan', 'rt', 'rw', 'dusun', 'desa_kelurahan',
            'kecamatan', 'kabupaten_kota', 'provinsi', 'kode_pos',
            'lintang', 'bujur', 'jenis_tinggal_id_str',
            'no_kks', 'penerima_kps', 'no_kps', 'layak_pip', 'alasan_layak_pip',
            'penerima_kip', 'no_kip', 'nama_di_kip', 'alasan_menolak_kip',
            'tahun_lahir_ayah', 'pendidikan_ayah_id_str', 'penghasilan_ayah_id_str', 'kebutuhan_khusus_ayah',
            'tahun_lahir_ibu', 'pendidikan_ibu_id_str', 'penghasilan_ibu_id_str', 'kebutuhan_khusus_ibu',
            'tahun_lahir_wali', 'pendidikan_wali_id_str', 'penghasilan_wali_id_str',
            'nama_wali', 'pekerjaan_wali_id_str',
            'alat_transportasi_id_str', 'jarak_rumah_ke_sekolah_km', 'waktu_tempuh_menit',
            'jumlah_saudara_kandung', 'tinggi_badan', 'berat_badan',
            'npsn_sekolah_asal', 'no_seri_ijazah', 'no_seri_skhun', 'no_ujian_nasional', 'no_registrasi_akta_lahir',
            'sekolah_asal', 'anak_keberapa',
            'email', 'nomor_telepon_seluler', 'agama_id_str', 'kewarganegaraan'
        ]);

        if ($request->hasFile('foto')) {
            $request->validate(['foto' => 'image|mimes:jpeg,png,jpg|max:5120']);

            if ($siswa->foto && Storage::disk('public')->exists($siswa->foto)) {
                Storage::disk('public')->delete($siswa->foto);
            }

            $file = $request->file('foto');
            $fileName = time() . '_' . Str::slug($siswa->nama) . '.jpg';
            $path = 'siswa/foto/' . $fileName;

            $manager = new ImageManager(new Driver());
            $image = $manager->read($file);
            $image->scale(width: 400);
            $encoded = $image->toJpeg(70);
            Storage::disk('public')->put($path, (string) $encoded);

            $dataToUpdate['foto'] = $path;
        }

        $siswa->update($dataToUpdate);

        if (session()->has('peserta_didik_id')) {
            return redirect()
                ->route('admin.personal.siswa.profil')
                ->with('success', 'Profil berhasil diperbarui.');
        }
        
        if ($request->has('_ids_multiple')) {
            return redirect()->route('admin.kesiswaan.siswa.show-multiple', [
                'ids' => $request->_ids_multiple,
                'current_id' => $siswa->id
            ])->with('success', 'Data berhasil disimpan.');
        }

        return redirect()->route('admin.kesiswaan.siswa.show', $siswa->id)
                         ->with('success', 'Data tambahan siswa berhasil diperbarui.');

    }

    public function destroy(Siswa $siswa)
    {
        if ($siswa->foto) {
            Storage::disk('public')->delete($siswa->foto);
        }
        $siswa->delete();
        return redirect()->route('admin.kesiswaan.siswa.index')->with('success', 'Data siswa berhasil dihapus.');
    }

    public function cetakKartu($id)
    {
        $siswa = Siswa::findOrFail($id);
        if (empty($siswa->qr_token)) {
            $siswa->qr_token = Str::uuid()->toString();
            $siswa->save();
        }
        return view('admin.kesiswaan.siswa.kartu', compact('siswa'));
    }

    public function showCetakMassalIndex()
    {
        $rombels = Rombel::orderBy('nama', 'asc')->get()->unique('nama');
        $sekolah = Sekolah::first();
        return view('admin.kesiswaan.siswa.cetak_massal_index', compact('rombels', 'sekolah'));
    }

   public function cetakKartuMassal(Rombel $rombel)
    {
        $anggotaData = is_array($rombel->anggota_rombel)
            ? $rombel->anggota_rombel
            : json_decode($rombel->anggota_rombel, true);

        $siswaIds = [];
        if (!empty($anggotaData)) {
             $anggotaPdIds = array_column($anggotaData, 'peserta_didik_id');
             $siswaIds = Siswa::whereIn('peserta_didik_id', $anggotaPdIds)->pluck('id');
        }

        $siswas = Siswa::whereIn('id', $siswaIds)->orderBy('nama', 'asc')->get();
        $sekolah = Sekolah::first();

        foreach ($siswas as $siswa) {
            if (empty($siswa->qr_token)) {
                $siswa->qr_token = Str::uuid()->toString();
                $siswa->save();
            }
        }

        return view('admin.kesiswaan.siswa.kartu_massal', compact('siswas', 'rombel', 'sekolah'));
    }

    public function uploadMedia(Request $request, $id)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:5120'
        ]);

        $siswa = Siswa::findOrFail($id);

        if ($siswa->foto && Storage::disk('public')->exists($siswa->foto)) {
            Storage::disk('public')->delete($siswa->foto);
        }

        $file = $request->file('foto');
        $fileName = time() . '_' . Str::slug($siswa->nama) . '.jpg';
        $path = 'siswa/foto/' . $fileName;

        $manager = new ImageManager(new Driver());
        $image = $manager->read($file);
        $image->scale(width: 400);
        $encoded = $image->toJpeg(70);
        Storage::disk('public')->put($path, (string) $encoded);

        $siswa->foto = $path;
        $siswa->save();

        return back()->with('success', 'Foto ' . $siswa->nama . ' berhasil dikompres dan disimpan!');
    }

    public function uploadBackgroundKartu(Request $request)
    {
        $request->validate([
            'background_kartu' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $sekolah = Sekolah::first();
        if (!$sekolah) {
            $sekolah = new Sekolah();
        }

        if ($request->hasFile('background_kartu')) {
            if ($sekolah->background_kartu && Storage::disk('public')->exists($sekolah->background_kartu)) {
                Storage::disk('public')->delete($sekolah->background_kartu);
            }

            $path = $request->file('background_kartu')->store('sekolah_media/background', 'public');
            $sekolah->background_kartu = $path;
            $sekolah->save();
        }

        return back()->with('success', 'Background kartu siswa berhasil diupdate!');
    }

    public function cetakPdf($id)
    {
        $siswa = Siswa::with(['rombel', 'mutasiKeluar'])->findOrFail($id);
        $sekolah = Sekolah::first();
        $siswas = collect([$siswa]);

        $pdf = Pdf::loadView('admin.kesiswaan.siswa.pdf_biodata', compact('siswas', 'sekolah'));
        $pdf->setPaper('a4', 'portrait');
        return $pdf->stream('Biodata_'.$siswa->nama.'.pdf');
    }

    public function cetakPdfMultiple(Request $request)
    {
        $idsStr = $request->input('ids');

        if (empty($idsStr)) {
            return redirect()->back()->with('error', 'Gagal mencetak: Tidak ada data siswa yang dipilih.');
        }

        $idsArray = explode(',', $idsStr);
        $sekolah = Sekolah::first();

        $siswas = Siswa::with('rombel')
                        ->whereIn('id', $idsArray)
                        ->orderBy('nama', 'asc')
                        ->get();

        if ($siswas->isEmpty()) {
            return redirect()->back()->with('error', 'Data siswa tidak ditemukan di database.');
        }

        $pdf = Pdf::loadView('admin.kesiswaan.siswa.pdf_biodata', compact('siswas', 'sekolah'));
        $pdf->setPaper('a4', 'portrait');

        $filename = 'Kumpulan_Biodata_Siswa_'.date('d-m-Y_His').'.pdf';
        return $pdf->stream($filename);
    }

    public function exportExcel(Request $request)
    {
        $ids = null;
        if ($request->has('ids') && !empty($request->query('ids'))) {
            $ids = explode(',', $request->query('ids'));
        }

        $sekolah = Sekolah::first();
        if ($sekolah) {
            $schoolName = Str::slug($sekolah->nama, '_');
        } else {
            $schoolName = 'DATA_SISWA';
        }

        $fileName = strtoupper($schoolName) . '_DATA_SISWA_' . date('d-m-Y') . '.xlsx';
        return Excel::download(new SiswaExport($ids), $fileName);
    }

    public function profil()
    {
        if (!auth()->check() || !session()->has('peserta_didik_id')) {
            abort(403);
        }

        $siswa = Siswa::where(
            'peserta_didik_id',
            session('peserta_didik_id')
        )->firstOrFail();

        $siswas = collect([$siswa]);

        return view('admin.personal.siswa.profil', compact('siswa'));
    }

    public function pelanggaran()
    {
        if (!session()->has('peserta_didik_id')) {
            abort(403, 'Akses ditolak');
        }

        $siswa = Siswa::where('peserta_didik_id', session('peserta_didik_id'))
            ->with('rombel')
            ->firstOrFail();

        $pelanggaranSiswa = PelanggaranNilai::where('nipd', $siswa->nipd)
            ->with('detailPoinSiswa')
            ->orderBy('tanggal', 'desc')
            ->get();

        $totalPoin = $pelanggaranSiswa->sum('poin');

        $sanksiAktif = PelanggaranSanksi::where('poin_min', '<=', $totalPoin)
            ->where('poin_max', '>=', $totalPoin)
            ->first();

        return view('admin.personal.siswa.pelanggaran', [
            'siswa'             => $siswa,
            'pelanggaranSiswa'  => $pelanggaranSiswa,
            'totalPoin'         => $totalPoin,
            'sanksiAktif'       => $sanksiAktif,
            'tingkatList' => collect(),
            'rombelList'  => collect(),
            'siswaList'   => collect(),
        ]);
    }

    public function inactive(Request $request)
    {
        $query = Siswa::with('rombel')->where('status', '!=', 'Aktif');

        if ($request->filled('rombel_id')) {
            $rombel = Rombel::find($request->rombel_id);
            if ($rombel) {
                $anggotaData = is_array($rombel->anggota_rombel)
                    ? $rombel->anggota_rombel
                    : json_decode($rombel->anggota_rombel, true);

                if (!empty($anggotaData)) {
                    $anggotaPdIds = array_column($anggotaData, 'peserta_didik_id');
                    $query->whereIn('peserta_didik_id', $anggotaPdIds);
                } else {
                    $query->whereRaw('1 = 0');
                }
            }
        }

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('nama', 'like', "%{$searchTerm}%")
                  ->orWhere('nisn', 'like', "%{$searchTerm}%")
                  ->orWhere('nik', 'like', "%{$searchTerm}%");
            });
        }

        $perPage = $request->input('per_page', 15);
        $siswas = ($perPage == 'all')
            ? $query->orderBy('nama', 'asc')->paginate(9999)
            : $query->orderBy('nama', 'asc')->paginate($perPage);

        $rombels = Rombel::select('id', 'nama', 'anggota_rombel')
                         ->orderBy('nama', 'asc')
                         ->get()
                         ->unique('nama');

        $siswaRombelMap = [];
        foreach($rombels as $r) {
            $members = is_array($r->anggota_rombel) ? $r->anggota_rombel : json_decode($r->anggota_rombel, true);
            if(is_array($members)) {
                foreach($members as $m) {
                    if(isset($m['peserta_didik_id'])) {
                        $siswaRombelMap[$m['peserta_didik_id']] = $r->nama;
                    }
                }
            }
        }

        return view('admin.kesiswaan.siswa.inactive', compact('siswas', 'rombels', 'siswaRombelMap'));
    }

    public function registerKeluar(Request $request, $id)
    {
        $request->validate([
            'status' => 'required',
            'tanggal_keluar' => 'required|date',
            'alasan' => 'nullable|string',
        ]);

        $siswa = Siswa::findOrFail($id);

        $siswa->mutasiKeluar()->create([
            'status' => $request->status,
            'tanggal_keluar' => $request->tanggal_keluar,
            'keterangan' => $request->alasan,
        ]);

        $siswa->status = $request->status;
        $siswa->save();

        return redirect()->route('admin.kesiswaan.siswa.index')
                         ->with('success', 'Status keluar siswa ' . $siswa->nama . ' berhasil diperbarui.');
    }

    public function unregisterKeluar(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);

        $mutasi = $siswa->mutasiKeluar;
        if (!$mutasi) {
            return redirect()->route('admin.kesiswaan.siswa.index')
                             ->with('error', 'Tidak ditemukan catatan keluar yang bisa dibatalkan.');
        }

        try {
            $mutasi->delete();
            $siswa->status = 'Aktif';

            if (isset($siswa->tanggal_keluar)) {
                $siswa->tanggal_keluar = null;
            }
            $siswa->save();

            return redirect()->route('admin.kesiswaan.siswa.index')
                             ->with('success', 'Pencatatan keluar siswa berhasil dibatalkan.');
        } catch (\Exception $e) {
            return redirect()->route('admin.kesiswaan.siswa.index')
                             ->with('error', 'Gagal membatalkan: ' . $e->getMessage());
        }
    }
}