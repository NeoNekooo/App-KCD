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

class RekapitulasiSekolahExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
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
        // 1. Siapkan Query Agregasi menggunakan Model agar FilterRegional Aktif
        $query = \App\Models\Sekolah::query()
            ->select(
                'kabupaten_kota',
                DB::raw("SUM(CASE WHEN status_sekolah_str LIKE '%Negeri%' THEN 1 ELSE 0 END) as total_negeri"),
                DB::raw("SUM(CASE WHEN status_sekolah_str LIKE '%Swasta%' THEN 1 ELSE 0 END) as total_swasta"),
                DB::raw("COUNT(id) as total_keseluruhan")
            )
            ->whereNotNull('kabupaten_kota');

        // Filter berdasarkan Jenjang (Status)
        if (!empty($this->jenjangTerpilih)) {
            $query->where('bentuk_pendidikan_id_str', $this->jenjangTerpilih);
        }

        // Ambil Data
        $data = $query->groupBy('kabupaten_kota')
                      ->orderBy('kabupaten_kota', 'asc')
                      ->get();

        // Paksa agar hasilnya menjadi integer eksplisit (bukan format yang mungkin hilang di Excel)
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
     * Judul Kolom di Excel (Header)
     */
    public function headings(): array
    {
        $judulHeader = "Rekapitulasi Jumlah Satuan Pendidikan";
        $teksJenjang = "Semua Jenjang";

        if (!empty($this->jenjangTerpilih)) {
            $teksJenjang = "Jenjang: " . $this->jenjangTerpilih;
        }

        return [
            [$judulHeader, '', '', '', ''], // Baris ke-1 (Judul utama)
            [$teksJenjang, '', '', '', ''], // Baris ke-2 (Info Jenjang)
            [''], // Baris ke-3 (Pemisah Kosong)
            [
                'NO.',
                'KABUPATEN / KOTA',
                'NEGERI',
                'SWASTA',
                'JUMLAH'
            ] // Baris ke-4 (Header Kolom Tabel)
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
     * Styling Tabel: Menambahkan Border dan Alignment yang rapi
     */
    public function styles(Worksheet $sheet)
    {
        // Merge cell untuk baris Judul (A1 sampai E1) dan Info Jenjang (A2 sampai E2)
        $sheet->mergeCells('A1:E1');
        $sheet->mergeCells('A2:E2');
        
        $highestRow = $sheet->getHighestRow();

        // Merge cell untuk teks 'JUMLAH KESELURUHAN' di row terakhir agar lebih elegan
        $sheet->mergeCells("A{$highestRow}:B{$highestRow}");

        // Tambahkan Border kotak tipis ke seluruh tabel (Mulai dari baris ke-4 sampai paling bawah)
        $styleArrayBorder = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];
        $sheet->getStyle('A4:E' . $highestRow)->applyFromArray($styleArrayBorder);

        return [
            // Style Header Laporan Utama (Baris 1)
            1 => [
                'font' => ['bold' => true, 'size' => 14], 
                'alignment' => ['horizontal' => 'center']
            ],
            // Style Info Jenjang (Baris 2)
            2 => [
                'font' => ['italic' => true, 'size' => 12, 'color' => ['argb' => 'FF444444']], 
                'alignment' => ['horizontal' => 'center']
            ],
            // Style Header Kolom Tabel (Baris 4 karena ada pergeseran)
            4 => [
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
            "A4:A" . ($highestRow - 1) => [
                'alignment' => ['horizontal' => 'center']
            ],
            // Angka Negeri, Swasta & Jumlah dibikin rata kanan
            "C4:E" . $highestRow => [
                'alignment' => ['horizontal' => 'right']
            ],
        ];
    }
}
