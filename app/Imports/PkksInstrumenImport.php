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
    protected $currentParentId = null;
    protected $currentChildId = null;

    public function __construct($instrumenId)
    {
        $this->instrumenId = $instrumenId;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // 1. LEVEL 1: POINT UTAMA
            if (!empty($row['point_utama'])) {
                $parent = PkksKompetensi::firstOrCreate([
                    'pkks_instrumen_id' => $this->instrumenId,
                    'parent_id' => null,
                    'nama' => $row['point_utama']
                ], [
                    'urutan' => PkksKompetensi::where('pkks_instrumen_id', $this->instrumenId)->whereNull('parent_id')->count() + 1
                ]);
                
                $this->currentParentId = $parent->id;
                $this->currentChildId = null; // Reset anak kalau bapaknya ganti
            }

            // 2. LEVEL 2: SUB KATEGORI (MANAJERIAL, DLL)
            if ($this->currentParentId && !empty($row['kompetensi'])) {
                $child = PkksKompetensi::firstOrCreate([
                    'pkks_instrumen_id' => $this->instrumenId,
                    'parent_id' => $this->currentParentId,
                    'nama' => $row['kompetensi']
                ], [
                    'urutan' => PkksKompetensi::where('parent_id', $this->currentParentId)->count() + 1
                ]);
                
                $this->currentChildId = $child->id;
            }

            // 3. LEVEL 3: INDIKATOR (BUTIR SOAL)
            // Bisa nempel ke Child (Sub) atau langsung ke Parent (kalau gak ada sub)
            $targetId = $this->currentChildId ?: $this->currentParentId;

            if ($targetId && !empty($row['kriteria'])) {
                PkksIndikator::create([
                    'pkks_kompetensi_id' => $targetId,
                    'nomor' => $row['no'] ?? $row['nomor'] ?? '-',
                    'kriteria' => $row['kriteria'],
                    'bukti_identifikasi' => $row['bukti'] ?? $row['bukti_identifikasi'] ?? null,
                ]);
            }
        }
    }
}
