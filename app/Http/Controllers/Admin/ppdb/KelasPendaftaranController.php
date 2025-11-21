<?php

namespace App\Http\Controllers\Admin\Ppdb;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TahunPelajaran;
use App\Models\KompetensiPendaftaran;
use App\Models\TingkatPendaftaran;
use App\Models\KelasPendaftaran;

class KelasPendaftaranController extends Controller
{

    // Fungsi konversi angka ke Romawi
    private function romawi(int $angka): string
    {
        $map = [
            1000 => 'M', 900 => 'CM', 500 => 'D', 400 => 'CD',
            100 => 'C', 90 => 'XC', 50 => 'L', 40 => 'XL',
            10 => 'X', 9 => 'IX', 5 => 'V', 4 => 'IV', 1 => 'I'
        ];
        $result = '';
        foreach ($map as $value => $roman) {
            while ($angka >= $value) {
                $result .= $roman;
                $angka -= $value;
            }
        }
        return $result;
    }

    public function index()
    {
        $tahunPpdb = TahunPelajaran::where('is_active', 1)->first();

        $tingkat = TingkatPendaftaran::where('is_active', 1)->first(); // Hanya 1 tingkat aktif

        $kompetensi = ($tahunPpdb && $tingkat && $tingkat->tingkat == 10)
            ? KompetensiPendaftaran::where('tahunPelajaran_id', $tahunPpdb->id)->get()
            : collect();

        $kelas = collect(); // Inisialisasi collection kosong

        if ($tahunPpdb && $tingkat) {
            $query = KelasPendaftaran::with('kompetensiPpdb');
            
            // 🛑 PENTING: Gunakan prefix tabel untuk kolom yang ada di lebih dari satu tabel
            $query->where('kelas_pendaftarans.tahunPelajaran_id', $tahunPpdb->id)
                  ->where('kelas_pendaftarans.tingkat', $tingkat->tingkat);
            
            // 💡 Gunakan LEFT JOIN agar data kelas NON-SMK (tingkat 1 atau 7) tidak hilang
            $query->leftJoin('kompetensi_pendaftarans', 'kelas_pendaftarans.kompetensiPendaftaran_id', '=', 'kompetensi_pendaftarans.id');

            // 💡 Kita harus secara eksplisit memilih kolom setelah JOIN
            $query->select('kelas_pendaftarans.*', 'kompetensi_pendaftarans.kode as kompetensi_kode_sort');
            
            // Pengurutan Utama: Urutkan berdasarkan kode kompetensi, jika NULL akan otomatis di akhir (sehingga kelas non-SMK tidak terpengaruh)
            // Namun, untuk memastikan kelas non-SMK tetap berurutan berdasarkan rombel, kita bisa menggunakan orderBy yang lebih fleksibel.

            // Urutan 1: Berdasarkan kode kompetensi (untuk Tingkat 10)
            $query->orderBy('kompetensi_kode_sort', 'asc');
            
            // Urutan 2: Berdasarkan Rombel (untuk semua tingkat)
            $query->orderBy('kelas_pendaftarans.rombel', 'asc');
            
            $kelas = $query->get();
        }

        // Tambahkan properti romawi di setiap kelas
        $kelas->transform(function($item) {
            $item->tingkat_romawi = $this->romawi($item->tingkat);
            return $item;
        });

        return view('admin.ppdb.kelas_pendaftaran', compact('kelas', 'tahunPpdb', 'kompetensi', 'tingkat'));
    }


    public function store(Request $request)
    {
        // Jika SMA (tingkat 10), kompetensi wajib
        $rules = [
            'tahunPelajaran_id' => 'required|exists:tahun_pelajarans,id',
            'tingkat' => 'required|string',
            'rombel' => 'required|string',
        ];

        if($request->tingkat == 10) {
            $rules['kompetensiPendaftaran_id'] = 'required|exists:kompetensi_pendaftarans,id';
        }

        $request->validate($rules);

        // Untuk SD/SMP, kompetensiPendaftaran_id bisa null
        $data = $request->only('tahunPelajaran_id', 'tingkat', 'rombel');
        $data['kompetensiPendaftaran_id'] = $request->tingkat == 10 ? $request->kompetensiPendaftaran_id : null;

        KelasPendaftaran::create($data);

        return redirect()->route('admin.ppdb.kelas-ppdb.index')
                         ->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function update(Request $request, string $id)
    {
        $kelas = KelasPendaftaran::findOrFail($id);

        $rules = [
            'tahunPelajaran_id' => 'required|exists:tahun_pelajarans,id',
            'tingkat' => 'required|string',
            'rombel' => 'required|string',
        ];

        if($request->tingkat == 10) {
            $rules['kompetensiPendaftaran_id'] = 'required|exists:kompetensi_pendaftarans,id';
        }

        $request->validate($rules);

        $data = $request->only('tahunPelajaran_id', 'tingkat', 'rombel');
        $data['kompetensiPendaftaran_id'] = $request->tingkat == 10 ? $request->kompetensiPendaftaran_id : null;

        $kelas->update($data);

        return redirect()->route('admin.ppdb.kelas-ppdb.index')
                         ->with('success', 'Kelas berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $kelas = KelasPendaftaran::findOrFail($id);
        $kode = $kelas->kompetensiPpdb->kode ?? $kelas->tingkat; // fallback jika non-SMA
        $kelas->delete();

        return redirect()->route('admin.ppdb.kelas-ppdb.index')
                         ->with('success', "Kelas {$kode} berhasil dihapus.");
    }
}
