<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class GenericSyncController extends Controller
{
    /**
     * Menangani semua permintaan sinkronisasi secara dinamis,
     * membuat atau mengubah tabel dan kolom sesuai kebutuhan.
     */
    public function handleSync(Request $request, $entity)
    {
        $dataFromDapodik = $request->all();

        if (empty($dataFromDapodik)) {
            return response()->json(['message' => 'Tidak ada data yang dikirim.'], 200);
        }

        // 1. Tentukan nama tabel dari nama entitas (misal: 'gtk' -> 'gtks')
        $tableName = Str::plural(Str::snake($entity));

        // 2. Ambil semua nama kolom dari data pertama yang dikirim
        $dapodikColumns = array_keys($dataFromDapodik[0]);

        // 3. Cek apakah tabel sudah ada. Jika belum, buat tabelnya.
        if (!Schema::hasTable($tableName)) {
            Schema::create($tableName, function ($table) use ($dapodikColumns) {
                $table->id(); // Kolom ID utama

                // Loop untuk membuat semua kolom secara dinamis
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

        // 5. Lakukan proses Update atau Create data (Upsert)
        $identifierColumn = $this->getIdentifierColumn($dapodikColumns, $entity);

        foreach ($dataFromDapodik as $row) {

            // FIX: Cek setiap nilai di dalam baris data
            foreach ($row as $key => $value) {
                // Jika nilainya adalah sebuah array, ubah menjadi string JSON
                if (is_array($value)) {
                    $row[$key] = json_encode($value, JSON_UNESCAPED_UNICODE);
                }
            }

            // Sekarang, $row dijamin aman untuk disimpan ke database
            DB::table($tableName)->updateOrInsert(
                [$identifierColumn => $row[$identifierColumn]], // Kunci untuk mencari
                $row // Data yang sudah bersih dan lengkap
            );
        }

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
        // Daftar prioritas kolom ID unik dari Dapodik
        $identifiers = [
            'peserta_didik_id',
            'gtk_id',
            'sekolah_id',
            'rombongan_belajar_id',
            'pengguna_id',

        ];

        foreach ($identifiers as $id) {
            if (in_array($id, $columns)) {
                return $id;
            }
        }

        // Fallback jika tidak ada, gunakan ID unik entitas (misal: 'siswa_id')
        $fallbackId = Str::snake($entity) . '_id';
        if(in_array($fallbackId, $columns)) {
            return $fallbackId;
        }

        // Jika semua gagal, asumsikan kolom pertama adalah ID (sangat berisiko)
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
            $table->string($column, 191)->nullable()->index(); // Gunakan string dengan index
        } elseif (str_contains($column, 'tanggal')) {
            $table->date($column)->nullable();
        } else {
            $table->text($column)->nullable();
        }
    }
}
