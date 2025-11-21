<?php

namespace App\Http\Controllers\Admin\Ppdb;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CalonSiswa;
use App\Models\JalurPendaftaran;
use App\Models\SyaratPendaftaran;
use App\Models\TahunPelajaran;
use App\Models\KompetensiPendaftaran;
use App\Models\TingkatPendaftaran;

class FormulirPendaftaranController extends Controller
{
    public function index()
    {
        $tahunAktif = TahunPelajaran::where('is_active', 1)->first();
        $tingkatAktif = TingkatPendaftaran::where('is_active', 1)->first();

        $formulirs = CalonSiswa::with(['jalurPendaftaran', 'syarat'])->get();
        $jurusans = KompetensiPendaftaran::all();
        $jalurs = $tahunAktif
            ? JalurPendaftaran::where('tahunPelajaran_id', $tahunAktif->id)
                ->where('is_active', true)->get()
            : collect();
        $syarats = $tahunAktif
            ? SyaratPendaftaran::where('tahunPelajaran_id', $tahunAktif->id)
                ->where('is_active', true)->get()
            : collect();

        // tentukan kelas asal sesuai tingkat
        $kelasAsal = [];
        switch($tingkatAktif->tingkat ?? null) {
            case 10:
                for($i=65; $i<=75; $i++) $kelasAsal[] = "IX ".chr($i); 
                break;
            case 7:
                for($i=65; $i<=75; $i++) $kelasAsal[] = "VI ".chr($i); 
                break;
            case 1:
                // I A - I K
                for ($i = 65; $i <= 75; $i++) {
                    $kelasAsal[] = chr($i);
                }
                break;
        }

        return view('admin.ppdb.formulir_pendaftaran', compact(
            'formulirs', 'jurusans', 'jalurs', 'tahunAktif', 'syarats', 'tingkatAktif', 'kelasAsal'
        ));
    }

    public function store(Request $request)
    {
        $tingkatAktif = TingkatPendaftaran::where('is_active', 1)->first();

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

        // isi otomatis kolom tingkat
        $validated['tingkat'] = $tingkatAktif->tingkat ?? null;

        // generate nomor resi otomatis
        $prefix = "137";
        $tanggal = now()->format('ymd');
        $last = CalonSiswa::whereDate('created_at', now()->toDateString())
            ->orderByDesc('id')->first();
        $urutan = $last ? str_pad((int)substr($last->nomor_resi, -3)+1, 3, '0', STR_PAD_LEFT) : "001";
        $validated['nomor_resi'] = "{$prefix}-{$tanggal}.{$urutan}";

        $calon = CalonSiswa::create($validated);

        // simpan syarat
        $syaratIds = $request->syarat_id ?? [];
        $syncData = [];
        foreach ($syaratIds as $id) {
            $syncData[$id] = ['is_checked' => true];
        }
        $calon->syarat()->sync($syncData);

        // cek syarat wajib
        $syaratWajib = SyaratPendaftaran::where('tahunPelajaran_id', $validated['tahun_id'])
            ->where('jalurPendaftaran_id', $validated['jalur_id'])
            ->where('is_active', true)->count();

        $syaratTerpenuhi = $calon->syarat()->wherePivot('is_checked', true)->count();
        $calon->status = $syaratTerpenuhi >= $syaratWajib ? 1 : 0;
        $calon->save();

        return redirect()->back()->with('success', 'Formulir calon peserta didik berhasil disimpan.');
    }

    public function edit($id)
    {
        $formulir = CalonSiswa::with('syarat')->findOrFail($id);
        $tahunAktif = TahunPelajaran::where('is_active', 1)->first();
        $tingkatAktif = TingkatPendaftaran::where('is_active', 1)->first();
        $jurusans = KompetensiPendaftaran::all();
        $jalurs = $tahunAktif
            ? JalurPendaftaran::where('tahunPelajaran_id', $tahunAktif->id)
                ->where('is_active', true)->get()
            : collect();
        $syarats = $tahunAktif
            ? SyaratPendaftaran::where('tahunPelajaran_id', $tahunAktif->id)
                ->where('is_active', true)->get()
            : collect();

        
        // tentukan kelas asal sesuai tingkat
        $kelasAsal = [];
        switch($tingkatAktif->tingkat ?? null) {
            case 10:
                for($i=65; $i<=75; $i++) $kelasAsal[] = "IX ".chr($i); 
                break;
            case 7:
                for($i=65; $i<=75; $i++) $kelasAsal[] = "VI ".chr($i); 
                break;
            case 1:
                $kelasAsal[] = "-";
                break;
        }

        return view('admin.ppdb.formulir_pendaftaran', compact(
            'formulir', 'jurusans', 'jalurs', 'tahunAktif', 'syarats', 'tingkatAktif', 'kelasAsal'
        ));
    }

    public function update(Request $request, $id)
    {
        $tingkatAktif = TingkatPendaftaran::where('is_active', 1)->first();
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

        // update tingkat
        $validated['tingkat'] = $tingkatAktif->tingkat ?? null;

        $calon->update($validated);

        // update syarat
        $calon->syarat()->sync(
            collect($request->syarat_id ?? [])->mapWithKeys(fn($id) => [$id => ['is_checked'=>true]])->toArray()
        );

        // cek syarat
        $syaratWajib = SyaratPendaftaran::where('tahunPelajaran_id', $validated['tahun_id'])
            ->where('jalurPendaftaran_id', $validated['jalur_id'])
            ->where('is_active', true)->count();

        $syaratTerpenuhi = $calon->syarat()->wherePivot('is_checked', true)->count();
        $calon->status = $syaratTerpenuhi >= $syaratWajib ? 1 : 0;
        $calon->save();

        return redirect()->route('admin.ppdb.daftar-calon-peserta-didik.index')
            ->with('success', 'Data Calon Peserta didik berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $calon = CalonSiswa::findOrFail($id);
        $calon->delete();

        return redirect()->route('admin.ppdb.formulir.index')
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
