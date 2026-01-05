<?php

namespace App\Exports;

use App\Models\Sekolah;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SekolahExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    /**
     * Query data yang akan diexport
     */
    public function query()
    {
        $query = Sekolah::query();
        $req = $this->request;

        // --- FILTER (Sesuai Controller) ---
        $query->when($req->kabupaten_kota, fn($q) => $q->where('kabupaten_kota', $req->kabupaten_kota));
        $query->when($req->kecamatan, fn($q) => $q->where('kecamatan', $req->kecamatan));
        $query->when($req->jenjang, fn($q) => $q->where('bentuk_pendidikan_id_str', $req->jenjang));
        $query->when($req->status_sekolah, fn($q) => $q->where('status_sekolah_str', $req->status_sekolah));
        
        // --- SEARCH ---
        $query->when($req->search, function ($q, $search) {
            $q->where(function ($sub) use ($search) {
                $sub->where('nama', 'like', "%{$search}%")
                    ->orWhere('npsn', 'like', "%{$search}%");
            });
        });

        return $query->orderBy('nama', 'asc');
    }

    /**
     * Judul Kolom di Excel (Header)
     */
    public function headings(): array
    {
        return [
            'NPSN',
            'Nama Satuan Pendidikan',
            'Jenjang',
            'Status',
            'Alamat',
            'RT/RW',
            'Kecamatan',
            'Kabupaten/Kota',
            'Provinsi',
            'Email',
            'Website',
            'No. Telepon',
        ];
    }

    /**
     * Mapping Data per Baris
     */
    public function map($sekolah): array
    {
        // Gabung RT/RW biar rapi
        $rtrw = ($sekolah->rt && $sekolah->rw) ? "RT $sekolah->rt / RW $sekolah->rw" : '-';

        return [
            $sekolah->npsn,
            $sekolah->nama,
            $sekolah->bentuk_pendidikan_id_str,
            $sekolah->status_sekolah_str,
            $sekolah->alamat_jalan,
            $rtrw,
            $sekolah->kecamatan,
            $sekolah->kabupaten_kota,
            $sekolah->provinsi,
            $sekolah->email,
            $sekolah->website,
            $sekolah->nomor_telepon,
        ];
    }

    /**
     * Styling Sederhana (Header Bold)
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}