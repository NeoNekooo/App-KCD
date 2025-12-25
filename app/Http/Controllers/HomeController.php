<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

// --- MODEL ---
use App\Models\Sekolah;
use App\Models\LandingSlider;
use App\Models\SambutanKepalaSekolah;
use App\Models\VideoProfil;
use App\Models\Jurusan;
use App\Models\Fasilitas;
use App\Models\Berita;
use App\Models\Prestasi;
use App\Models\Galeri;
use App\Models\Mitra;
use App\Models\Testimoni;
use App\Models\Ekstrakurikuler;
use App\Models\Agenda;

class HomeController extends Controller
{
    /**
     * Menampilkan Halaman Utama (Home / Landing Page)
     */
    public function index()
    {
        // 1. Identitas Sekolah & Video Profil
        $sekolah = Sekolah::first();
        $video   = VideoProfil::first();

        // 2. Slider Banner Utama (Diurutkan berdasarkan 'urutan')
        $sliders = LandingSlider::orderBy('urutan', 'asc')->get();

        // 3. Sambutan Kepala Sekolah
        $sambutanRaw = SambutanKepalaSekolah::first();
        // Siapkan objek sambutan agar view lebih bersih (opsional, tapi bagus untuk kontrol data)
        $sambutan = $sambutanRaw ? (object) [
            'judul_sambutan'      => $sambutanRaw->judul_sambutan,
            'nama_kepala_sekolah' => $sambutanRaw->nama_kepala_sekolah,
            'isi_sambutan'        => $sambutanRaw->isi_sambutan, // Kirim raw HTML untuk halaman detail
            'isi_singkat'         => Str::limit(strip_tags($sambutanRaw->isi_sambutan), 400), // Versi plain text untuk Home
            'foto'                => $sambutanRaw->foto,
            'visi'                => $sambutanRaw->visi,
            'misi'                => $sambutanRaw->misi,
            'program_kerja'       => $sambutanRaw->program_kerja,
        ] : null;

        // 4. Daftar Jurusan (Limit 3 untuk Home)
        $jurusans = Jurusan::latest()->take(3)->get();

        // 5. Fasilitas (Limit 6)
        $fasilitas = Fasilitas::latest()->take(6)->get();

        // 6. Berita Terbaru (Limit 3)
        $beritas = Berita::latest()->take(3)->get();

        // 7. Prestasi Terbaru (Limit 6)
        $prestasis = Prestasi::latest()->take(6)->get();

        // 8. Galeri Kegiatan (Limit 8)
        $galeris = Galeri::latest()->take(8)->get();

        // 9. Mitra Industri (Semua)
        $mitras = Mitra::all();

        // 10. Testimoni (Hanya yang dipublish)
        $testimonis = Testimoni::where('is_published', true)->latest()->get();

        // 11. Ekstrakurikuler (Semua)
        $ekskuls = Ekstrakurikuler::all();

        // 12. Agenda Kegiatan (Yang belum lewat hari ini)
        $agendas = Agenda::whereDate('tanggal_mulai', '>=', now())
                         ->orderBy('tanggal_mulai', 'asc')
                         ->take(6)
                         ->get();

        return view('landing.web.home', compact(
            'sekolah',
            'video',
            'sliders',
            'sambutan',
            'jurusans',
            'fasilitas',
            'beritas',
            'prestasis',
            'galeris',
            'mitras',
            'testimonis',
            'ekskuls',
            'agendas'
        ));
    }

    /**
     * Halaman Profil Sekolah Lengkap
     */
    public function profilSekolah()
    {
        $sekolah  = Sekolah::first();
        $sambutan = SambutanKepalaSekolah::first();
        $video    = VideoProfil::first();

        return view('landing.web.profil_sekolah', compact('sekolah', 'sambutan', 'video'));
    }

    /**
     * Halaman Sambutan Lengkap (Detail)
     */
    public function sambutanLengkap()
    {
        $sambutan = SambutanKepalaSekolah::first();
        $video    = VideoProfil::first(); // Untuk footer jika perlu

        return view('landing.web.sambutan_lengkap', compact('sambutan', 'video'));
    }

    /**
     * Halaman Daftar Semua Jurusan
     */
    public function jurusanLengkap()
    {
        // Ambil SEMUA jurusan untuk halaman index jurusan
        $jurusans = Jurusan::latest()->get();
        $video    = VideoProfil::first();

        return view('landing.web.jurusan_lengkap', compact('jurusans', 'video'));
    }

    /**
     * Halaman Detail Jurusan
     */
    public function showJurusan($id)
    {
        $jurusan = Jurusan::findOrFail($id);
        $video   = VideoProfil::first();

        return view('landing.web.jurusan_detail', compact('jurusan', 'video'));
    }

    /**
     * Halaman Semua Fasilitas
     */
    public function fasilitasSemua()
    {
        $fasilitas = Fasilitas::latest()->get();
        $video     = VideoProfil::first();

        return view('landing.web.fasilitas_semua', compact('fasilitas', 'video'));
    }

    /**
     * Halaman Detail Berita
     */
    public function showBerita($id)
    {
        $berita = Berita::findOrFail($id);
        
        // Berita lain untuk sidebar (kecuali yang sedang dibuka)
        $beritaTerbaru = Berita::where('id', '!=', $id)
                               ->latest()
                               ->take(5)
                               ->get();
        
        $video = VideoProfil::first();

        return view('landing.web.berita_detail', compact('berita', 'beritaTerbaru', 'video'));
    }

    /**
     * Halaman Detail Prestasi
     */
    public function showPrestasi($id)
    {
        $prestasi = Prestasi::findOrFail($id);

        $prestasiLain = Prestasi::where('id', '!=', $id)
                                ->latest()
                                ->take(5)
                                ->get();

        $video = VideoProfil::first();

        return view('landing.web.prestasi_detail', compact('prestasi', 'prestasiLain', 'video'));
    }

    /**
     * Halaman Galeri Lengkap
     */
    public function galeriLengkap()
    {
        // Pagination 12 foto per halaman
        $galeris = Galeri::latest()->paginate(12);
        $video   = VideoProfil::first();

        return view('landing.web.galeri_index', compact('galeris', 'video'));
    }

    /**
     * Halaman Kontak
     */
    public function kontak()
    {
        $sekolah = Sekolah::first();
        $video   = VideoProfil::first();

        return view('landing.web.kontak', compact('sekolah', 'video'));
    }

    /**
     * Proses Simpan Testimoni (Pengunjung)
     */
    public function storeTestimoni(Request $request)
    {
        $request->validate([
            'nama'   => 'required|string|max:100',
            'status' => 'required|string|max:100',
            'isi'    => 'required|string|max:1000',
        ]);

        try {
            Testimoni::create([
                'nama'         => $request->nama,
                'status'       => $request->status, 
                'isi'          => $request->isi,    
                'is_published' => false, // Default tidak tampil (perlu approval admin)
                'foto'         => null,
            ]);

            return redirect()->back()->with('success_testimoni', 'Terima kasih! Testimoni Anda berhasil dikirim dan menunggu persetujuan Admin.');
        
        } catch (\Exception $e) {
            return redirect()->back()->with('error_testimoni', 'Maaf, terjadi kesalahan saat mengirim testimoni. Silakan coba lagi.');
        }
    }

    /**
     * Cek Status Jadwal PPDB (Redirect Logic)
     */
    public function cekStatusPpdb()
    {
        $sekarang = Carbon::now();

        // Cari Agenda PPDB yang SEDANG AKTIF hari ini
        $agendaAktif = Agenda::where('kategori', 'PPDB') 
                             ->whereDate('tanggal_mulai', '<=', $sekarang)
                             ->whereDate('tanggal_selesai', '>=', $sekarang)
                             ->first();

        if ($agendaAktif) {
            // Logic redirect ke subdomain spmb.*
            $host = request()->getHost();
            // Asumsi: jika local (localhost/127.0.0.1) mungkin perlu penanganan khusus
            // Jika live, ganti 'www' atau subdomain saat ini menjadi 'spmb'
            $spmbHost = preg_replace('/^[^.]+/', 'spmb', $host);
            
            // Fallback sederhana jika di localhost tanpa subdomain
            if ($host == '127.0.0.1' || $host == 'localhost') {
                 // Anda mungkin ingin mengarahkan ke route tertentu di app yang sama jika lokal
                 // return redirect()->route('ppdb.beranda'); 
                 // Tapi sesuai kode lama, kita coba construct URL:
                 $spmbUrl = request()->getScheme() . '://' . $host . '/ppdb'; // Contoh path lokal
            } else {
                 $spmbUrl = request()->getScheme() . '://' . $spmbHost;
            }
                    
            // Jika ingin konsisten dengan kode lama:
            // $spmbUrl = request()->getScheme() . '://' . $spmbHost;
            // return redirect()->away($spmbUrl);
            
            // SEMENTARA: Redirect ke halaman PPDB internal landing
            return redirect()->route('ppdb.beranda');
        }
        
        // JIKA TUTUP
        return redirect()->route('ppdb.tutup');
    }

    /**
     * Halaman Informasi PPDB Tutup
     */
    public function halamanTutup()
    {
        // Cari Agenda PPDB berikutnya (yang akan datang)
        $agendaAkanDatang = Agenda::where('kategori', 'PPDB')
                                  ->whereDate('tanggal_mulai', '>', Carbon::now())
                                  ->orderBy('tanggal_mulai', 'asc')
                                  ->first();
        
        return view('landing.web.closed', compact('agendaAkanDatang'));
    }
}