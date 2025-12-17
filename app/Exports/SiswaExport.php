<?php

namespace App\Exports;

use App\Models\Siswa;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class SiswaExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    /**
    * Ambil semua data siswa
    */
    public function collection()
    {
        // Mengambil data beserta relasi rombel untuk nama kelas
        return Siswa::with('rombel')->orderBy('nama', 'asc')->get();
    }

    /**
     * Judul Kolom (Header) Excel
     */
    public function headings(): array
    {
        return [
            'NO',
            // IDENTITAS UTAMA
            'NAMA LENGKAP',
            'NIPD',
            'NISN',
            'NIK',
            'JENIS KELAMIN',
            'TEMPAT LAHIR',
            'TANGGAL LAHIR',
            'AGAMA',
            'KEWARGANEGARAAN',
            
            // KONTAK
            'EMAIL',
            'NO HP',

            // ALAMAT LENGKAP (Data Baru)
            'ALAMAT JALAN',
            'RT',
            'RW',
            'DUSUN',
            'DESA/KELURAHAN',
            'KECAMATAN',
            'KABUPATEN/KOTA',
            'PROVINSI',
            'KODE POS',
            'JENIS TINGGAL',
            'TRANSPORTASI',
            'JARAK (KM)',
            'WAKTU TEMPUH (MENIT)',

            // ORANG TUA (Ayah)
            'NAMA AYAH',
            'THN LAHIR AYAH',
            'PENDIDIKAN AYAH',
            'PEKERJAAN AYAH',
            'PENGHASILAN AYAH',

            // ORANG TUA (Ibu)
            'NAMA IBU',
            'THN LAHIR IBU',
            'PENDIDIKAN IBU',
            'PEKERJAAN IBU',
            'PENGHASILAN IBU',

            // WALI
            'NAMA WALI',
            'PEKERJAAN WALI',

            // AKADEMIK & KELAS
            'KELAS SAAT INI',
            'TINGKAT',
            'SEKOLAH ASAL',
            'NPSN SEKOLAH ASAL',
            'NO IJAZAH',
            'NO SKHUN',
            'NO PESERTA UN',
            'STATUS SISWA',

            // BANTUAN
            'PENERIMA KIP',
            'NO KIP',
            'LAYAK PIP',
            'PENERIMA KPS',
            'NO KPS/KKS',
        ];
    }

    /**
     * Mapping Data per Baris
     */
    public function map($siswa): array
    {
        static $no = 0;
        $no++;

        // Logic Nama Kelas
        $namaKelas = $siswa->nama_rombel;
        if (empty($namaKelas)) $namaKelas = optional($siswa->rombel)->nama;
        if (empty($namaKelas)) $namaKelas = 'Belum Masuk Kelas';

        return [
            $no,
            // IDENTITAS
            $siswa->nama,
            $siswa->nipd,
            $siswa->nisn,
            "'".$siswa->nik, // Tanda kutip agar excel membaca sebagai string (biar angka tidak berubah)
            ($siswa->jenis_kelamin == 'L' || $siswa->jenis_kelamin == 'Laki-laki') ? 'Laki-laki' : 'Perempuan',
            $siswa->tempat_lahir,
            $siswa->tanggal_lahir ? Carbon::parse($siswa->tanggal_lahir)->format('d-m-Y') : '-',
            $siswa->agama_id_str,
            $siswa->kewarganegaraan,

            // KONTAK
            $siswa->email,
            "'".$siswa->nomor_telepon_seluler,

            // ALAMAT
            $siswa->alamat_jalan,
            $siswa->rt,
            $siswa->rw,
            $siswa->dusun ?? $siswa->nama_dusun,
            $siswa->desa_kelurahan,
            $siswa->kecamatan,
            $siswa->kabupaten_kota,
            $siswa->provinsi,
            $siswa->kode_pos,
            $siswa->jenis_tinggal_id_str,
            $siswa->alat_transportasi_id_str,
            $siswa->jarak_rumah_ke_sekolah_km,
            $siswa->waktu_tempuh_menit,

            // AYAH
            $siswa->nama_ayah,
            $siswa->tahun_lahir_ayah,
            $siswa->pendidikan_ayah_id_str,
            $siswa->pekerjaan_ayah_id_str,
            $siswa->penghasilan_ayah_id_str,

            // IBU
            $siswa->nama_ibu,
            $siswa->tahun_lahir_ibu,
            $siswa->pendidikan_ibu_id_str,
            $siswa->pekerjaan_ibu_id_str,
            $siswa->penghasilan_ibu_id_str,

            // WALI
            $siswa->nama_wali,
            $siswa->pekerjaan_wali_id_str,

            // AKADEMIK
            $namaKelas,
            $siswa->tingkat_pendidikan_id,
            $siswa->sekolah_asal,
            $siswa->npsn_sekolah_asal,
            $siswa->no_seri_ijazah,
            $siswa->no_seri_skhun,
            $siswa->no_ujian_nasional,
            $siswa->status ?? 'Aktif',

            // BANTUAN
            $siswa->penerima_kip,
            $siswa->no_kip,
            $siswa->layak_pip,
            $siswa->penerima_kps,
            $siswa->no_kps ?? $siswa->no_kks,
        ];
    }

    /**
     * Styling Header Excel agar Bold & Rapi
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Baris 1 (Header) di-bold
            1    => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}