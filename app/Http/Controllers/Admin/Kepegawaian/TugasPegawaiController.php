<?php

namespace App\Http\Controllers\Admin\Kepegawaian;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Rombel, Gtk, Tapel, TipeSurat, TugasPegawai};
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TugasPegawaiController extends Controller
{
    public function index(Request $request)
    {
        $tapelAktif = Tapel::where('is_active', 1)->first();
        $tahunAktif = $tapelAktif->tahun_ajaran ?? '-';
        $semesterAktif = $tapelAktif->semester ?? '-';

        // 1. Query Mapel
        $tugasPokok = TugasPegawai::select('tugas_pegawais.*')
            ->join('gtks', 'tugas_pegawais.pegawai_id', '=', 'gtks.id')
            ->with('gtk')
            ->where('tahun_pelajaran', $tahunAktif)
            ->where('semester', $semesterAktif)
            ->where('keterangan', 'pembelajaran')
            ->when($request->search_mapel, function($q) use ($request) {
                $q->where('gtks.nama', 'like', '%' . $request->search_mapel . '%');
            })
            ->orderBy('gtks.nama', 'asc')
            ->paginate(10, ['*'], 'page_mapel');

        // 2. Query Struktural
        $jabatanStruktural = TugasPegawai::select('tugas_pegawais.*')
            ->join('gtks', 'tugas_pegawais.pegawai_id', '=', 'gtks.id')
            ->with('gtk')
            ->where('tahun_pelajaran', $tahunAktif)
            ->where('semester', $semesterAktif)
            ->where('keterangan', 'struktural')
            ->where('tugas_pokok', 'NOT LIKE', '%Kepala Sekolah%')
            ->when($request->search_struktural, function($q) use ($request) {
                $q->where('gtks.nama', 'like', '%' . $request->search_struktural . '%')
                  ->orWhere('tugas_pokok', 'like', '%' . $request->search_struktural . '%');
            })
            ->orderBy('gtks.nama', 'asc')
            ->paginate(10, ['*'], 'page_struktural');

        // Jika Request AJAX, kembalikan hanya tabelnya (Partial View)
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
        $tapel = Tapel::where('is_active', 1)->first();
        $rombels = Rombel::where('semester_id', $tapel->kode_tapel)->get();

        DB::beginTransaction();
        try {
            // A. PROSES MAPEL (Update or Create agar SK aman)
            $allLessons = collect();
            foreach ($rombels as $r) {
                $pems = is_array($r->pembelajaran) ? $r->pembelajaran : json_decode($r->pembelajaran, true);
                if ($pems) {
                    foreach ($pems as $p) {
                        if (!empty($p['ptk_id'])) {
                            $allLessons->push([
                                'ptk_id' => $p['ptk_id'],
                                'mapel' => Str::title(strtolower(trim($p['nama_mata_pelajaran']))),
                                'jam' => (int)($p['jam_mengajar_per_minggu'] ?? 0)
                            ]);
                        }
                    }
                }
            }

            // Hapus data mapel lama yang tidak ada di sinkronisasi baru (opsional, tapi updateOrCreate lebih aman)
            // Disini kita overwrite tugas_pokok dan jam, tapi biarkan nomor_sk
            foreach ($allLessons->groupBy('ptk_id') as $ptkId => $lessons) {
                $gtk = Gtk::where('ptk_id', $ptkId)->first();
                if ($gtk) {
                    TugasPegawai::updateOrCreate(
                        ['pegawai_id' => $gtk->id, 'tahun_pelajaran' => $tapel->tahun_ajaran, 'semester' => $tapel->semester, 'keterangan' => 'pembelajaran'],
                        ['tugas_pokok' => $lessons->pluck('mapel')->unique()->implode(', '), 'jumlah_jam' => $lessons->sum('jam')]
                    );
                }
            }

            // B. PROSES WALI KELAS (Merge ke Struktural)
            foreach ($rombels as $r) {
                if ($r->ptk_id && $r->jenis_rombel_str == 'Kelas') {
                    $gtk = Gtk::where('ptk_id', $r->ptk_id)->first();
                    if ($gtk) {
                        $this->mergeWaliKelas($gtk->id, 'Wali Kelas '.strtoupper($r->nama), 2, $tapel);
                    }
                }
            }
            DB::commit(); return back()->with('success', 'Sinkronisasi Rombel Berhasil.');
        } catch (\Exception $e) { DB::rollBack(); return back()->with('error', $e->getMessage()); }
    }

    private function mergeWaliKelas($pegawaiId, $namaWali, $jam, $tapel)
    {
        // Cari data struktural guru tersebut
        $tugas = TugasPegawai::firstOrCreate(
            ['pegawai_id' => $pegawaiId, 'tahun_pelajaran' => $tapel->tahun_ajaran, 'semester' => $tapel->semester, 'keterangan' => 'struktural'],
            ['tugas_pokok' => '', 'jumlah_jam' => 0] // Default jika baru dibuat
        );

        $jabatans = array_filter(array_map('trim', explode(',', $tugas->tugas_pokok)));

        // Cek apakah sudah ada "Wali Kelas ..." sebelumnya
        $updated = false;
        foreach($jabatans as $key => $val) {
            if (Str::startsWith($val, 'Wali Kelas')) {
                $jabatans[$key] = $namaWali; // Update nama kelasnya (misal X RPL -> XI RPL)
                $updated = true;
                break;
            }
        }

        if (!$updated) {
            $jabatans[] = $namaWali; // Tambah baru jika belum ada
            $tugas->jumlah_jam += $jam; // Tambah jam ekuivalensi
        }

        $tugas->tugas_pokok = implode(', ', $jabatans);
        $tugas->save();
    }

    public function store(Request $request)
    {
        $tapel = Tapel::where('is_active', 1)->first();
        TugasPegawai::updateOrCreate(
            ['pegawai_id' => $request->pegawai_id, 'tahun_pelajaran' => $tapel->tahun_ajaran, 'semester' => $tapel->semester, 'keterangan' => 'struktural'],
            ['tugas_pokok' => $request->tugas_pokok, 'jumlah_jam' => $request->jumlah_jam, 'nomor_sk' => $request->nomor_sk]
        );
        return back()->with('success', 'Data Struktural Berhasil Disimpan.');
    }

    public function update(Request $request, $id) {
        TugasPegawai::findOrFail($id)->update($request->only(['tugas_pokok', 'jumlah_jam', 'nomor_sk']));
        return back()->with('success', 'Data berhasil diperbarui.');
    }

    public function destroy($id) {
        TugasPegawai::findOrFail($id)->delete();
        return back()->with('success', 'Data berhasil dihapus.');
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

    public function cetak(Request $request, $id)
    {
        $tugas = TugasPegawai::with('gtk')->findOrFail($id);
        $template = TipeSurat::findOrFail($request->template_id);
        $isiSurat = $template->template_isi;
        $replacements = [
            '{{ nama }}' => Str::title(strtolower($tugas->gtk->nama)),
            '{{ nip }}' => $tugas->gtk->nip ?? $tugas->gtk->nik ?? '-',
            '{{ jabatan }}' => $tugas->tugas_pokok,
            '{{ nomor_sk }}' => $tugas->nomor_sk ?? '.../.../...',
            '{{ mata_pelajaran }}' => $tugas->tugas_pokok,
            '{{ jumlah_jam }}' => $tugas->jumlah_jam,
            '{{ tahun_ajaran }}' => $tugas->tahun_pelajaran,
            '{{ semester }}' => $tugas->semester,
            '{{ tanggal }}' => now()->translatedFormat('d F Y'),
        ];
        foreach ($replacements as $key => $val) { $isiSurat = str_replace($key, $val, $isiSurat); }

        $pdf = Pdf::loadView('admin.kepegawaian.tugas-pegawai.pdf-template', compact('isiSurat', 'template'))
                  ->setPaper(strtolower($template->ukuran_kertas), 'portrait');
        return $pdf->stream("SK_{$tugas->gtk->nama}.pdf");
    }
}
