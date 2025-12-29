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
            ->where('status', 'Aktif'); // hanya yang aktif

        $query->when($request->search, function ($q, $search) {
            return $q->where(function ($sub) use ($search) {
                $sub->where('nama', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            });
        });

        // Pagination
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
        $query = Gtk::query()
            ->with('pengguna')
            ->whereIn('jenis_ptk_id', ['91', '93'])
            ->where('status', 'Aktif');

        $query->when($request->search, function ($q, $search) {
            return $q->where(function ($sub) use ($search) {
                $sub->where('nama', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            });
        });

        $perPage = $request->input('per_page', 15);
        if ($perPage === 'all') {
            $perPage = $query->count();
            if ($perPage == 0) $perPage = 15;
        }

        $tendiks = $query->latest()->paginate($perPage)->appends($request->all());
        return view('admin.kepegawaian.gtk.index_tendik', compact('tendiks'));
    }

    // --- FUNGSI EXPORT & PDF ---

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
        $qrCodeData = "Nama: " . $gtk->nama . "\nNUPTK: " . ($gtk->nuptk ?? '-');

        // Robust search for ptk_id inside JSON (fallbacks for different formatting)
        $rombelMengajar = Rombel::where('pembelajaran', 'like', '%"ptk_id":"' . $gtk->ptk_id . '"%')
                                ->orWhere('pembelajaran', 'like', '%"ptk_id": "' . $gtk->ptk_id . '"%')
                                ->get();

        $rombelWali = Rombel::where('ptk_id', $gtk->ptk_id)->first();
        $tugasTerbaru = TugasPegawai::where('pegawai_id', $gtk->ptk_id)->orderBy('tmt', 'desc')->first();

        $pdf = Pdf::loadView('admin.kepegawaian.gtk.gtk_pdf', compact(
            'gtk', 'sekolah', 'qrCodeData', 'rombelWali', 'rombelMengajar', 'tugasTerbaru'
        ));

        $fileName = 'Profil GTK - ' . $gtk->nama . '.pdf';
        return $pdf->stream($fileName);
    }

    public function cetakPdfMultiple(Request $request)
    {
        $request->validate(['ids' => 'required|string']);
        $ids = explode(',', $request->input('ids'));
        $gtks = Gtk::with('pengguna')->whereIn('id', $ids)->get();
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
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:5120', // naik ke 5MB sebab kita kompres
            'tandatangan' => 'nullable|image|mimes:png|max:1024',
        ]);

        $gtk = Gtk::with('pengguna')->findOrFail($id);

        if ($request->hasFile('foto')) {
            // hapus foto lama
            if ($gtk->foto && Storage::disk('public')->exists($gtk->foto)) {
                Storage::disk('public')->delete($gtk->foto);
            }

            $file = $request->file('foto');
            $fileName = time() . '_' . preg_replace('/[^A-Za-z0-9\-]/', '_', $gtk->nama) . '.jpg';
            $path = 'gtk_media/foto/' . $fileName;

            $manager = new ImageManager(new Driver());
            $image = $manager->make($file);

            // resize and encode as jpeg
            $image->resize(400, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            $encoded = (string) $image->encode('jpg', 70);
            Storage::disk('public')->put($path, $encoded);
            $gtk->foto = $path;
        }

        if ($request->hasFile('tandatangan')) {
            if ($gtk->tandatangan && Storage::disk('public')->exists($gtk->tandatangan)) {
                Storage::disk('public')->delete($gtk->tandatangan);
            }
            $path = $request->file('tandatangan')->store('gtk_media/tandatangan', 'public');
            $gtk->tandatangan = $path;
        }

        $gtk->save();
        return back()->with('success', 'Media ' . $gtk->nama . ' berhasil diperbarui!');
    }

    // --- FITUR CETAK KARTU ---

    public function indexCetakKartu(Request $request)
    {
        $query = Gtk::query()->with('pengguna');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('nuptk', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('jenis_ptk_id_str', $request->status);
        }

        $gtks = $query->orderBy('nama', 'asc')->paginate(10);
        $sekolah = Sekolah::first();

        return view('admin.kepegawaian.gtk.index_cetak_kartu', compact('gtks', 'sekolah'));
    }

    public function inactive(Request $request)
    {
        $query = Gtk::query()->where('status', '!=', 'Aktif');

        $query->when($request->search, function ($q, $search) {
            return $q->where(function ($sub) use ($search) {
                $sub->where('nama', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            });
        });

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
                  ->orWhere('nik', 'like', "%{$search}%");
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

        $sekolah = Sekolah::first();

        if (!$sekolah) {
            $sekolah = new Sekolah();
        }

        if ($request->hasFile('background_kartu')) {
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
        if (!session()->has('ptk_id')) {
            abort(403, 'Akses ditolak');
        }

        $gtk = Gtk::with('pengguna')->where('ptk_id', session('ptk_id'))->firstOrFail();

        $pelanggaranGuru = PelanggaranNilaiGtk::where('nama_guru', $gtk->nama)
            ->with('detailPoinGtk')
            ->orderBy('tanggal', 'desc')
            ->get();

        $totalPoin = $pelanggaranGuru->sum('poin');

        $sanksiAktif = PelanggaranSanksiGtk::where('poin_min', '<=', $totalPoin)
            ->where('poin_max', '>=', $totalPoin)
            ->first();

        return view('admin.personal.guru.pelanggaran', [
            'namaGuru'        => $gtk->nama,
            'pelanggaranGuru' => $pelanggaranGuru,
            'totalPoin'       => $totalPoin,
            'sanksiAktif'     => $sanksiAktif,
            'guruList'        => collect(),
        ]);
    }

    public function registerKeluar(Request $request, $id)
    {
        $request->validate([
            'status' => 'required',
            'tanggal_keluar' => 'required|date',
            'alasan' => 'nullable|string',
        ]);

        $gtk = Gtk::findOrFail($id);

        $gtk->mutasiKeluar()->create([
            'status' => $request->status,
            'tanggal_keluar' => $request->tanggal_keluar,
            'keterangan' => $request->alasan,
        ]);

        $gtk->status = $request->status;
        $gtk->save();

        return redirect()->back()->with('success', 'Data keluar GTK ' . $gtk->nama . ' berhasil dicatat dan status diperbarui.');
    }
}
