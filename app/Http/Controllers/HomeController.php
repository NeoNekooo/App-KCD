<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 

// --- Panggil Semua Model Landing Page ---
use App\Models\LandingSlider;
use App\Models\SambutanKepalaSekolah;
use App\Models\Fasilitas;
use App\Models\Jurusan;
use App\Models\Berita;
use App\Models\Prestasi;
use App\Models\Galeri;
use App\Models\Mitra;
use App\Models\Testimoni;
use App\Models\Ekstrakurikuler;
use App\Models\Agenda;
use App\Models\VideoProfil;
use App\Models\Sekolah; // Tambahkan ini

class HomeController extends Controller
{
    /**
     * Menampilkan Halaman Utama Website Sekolah (Landing Page)
     */
    public function index()
    {
        // 1. Ambil Slider Utama
        $sliders = LandingSlider::orderBy('urutan', 'asc')->get();

        // 2. Ambil Sambutan Kepala Sekolah
        $sambutan = SambutanKepalaSekolah::first();

        // 3. Ambil Video Profil Sekolah
        $video = VideoProfil::first();
                             
        // 4. Ambil Daftar Jurusan
        $jurusans = Jurusan::all();

        // 5. Ambil Fasilitas (Ambil 6 terbaru untuk preview di home)
        $fasilitas = Fasilitas::latest()->take(6)->get();

        // 6. Ambil Berita Terbaru (Ambil 3)
        $beritas = Berita::latest()->take(3)->get(); 

        // 7. Ambil Prestasi (Ambil 6 terbaru untuk beranda)
        $prestasis = Prestasi::latest()->take(6)->get();

        // 8. Ambil Galeri Kegiatan (Ambil 8 terbaru)
        $galeris = Galeri::latest()->take(8)->get();

        // 9. Ambil Mitra Industri
        $mitras = Mitra::all();

        // 10. Ambil Testimoni (Hanya yang statusnya PUBLISHED)
        $testimonis = Testimoni::where('is_published', true)
                        ->latest()
                        ->get();

        // 11. Ambil Ekstrakurikuler
        $ekskuls = Ekstrakurikuler::all();

        // 12. Ambil Agenda Sekolah
        $agendas = Agenda::select('id', 'judul', 'tanggal_mulai', 'tanggal_selesai', 'kategori', 'deskripsi')
                        ->get();

        // --- KIRIM SEMUA DATA KE VIEW HOME ---
        return view('landing.web.home', compact(
            'sliders', 
            'sambutan', 
            'video',
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
     * [BARU] Menampilkan Halaman Profil Sekolah Lengkap
     */
    public function profilSekolah()
    {
        // 1. Ambil Identitas Sekolah (Nama, Alamat, dll)
        $sekolah = Sekolah::first();
        
        // 2. Ambil Data Sambutan (Disini tersimpan Visi, Misi, Program Kerja)
        $sambutan = SambutanKepalaSekolah::first();

        // 3. Video Profil
        $video = VideoProfil::first();

        return view('landing.web.profil_sekolah', compact('sekolah', 'sambutan', 'video'));
    }

    /**
     * Menampilkan Halaman Kontak
     */
    public function kontak()
    {
        $sekolah = \App\Models\Sekolah::first();
        // Video profil opsional untuk footer
        $video = \App\Models\VideoProfil::first();

        return view('landing.web.kontak', compact('sekolah', 'video'));
    }

    /**
     * Menampilkan Halaman Detail Jurusan
     */
    public function showJurusan($id)
    {
        $jurusan = Jurusan::findOrFail($id);
        $video = VideoProfil::first(); // Opsional untuk footer

        return view('landing.web.jurusan_detail', compact('jurusan', 'video'));
    }

    /**
     * Menampilkan Halaman Detail Berita
     */
    public function showBerita($id)
    {
        // 1. Ambil berita utama yang sedang dibuka
        $berita = Berita::findOrFail($id);

        // 2. Ambil 5 berita terbaru lainnya (kecuali yang sedang dibuka) untuk sidebar
        $beritaTerbaru = Berita::where('id', '!=', $id)
                                ->latest()
                                ->take(5)
                                ->get();
        
        $video = VideoProfil::first(); 

        return view('landing.web.berita_detail', compact('berita', 'beritaTerbaru', 'video'));
    }

    /**
     * Menampilkan Halaman Detail Prestasi
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
     * Menampilkan Halaman Semua Fasilitas
     */
    public function fasilitasSemua()
    {
        $fasilitas = Fasilitas::latest()->get(); 
        $video = VideoProfil::first();

        return view('landing.web.fasilitas_semua', compact('fasilitas', 'video'));
    }

    /**
     * Menampilkan Halaman Galeri Lengkap
     */
    public function galeriLengkap()
    {
        // Ambil semua data galeri dengan pagination (misal 12 per halaman)
        $galeris = Galeri::latest()->paginate(12);
        
        $video = VideoProfil::first(); 

        return view('landing.web.galeri_index', compact('galeris', 'video'));
    }

    /**
     * Menampilkan Halaman Sambutan Kepala Sekolah Lengkap
     */
    public function sambutanLengkap()
    {
        $sambutan = SambutanKepalaSekolah::first();
        $video = VideoProfil::first();

        return view('landing.web.sambutan_lengkap', compact('sambutan', 'video'));
    }

    /**
     * Menyimpan Testimoni Baru dari Pengunjung
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
                'nama'          => $request->nama,
                'status'        => $request->status, 
                'isi'           => $request->isi,    
                'is_published'  => false,
                'foto'          => null,
            ]);

            return redirect()->back()->with('success_testimoni', 'Terima kasih! Testimoni Anda berhasil dikirim dan menunggu persetujuan Admin.');
        
        } catch (\Exception $e) {
            return redirect()->back()->with('error_testimoni', 'Maaf, terjadi kesalahan saat mengirim testimoni. Silakan coba lagi.');
        }
    }
}