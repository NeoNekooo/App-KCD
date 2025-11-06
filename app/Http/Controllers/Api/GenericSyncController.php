<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Siswa;
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

        // 3. Cek apakah tabel sudah ada. Jika belum, buat tabel dan pastikan kolom `ptk_id` ada.
        if (!Schema::hasTable($tableName)) {
            Schema::create($tableName, function ($table) use ($dapodikColumns, $tableName) {
                $table->id(); // Kolom ID utama

                // Loop untuk membuat semua kolom secara dinamis
                foreach ($dapodikColumns as $column) {
                    $this->defineColumnType($table, $column);
                }

                // Logika tambahan khusus untuk tabel 'rombels'
                if ($tableName === 'rombels' && !in_array('ptk_id', $dapodikColumns)) {
                    $table->unsignedBigInteger('ptk_id')->nullable()->index();
                }

                $table->timestamps();
            });
        } else {
            // 4. Jika tabel sudah ada, cek dan tambahkan kolom baru jika diperlukan.
            $existingColumns = Schema::getColumnListing($tableName);
            $newColumns = array_diff($dapodikColumns, $existingColumns);

            // Cek juga apakah kolom `ptk_id` perlu ditambahkan
            if (!empty($newColumns) || ($tableName === 'rombels' && !in_array('ptk_id', $existingColumns))) {
                Schema::table($tableName, function ($table) use ($newColumns, $tableName, $existingColumns) {
                    foreach ($newColumns as $column) {
                        $this->defineColumnType($table, $column);
                    }
                    
                    // Logika tambahan khusus untuk tabel 'rombels'
                    if ($tableName === 'rombels' && !in_array('ptk_id', $existingColumns)) {
                        $table->unsignedBigInteger('ptk_id')->nullable()->index();
                    }
                });
            }
        }

        // 5. Lakukan proses Update atau Create data (Upsert)
        $identifierColumn = $this->getIdentifierColumn($dapodikColumns, $entity);

        foreach ($dataFromDapodik as $row) {
            // Ubah nilai array menjadi format JSON
            foreach ($row as $key => $value) {
                if (is_array($value)) {
                    $row[$key] = json_encode($value, JSON_UNESCAPED_UNICODE);
                }
            }
            
            // =============================================================
            // --- PEMBARUAN UTAMA V2: PENCARIAN NAMA YANG LEBIH FLEKSIBEL ---
            // =============================================================
            if ($tableName === 'rombels' && !empty($row['ptk_id_str'])) {
                
                // 1. Bersihkan nama dari Dapodik (hapus gelar, spasi berlebih)
                $cleanedName = trim(preg_replace('/,.*$/', '', $row['ptk_id_str']));

                // 2. Cari di database dengan mencocokkan nama yang sudah dibersihkan
                $gtk = DB::table('gtks')
                         ->whereRaw('TRIM(REGEXP_REPLACE(nama, ",.*$", "")) LIKE ?', ["%{$cleanedName}%"])
                         ->select('id')
                         ->first();

                // 3. Tetapkan ID jika ditemukan, jika tidak, biarkan null
                $row['ptk_id'] = $gtk ? $gtk->id : null;
            }
            // =============================================================
            // --- AKHIR PEMBARUAN UTAMA V2 ---
            // =============================================================
            
            // Logika khusus untuk tabel siswa agar tidak menimpa data 'foto'
            if ($tableName === 'siswas') {
                $siswa = Siswa::firstOrNew([$identifierColumn => $row[$identifierColumn]]);
                $siswa->fill($row);
                $siswa->save();
                if ($siswa->wasRecentlyCreated && is_null($siswa->qr_token)) {
                    $siswa->qr_token = Str::uuid()->toString();
                    $siswa->save();
                }
            } else {
                // Untuk tabel lain, gunakan metode updateOrInsert biasa
                DB::table($tableName)->updateOrInsert(
                    [$identifierColumn => $row[$identifierColumn]],
                    $row
                );
            }
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