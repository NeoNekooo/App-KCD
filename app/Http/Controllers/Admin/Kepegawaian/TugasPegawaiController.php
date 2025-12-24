<?php

namespace App\Http\Controllers\Admin\Kepegawaian;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Rombel, Gtk, Tapel, TipeSurat, TugasPegawai, TugasPegawaiDetail};
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class TugasPegawaiController extends Controller
{
    public function index(Request $request)
{
    $tapelAktif = Tapel::where('is_active', 1)->first();
    $tahunAktif = $tapelAktif->tahun_ajaran ?? '-';
    $semesterAktif = $tapelAktif->semester ?? '-';

    // 1. Query Mapel (Diurutkan A-Z berdasarkan Nama Guru)
    $tugasPokok = TugasPegawai::with(['gtk', 'details'])
        // JOIN ke tabel GTK untuk mengambil akses kolom 'nama'
        ->join('gtks', 'tugas_pegawais.pegawai_id', '=', 'gtks.id')
        ->where('tahun_pelajaran', $tahunAktif)
        ->where('semester', $semesterAktif)
        ->whereHas('details', function($q) {
            $q->where('jenis', 'pembelajaran');
        })
        ->when($request->search_mapel, function($q) use ($request) {
            $q->where('gtks.nama', 'like', '%' . $request->search_mapel . '%');
        })
        // PENTING: Pilih kolom tugas_pegawai saja agar ID tidak tertimpa ID GTK
        ->select('tugas_pegawais.*')
        // URUTKAN berdasarkan nama di tabel GTK
        ->orderBy('gtks.nama', 'asc')
        ->paginate(10, ['*'], 'page_mapel');

    // 2. Query Struktural (Diurutkan A-Z berdasarkan Nama Guru)
    $jabatanStruktural = TugasPegawaiDetail::with('parent.gtk')
        // JOIN BERTINGKAT: Detail -> Header (TugasPegawai) -> GTK
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
        // PENTING: Pilih kolom detail saja
        ->select('tugas_pegawai_details.*')
        // URUTKAN
        ->orderBy('gtks.nama', 'asc')
        ->paginate(10, ['*'], 'page_struktural');

    // Fitur AJAX Pagination & Search Tetap Dipertahankan
    if ($request->ajax()) {
        if ($request->has('page_mapel') || $request->has('search_mapel'))
            return view('admin.kepegawaian.tugas-pegawai.partials._table_mapel', compact('tugasPokok'))->render();
        if ($request->has('page_struktural') || $request->has('search_struktural'))
            return view('admin.kepegawaian.tugas-pegawai.partials._table_struktural', compact('jabatanStruktural'))->render();
    }

    $allGtk = Gtk::orderBy('nama', 'asc')->get();

    return view('admin.kepegawaian.tugas-pegawai.index', compact(
        'tugasPokok', 'jabatanStruktural', 'allGtk', 'tahunAktif', 'semesterAktif'
    ));
}

public function syncDariRombel()
{
    Log::channel('single')->info("============================================");
    Log::channel('single')->info("START DEBUG MAPEL: " . now());

    try {
        $tapel = Tapel::where('is_active', 1)->first();
        if (!$tapel) return back()->with('error', 'Tapel aktif tidak ditemukan.');

        $rombels = Rombel::where('semester_id', $tapel->kode_tapel)->get();
        if ($rombels->count() == 0) return back()->with('error', 'Data Rombel kosong.');

        DB::beginTransaction();

        // BERSIHKAN DATA LAMA
        $headerIds = TugasPegawai::where('tahun_pelajaran', $tapel->tahun_ajaran)
                                 ->where('semester', $tapel->semester)
                                 ->pluck('id');

        TugasPegawaiDetail::whereIn('tugas_pegawai_id', $headerIds)
                          ->whereIn('jenis', ['pembelajaran', 'struktural'])
                          ->delete();

        Log::channel('single')->info("Data lama dibersihkan. Memulai scan mapel...");

        $totalMapelSukses = 0;
        $totalMapelSkip   = 0;

        foreach ($rombels as $index => $r) {
            Log::channel('single')->info("--- Rombel #".($index+1).": {$r->nama} ({$r->jenis_rombel_str}) ---");

            // 1. DECODE JSON PEMBELAJARAN
            $pems = is_array($r->pembelajaran) ? $r->pembelajaran : json_decode($r->pembelajaran, true);

            if (!$pems || count($pems) == 0) {
                Log::channel('single')->warning("   [!] KOSONG: Tidak ada data pembelajaran di JSON rombel ini.");
                continue;
            }

            Log::channel('single')->info("   -> Ditemukan " . count($pems) . " mapel di JSON.");

            // 2. LOOP SETIAP MAPEL
            foreach ($pems as $key => $p) {
$namaMapel = $p['mata_pelajaran_id_str'] ?? $p['nama_mata_pelajaran'] ?? 'TANPA NAMA';                $ptkId     = $p['ptk_id'] ?? null;
                $jam       = $p['jam_mengajar_per_minggu'] ?? 0;

                // A. Cek ID Guru
                if (empty($ptkId)) {
                    Log::channel('single')->warning("      [SKIP] Mapel: '$namaMapel' -> ID Guru (ptk_id) kosong/null di Dapodik.");
                    $totalMapelSkip++;
                    continue;
                }

                // B. Cek Guru di Database Lokal
                $gtk = Gtk::where('ptk_id', $ptkId)->first();

                if (!$gtk) {
                    // INI MASALAH PALING UMUM
                    Log::channel('single')->error("      [GAGAL] Mapel: '$namaMapel' -> Guru dengan PTK_ID ($ptkId) TIDAK ADA di tabel GTK lokal.");
                    $totalMapelSkip++;
                    continue;
                }

                // C. Simpan jika semua aman
                $header = TugasPegawai::firstOrCreate([
                    'pegawai_id' => $gtk->id,
                    'tahun_pelajaran' => $tapel->tahun_ajaran,
                    'semester' => $tapel->semester
                ]);

                TugasPegawaiDetail::create([
                    'tugas_pegawai_id' => $header->id,
                    'tugas_pokok' => Str::title(strtolower(trim($namaMapel))),
                    'kelas' => $r->nama,
                    'jumlah_jam' => (int)$jam,
                    'jenis' => 'pembelajaran'
                ]);

                // Log Sukses (Opsional: dimatikan kalau terlalu penuh, tapi bagus untuk debug)
                Log::channel('single')->info("      [OK] $namaMapel - Guru: {$gtk->nama_lengkap}");

                $totalMapelSukses++;
            }

            // 3. WALI KELAS (KODE YANG SUDAH FIX KITA PERTAHANKAN)
            $jenisValid = ['Kelas', 'Matapelajaran Pilihan'];
            if (!empty($r->ptk_id) && in_array($r->jenis_rombel_str, $jenisValid)) {
                $gtkWali = Gtk::where('ptk_id', $r->ptk_id)->first();
                if ($gtkWali) {
                    $headerWali = TugasPegawai::firstOrCreate([
                        'pegawai_id' => $gtkWali->id,
                        'tahun_pelajaran' => $tapel->tahun_ajaran,
                        'semester' => $tapel->semester
                    ]);
                    TugasPegawaiDetail::updateOrCreate(
                        ['tugas_pegawai_id' => $headerWali->id, 'tugas_pokok' => 'Wali Kelas ' . $r->nama, 'jenis' => 'struktural'],
                        ['kelas' => $r->nama, 'jumlah_jam' => 2]
                    );
                }
            }
        }

        DB::commit();
        Log::channel('single')->info("DEBUG SELESAI. Sukses: $totalMapelSukses, Gagal/Skip: $totalMapelSkip");
        Log::channel('single')->info("============================================");

        return back()->with('success', "Cek Log untuk detail Mapel. Sukses: $totalMapelSukses, Skip: $totalMapelSkip");

    } catch (\Exception $e) {
        DB::rollBack();
        Log::channel('single')->error($e->getMessage());
        return back()->with('error', $e->getMessage());
    }
}

    public function store(Request $request)
    {
        $tapel = Tapel::where('is_active', 1)->first();
        $header = TugasPegawai::firstOrCreate([
            'pegawai_id' => $request->pegawai_id,
            'tahun_pelajaran' => $tapel->tahun_ajaran,
            'semester' => $tapel->semester
        ]);

        if($request->nomor_sk) $header->update(['nomor_sk' => $request->nomor_sk]);

        TugasPegawaiDetail::create([
            'tugas_pegawai_id' => $header->id,
            'tugas_pokok' => $request->tugas_pokok,
            'jumlah_jam' => $request->jumlah_jam,
            'jenis' => 'struktural'
        ]);

        return back()->with('success', 'Data Struktural Berhasil Disimpan.');
    }

    public function updateSk(Request $request)
    {
        $tugas = TugasPegawai::find($request->id);
        if ($tugas) {
            $tugas->nomor_sk = $request->nomor_sk;
            $tugas->save();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 404);
    }

    public function destroy($id)
    {
        // Menghapus Header akan menghapus Detail karena onDelete('cascade') di migration
        TugasPegawai::findOrFail($id)->delete();
        return back()->with('success', 'Data berhasil dihapus.');
    }

    public function cetak(Request $request, $id)
    {
        $tugas = TugasPegawai::with(['gtk', 'details'])->findOrFail($id);
        $template = TipeSurat::findOrFail($request->template_id);

        // Gabungkan semua mapel/jabatan untuk tampilan di SK
        $allTugas = $tugas->details->pluck('tugas_pokok')->unique()->implode(', ');
        $totalJam = $tugas->details->sum('jumlah_jam');

        $isiSurat = $template->template_isi;
        $replacements = [
            '{{ nama }}' => Str::title(strtolower($tugas->gtk->nama)),
            '{{ nip }}' => $tugas->gtk->nip ?? $tugas->gtk->nik ?? '-',
            '{{ jabatan }}' => $allTugas,
            '{{ nomor_sk }}' => $tugas->nomor_sk ?? '.../.../...',
            '{{ mata_pelajaran }}' => $allTugas,
            '{{ jumlah_jam }}' => $totalJam,
            '{{ tahun_ajaran }}' => $tugas->tahun_pelajaran,
            '{{ semester }}' => $tugas->semester,
            '{{ tanggal }}' => now()->translatedFormat('d F Y'),
        ];

        foreach ($replacements as $key => $val) { $isiSurat = str_replace($key, $val, $isiSurat); }

        // --- 1. PROSES HTML (Tabel & Border) ---
        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadHTML('<?xml encoding="utf-8" ?><div>' . $rawContent . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        $tables = $dom->getElementsByTagName('table');
        foreach ($tables as $table) {
            // A. Deteksi Tabel No Border
            $borderAttr = $table->getAttribute('border');
            $styleAttr = $table->getAttribute('style');
            $isBorderless = ($borderAttr === '0') ||
                            (strpos($styleAttr, 'border-style: hidden') !== false) ||
                            (strpos($styleAttr, 'border: none') !== false) ||
                            (strpos($styleAttr, 'border: 0') !== false);

            // B. Set Style Dasar Tabel
            $baseTableStyle = "width: 100%; table-layout: fixed; border-collapse: collapse; margin-bottom: 1em;";
            if ($isBorderless) {
                $baseTableStyle .= " border: none !important;";
            }
            $table->setAttribute('style', $styleAttr . '; ' . $baseTableStyle);

            // C. Ambil Lebar Kolom
            $colWidths = [];
            $colgroups = $table->getElementsByTagName('colgroup');
            if ($colgroups->length > 0) {
                $cols = $colgroups->item(0)->getElementsByTagName('col');
                foreach ($cols as $col) {
                    $cStyle = $col->getAttribute('style');
                    if (preg_match('/width:\s*([\d\.]+(%|px))/', $cStyle, $matches)) {
                        $colWidths[] = $matches[1];
                    } else {
                        $colWidths[] = null;
                    }
                }
            }

            // D. Proses Sel (TD/TH)
            $rows = $table->getElementsByTagName('tr');
            foreach ($rows as $row) {
                $cells = $row->childNodes;
                $colIndex = 0;

                foreach ($cells as $cell) {
                    if ($cell->nodeType !== XML_ELEMENT_NODE) continue;

                    $currentStyle = $cell->getAttribute('style');
                    $widthStyle = "";

                    if (isset($colWidths[$colIndex]) && $colWidths[$colIndex]) {
                        $widthStyle = "width: {$colWidths[$colIndex]};";
                    }

                    $cellCss = "padding: 2px 4px; vertical-align: top; word-wrap: break-word; overflow-wrap: break-word; word-break: break-all;";

                    if ($isBorderless) {
                        $cellCss .= " border: none !important;";
                    }

                    $cleanStyle = preg_replace('/width:\s*[^;]+;?/', '', $currentStyle);
                    if ($isBorderless) {
                        $cleanStyle = preg_replace('/border[^;]+;?/', '', $cleanStyle);
                    }

                    $finalStyle = $cleanStyle . '; ' . $cellCss . $widthStyle;
                    $cell->setAttribute('style', $finalStyle);
                    $colIndex++;
                }
            }
        }

        $isiSuratConverted = '';
        $container = $dom->getElementsByTagName('div')->item(0);
        foreach ($container->childNodes as $child) {
            $isiSuratConverted .= $dom->saveHTML($child);
        }
        libxml_clear_errors();

        // --- 2. DATA UNTUK REPLACEMENTS (FORMAT TANGGAL D F Y = Date Only) ---

        // Helper Format Tanggal Indonesia (Tanpa Jam)
        $tglLahir = $tugas->gtk->tanggal_lahir ? Carbon::parse($tugas->gtk->tanggal_lahir)->translatedFormat('d F Y') : '-';
        $tmtTugas = $tugas->gtk->tmt_pengangkatan ? Carbon::parse($tugas->gtk->tmt_pengangkatan)->translatedFormat('d F Y') : '-';
        $tanggalCetak = now()->translatedFormat('d F Y'); // Hanya Tanggal (Misal: 24 Desember 2025)

        // Helper Alamat Lengkap
        $alamatLengkap = collect([
            $tugas->gtk->alamat_jalan,
            $tugas->gtk->desa_kelurahan ? 'Desa ' . $tugas->gtk->desa_kelurahan : null,
            $tugas->gtk->kecamatan ? 'Kec. ' . $tugas->gtk->kecamatan : null,
            $tugas->gtk->kabupaten ? 'Kab. ' . $tugas->gtk->kabupaten : null
        ])->filter()->implode(', ');

        $replacements = [
            // === VARIABLE LAMA ===
            '{{nama}}'             => Str::title(strtolower($tugas->gtk->nama)),
            '{{nip}}'              => $tugas->gtk->nip ?? '-',
            '{{jabatan}}'          => $tugas->tugas_pokok,
            '{{nomor_surat}}'      => $tugas->nomor_sk ?? '.../.../...',
            '{{nomor_sk}}'         => $tugas->nomor_sk ?? '.../.../...',
            '{{mata_pelajaran}}'   => $tugas->tugas_pokok,
            '{{jumlah_jam}}'       => $tugas->jumlah_jam,
            '{{tahun_ajaran}}'     => $tugas->tahun_pelajaran,
            '{{semester}}'         => $tugas->semester,
            '{{tanggal}}'          => $tanggalCetak, // Tanggal Cetak (Date Only)

            // === VARIABLE BARU ===
            '{{nik}}'              => $tugas->gtk->nik ?? '-',
            '{{nuptk}}'            => $tugas->gtk->nuptk ?? '-',
            '{{tempat_lahir}}'     => $tugas->gtk->tempat_lahir ?? '-',
            '{{tanggal_lahir}}'    => $tglLahir, // Tanggal Lahir (Date Only)
            '{{ttl}}'              => ($tugas->gtk->tempat_lahir ?? '-') . ', ' . $tglLahir,
            '{{jenis_kelamin}}'    => ($tugas->gtk->jenis_kelamin == 'L') ? 'Laki-laki' : 'Perempuan',
            '{{agama}}'            => $tugas->gtk->agama_id_str ?? '-',
            '{{pendidikan}}'       => $tugas->gtk->pendidikan_terakhir ?? '-',
            '{{pangkat}}'          => $tugas->gtk->pangkat_golongan_terakhir ?? '-',
            '{{golongan}}'         => $tugas->gtk->pangkat_golongan_terakhir ?? '-',
            '{{jabatan_gtk}}'      => $tugas->gtk->jabatan_ptk_id_str ?? '-',
            '{{status_pegawai}}'   => $tugas->gtk->status_kepegawaian_id_str ?? '-',
            '{{alamat}}'           => $alamatLengkap,
            '{{tmt}}'              => $tmtTugas, // TMT (Date Only)
        ];

        $isiSurat = $isiSuratConverted;
        foreach ($replacements as $key => $val) {
            // Case-insensitive replace
            $isiSurat = str_ireplace($key, $val, $isiSurat);
        }

        // --- 3. RENDER PDF ---
        $pdf = Pdf::loadView('admin.kepegawaian.tugas-pegawai.pdf-template', compact('isiSurat', 'template'))
                  ->setPaper(strtolower($template->ukuran_kertas), 'portrait')
                  ->setOptions([
                      'isHtml5ParserEnabled' => true,
                      'isRemoteEnabled'      => true,
                      'dpi'                  => 96,
                      'defaultFont'          => 'serif'
                  ]);

        return $pdf->stream("SK_{$tugas->gtk->nama}.pdf");
    }

    public function getDetail($id)
    {
        $details = TugasPegawaiDetail::where('tugas_pegawai_id', $id)->get();
        return response()->json($details);
    }

    public function destroyDetail($id)
{
    $detail = TugasPegawaiDetail::findOrFail($id);
    $headerId = $detail->tugas_pegawai_id;
    $detail->delete();

    // Opsional: Jika detail sudah habis, hapus headernya juga
    $remaining = TugasPegawaiDetail::where('tugas_pegawai_id', $headerId)->count();
    if ($remaining === 0) {
        TugasPegawai::find($headerId)->delete();
    }

    return back()->with('success', 'Jabatan berhasil dihapus.');
}

public function update(Request $request, $id)
{
    $detail = TugasPegawaiDetail::findOrFail($id);

    // 1. Update data detail (tugas & jam)
    $detail->update([
        'tugas_pokok' => $request->tugas_pokok,
        'jumlah_jam'  => $request->jumlah_jam,
    ]);

    // 2. Update nomor SK di Header (Parent)
    if ($detail->parent) {
        $detail->parent->update([
            'nomor_sk' => $request->nomor_sk
        ]);
    }

    return back()->with('success', 'Data Struktural berhasil diperbarui.');
}
}
