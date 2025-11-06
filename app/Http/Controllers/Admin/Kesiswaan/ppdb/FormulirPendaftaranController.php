<?php

namespace App\Http\Controllers\Admin\Kesiswaan\Ppdb;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CalonSiswa;
use App\Models\JalurPendaftaran;
use App\Models\SyaratPendaftaran;
use App\Models\TahunPelajaran;
use App\Models\Rombel;

class FormulirPendaftaranController extends Controller
{


    public function index()
    {
        $tahunAktif = TahunPelajaran::where('is_active', 1)->first();

        $formulirs = CalonSiswa::with(['jalurPendaftaran', 'syarat'])->get();

        $jurusans = Rombel::select('jurusan_id_str')
            ->distinct()
            ->orderBy('jurusan_id_str')
            ->pluck('jurusan_id_str');

        $jalurs = $tahunAktif
            ? JalurPendaftaran::where('tahunPelajaran_id', $tahunAktif->id)
                ->where('is_active', true)
                ->get()
            : collect();

        $syarats = $tahunAktif
            ? SyaratPendaftaran::where('tahunPelajaran_id', $tahunAktif->id)
                ->where('is_active', true)
                ->get()
            : collect();

        return view('admin.kesiswaan.ppdb.formulir_pendaftaran', compact(
            'formulirs',
            'jurusans',
            'jalurs',
            'tahunAktif',
            'syarats'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tahun_id'      => 'required|exists:tahun_pelajarans,id',
            'jalur_id'      => 'required|exists:jalur_pendaftarans,id',
            'nama_lengkap'  => 'required|string|max:255',
            'nisn'          => 'nullable|string|max:20',
            'npun'          => 'nullable|string|max:20',
            'jenis_kelamin' => 'nullable|in:L,P',
            'tempat_lahir'  => 'nullable|string|max:100',
            'tgl_lahir'     => 'nullable|date',
            'nama_ayah'     => 'nullable|string|max:255',
            'nama_ibu'      => 'nullable|string|max:255',
            'alamat'        => 'nullable|string|max:255',
            'desa'          => 'nullable|string|max:100',
            'kecamatan'     => 'nullable|string|max:100',
            'kabupaten'     => 'nullable|string|max:100',
            'provinsi'      => 'nullable|string|max:100',
            'kode_pos'      => 'nullable|string|max:10',
            'kontak'        => 'nullable|string|max:20',
            'asal_sekolah'  => 'nullable|string|max:255',
            'kelas'         => 'nullable|string|max:20',
            'jurusan'       => 'nullable|string|max:50',
            'ukuran_pakaian'=> 'nullable|string|max:20',
            'pembayaran'    => 'nullable|numeric',
        ]);

        // generate nomor resi
        $prefix = "137";
        $tanggal = now()->format('ymd');

        $last = CalonSiswa::whereDate('created_at', now()->toDateString())
            ->orderByDesc('id')
            ->first();

        if ($last) {
            $lastNumber = (int) substr($last->nomor_resi, -3);
            $urutan = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $urutan = "001";
        }

        $nomerSeri = "{$prefix}-{$tanggal}.{$urutan}";
        $validated['nomor_resi'] = $nomerSeri;

        $calon = CalonSiswa::create($validated);

        // simpan syarat
        $syaratIds = $request->syarat_id ?? [];
        $syncData = [];
        foreach ($syaratIds as $id) {
            $syncData[$id] = ['is_checked' => true];
        }
        $calon->syarat()->sync($syncData);

        // cek syarat wajib
        $tahunId = $validated['tahun_id'];
        $jalurId = $validated['jalur_id'];

        $syaratWajib = SyaratPendaftaran::where('tahunPelajaran_id', $tahunId)
            ->where('jalurPendaftaran_id', $jalurId)
            ->where('is_active', true)
            ->count();

        $syaratTerpenuhi = $calon->syarat()
            ->wherePivot('is_checked', true)
            ->count();

        if ($syaratTerpenuhi >= $syaratWajib) {
            // $calon->nis = $this->generateNis($tahunId, $jalurId);
            $calon->status = 1;
        } else {
            $calon->status = 0;
        }
        
        $calon->save();

        return redirect()->back()->with('success', 'Formulir calon peserta didik berhasil disimpan.');
    }

    public function edit($id)
    {
        $formulir = CalonSiswa::with('syarat')->findOrFail($id);
        $tahunAktif = TahunPelajaran::where('is_active', 1)->first();

        $jurusans = Rombel::select('jurusan_id_str')
            ->distinct()
            ->orderBy('jurusan_id_str')
            ->pluck('jurusan_id_str');

        $jalurs = $tahunAktif
            ? JalurPendaftaran::where('tahunPelajaran_id', $tahunAktif->id)
                ->where('is_active', true)
                ->get()
            : collect();

        $syarats = $tahunAktif
            ? SyaratPendaftaran::where('tahunPelajaran_id', $tahunAktif->id)
                ->where('is_active', true)
                ->get()
            : collect();

        return view('admin.kesiswaan.ppdb.formulir_pendaftaran', [
            'formulir'   => $formulir,
            'jurusans'   => $jurusans,
            'jalurs'     => $jalurs,
            'tahunAktif' => $tahunAktif,
            'syarats'    => $syarats,
        ]);
    }

    public function update(Request $request, $id)
    {
        $calon = CalonSiswa::findOrFail($id);

        $validated = $request->validate([
            'tahun_id'      => 'required|exists:tahun_pelajarans,id',
            'jalur_id'      => 'required|exists:jalur_pendaftarans,id',
            'nama_lengkap'  => 'required|string|max:255',
            'nisn'          => 'nullable|string|max:20',
            'npun'          => 'nullable|string|max:20',
            'jenis_kelamin' => 'nullable|in:L,P',
            'tempat_lahir'  => 'nullable|string|max:100',
            'tgl_lahir'     => 'nullable|date',
            'nama_ayah'     => 'nullable|string|max:255',
            'nama_ibu'      => 'nullable|string|max:255',
            'alamat'        => 'nullable|string|max:255',
            'desa'          => 'nullable|string|max:100',
            'kecamatan'     => 'nullable|string|max:100',
            'kabupaten'     => 'nullable|string|max:100',
            'provinsi'      => 'nullable|string|max:100',
            'kode_pos'      => 'nullable|string|max:10',
            'kontak'        => 'nullable|string|max:20',
            'asal_sekolah'  => 'nullable|string|max:255',
            'kelas'         => 'nullable|string|max:20',
            'jurusan'       => 'nullable|string|max:50',
            'ukuran_pakaian'=> 'nullable|string|max:20',
            'pembayaran'    => 'nullable|numeric',
        ]);

        $calon->update($validated);

        // update syarat
        $calon->syarat()->sync(
            collect($request->syarat_id ?? [])->mapWithKeys(fn($id) => [$id => ['is_checked' => true]])->toArray()
        );

        // cek syarat
        $tahunId = $validated['tahun_id'];
        $jalurId = $validated['jalur_id'];
        
        $syaratWajib = SyaratPendaftaran::where('tahunPelajaran_id', $tahunId)
            ->where('jalurPendaftaran_id', $jalurId)
            ->where('is_active', true)->count();

        $syaratTerpenuhi = $calon->syarat()->wherePivot('is_checked', true)->count();

        if ($syaratTerpenuhi >= $syaratWajib) {
            // $calon->nis = $this->generateNis($tahunId, $validated['jalur_id']);
            $calon->status = 1;
        } else {
            $calon->status= 0;
        }
        $calon->save();


        return redirect()->route('admin.kesiswaan.ppdb.daftar-calon-peserta-didik.index')
            ->with('success', 'Data Calon Peserta didik berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $calon = CalonSiswa::findOrFail($id);
        $calon->delete();

        return redirect()->route('admin.kesiswaan.ppdb.formulir.index')
            ->with('success', 'Formulir pendaftaran berhasil dihapus.');
    }

    public function updateStatus(Request $request, $id)
    {
        $calon = CalonSiswa::findOrFail($id);
        $calon->status = $request->status;
        $calon->save();

        return back()->with('success', 'Status berhasil diperbarui!');
    }

}
