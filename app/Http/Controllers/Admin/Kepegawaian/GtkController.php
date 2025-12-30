<?php

namespace App\Http\Controllers\Admin\Kepegawaian;

use App\Models\Gtk;
use App\Models\Sekolah;
use App\Models\Rombel;
use App\Models\TugasPegawai;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\GtkExport;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
class GtkController extends Controller
{
    // --- FUNGSI DAFTAR PEGAWAI (Data Guru & Tendik) ---

    public function indexGuru(Request $request)
    {
      $query = Gtk::query()
        ->with('pengguna')
        ->where('jenis_ptk_id_str', 'Guru')
        ->where('status', 'Aktif'); // <--- Filter hanya yang aktif

        $query->when($request->search, function ($q, $search) {
            return $q->where(function ($sub) use ($search) {
                $sub->where('nama', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            });
        });
  $query->orderBy('nama', 'asc');
        // --- LOGIKA PAGINATION DINAMIS ---
        $perPage = $request->input('per_page', 15);

        if ($perPage === 'all') {
            $perPage = $query->count();
            if ($perPage == 0) $perPage = 15;
        }

        $gurus = $query->latest()->paginate($perPage)->appends($request->all());

        return view('admin.kepegawaian.gtk.index_guru', compact('gurus'));
    }

    public function indexTendik(Request $request)
    {
        // 1. Filter Data (Gabungkan Kepsek (91) & Tendik (93))
       $query = Gtk::query()
        ->with('pengguna')
        ->whereIn('jenis_ptk_id', ['91', '93'])
        ->where('status', 'Aktif'); // <--- Filter hanya yang aktif

        $query->when($request->search, function ($q, $search) {
            return $q->where(function ($sub) use ($search) {
                $sub->where('nama', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            });
        });

        // 2. LOGIKA PAGINATION DINAMIS
        $perPage = $request->input('per_page', 15);

        if ($perPage === 'all') {
            $perPage = $query->count();
            if ($perPage == 0) $perPage = 15;
        }

        $tendiks = $query->latest()->paginate($perPage)->appends($request->all());

        return view('admin.kepegawaian.gtk.index_tendik', compact('tendiks'));
    }

    // --- FUNGSI EXPORT & PDF BAWAAN ---

    public function exportGuruExcel(Request $request)
    {
        $query = Gtk::query()->with('pengguna')->where('jenis_ptk_id_str', 'Guru');

        if ($request->has('ids')) {
            $ids = explode(',', $request->input('ids'));
            $query->whereIn('id', $ids);
        } elseif ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($sub) use ($search) {
                $sub->where('nama', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        $query->latest();
        $fileName = 'Data_Guru_Sekull.xlsx';
        return Excel::download(new GtkExport($query), $fileName);
    }

    public function exportTendikExcel(Request $request)
    {
        $query = Gtk::query()->with('pengguna')->whereIn('jenis_ptk_id', ['91', '93']);

        if ($request->has('ids')) {
            $ids = explode(',', $request->input('ids'));
            $query->whereIn('id', $ids);
        } elseif ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($sub) use ($search) {
                $sub->where('nama', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        $query->latest();
        $fileName = 'Data_Tendik_Sekull.xlsx';
        return Excel::download(new GtkExport($query), $fileName);
    }

    public function showMultiple(Request $request)
    {
        $request->validate(['ids' => 'required|string']);
        $ids = explode(',', $request->input('ids'));
        $gtks = Gtk::with('pengguna')->whereIn('id', $ids)->get();
        return view('admin.kepegawaian.gtk.show_multiple', compact('gtks'));
    }

 public function cetakPdf($id)
{
    $gtk = Gtk::with('pengguna')->findOrFail($id);
    $sekolah = Sekolah::first();

    // Gunakan LIKE untuk mencari ptk_id di dalam string JSON sebagai fallback yang lebih kuat
    $rombelMengajar = Rombel::where('pembelajaran', 'like', '%"ptk_id":"' . $gtk->ptk_id . '"%')
                            ->orWhere('pembelajaran', 'like', '%"ptk_id": "' . $gtk->ptk_id . '"%')
                            ->get();

    $rombelWali = Rombel::where('ptk_id', $gtk->ptk_id)->first();
    $tugasTerbaru = TugasPegawai::where('pegawai_id', $gtk->ptk_id)->orderBy('tmt', 'desc')->first();

    $pdf = Pdf::loadView('admin.kepegawaian.gtk.gtk_pdf', compact(
        'gtk', 'sekolah', 'rombelWali', 'rombelMengajar', 'tugasTerbaru'
    ));

    return $pdf->stream('Profil GTK - ' . $gtk->nama . '.pdf');
}

    public function cetakPdfMultiple(Request $request)
    {
        $request->validate(['ids' => 'required|string']);
        $ids = explode(',', $request->input('ids'));
        $gtks = Gtk::with('pengguna')->find($ids);
        $sekolah = Sekolah::first();

        $rombelMengajar = Rombel::all();

        $pdf = Pdf::loadView('admin.kepegawaian.gtk.gtk_pdf_multiple', compact('gtks', 'sekolah', 'rombelMengajar'));
        $fileName = 'Kumpulan_Profil_GTK.pdf';
        return $pdf->stream($fileName);
    }

    // --- FUNGSI UPDATE DATA & UPLOAD MEDIA ---

    public function updateData(Request $request, $id)
    {
        $gtk = Gtk::with('pengguna')->findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255',
            'nik' => 'nullable|numeric',
            'email' => 'nullable|email',
            'tanggal_lahir' => 'nullable|date',
        ]);

        try {
            $data = $request->except(['_token', '_method', 'email', 'no_hp']);

            // Update GTK attributes
            $gtk->update($data);

            // Sinkronkan email dan no_hp ke tabel penggunas melalui relasi ptk_id
            if ($request->filled('email') || $request->filled('no_hp')) {
                $gtk->pengguna()->updateOrCreate(
                    ['ptk_id' => $gtk->ptk_id],
                    ['email' => $request->input('email'), 'no_hp' => $request->input('no_hp')]
                );
            }

            return redirect()->back()->with('success', 'Data lengkap ' . $gtk->nama . ' berhasil diperbarui!');

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['msg' => 'Gagal menyimpan data: ' . $e->getMessage()])->withInput();
        }
    }

   public function uploadMedia(Request $request, $id)
{
    $request->validate([
        'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:5120', // Limit naik ke 5MB karena akan dikompres
        'tandatangan' => 'nullable|image|mimes:png|max:1024',
    ]);

    $gtk = Gtk::with('pengguna')->findOrFail($id);

    if ($request->hasFile('foto')) {
        // 1. Hapus foto lama jika ada
        if ($gtk->foto && Storage::disk('public')->exists($gtk->foto)) {
            Storage::disk('public')->delete($gtk->foto);
        }

        $file = $request->file('foto');
        $fileName = time() . '_' . $gtk->nama . '.jpg'; // Simpan sebagai jpg untuk kompresi terbaik
        $path = 'gtk_media/foto/' . $fileName;

        // 2. Proses Kompresi Menggunakan Intervention Image
        // Inisialisasi Manager (Contoh untuk Intervention v3)
        $manager = new ImageManager(new Driver());
        $image = $manager->read($file);

        $image->scale(width: 400);

        // Simpan ke Storage dengan kualitas 70% (sangat menghemat ruang)
        $encoded = $image->toJpeg(70);
        Storage::disk('public')->put($path, (string) $encoded);

        $gtk->foto = $path;
    }

    // Bagian tanda tangan tetap (atau bisa dikompres juga dengan cara yang sama)
    if ($request->hasFile('tandatangan')) {
        if ($gtk->tandatangan && Storage::disk('public')->exists($gtk->tandatangan)) {
            Storage::disk('public')->delete($gtk->tandatangan);
        }
        $path = $request->file('tandatangan')->store('gtk_media/tandatangan', 'public');
        $gtk->tandatangan = $path;
    }

    $gtk->save();
    return back()->with('success', 'Foto ' . $gtk->nama . ' berhasil dikompres dan disimpan!');
}

    // --- FITUR BARU: CETAK KARTU ID ---

    public function indexCetakKartu(Request $request)
    {
        $query = Gtk::query()->with('pengguna');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('nuptk', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%"); // <--- UPDATE: Tambahkan pencarian NIK
            });
        }

        if ($request->filled('status')) {
             $query->where('jenis_ptk_id_str', $request->status);
        }

        $gtks = $query->orderBy('nama', 'asc')->paginate(10);
        $sekolah = Sekolah::first();

        return view('admin.kepegawaian.gtk.index_cetak_kartu', compact('gtks', 'sekolah'));
    }

    // --- MENU: GTK Non-Aktif ---
    public function inactive(Request $request)
    {
        // Ambil GTK yang statusnya bukan 'Aktif'
        $query = Gtk::query()->where('status', '!=', 'Aktif');

        $query->when($request->search, function ($q, $search) {
            return $q->where(function ($sub) use ($search) {
                $sub->where('nama', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            });
        });

        // --- LOGIKA PAGINATION DINAMIS ---
        $perPage = $request->input('per_page', 15);

        if ($perPage === 'all') {
            $perPage = $query->count();
            if ($perPage == 0) $perPage = 15;
        }

        $gtks = $query->orderBy('nama', 'asc')->paginate($perPage)->appends($request->all());
        $sekolah = Sekolah::first();

        return view('admin.kepegawaian.gtk.index_nonaktif', compact('gtks', 'sekolah'));
    }

    public function exportInactiveExcel(Request $request)
    {
        $query = Gtk::query()->with('pengguna')->where('status', '!=', 'Aktif');

        if ($request->has('ids')) {
            $ids = explode(',', $request->input('ids'));
            $query->whereIn('id', $ids);
        } elseif ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($sub) use ($search) {
                $sub->where('nama', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        $query->latest();
        $fileName = 'Data_GTK_Nonaktif.xlsx';
        return Excel::download(new GtkExport($query), $fileName);
    }

    public function cetakSemua(Request $request)
    {
        $query = Gtk::query()->with('pengguna');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('nuptk', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%"); // <--- UPDATE: Tambahkan pencarian NIK
            });
        }

        if ($request->filled('status')) {
            $query->where('jenis_ptk_id_str', $request->status);
        }

        $gtks = $query->orderBy('nama', 'asc')->get();
        $sekolah = Sekolah::first();

        return view('admin.kepegawaian.gtk.cetak_kartu_massal', compact('gtks', 'sekolah'));
    }

    public function cetakKartu($id)
    {
        $gtk = Gtk::with('pengguna')->findOrFail($id);
        $sekolah = Sekolah::first();
        $gtks = collect([$gtk]);

        return view('admin.kepegawaian.gtk.cetak_kartu_massal', compact('gtks', 'sekolah'));
    }

    public function uploadBackgroundKartu(Request $request)
{
    $request->validate([
        'background_kartu' => 'required|image|mimes:jpeg,png,jpg|max:2048',
    ]);

    // Ambil sekolah yang sudah ada, jangan dipaksa ID 1 jika tidak yakin
    $sekolah = Sekolah::first();

    if (!$sekolah) {
        $sekolah = new Sekolah();
    }

    if ($request->hasFile('background_kartu')) {
        // Hapus file lama jika ada
        if ($sekolah->background_kartu && Storage::disk('public')->exists($sekolah->background_kartu)) {
            Storage::disk('public')->delete($sekolah->background_kartu);
        }

        $path = $request->file('background_kartu')->store('sekolah_media/background', 'public');
        $sekolah->background_kartu = $path;
        $sekolah->save();
    }

    return back()->with('success', 'Background kartu berhasil diupdate!');
}

    // Personal
    public function profil()
    {
        if (!auth()->check() || !session()->has('ptk_id')) {
            abort(403);
        }

        $gtk = Gtk::with('pengguna')->where('ptk_id', session('ptk_id'))->firstOrFail();
        $gtks = collect([$gtk]);

        return view('admin.personal.guru.profil', compact('gtks'));
    }

    public function pelanggaran()
    {
        // ===============================
        // AMBIL GTK BERDASARKAN LOGIN
        // ===============================
        if (!session()->has('ptk_id')) {
            abort(403, 'Akses ditolak');
        }

        $gtk = Gtk::with('pengguna')->where('ptk_id', session('ptk_id'))->firstOrFail();

        // ===============================
        // AMBIL DATA PELANGGARAN
        // ===============================
        $pelanggaranGuru = PelanggaranNilaiGtk::where('nama_guru', $gtk->nama)
            ->with('detailPoinGtk')
            ->orderBy('tanggal', 'desc')
            ->get();

        // ===============================
        // HITUNG TOTAL POIN
        // ===============================
        $totalPoin = $pelanggaranGuru->sum('poin');

        // ===============================
        // AMBIL SANKSI AKTIF
        // ===============================
        $sanksiAktif = PelanggaranSanksiGtk::where('poin_min', '<=', $totalPoin)
            ->where('poin_max', '>=', $totalPoin)
            ->first();

        return view('admin.personal.guru.pelanggaran', [
            'namaGuru'        => $gtk->nama,
            'pelanggaranGuru' => $pelanggaranGuru,
            'totalPoin'       => $totalPoin,
            'sanksiAktif'     => $sanksiAktif,
            'guruList'        => collect(), // biar blade gak error
        ]);
    }
public function registerKeluar(Request $request, $id)
{
    $request->validate([
        'status' => 'required', // misal: Pensiun, Resign, Diberhentikan
        'tanggal_keluar' => 'required|date',
        'alasan' => 'nullable|string',
    ]);

    $gtk = Gtk::findOrFail($id);

    // 1. Simpan ke tabel mutasi_keluar (Logika Anda yang lama)
    $gtk->mutasiKeluar()->create([
        'status' => $request->status,
        'tanggal_keluar' => $request->tanggal_keluar,
        'keterangan' => $request->alasan,
    ]);
    $gtk->status = $request->status;
    $gtk->save(); // <--- INI YANG PENTING
    if ($gtk->pengguna) {
        // $gtk->pengguna->update(['status' => 0]);
    }

    return redirect()->back()
                     ->with('success', 'Data keluar GTK ' . $gtk->nama . ' berhasil dicatat dan status diperbarui.');
}

}
