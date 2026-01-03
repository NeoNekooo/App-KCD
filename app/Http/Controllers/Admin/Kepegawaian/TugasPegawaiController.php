<?php

namespace App\Http\Controllers\Admin\Kepegawaian;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Rombel, Gtk, Tapel, TipeSurat, TugasPegawai, TugasPegawaiDetail};
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Http\Controllers\Admin\Kepegawaian\NomorSuratSettingController;

class TugasPegawaiController extends Controller
{
    /**
     * Menampilkan daftar tugas pegawai (Mapel & Struktural)
     */
    public function index(Request $request)
    {
        // ... (kode index sama seperti sebelumnya) ...
        $tapelAktif = Tapel::where('is_active', 1)->first();
        $tahunAktif = $tapelAktif->tahun_ajaran ?? '-';
        $semesterAktif = $tapelAktif->semester ?? '-';

        // Data Tugas Mengajar (Mapel)
        $tugasPokok = TugasPegawai::with(['gtk', 'details'])
            ->join('gtks', 'tugas_pegawais.pegawai_id', '=', 'gtks.id')
            ->where('tahun_pelajaran', $tahunAktif)
            ->where('semester', $semesterAktif)
            ->whereHas('details', function($q) {
                $q->where('jenis', 'pembelajaran');
            })
            ->when($request->search_mapel, function($q) use ($request) {
                $q->where('gtks.nama', 'like', '%' . $request->search_mapel . '%');
            })
            ->select('tugas_pegawais.*')
            ->orderBy('gtks.nama', 'asc')
            ->paginate(10, ['*'], 'page_mapel');

        // Data Tugas Struktural
        $jabatanStruktural = TugasPegawaiDetail::with('parent.gtk')
            ->join('tugas_pegawais', 'tugas_pegawai_details.tugas_pegawai_id', '=', 'tugas_pegawais.id')
            ->join('gtks', 'tugas_pegawais.pegawai_id', '=', 'gtks.id')
            ->where('tugas_pegawai_details.jenis', 'struktural')
            ->where('tugas_pegawais.tahun_pelajaran', $tahunAktif)
            ->where('tugas_pegawais.semester', $semesterAktif)
            ->when($request->search_struktural, function($q) use ($request) {
                $q->where(function($query) use ($request) {
                    $query->where('tugas_pegawai_details.tugas_pokok', 'like', '%' . $request->search_struktural . '%')
                          ->orWhere('gtks.nama', 'like', '%' . $request->search_struktural . '%');
                });
            })
            ->select('tugas_pegawai_details.*')
            ->orderBy('gtks.nama', 'asc')
            ->paginate(10, ['*'], 'page_struktural');

        if ($request->ajax()) {
            if ($request->has('page_mapel') || $request->has('search_mapel'))
                return view('admin.kepegawaian.tugas-pegawai.partials._table_mapel', compact('tugasPokok'))->render();
            if ($request->has('page_struktural') || $request->has('search_struktural'))
                return view('admin.kepegawaian.tugas-pegawai.partials._table_struktural', compact('jabatanStruktural'))->render();
        }

        $allGtk = Gtk::orderBy('nama', 'asc')->get();
        // Ambil Template Kategori SK untuk dropdown cetak
        $templates = TipeSurat::where('kategori', 'sk')->get();

        return view('admin.kepegawaian.tugas-pegawai.index', compact(
            'tugasPokok', 'jabatanStruktural', 'allGtk', 'tahunAktif', 'semesterAktif', 'templates'
        ));
    }

    /**
     * Sinkronisasi data tugas mengajar dari data Rombel/Dapodik Lokal
     */
    public function syncDariRombel()
    {
        // ... (kode syncDariRombel sama seperti sebelumnya) ...
        try {
            $tapel = Tapel::where('is_active', 1)->first();
            if (!$tapel) return back()->with('error', 'Tapel aktif tidak ditemukan.');
            
            $rombels = Rombel::where('semester_id', $tapel->kode_tapel)->get();
            
            DB::beginTransaction();
            
            // Hapus detail tugas pembelajaran lama untuk tahun/semester ini agar bersih
            $headerIds = TugasPegawai::where('tahun_pelajaran', $tapel->tahun_ajaran)
                ->where('semester', $tapel->semester)
                ->pluck('id');
            TugasPegawaiDetail::whereIn('tugas_pegawai_id', $headerIds)
                ->where('jenis', 'pembelajaran')
                ->delete();

            foreach ($rombels as $r) {
                $pems = is_array($r->pembelajaran) ? $r->pembelajaran : json_decode($r->pembelajaran, true);
                if (!$pems) continue;
                
                foreach ($pems as $p) {
                    $ptkId = $p['ptk_id'] ?? null;
                    if (!$ptkId) continue;
                    
                    $gtk = Gtk::where('ptk_id', $ptkId)->first();
                    if (!$gtk) continue;
                    
                    $header = TugasPegawai::firstOrCreate([
                        'pegawai_id' => $gtk->id,
                        'tahun_pelajaran' => $tapel->tahun_ajaran,
                        'semester' => $tapel->semester
                    ]);
                    
                    TugasPegawaiDetail::create([
                        'tugas_pegawai_id' => $header->id,
                        'tugas_pokok' => Str::title(strtolower(trim($p['mata_pelajaran_id_str'] ?? $p['nama_mata_pelajaran']))),
                        'kelas' => $r->nama,
                        'jumlah_jam' => (int)($p['jam_mengajar_per_minggu'] ?? 0),
                        'jenis' => 'pembelajaran'
                    ]);
                }
            }
            DB::commit();
            return back()->with('success', "Sinkronisasi Berhasil.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * GENERATE PDF SK (Langsung Stream)
     */
    public function cetak(Request $request, $id)
    {
        $request->validate([
            'template_id' => 'required|exists:tipe_surats,id',
        ]);

        $tugas = TugasPegawai::with(['gtk', 'details'])->findOrFail($id);
        $template = TipeSurat::findOrFail($request->template_id);

        // 1. GENERATE NOMOR SURAT (Jika Belum Ada)
        if (empty($tugas->nomor_sk)) {
            $keteranganLog = "SK Pembagian Tugas a.n " . $tugas->gtk->nama;
            $hasilNomor = NomorSuratSettingController::generateNomor('sk', $keteranganLog, $template->template_isi);

            if ($hasilNomor['status'] == 'error') {
                return back()->with('error', 'Gagal generate nomor: ' . $hasilNomor['pesan']);
            }
            
            $tugas->nomor_sk = $hasilNomor['nomor_saja'];
            $tugas->save();
            
            $isiSuratRaw = $template->template_isi; 
        } else {
            $isiSuratRaw = $template->template_isi;
        }

        // 2. FIX TABLE WIDTH & PRESERVE BORDER (Penting untuk DomPDF)
        if (strpos($isiSuratRaw, '<colgroup>') !== false) {
            $isiSuratRaw = preg_replace_callback('/(<table[^>]*>)(.*?)<\/table>/is', function($tableMatch) {
                $openingTag = $tableMatch[1];
                $tableContent = $tableMatch[2];
                
                if (preg_match_all('/<col [^>]*style="width:\s*([^;%"]+)%[^"]*"[^>]*>/i', $tableContent, $colMatches)) {
                    $widths = $colMatches[1];
                    $tableContent = preg_replace_callback('/<tr[^>]*>(.*?)<\/tr>/is', function($trMatch) use ($widths) {
                        $cells = $trMatch[1];
                        $cellIndex = 0;
                        return '<tr>' . preg_replace_callback('/<(td|th)([^>]*)>/i', function($tdMatch) use ($widths, &$cellIndex) {
                            $tag = $tdMatch[1];
                            $attr = $tdMatch[2];
                            if (isset($widths[$cellIndex])) {
                                if (strpos($attr, 'style=') !== false) {
                                    $attr = preg_replace('/style="/i', 'style="width:' . $widths[$cellIndex] . '%;', $attr);
                                } else {
                                    $attr .= ' style="width:' . $widths[$cellIndex] . '%;"';
                                }
                            }
                            $cellIndex++;
                            return "<$tag$attr>";
                        }, $cells) . '</tr>';
                    }, $tableContent);
                }
                return $openingTag . $tableContent . '</table>';
            }, $isiSuratRaw);
        }

        // 3. SIAPKAN DATA VARIABEL
        // Gabungkan tugas menjadi list string
        $allTugas = $tugas->details->map(function($d) {
            return $d->tugas_pokok . ($d->kelas ? " ({$d->kelas})" : "");
        })->implode(', ');
        
        $totalJam = $tugas->details->sum('jumlah_jam');
        $tglLahir = $tugas->gtk->tanggal_lahir ? Carbon::parse($tugas->gtk->tanggal_lahir)->translatedFormat('d F Y') : '-';
        $alamatLengkap = collect([$tugas->gtk->alamat_jalan, $tugas->gtk->desa_kelurahan, $tugas->gtk->kecamatan, $tugas->gtk->kabupaten])->filter()->implode(', ');

        $replacements = [
            '{{no_surat}}'        => $tugas->nomor_sk,
            '{{nama}}'            => Str::title(strtolower($tugas->gtk->nama)),
            '{{nip}}'             => $tugas->gtk->nip ?? '-',
            '{{nik}}'             => $tugas->gtk->nik ?? '-',
            '{{nuptk}}'           => $tugas->gtk->nuptk ?? '-',
            '{{jabatan_gtk}}'     => $tugas->gtk->jenis_ptk ?? '-', 
            '{{jabatan}}'         => $allTugas, 
            '{{jumlah_jam}}'      => $totalJam,
            '{{tahun_ajaran}}'    => $tugas->tahun_pelajaran,
            '{{semester}}'        => $tugas->semester,
            '{{tanggal}}'         => now()->translatedFormat('d F Y'),
            '{{tempat_lahir}}'    => $tugas->gtk->tempat_lahir ?? '-',
            '{{tanggal_lahir}}'   => $tglLahir,
            '{{pangkat}}'         => $tugas->gtk->pangkat_golongan ?? '-',
            '{{pendidikan}}'      => $tugas->gtk->pendidikan_terakhir ?? '-',
            '{{alamat}}'          => $alamatLengkap,
            '{{status_pegawai}}'  => $tugas->gtk->status_kepegawaian ?? '-',
            '{{tmt}}'             => $tugas->gtk->tmt_pengangkatan ? Carbon::parse($tugas->gtk->tmt_pengangkatan)->translatedFormat('d F Y') : '-',
        ];

        $finalContent = $isiSuratRaw;
        foreach ($replacements as $key => $val) {
            $finalContent = str_ireplace($key, $val, $finalContent);
        }

        // 4. CLEANUP (Membersihkan Sampah Enter)
        // Hapus enter kosong di awal dokumen
        $finalContent = preg_replace('/^(<p>(&nbsp;|\s|<br>)*<\/p>\s*)+/i', '', $finalContent);
        // Hapus enter kosong di akhir dokumen
        $finalContent = preg_replace('/(<p>(&nbsp;|\s|<br>)*<\/p>\s*)+$/i', '', $finalContent);

        // 5. CONFIG PDF
        $paperMap = [
            'A4'     => [0, 0, 595.28, 841.89],
            'F4'     => [0, 0, 609.45, 935.43],
            'Legal'  => [0, 0, 612.00, 1008.00],
            'Letter' => [0, 0, 612.00, 792.00],
        ];
        
        $uk = $template->ukuran_kertas ?? 'A4';
        $paperSize = $paperMap[$uk] ?? $paperMap['A4'];

        $mt = ($template->margin_top ?? 20) . 'mm';
        $mr = ($template->margin_right ?? 25) . 'mm';
        $mb = ($template->margin_bottom ?? 20) . 'mm';
        $ml = ($template->margin_left ?? 25) . 'mm';

        // 6. RENDER HTML WRAPPER
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                @page { margin: 0px; }
                body { 
                    margin-top: '.$mt.'; margin-right: '.$mr.'; margin-bottom: '.$mb.'; margin-left: '.$ml.'; 
                    font-family: "Times New Roman", serif; font-size: 12pt; line-height: 1.5; color: #000;
                }
                
                /* Config Page Break untuk PDF */
                .mce-pagebreak { 
                    page-break-before: always !important; 
                    display: block !important;
                    height: 0px !important; 
                    margin: 0 !important; 
                    padding: 0 !important; 
                    border: none !important; 
                    visibility: hidden;
                }
                .mce-pagebreak:first-child { page-break-before: avoid !important; }

                table { width: 100%; border-collapse: collapse; }
                td, th { vertical-align: top; padding: 2px; }
                p { margin-top: 0; margin-bottom: 0.8rem; }
            </style>
        </head>
        <body>'.$finalContent.'</body>
        </html>';

        // 7. STREAM PDF
        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper($paperSize, 'portrait');

        return $pdf->stream("SK_{$tugas->gtk->nama}.pdf");
    }

    // ... (Fungsi store, update, destroy tetap sama) ...
    public function store(Request $request) {
        $tapel = Tapel::where('is_active', 1)->first();
        $header = TugasPegawai::firstOrCreate([
            'pegawai_id' => $request->pegawai_id, 
            'tahun_pelajaran' => $tapel->tahun_ajaran, 
            'semester' => $tapel->semester
        ]);
        TugasPegawaiDetail::create([
            'tugas_pegawai_id' => $header->id, 
            'tugas_pokok' => $request->tugas_pokok, 
            'jumlah_jam' => $request->jumlah_jam, 
            'jenis' => 'struktural'
        ]);
        return back()->with('success', 'Data Struktural Berhasil Disimpan.');
    }

    public function update(Request $request, $id) {
        TugasPegawaiDetail::findOrFail($id)->update([
            'tugas_pokok' => $request->tugas_pokok, 
            'jumlah_jam' => $request->jumlah_jam
        ]);
        return back()->with('success', 'Data berhasil diperbarui.');
    }

    public function destroy($id) {
        TugasPegawai::findOrFail($id)->delete();
        return back()->with('success', 'Data berhasil dihapus.');
    }

    public function destroyDetail($id) {
        TugasPegawaiDetail::findOrFail($id)->delete();
        return back()->with('success', 'Detail tugas berhasil dihapus.');
    }

    public function getDetail($id) {
        return response()->json(TugasPegawaiDetail::where('tugas_pegawai_id', $id)->get());
    }
}