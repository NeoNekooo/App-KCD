<?php

namespace App\Exports;

use App\Models\Gtk; // Pastikan model Gtk di-import
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Database\Query\Builder; // Import ini

class GtkExport implements FromQuery, WithHeadings, WithMapping
{
    protected $query;

    /**
    * Kita gunakan constructor untuk menerima query
    * yang sudah difilter dari controller.
    */
    public function __construct($query)
    {
        $this->query = $query;
    }

    /**
    * Ini akan menjalankan query secara efisien.
    * @return \Illuminate\Database\Eloquent\Builder|Builder
    */
    public function query()
    {
        return $this->query;
    }

    /**
    * Ini adalah fungsi untuk memetakan data per baris.
    * (Ambil dari logika fputcsv lama Anda)
    *
    * @param Gtk $gtk
    * @return array
    */
    public function map($gtk): array
    {
        return [
            $gtk->nama,
            $gtk->jenis_kelamin,
            $gtk->tempat_lahir,
            $gtk->tanggal_lahir,
            $gtk->agama_id_str,
            $gtk->nik,
            $gtk->status_kepegawaian_id_str,
            $gtk->nip,
            $gtk->nuptk,
            $gtk->jenis_ptk_id_str,
            $gtk->jabatan_ptk_id_str,
            $gtk->tanggal_surat_tugas,
            $gtk->ptk_induk == 1 ? 'Induk' : 'Non-Induk',
            $gtk->pendidikan_terakhir,
            $gtk->bidang_studi_terakhir,
            $gtk->pangkat_golongan_terakhir,
            $gtk->rwy_pend_formal,
            $gtk->rwy_kepangkatan
        ];
    }

    /**
    * Ini adalah fungsi untuk header kolom.
    * (Ambil dari logika fputcsv lama Anda)
    *
    * @return array
    */
    public function headings(): array
    {
        return [
            'Nama Lengkap', 'Jenis Kelamin', 'Tempat Lahir', 'Tanggal Lahir', 'Agama', 'NIK',
            'Status Kepegawaian', 'NIP', 'NUPTK', 'Jenis PTK', 'Jabatan', 'Tanggal Surat Tugas', 'Status Induk',
            'Pendidikan Terakhir', 'Bidang Studi Terakhir', 'Pangkat/Golongan Terakhir',
            'Riwayat Pendidikan Formal', 'Riwayat Kepangkatan'
        ];
    }
}