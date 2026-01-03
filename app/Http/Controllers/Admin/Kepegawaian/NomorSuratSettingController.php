<?php

namespace App\Http\Controllers\Admin\Kepegawaian; // <--- INI SAYA SESUAIKAN JADI KEPEGAWAIAN

use App\Http\Controllers\Controller;
use App\Models\NomorSuratSetting;
use App\Models\SuratLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Tambahkan ini biar DB::transaction jalan

class NomorSuratSettingController extends Controller
{
    public function index()
    {
        $settings = NomorSuratSetting::all();
        $logs = SuratLog::latest()->limit(100)->get();
        foreach($settings as $s) {
            $s->preview = $this->previewFormat($s);
        }
        return view('admin.administrasi.pengaturan-nomor.index', compact('settings', 'logs'));
    }

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

    public function update(Request $request, $id)
    {
        NomorSuratSetting::findOrFail($id)->update([
            'judul_kop' => $request->judul_kop,
            'format_surat' => $request->format_surat,
            'nomor_terakhir' => $request->nomor_terakhir,
        ]);
        return back()->with('success', 'Update berhasil!');
    }

    public function resetCounter($id)
    {
        NomorSuratSetting::findOrFail($id)->update(['nomor_terakhir' => 0]);
        return back()->with('success', 'Counter reset ke 0!');
    }

    public function destroy($id)
    {
        NomorSuratSetting::destroy($id);
        return back()->with('success', 'Format nomor berhasil dihapus!');
    }

    // === MAGIC FUNCTION ===
    public static function generateNomor($kategori, $logInfo, $isiSurat)
    {
        if (strpos($isiSurat, '{{no_surat}}') === false) {
            return ['status' => 'skip', 'hasil' => $isiSurat, 'nomor_saja' => ''];
        }

        $setting = NomorSuratSetting::where('kategori', $kategori)->first();
        if(!$setting) {
            return ['status' => 'error', 'pesan' => 'Format nomor belum diatur!'];
        }

        $newCounter = $setting->nomor_terakhir + 1;
        $noUrut = str_pad($newCounter, 3, '0', STR_PAD_LEFT);
        $romawi = ['', 'I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'];
        
        $finalNumber = str_replace(
            ['{no}', '{bulan}', '{tahun}', '{romawi}'],
            [$noUrut, date('m'), date('Y'), $romawi[date('n')]],
            $setting->format_surat
        );

        $setting->update(['nomor_terakhir' => $newCounter]);
        
        SuratLog::create([
            'kategori' => $kategori,
            'nomor_surat_final' => $finalNumber,
            'nomor_urut' => $noUrut,
            'tujuan' => $logInfo,
            'tanggal_dibuat' => now(),
        ]);

        $isiFinal = str_replace('{{no_surat}}', $finalNumber, $isiSurat);

        return [
            'status' => 'success', 
            'hasil' => $isiFinal,       
            'nomor_saja' => $finalNumber 
        ];
    }

    public static function getPreviewNomor($kategori)
    {
        $setting = NomorSuratSetting::where('kategori', $kategori)->first();
        if(!$setting) return '[Format Belum Diatur]';
        $next = $setting->nomor_terakhir + 1;
        $no = str_pad($next, 3, '0', STR_PAD_LEFT);
        $romawi = ['', 'I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'];
        return str_replace(
            ['{no}', '{bulan}', '{tahun}', '{romawi}'],
            [$no, date('m'), date('Y'), $romawi[date('n')]],
            $setting->format_surat
        );
    }

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