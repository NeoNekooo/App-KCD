<?php

namespace App\Http\Controllers\Admin\Akademik;

use App\Http\Controllers\Controller;
use App\Models\JadwalPelajaran;
use App\Models\Rombel;
use App\Models\Gtk;
use App\Models\Tapel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class JadwalPelajaranController extends Controller
{
    /**
     * Helper untuk mengambil data Mapel.
     */
    private function getAllUniqueMapels($asAssociativeArray = false)
    {
        $rombels = DB::table('rombels')->select('pembelajaran')->get();
        $uniqueMapels = [];
        foreach ($rombels as $rombel) {
            $pembelajaran_data = json_decode($rombel->pembelajaran, true);
            if (is_array($pembelajaran_data)) {
                foreach ($pembelajaran_data as $pembelajaran) {
                    $mapel_id = $pembelajaran['mata_pelajaran_id'] ?? null;
                    $mapel_nama = $pembelajaran['mata_pelajaran_id_str'] ?? 'N/A';
                    if ($mapel_id && $mapel_nama !== 'N/A') {
                        if (!isset($uniqueMapels[$mapel_id])) {
                            $uniqueMapels[$mapel_id] = ['kode' => $mapel_id, 'nama_mapel' => $mapel_nama];
                        }
                    }
                }
            }
        }
        if ($asAssociativeArray) {
            $list = [];
            foreach ($uniqueMapels as $id => $data) {
                $list[$id] = $data['nama_mapel'];
            }
            return $list;
        }
        return array_values($uniqueMapels);
    }

    /**
     * Mengambil data umum untuk form DAN data grid untuk rombel yang dipilih.
     */
    private function getCommonData(Request $request)
    {
        $rombelFilterId = $request->query('rombel_id_filter');

        $jadwalGrid = [];
        $uniqueTimeSlotsRaw = [];
        $daftarHari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

        if ($rombelFilterId) {
            // Eager load 'gtk' (Membutuhkan relasi di Model JadwalPelajaran)
            $jadwalsForGrid = JadwalPelajaran::with('gtk') 
                ->where('rombel_id', $rombelFilterId)
                ->orderBy('jam_mulai')
                ->get();

            foreach ($jadwalsForGrid as $jadwal) {
                $jamMulai = \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i');
                $jamSelesai = \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i');
                $timeSlotKey = $jamMulai . ' - ' . $jamSelesai;

                if (!isset($uniqueTimeSlotsRaw[$timeSlotKey])) {
                    $uniqueTimeSlotsRaw[$timeSlotKey] = $jamMulai;
                }
                $jadwalGrid[$timeSlotKey][$jadwal->hari] = $jadwal;
            }
        }
        
        asort($uniqueTimeSlotsRaw);
        $uniqueTimeSlots = array_keys($uniqueTimeSlotsRaw); 

        return [
            'jadwalGrid' => $jadwalGrid,
            'uniqueTimeSlots' => $uniqueTimeSlots,
            'daftarHari' => $daftarHari,
            'allRombels' => Rombel::orderBy('nama')->get(), 
            'rombelFilterId' => $rombelFilterId, 
            'mapelList' => $this->getAllUniqueMapels(true),
            'rombels' => Rombel::orderBy('nama')->get(), 
            'mapels' => $this->getAllUniqueMapels(), 
            'gtks' => Gtk::orderBy('nama')->get(),
            'tapelAktif' => Tapel::where('is_active', 1)->first(), 
        ];
    }


    /**
     * Menampilkan view index.
     */
    public function index(Request $request)
    {
        $data = $this->getCommonData($request);
        return view('admin.akademik.jadwal-pelajaran.index', $data, ['jadwalToEdit' => null]);
    }

    /**
     * [INI PERBAIKANNYA]
     * Menyimpan data jadwal pelajaran dengan PTK ID (UUID) yang benar.
     */
    public function store(Request $request)
    {
        // [PERBAIKAN VALIDASI] Mengganti 'ptk_id' => 'gtk_id'
        $validatedData = $request->validate([
            'tahun_ajaran_id' => 'required|exists:tapel,id',
            'rombel_id' => 'required|exists:rombels,id',
            'mapel_id' => 'required|numeric',
            'gtk_id' => 'required|exists:gtks,id', // <-- INI YANG SUDAH DIPERBAIKI
            'hari' => 'required|string',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
        ]);

        $tapel = Tapel::find($validatedData['tahun_ajaran_id']);
        if (!$tapel) {
            return redirect()->back()->withInput()->with('error', 'Tahun Ajaran tidak valid.');
        }

        $semesterId = ($tapel->semester == 'Ganjil') ? 1 : (($tapel->semester == 'Genap') ? 2 : null);
        if (is_null($semesterId)) {
            throw ValidationException::withMessages(['tahun_ajaran_id' => 'Data semester pada Tahun Ajaran terpilih tidak valid.']);
        }

        $mapelList = $this->getAllUniqueMapels(true);
        $namaMapel = $mapelList[$validatedData['mapel_id']] ?? 'Mapel Tidak Ditemukan';

        // Ambil ptk_id (UUID) dari guru yang dipilih
        $guru = Gtk::find($validatedData['gtk_id']);
        if (!$guru || !$guru->ptk_id) {
            return back()->with('error', 'Data GTK tidak lengkap (PTK ID internal tidak ditemukan).');
        }
        $ptk_id_uuid = $guru->ptk_id; // Ini adalah UUID

        JadwalPelajaran::create([
            'tahun_ajaran_id' => $validatedData['tahun_ajaran_id'],
            'semester_id' => $semesterId,
            'rombel_id' => $validatedData['rombel_id'],
            'mata_pelajaran' => $namaMapel,
            'ptk_id' => $ptk_id_uuid, // [PERBAIKAN] Simpan UUID, bukan 'id' guru
            'hari' => $validatedData['hari'],
            'jam_mulai' => $validatedData['jam_mulai'],
            'jam_selesai' => $validatedData['jam_selesai'],
        ]);

        return redirect()->route('admin.akademik.jadwal-pelajaran.index', [
            'rombel_id_filter' => $validatedData['rombel_id']
        ])->with('success', 'Jadwal pelajaran berhasil ditambahkan.');
    }

    /**
     * Menampilkan form edit.
     */
    public function edit(Request $request, JadwalPelajaran $jadwalPelajaran)
    {
        if (!$request->has('rombel_id_filter')) {
            $request->merge(['rombel_id_filter' => $jadwalPelajaran->rombel_id]);
        }
        
        $data = $this->getCommonData($request);
        return view('admin.akademik.jadwal-pelajaran.index', $data, ['jadwalToEdit' => $jadwalPelajaran]);
    }

    /**
     * [INI PERBAIKANNYA]
     * Memperbarui data jadwal pelajaran dengan PTK ID (UUID) yang benar.
     */
    public function update(Request $request, JadwalPelajaran $jadwalPelajaran)
    {
        // [PERBAIKAN VALIDASI] Mengganti 'ptk_id' => 'gtk_id'
        $validatedData = $request->validate([
            'tahun_ajaran_id' => 'required|exists:tapel,id',
            'rombel_id' => 'required|exists:rombels,id',
            'mapel_id' => 'required|numeric',
            'gtk_id' => 'required|exists:gtks,id', // <-- INI YANG SUDAH DIPERBAIKI
            'hari' => 'required|string',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
        ]);

        $tapel = Tapel::find($validatedData['tahun_ajaran_id']);
        if (!$tapel) {
            return redirect()->back()->withInput()->with('error', 'Tahun Ajaran tidak valid.');
        }

        $semesterId = ($tapel->semester == 'Ganjil') ? 1 : (($tapel->semester == 'Genap') ? 2 : null);
        if (is_null($semesterId)) {
            throw ValidationException::withMessages(['tahun_ajaran_id' => 'Data semester pada Tahun Ajaran terpilih tidak valid.']);
        }

        $mapelList = $this->getAllUniqueMapels(true);
        $namaMapel = $mapelList[$validatedData['mapel_id']] ?? 'Mapel Tidak Ditemukan';

        // Ambil ptk_id (UUID) dari guru yang dipilih
        $guru = Gtk::find($validatedData['gtk_id']);
        if (!$guru || !$guru->ptk_id) {
            return back()->with('error', 'Data GTK tidak lengkap (PTK ID internal tidak ditemukan).');
        }
        $ptk_id_uuid = $guru->ptk_id; // Ini adalah UUID

        $dataToUpdate = [
            'tahun_ajaran_id' => $validatedData['tahun_ajaran_id'],
            'semester_id' => $semesterId,
            'rombel_id' => $validatedData['rombel_id'],
            'mata_pelajaran' => $namaMapel,
            'ptk_id' => $ptk_id_uuid,
            'hari' => $validatedData['hari'],
            'jam_mulai' => $validatedData['jam_mulai'],
            'jam_selesai' => $validatedData['jam_selesai'],
        ];

        $jadwalPelajaran->update($dataToUpdate);

        return redirect()->route('admin.akademik.jadwal-pelajaran.index', [
            'rombel_id_filter' => $validatedData['rombel_id']
        ])->with('success', 'Jadwal pelajaran berhasil diperbarui.');
    }

    /**
     * Menghapus jadwal pelajaran.
     */
    public function destroy(Request $request, JadwalPelajaran $jadwalPelajaran)
    {
        $rombelId = $jadwalPelajaran->rombel_id;
        try {
            $jadwalPelajaran->delete();
            return redirect()->route('admin.akademik.jadwal-pelajaran.index', [
                'rombel_id_filter' => $request->query('rombel_id_filter', $rombelId)
            ])->with('success', 'Jadwal pelajaran berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('admin.akademik.jadwal-pelajaran.index', [
                'rombel_id_filter' => $request->query('rombel_id_filter', $rombelId)
            ])->with('error', 'Gagal menghapus jadwal: ' . $e->getMessage());
        }
    }
}