<?php

namespace App\Http\Controllers\Admin\Administrasi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SuratLog;
use App\Models\TipeSurat;
use App\Models\Siswa;
use App\Models\Gtk;
use App\Models\Tapel;
use App\Models\TugasPegawai;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ArsipSuratController extends Controller
{
    // === 1. HALAMAN UTAMA (TABEL) ===
    public function index(Request $request)
    {
        $query = SuratLog::latest();

        // Fitur Pencarian
        if ($request->has('q') && $request->q != '') {
            $q = $request->q;
            $query->where(function($sql) use ($q) {
                $sql->where('nomor_surat_final', 'like', "%$q%")
                    ->orWhere('tujuan', 'like', "%$q%")
                    ->orWhere('kategori', 'like', "%$q%");
            });
        }

        $arsip = $query->paginate(20);

        return view('admin.administrasi.arsip.index', compact('arsip'));
    }

    // === 2. HAPUS LOG ===
    public function destroy($id)
    {
        SuratLog::findOrFail($id)->delete();
        return back()->with('success', 'Log arsip berhasil dihapus.');
    }

    // === 3. FITUR CETAK ULANG (RE-GENERATE) ===
    public function cetakUlang($id)
    {
        $log = SuratLog::findOrFail($id);
        
        // Cek Template Masih Ada/Tidak
        $template = TipeSurat::find($log->template_id);
        if (!$template) {
            return back()->with('error', 'Gagal cetak ulang: Template surat asli sudah dihapus dari sistem.');
        }

        // --- LOGIC PEMILIHAN DATA ---
        $dataMap = [];
        $target = null;

        // A. Jika Surat SISWA
        if ($log->kategori == 'siswa') {
            $target = Siswa::find($log->target_id);
            if ($target) $dataMap = $this->getDataSiswa($target);
        } 
        // B. Jika Surat GURU
        elseif ($log->kategori == 'guru') {
            $target = Gtk::find($log->target_id);
            if ($target) $dataMap = $this->getDataGuru($target);
        }
        // C. Jika Surat SK (Tugas Pegawai)
        elseif ($log->kategori == 'sk') {
            // Cek apakah targetnya TugasPegawai atau GTK langsung
            if (str_contains($log->target_type, 'TugasPegawai')) {
                $target = TugasPegawai::with('gtk', 'details')->find($log->target_id);
                if ($target) $dataMap = $this->getDataSK($target);
            } else {
                $target = Gtk::find($log->target_id);
                if ($target) $dataMap = $this->getDataGuru($target);
            }
        }

        if (!$target) {
            return back()->with('error', 'Gagal cetak ulang: Data Penerima (Siswa/Guru) sudah dihapus dari database.');
        }

        // --- PROSES REPLACE VARIABEL ---
        
        // 1. Masukkan Nomor Surat LAMA (PENTING!)
        $dataMap['{{no_surat}}'] = $log->nomor_surat_final;
        $dataMap['[Nomor Resmi]'] = $log->nomor_surat_final;
        
        // 2. Masukkan Tanggal (Sesuai tanggal log dibuat)
        $tglLog = Carbon::parse($log->tanggal_dibuat)->translatedFormat('d F Y');
        $dataMap['{{tanggal}}'] = $tglLog;

        // 3. Replace Isi Template
        $finalContent = $template->template_isi;
        foreach ($dataMap as $key => $val) {
            // Handle variasi penulisan {{nama}} atau {{ NAMA }}
            $keyClean = str_replace(['{{','}}'], '', $key); 
            $pattern = '/\{\{\s*' . preg_quote($keyClean, '/') . '\s*\}\}/i';
            $finalContent = preg_replace($pattern, $val, $finalContent);
        }
        
        // Replace khusus [Nomor Resmi] jika tidak pakai kurung kurawal
        $finalContent = str_replace('[Nomor Resmi]', $log->nomor_surat_final, $finalContent);

        // --- CLEANUP HTML (Hapus Enter Kosong) ---
        $finalContent = preg_replace('/^(<p>(&nbsp;|\s|<br>)*<\/p>\s*)+/i', '', $finalContent);
        $finalContent = preg_replace('/(<p>(&nbsp;|\s|<br>)*<\/p>\s*)+$/i', '', $finalContent);

        // --- RENDER PDF ---
        $paperMap = [
            'A4' => [0,0,595.28,841.89], 
            'F4' => [0,0,609.45,935.43], 
            'Legal' => [0,0,612,1008]
        ];
        $uk = $template->ukuran_kertas ?? 'A4';
        $paperSize = $paperMap[$uk] ?? $paperMap['A4'];
        
        $mt = ($template->margin_top ?? 20).'mm'; 
        $mr = ($template->margin_right ?? 25).'mm';
        $mb = ($template->margin_bottom ?? 20).'mm'; 
        $ml = ($template->margin_left ?? 25).'mm';

        $html = '
        <!DOCTYPE html><html><head><style>
            @page { margin: 0px; }
            body { margin-top: '.$mt.'; margin-right: '.$mr.'; margin-bottom: '.$mb.'; margin-left: '.$ml.'; font-family: "Times New Roman", serif; font-size: 12pt; line-height: 1.5; color: #000; }
            .mce-pagebreak { page-break-after: always; visibility: hidden; height: 0; display: block; }
            table { width: 100%; border-collapse: collapse; }
            td, th { vertical-align: top; padding: 2px; }
            p { margin-top: 0; margin-bottom: 0.8rem; }
        </style></head><body>'.$finalContent.'</body></html>';

        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper($paperSize, 'portrait');
        
        // Nama file aman
        $safeFile = str_replace(['/','\\'], '_', $log->nomor_surat_final);
        return $pdf->stream("ARSIP_{$safeFile}.pdf");
    }

    // === HELPER MAPPING DATA (Sama persis dengan controller cetak masing-masing) ===
    
    private function getDataSiswa($siswa) {
        $tapel = Tapel::where('is_active', 1)->first();
        $tglLahir = $siswa->tanggal_lahir ? Carbon::parse($siswa->tanggal_lahir)->translatedFormat('d F Y') : '-';
        $alamatParts = [];
        if ($siswa->alamat_jalan) $alamatParts[] = $siswa->alamat_jalan;
        if ($siswa->desa_kelurahan) $alamatParts[] = "Desa " . $siswa->desa_kelurahan;
        if ($siswa->kecamatan) $alamatParts[] = "Kec. " . $siswa->kecamatan;
        if ($siswa->kabupaten) $alamatParts[] = "Kab. " . $siswa->kabupaten;
        
        return [
            '{{nama}}' => Str::title($siswa->nama),
            '{{nisn}}' => $siswa->nisn ?? '-',
            '{{nipd}}' => $siswa->nipd ?? '-',
            '{{nik}}' => $siswa->nik ?? '-',
            '{{kelas}}' => $siswa->nama_rombel ?? '-',
            '{{ttl}}' => ($siswa->tempat_lahir ?? '-').', '.$tglLahir,
            '{{jk}}' => $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan',
            '{{agama}}' => $siswa->agama ?? '-',
            '{{alamat}}' => !empty($alamatParts) ? implode(', ', $alamatParts) : '-',
            '{{nama_ayah}}' => $siswa->nama_ayah ?? '-',
            '{{nama_ibu}}' => $siswa->nama_ibu ?? '-',
            '{{pekerjaan_ayah}}' => $siswa->pekerjaan_ayah ?? '-',
            '{{tahun_ajaran}}' => $tapel->tahun_ajaran ?? '-',
        ];
    }

    private function getDataGuru($guru) {
        $tapel = Tapel::where('is_active', 1)->first();
        $tglLahir = $guru->tanggal_lahir ? Carbon::parse($guru->tanggal_lahir)->translatedFormat('d F Y') : '-';
        return [
            '{{nama}}' => $guru->nama,
            '{{nip}}' => $guru->nip ?? '-',
            '{{nuptk}}' => $guru->nuptk ?? '-',
            '{{nik}}' => $guru->nik ?? '-',
            '{{jenis_ptk}}' => $guru->jenis_ptk ?? '-',
            '{{jabatan}}' => $guru->jenis_ptk ?? '-',
            '{{ttl}}' => ($guru->tempat_lahir ?? '-').', '.$tglLahir,
            '{{tempat_lahir}}' => $guru->tempat_lahir ?? '-',
            '{{tanggal_lahir}}' => $tglLahir,
            '{{jk}}' => ($guru->jenis_kelamin == 'L') ? 'Laki-laki' : 'Perempuan',
            '{{alamat}}' => $guru->alamat_jalan ?? '-',
            '{{pangkat}}' => $guru->pangkat_golongan ?? '-',
            '{{pendidikan}}' => $guru->pendidikan_terakhir ?? '-',
            '{{status_pegawai}}' => $guru->status_kepegawaian ?? '-',
            '{{tmt}}' => $guru->tmt_pengangkatan ? Carbon::parse($guru->tmt_pengangkatan)->translatedFormat('d F Y') : '-',
            '{{tahun_ajaran}}' => $tapel->tahun_ajaran ?? '-',
        ];
    }

    private function getDataSK($tugas) {
        $allTugas = $tugas->details->map(fn($d) => $d->tugas_pokok . ($d->kelas ? " ({$d->kelas})" : ""))->implode(', ');
        $totalJam = $tugas->details->sum('jumlah_jam');
        $baseData = $this->getDataGuru($tugas->gtk);
        
        return array_merge($baseData, [
            '{{jabatan}}' => $allTugas, // Override jabatan dengan tugas SK
            '{{jumlah_jam}}' => $totalJam,
            '{{semester}}' => $tugas->semester,
        ]);
    }
}