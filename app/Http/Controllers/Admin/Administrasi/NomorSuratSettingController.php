<?php

namespace App\Http\Controllers\Admin\Administrasi;

use App\Http\Controllers\Controller;
use App\Models\NomorSuratSetting;
use App\Models\SuratLog;
use Illuminate\Http\Request;

class NomorSuratSettingController extends Controller
{
    // === 1. TAMPILAN ADMIN ===
    public function index()
    {
        $settings = NomorSuratSetting::all();
        
        // Limit log 100 biar tidak berat
        $logs = SuratLog::latest()->limit(100)->get();

        // Generate preview di tabel admin
        foreach($settings as $s) {
            $s->preview = $this->previewFormat($s);
        }

        return view('admin.administrasi.pengaturan-nomor.index', compact('settings', 'logs'));
    }

    // === 2. SIMPAN SETTING BARU ===
    public function store(Request $request)
    {
        $request->validate(['kategori' => 'unique:nomor_surat_settings,kategori']);

        NomorSuratSetting::create([
            'kategori' => $request->kategori,
            'judul_kop' => $request->judul_kop,
            'format_surat' => $request->format_surat,
            'nomor_terakhir' => $request->nomor_terakhir ?? 0,
        ]);
        return back()->with('success', 'Format tersimpan!');
    }

    // === 3. UPDATE ===
    public function update(Request $request, $id)
    {
        NomorSuratSetting::findOrFail($id)->update([
            'judul_kop' => $request->judul_kop,
            'format_surat' => $request->format_surat,
            'nomor_terakhir' => $request->nomor_terakhir,
        ]);
        return back()->with('success', 'Update berhasil!');
    }

    // === 4. RESET COUNTER ===
    public function resetCounter($id)
    {
        NomorSuratSetting::findOrFail($id)->update(['nomor_terakhir' => 0]);
        return back()->with('success', 'Counter reset ke 0!');
    }

    // === 5. HAPUS FORMAT ===
    public function destroy($id)
    {
        NomorSuratSetting::destroy($id);
        return back()->with('success', 'Format nomor berhasil dihapus!');
    }

    // === 6. MAGIC FUNCTION: GENERATE RESMI (Simpan DB) ===
    public static function generateNomor($kategori, $logInfo, $isiSurat)
    {
        // Cek placeholder {{no_surat}}
        if (strpos($isiSurat, '{{no_surat}}') === false) {
            return ['status' => 'skip', 'hasil' => $isiSurat];
        }

        $setting = NomorSuratSetting::where('kategori', $kategori)->first();
        if(!$setting) {
            return ['status' => 'error', 'pesan' => 'Format nomor untuk kategori ini belum diatur!'];
        }

        // Generate Nomor Baru
        $newCounter = $setting->nomor_terakhir + 1;
        $noUrut = str_pad($newCounter, 3, '0', STR_PAD_LEFT);
        
        $romawi = ['', 'I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'];
        
        $finalNumber = str_replace(
            ['{no}', '{bulan}', '{tahun}', '{romawi}'],
            [$noUrut, date('m'), date('Y'), $romawi[date('n')]],
            $setting->format_surat
        );

        // Simpan Perubahan Counter & Log
        $setting->update(['nomor_terakhir' => $newCounter]);
        
        SuratLog::create([
            'kategori' => $kategori,
            'nomor_surat_final' => $finalNumber,
            'nomor_urut' => $noUrut,
            'tujuan' => $logInfo,
            'tanggal_dibuat' => now(),
        ]);

        // Replace text
        $isiFinal = str_replace('{{no_surat}}', $finalNumber, $isiSurat);

        return ['status' => 'success', 'hasil' => $isiFinal];
    }

    // === 7. MAGIC FUNCTION: PREVIEW ONLY (Tanpa Simpan DB) ===
    // INI YANG DIPANGGIL TOMBOL MATA (PREVIEW)
    public static function getPreviewNomor($kategori)
    {
        $setting = NomorSuratSetting::where('kategori', $kategori)->first();
        
        // Kalau setting belum ada, kasih placeholder default
        if(!$setting) return '[Format Belum Diatur]';

        // Hitung estimasi nomor selanjutnya
        $next = $setting->nomor_terakhir + 1;
        $no = str_pad($next, 3, '0', STR_PAD_LEFT);
        $romawi = ['', 'I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'];
        
        return str_replace(
            ['{no}', '{bulan}', '{tahun}', '{romawi}'],
            [$no, date('m'), date('Y'), $romawi[date('n')]],
            $setting->format_surat
        );
    }

    // === 8. HELPER PRIVATE UNTUK TABEL ADMIN ===
    private function previewFormat($setting) {
        $next = $setting->nomor_terakhir + 1;
        $no = str_pad($next, 3, '0', STR_PAD_LEFT);
        $romawi = ['', 'I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'];
        
        return str_replace(
            ['{no}', '{bulan}', '{tahun}', '{romawi}'],
            [$no, date('m'), date('Y'), $romawi[date('n')]],
            $setting->format_surat
        );
    }
}