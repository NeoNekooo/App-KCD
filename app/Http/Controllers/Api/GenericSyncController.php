<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class GenericSyncController extends Controller
{
    /**
     * Menangani semua permintaan sinkronisasi secara dinamis,
     * membuat atau mengubah tabel dan kolom sesuai kebutuhan.
     */
    public function handleSync(Request $request, $entity)
    {
        Log::info("--- MEMULAI SINKRONISASI ENTITAS: " . strtoupper($entity) . " ---");

        $dataFromDapodik = $request->all();
        $totalData = count($dataFromDapodik);

        Log::info("Jumlah data mentah diterima: " . $totalData);

        if (empty($dataFromDapodik)) {
            Log::warning("Data kosong. Proses dihentikan.");
            return response()->json(['message' => 'Tidak ada data yang dikirim.'], 200);
        }

        // 1. Tentukan nama tabel
        $tableName = Str::plural(Str::snake($entity));

        // 2. Ambil semua nama kolom dari data pertama yang dikirim
        $dapodikColumns = array_keys($dataFromDapodik[0]);

        // 3. Cek apakah tabel sudah ada. Jika belum, buat tabelnya.
        if (!Schema::hasTable($tableName)) {
            Schema::create($tableName, function ($table) use ($dapodikColumns) {
                $table->id(); 
                foreach ($dapodikColumns as $column) {
                    $this->defineColumnType($table, $column);
                }
                $table->timestamps();
            });
        } else {
            $existingColumns = Schema::getColumnListing($tableName);
            $newColumns = array_diff($dapodikColumns, $existingColumns);

            if (!empty($newColumns)) {
                Schema::table($tableName, function ($table) use ($newColumns) {
                    foreach ($newColumns as $column) {
                        $this->defineColumnType($table, $column);
                    }
                });
            }
        }

        // 4. Lakukan proses Update atau Create data (Upsert)
        // FIX: Prioritas pengambilan ID sudah diperbaiki di fungsi getIdentifierColumn
        $identifierColumn = $this->getIdentifierColumn($dapodikColumns, $entity);
        
        Log::info("Kolom Identifier yang dipilih otomatis: " . $identifierColumn);

        $successCount = 0;
        $failCount = 0;

        foreach ($dataFromDapodik as $index => $row) {
            
            // Validasi: Pastikan kolom identifier ada datanya
            if (!isset($row[$identifierColumn])) {
                // Jika identifier null, skip agar tidak menimpa data lain
                // Kecuali jika Anda ingin membuat ID baru, tapi untuk sinkronisasi sebaiknya skip
                $failCount++;
                continue; 
            }

            foreach ($row as $key => $value) {
                if (is_array($value)) {
                    $row[$key] = json_encode($value, JSON_UNESCAPED_UNICODE);
                }
            }

            try {
                DB::table($tableName)->updateOrInsert(
                    [$identifierColumn => $row[$identifierColumn]], 
                    $row 
                );
                $successCount++;
            } catch (\Exception $e) {
                Log::error("DB ERROR (Index: $index): " . $e->getMessage());
                $failCount++;
            }
        }

        Log::info("--- SELESAI ---");
        Log::info("Total Diterima: $totalData | Sukses Masuk DB: $successCount | Gagal/Skip: $failCount");

        return response()->json([
            'success' => true,
            'message' => 'Sinkronisasi ' . ucfirst($entity) . ' selesai.',
            'details' => count($dataFromDapodik) . ' data berhasil diproses.'
        ]);
    }

    /**
     * Helper untuk menebak kolom mana yang menjadi Primary Key dari Dapodik
     */
    private function getIdentifierColumn(array $columns, string $entity)
    {
        // FIX: Urutan dipindah. 'pengguna_id' & 'ptk_id' ditaruh paling atas
        // agar data guru/staf tidak dianggap null dan tidak saling menimpa.
        $identifiers = [
            'pengguna_id',          // Prioritas 1: ID unik tabel pengguna
            'ptk_id',               // Prioritas 2: ID unik tabel Guru/PTK
            'peserta_didik_id',     // Prioritas 3: ID unik tabel Siswa
            'gtk_id',
            'sekolah_id',
            'rombongan_belajar_id',
        ];

        foreach ($identifiers as $id) {
            if (in_array($id, $columns)) {
                return $id;
            }
        }

        // Fallback
        $fallbackId = Str::snake($entity) . '_id';
        if(in_array($fallbackId, $columns)) {
            return $fallbackId;
        }

        return $columns[0];
    }

    /**
     * Helper untuk mendefinisikan tipe kolom secara dinamis.
     */
    private function defineColumnType($table, string $column)
    {
        if (Str::endsWith($column, '_id_str')) {
            $table->text($column)->nullable();
        } elseif (Str::endsWith($column, '_id')) {
            $table->string($column, 191)->nullable()->index(); 
        } elseif (str_contains($column, 'tanggal')) {
            $table->date($column)->nullable();
        } else {
            $table->text($column)->nullable();
        }
    }
}