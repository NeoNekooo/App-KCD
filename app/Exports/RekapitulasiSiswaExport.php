<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class RekapitulasiSiswaExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $jenjangTerpilih;
    private $rowNumber = 1;

    public function __construct($jenjangTerpilih = null)
    {
        $this->jenjangTerpilih = $jenjangTerpilih;
    }

    /**
     * Data Utama yang akan Dieksport
     */
    public function collection()
    {
        // 1. Query Agregasi: Join 'siswas' dengan 'sekolahs'
        // Menggunakan Model Siswa agar FilterRegional Aktif
        $query = \App\Models\Siswa::query()
            ->join('sekolahs', 'siswas.sekolah_id', '=', 'sekolahs.sekolah_id')
            ->where('siswas.status', 'Aktif') // Hanya siswa aktif
            ->select(
                'sekolahs.kabupaten_kota',
                DB::raw("SUM(CASE WHEN sekolahs.status_sekolah_str LIKE '%Negeri%' THEN 1 ELSE 0 END) as total_negeri"),
                DB::raw("SUM(CASE WHEN sekolahs.status_sekolah_str LIKE '%Swasta%' THEN 1 ELSE 0 END) as total_swasta"),
                DB::raw("COUNT(siswas.id) as total_keseluruhan")
            )
            ->whereNotNull('sekolahs.kabupaten_kota');

        // ==== Filter Kondisional Jenjang ====
        if (!empty($this->jenjangTerpilih)) {
            $query->where('sekolahs.bentuk_pendidikan_id_str', $this->jenjangTerpilih);
        }

        // Ambil Data
        $data = $query->groupBy('sekolahs.kabupaten_kota')
                      ->orderBy('sekolahs.kabupaten_kota', 'asc')
                      ->get();

        // Paksa cast integer
        $data->transform(function ($item) {
            $item->total_negeri = (int) $item->total_negeri;
            $item->total_swasta = (int) $item->total_swasta;
            $item->total_keseluruhan = (int) $item->total_keseluruhan;
            return $item;
        });

        // 2. Tambahkan Baris Grand Total di akhir koleksi
        $grandTotalNegeri = $data->sum('total_negeri');
        $grandTotalSwasta = $data->sum('total_swasta');
        $grandTotalAkhir = $data->sum('total_keseluruhan');

        $data->push((object)[
            'kabupaten_kota' => 'TOTAL JUMLAH',
            'total_negeri' => (int) $grandTotalNegeri,
            'total_swasta' => (int) $grandTotalSwasta,
            'total_keseluruhan' => (int) $grandTotalAkhir
        ]);

        return $data;
    }

    /**
     * Judul Kolom di Excel (Header Lapis Ganda)
     */
    public function headings(): array
    {
        $judulHeader = "Rekapitulasi Kesiswaan (Peserta Didik Aktif)";
        
        $infoFilter = "Semua Jenjang";
        if (!empty($this->jenjangTerpilih)) {
            $infoFilter = "Jenjang: " . $this->jenjangTerpilih;
        }

        return [
            [$judulHeader, '', '', '', ''], // Baris ke-1 (Judul Utama)
            [$infoFilter, '', '', '', ''], // Baris ke-2 (Info Filter)
            [''], // Baris ke-3 (Kosong Pemisah)
            [
                'NO.',
                'KABUPATEN / KOTA',
                'STATUS SEKOLAH', // Span 2 kolom ke Kanan
                '', // Dikosongkan karena di-merge oleh C4
                'JUMLAH'
            ], // Baris ke-4 (Header Atas)
            [
                '', // Kosong, di-merge dari Atas (A4)
                '', // Kosong, di-merge dari Atas (B4)
                'NEGERI',
                'SWASTA',
                ''  // Kosong, di-merge dari Atas (E4)
            ]  // Baris ke-5 (Header Bawah/Sub-Kolom)
        ];
    }

    /**
     * Maping Data ke Baris/Kolom
     */
    public function map($row): array
    {
        if ($row->kabupaten_kota === 'TOTAL JUMLAH') {
            return [
                'TOTAL JUMLAH',
                '', // Kosongkan kolom kedua karena akan dimerge
                (string) $row->total_negeri,
                (string) $row->total_swasta,
                (string) $row->total_keseluruhan
            ];
        }

        $nomor = $this->rowNumber++;
        $namaKota = strtoupper(str_replace(['Kab. ', 'Kota '], '', $row->kabupaten_kota));

        return [
            $nomor,
            $namaKota,
            // Cast string secara eksplisit untuk menjaga '0' tidak dihapus
            (string) $row->total_negeri,
            (string) $row->total_swasta,
            (string) $row->total_keseluruhan
        ];
    }

    /**
     * Styling Tabel: Border Lapis Ganda & Merge Cells Rumit
     */
    public function styles(Worksheet $sheet)
    {
        // 1. Merge Header Judul Laporan
        $sheet->mergeCells('A1:E1');
        $sheet->mergeCells('A2:E2');
        
        // 2. Merge Cells Group Header (Baris 4 dan 5)
        $sheet->mergeCells('A4:A5'); // NO dimerge ke bawah
        $sheet->mergeCells('B4:B5'); // KAB/KOTA dimerge ke bawah
        $sheet->mergeCells('C4:D4'); // STATUS SEKOLAH dimerge ke KANAN (membawahi Negeri/Swasta)
        $sheet->mergeCells('E4:E5'); // JUMLAH dimerge ke bawah
        
        $highestRow = $sheet->getHighestRow();

        // 3. Merge cell untuk teks 'TOTAL JUMLAH' di row terakhir
        $sheet->mergeCells("A{$highestRow}:B{$highestRow}");

        // 4. Set Border
        $styleArrayBorder = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];
        // Border diterapkan dari Baris 4 (Mulai Header Lapis Atas) sampai Baris Terakhir
        $sheet->getStyle('A4:E' . $highestRow)->applyFromArray($styleArrayBorder);

        return [
            // Style Header Utama (Baris 1)
            1 => [
                'font' => ['bold' => true, 'size' => 14], 
                'alignment' => ['horizontal' => 'center']
            ],
            // Style Info Jenjang (Baris 2)
            2 => [
                'font' => ['italic' => true, 'size' => 11, 'color' => ['argb' => 'FF555555']], 
                'alignment' => ['horizontal' => 'center']
            ],
            // Style Header Lapis 1 (Baris 4)
            4 => [
                'font' => ['bold' => true], 
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'FFD9DEE3']],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center']
            ],
            // Style Header Lapis 2 (Baris 5)
            5 => [
                'font' => ['bold' => true], 
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'FFD9DEE3']],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center']
            ],
            // Style Grand Total (Baris Terakhir)
            $highestRow => [
                'font' => ['bold' => true], 
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'FFCCCCCC']],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center']
            ],
            // Alignment isi tabel
            "A6:A" . ($highestRow - 1) => [
                'alignment' => ['horizontal' => 'center']
            ],
            // Angka Negeri, Swasta & Jumlah dibikin rata kanan
            "C6:E" . $highestRow => [
                'alignment' => ['horizontal' => 'right']
            ],
        ];
    }
}
