<?php

namespace App\Exports;

use App\Models\AntrianTamu;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AntrianExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return AntrianTamu::with('tujuanPegawai')->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'No Antrian',
            'Tanggal',
            'Nama Tamu',
            'Asal Instansi',
            'Jabatan',
            'Tujuan (Pegawai)',
            'Keperluan',
            'Status',
            'Waktu Selesai'
        ];
    }

    public function map($antrian): array
    {
        return [
            $antrian->nomor_antrian,
            $antrian->created_at->format('d-m-Y H:i'),
            $antrian->nama,
            $antrian->asal_instansi,
            $antrian->jabatan_pengunjung,
            $antrian->tujuanPegawai ? $antrian->tujuanPegawai->nama : '-',
            $antrian->keperluan,
            ucfirst($antrian->status),
            $antrian->waktu_selesai ? $antrian->waktu_selesai->format('H:i') : '-'
        ];
    }
}
