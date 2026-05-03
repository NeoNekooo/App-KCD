<?php

namespace App\Imports;

use App\Models\PkksKompetensi;
use App\Models\PkksIndikator;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PkksInstrumenImport implements ToCollection, WithHeadingRow
{
    protected $instrumenId;
    protected $currentKompetensiId = null;

    public function __construct($instrumenId)
    {
        $this->instrumenId = $instrumenId;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Jika kolom 'kompetensi' diisi, cari atau buat kompetensi baru
            if (!empty($row['kompetensi'])) {
                $kompetensi = PkksKompetensi::firstOrCreate([
                    'pkks_instrumen_id' => $this->instrumenId,
                    'nama' => $row['kompetensi']
                ], [
                    'urutan' => PkksKompetensi::where('pkks_instrumen_id', $this->instrumenId)->count() + 1
                ]);
                
                $this->currentKompetensiId = $kompetensi->id;
            }

            // Jika ada nomor dan kriteria, masukkan sebagai indikator
            if ($this->currentKompetensiId && !empty($row['kriteria'])) {
                PkksIndikator::create([
                    'pkks_kompetensi_id' => $this->currentKompetensiId,
                    'nomor' => $row['no'] ?? $row['nomor'] ?? '-',
                    'kriteria' => $row['kriteria'],
                    'bukti_identifikasi' => $row['bukti'] ?? $row['bukti_identifikasi'] ?? null,
                ]);
            }
        }
    }
}
