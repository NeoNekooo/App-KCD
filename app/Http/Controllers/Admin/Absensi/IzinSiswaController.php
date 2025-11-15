<?php

    namespace App\Http\Controllers\Admin\Absensi;

    use App\Http\Controllers\Controller;
    use App\Models\IzinSiswa;
    use App\Models\Siswa;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Str;

    class IzinSiswaController extends Controller
    {
        /**
         * Menampilkan daftar semua izin yang tercatat.
         */
        public function index()
        {
            $izinSiswa = IzinSiswa::with('siswa', 'pencatat')
                            ->latest()
                            ->paginate(15);
            
            return view('admin.absensi.izin_siswa.index', compact('izinSiswa'));
        }

        /**
         * Menampilkan form untuk membuat izin baru.
         */
        public function create()
        {
            $siswas = Siswa::orderBy('nama')->get(['id', 'nama']);
            return view('admin.absensi.izin_siswa.create', compact('siswas'));
        }

        /**
         * Menyimpan data izin baru ke database.
         */
        public function store(Request $request)
        {
            $request->validate([
                'siswa_id' => 'required|exists:siswas,id',
                'tanggal_izin' => 'required|date',
                'tipe_izin' => 'required|in:DATANG_TERLAMBAT,PULANG_AWAL,KELUAR_SEMENTARA',
                'alasan' => 'required|string',
                'jam_izin_mulai' => 'required_if:tipe_izin,PULANG_AWAL,KELUAR_SEMENTARA|nullable|date_format:H:i',
                'jam_izin_selesai' => 'required_if:tipe_izin,DATANG_TERLAMBAT,KELUAR_SEMENTARA|nullable|date_format:H:i',
            ]);

            $data = $request->all();
            $data['dicatat_oleh'] = Auth::id() ?? 1; // Ganti 1 dengan ID admin default jika perlu

            // Logika khusus untuk 'KELUAR_SEMENTARA'
            if ($request->tipe_izin === 'KELUAR_SEMENTARA') {
                $data['token_sementara'] = Str::uuid()->toString();
            }

            $izin = IzinSiswa::create($data);

            return redirect()->route('admin.absensi.izin-siswa.show', $izin->id)
                             ->with('success', 'Izin berhasil dicatat. Silakan berikan QR Code kepada siswa jika diperlukan.');
        }

        /**
         * Menampilkan detail satu izin, termasuk QR Code sementara.
         */
        public function show(IzinSiswa $izinSiswa)
        {
            $izinSiswa->load('siswa', 'pencatat');
            return view('admin.absensi.izin_siswa.show', compact('izinSiswa'));
        }

        /**
         * Menghapus data izin.
         */
        public function destroy(IzinSiswa $izinSiswa)
        {
            $izinSiswa->delete();
            return redirect()->route('admin.absensi.izin-siswa.index')
                             ->with('success', 'Data izin berhasil dihapus.');
        }
    }
    

