<?php

namespace App\Http\Controllers\Admin\Kepegawaian;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rombel;
use App\Models\Gtk;
use App\Models\Tapel;
use App\Models\TipeSurat;
use App\Models\TugasPegawai;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TugasPegawaiController extends Controller
{
    // ... part of index method
public function index(Request $request)
{
    $tapelAktif = Tapel::where('is_active', 1)->first();
    $tahunAktifTampil = $tapelAktif->tahun_ajaran ?? '-';
    $semesterAktifTampil = $tapelAktif->semester ?? '-';

    // 1. Tab Mata Pelajaran (Hanya yang memiliki jam mengajar > 0)
    $tugasPokok = TugasPegawai::select('tugas_pegawais.*')
        ->join('gtks', 'tugas_pegawais.pegawai_id', '=', 'gtks.id')
        ->with('gtk')
        ->where('tahun_pelajaran', $tahunAktifTampil)
        ->where('semester', $semesterAktifTampil)
        ->where('jumlah_jam', '>', 0)
        ->orderBy('gtks.nama', 'asc')
        ->paginate(10, ['*'], 'page_mapel');

    // 2. Tab Jabatan Struktural (CRUD - Memfilter Kepala Sekolah)
    // Kita ambil data tugas tambahan yang tidak ada di rombel (jam bisa 0 atau ekuivalen)
    $jabatanStruktural = TugasPegawai::with('gtk')
        ->where('tahun_pelajaran', $tahunAktifTampil)
        ->where('semester', $semesterAktifTampil)
        ->where('tugas_pokok', 'NOT LIKE', '%Kepala Sekolah%')
        ->where('jumlah_jam', '=', 0) // Asumsi tugas tambahan jamnya diinput manual/0
        ->orWhereIn('tugas_pokok', ['Wakil Kepala Sekolah', 'Kepala Lab', 'Kepala Perpustakaan'])
        ->paginate(10, ['*'], 'page_struktural');

    // Data Pegawai untuk Dropdown di Modal Tambah
    $allGtk = Gtk::orderBy('nama', 'asc')->get();

    return view('admin.kepegawaian.tugas-pegawai.index', compact(
        'tugasPokok', 'jabatanStruktural', 'allGtk',
        'tahunAktifTampil', 'semesterAktifTampil'
    ));
}

public function store(Request $request)
{
    $tapelAktif = Tapel::where('is_active', 1)->first();

    TugasPegawai::create([
        'pegawai_id' => $request->pegawai_id,
        'tugas_pokok' => $request->tugas_pokok,
        'jumlah_jam' => $request->jumlah_jam ?? 0,
        'nomor_sk' => $request->nomor_sk,
        'tahun_pelajaran' => $tapelAktif->tahun_ajaran,
        'semester' => $tapelAktif->semester,
    ]);

    return back()->with('success', 'Tugas tambahan berhasil ditambahkan.');
}

public function destroy($id)
{
    TugasPegawai::findOrFail($id)->delete();
    return back()->with('success', 'Tugas berhasil dihapus.');
}

    public function syncDariRombel()
    {
        $tapelAktif = Tapel::where('is_active', 1)->first();
        if (!$tapelAktif) return back()->with('error', 'Tahun Pelajaran Aktif tidak ditemukan.');

        $rombels = Rombel::where('semester_id', $tapelAktif->kode_tapel)->get();
        $allLessons = collect();

        foreach ($rombels as $rombel) {
            $pems = is_array($rombel->pembelajaran) ? $rombel->pembelajaran : json_decode($rombel->pembelajaran, true);
            if ($pems) {
                foreach ($pems as $p) {
                    if (!empty($p['ptk_id'])) {
                        // Memastikan mapel tidak capslock saat dimasukkan ke koleksi
                        $allLessons->push([
                            'ptk_id' => $p['ptk_id'],
                            'nama_mapel' => Str::title(strtolower(trim($p['nama_mata_pelajaran']))),
                            'jam' => (int)($p['jam_mengajar_per_minggu'] ?? 0)
                        ]);
                    }
                }
            }
        }

        $groupedByGtk = $allLessons->groupBy('ptk_id');

        DB::beginTransaction();
        try {
            TugasPegawai::where('tahun_pelajaran', $tapelAktif->tahun_ajaran)
                        ->where('semester', $tapelAktif->semester)
                        ->delete();

            foreach ($groupedByGtk as $ptkId => $lessons) {
                $gtk = Gtk::where('ptk_id', $ptkId)->first();
                if ($gtk) {
                    $mergedMapel = $lessons->pluck('nama_mapel')->unique()->implode(', ');
                    $totalJam = $lessons->sum('jam');

                    TugasPegawai::create([
                        'pegawai_id'      => $gtk->id,
                        'tahun_pelajaran' => $tapelAktif->tahun_ajaran,
                        'semester'        => $tapelAktif->semester,
                        'tugas_pokok'     => $mergedMapel,
                        'jumlah_jam'      => $totalJam,
                        'nomor_sk'        => null,
                    ]);
                }
            }
            DB::commit();
            return back()->with('success', 'Sinkronisasi berhasil! Format teks telah diperbaiki.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
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
        $template = TipeSurat::where('id', $request->template_id)->where('kategori', 'sk')->firstOrFail();

        $isiSurat = $template->template_isi;
        $replacements = [
            '{{ nama }}'           => Str::title(strtolower($tugas->gtk->nama)),
            '{{ nip }}'            => $tugas->gtk->nip ?? $tugas->gtk->nik ?? '-',
            '{{ jabatan }}'        => Str::title(strtolower($tugas->gtk->jabatan_ptk_id_str ?? 'Guru')),
            '{{ nomor_sk }}'       => $tugas->nomor_sk ?? '.../.../...',
            '{{ mata_pelajaran }}' => $tugas->tugas_pokok,
            '{{ jumlah_jam }}'     => $tugas->jumlah_jam,
            '{{ tahun_ajaran }}'   => $tugas->tahun_pelajaran,
            '{{ semester }}'       => $tugas->semester,
            '{{ tanggal }}'        => now()->translatedFormat('d F Y'),
        ];

        foreach ($replacements as $key => $value) {
            $isiSurat = str_replace($key, $value, $isiSurat);
        }

        $pdf = Pdf::loadView('admin.kepegawaian.tugas-pegawai.pdf-template', compact('isiSurat', 'template'))
                  ->setPaper(strtolower($template->ukuran_kertas), 'portrait');

        return $pdf->stream("SK_{$tugas->gtk->nama}.pdf");
    }
}
