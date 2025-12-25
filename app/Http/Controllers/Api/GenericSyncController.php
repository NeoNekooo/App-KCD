<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class GenericSyncController extends Controller
{
    public function handleSync(Request $request, $entity)
    {
        // 1. Ambil data
        $rawInput = $request->all();
        $dataFromDapodik = isset($rawInput['rows']) ? $rawInput['rows'] : $rawInput;

        if (empty($dataFromDapodik)) {
            return response()->json(['message' => 'Tidak ada data yang dikirim.'], 200);
        }

        if (isset($dataFromDapodik['rombongan_belajar_id']) || isset($dataFromDapodik[0]) === false) {
             $dataFromDapodik = [$dataFromDapodik];
        }

        // 2. Tentukan nama tabel
        $tableName = Str::plural(Str::snake($entity));
        $firstRow = (array) $dataFromDapodik[0];
        $dapodikColumns = array_keys($firstRow);

        // 3. Cek/Buat Tabel (Skema)
        if (!Schema::hasTable($tableName)) {
            Schema::create($tableName, function ($table) use ($dapodikColumns, $entity) {
                $table->id();
                // Tambahkan qr_token khusus untuk siswa/gtk
                if (in_array($entity, ['siswa', 'siswas', 'gtk', 'gtks'])) {
                    $table->string('qr_token')->nullable()->unique();
                }
                foreach ($dapodikColumns as $column) {
                    $this->defineColumnType($table, $column);
                }
                $table->timestamps();
            });
        } else {
            $existingColumns = Schema::getColumnListing($tableName);

            // Cek apakah kolom qr_token sudah ada, jika belum inject sekarang
            if (in_array($entity, ['siswa', 'siswas', 'gtk', 'gtks']) && !in_array('qr_token', $existingColumns)) {
                Schema::table($tableName, function ($table) {
                    $table->string('qr_token')->nullable()->unique()->after('id');
                });
            }

            $newColumns = array_diff($dapodikColumns, $existingColumns);
            if (!empty($newColumns)) {
                Schema::table($tableName, function ($table) use ($newColumns) {
                    foreach ($newColumns as $column) {
                        $this->defineColumnType($table, $column);
                    }
                });
            }
        }

        // 4. Tentukan Identifier Utama (Untuk UpdateOrInsert)
        if ($entity === 'rombel' || $entity === 'rombongan_belajar') {
            $identifierColumn = 'rombongan_belajar_id';
        } elseif ($entity === 'pembelajaran') {
            $identifierColumn = 'pembelajaran_id';
        } else {
            $identifierColumn = $this->getIdentifierColumn($dapodikColumns, $entity);
        }

        $processed = 0;

        // Ambil Base URL (misal: https://sekolahku.id)
        $baseUrl = $request->getSchemeAndHttpHost();

        // 5. Proses Data
        foreach ($dataFromDapodik as $row) {
            $row = (array) $row;

            // --- LOGIKA GENERATE QR TOKEN ---
            if (in_array($entity, ['siswa', 'siswas', 'gtk', 'gtks'])) {

                $uniqueCode = null;

                // A. Jika ini SISWA, cari ID unik siswa
                if ($entity === 'siswa' || $entity === 'siswas') {
                    // Prioritas 1: peserta_didik_id (Paling aman, menempel ke orangnya)
                    if (!empty($row['peserta_didik_id'])) {
                        $uniqueCode = $row['peserta_didik_id'];
                    }
                    // Prioritas 2: registrasi_id (Jika prioritas 1 gagal)
                    elseif (!empty($row['registrasi_id'])) {
                        $uniqueCode = $row['registrasi_id'];
                    }
                }
                // B. Jika ini GTK, cari ID unik guru
                elseif ($entity === 'gtk' || $entity === 'gtks') {
                    // Prioritas 1: ptk_id
                    if (!empty($row['ptk_id'])) {
                        $uniqueCode = $row['ptk_id'];
                    }
                    // Prioritas 2: ptk_terdaftar_id
                    elseif (!empty($row['ptk_terdaftar_id'])) {
                        $uniqueCode = $row['ptk_terdaftar_id'];
                    }
                }

                // C. Fallback Terakhir (Jaga-jaga jika ID di atas kosong semua)
                if (!$uniqueCode) {
                    $uniqueCode = $row[$identifierColumn] ?? uniqid();
                }

                // D. Susun URL Akhir
                // Hasil: https://sekolah.id/scan/uuid-panjang-disini
                // Saya tambahkan path '/scan/' agar nanti di frontend/web.php mudah di-routing
                $row['qr_token'] = $baseUrl . '/scan/' . $uniqueCode;
            }
            // --------------------------------

            // Bersihkan Array -> JSON
            foreach ($row as $key => $value) {
                if (is_array($value)) {
                    $row[$key] = json_encode($value, JSON_UNESCAPED_UNICODE);
                }
            }

            // Logika Rombel (Merge Pembelajaran)
            if (($entity === 'rombel' || $entity === 'rombongan_belajar') && isset($row['pembelajaran'])) {
                $existingRecord = DB::table($tableName)
                    ->where($identifierColumn, $row[$identifierColumn])
                    ->first();

                if ($existingRecord && !empty($existingRecord->pembelajaran)) {
                    $oldPems = json_decode($existingRecord->pembelajaran, true) ?? [];
                    $newPems = json_decode($row['pembelajaran'], true) ?? [];
                    $merged = array_merge($oldPems, $newPems);
                    $uniquePems = [];
                    foreach ($merged as $item) {
                        $keyId = $item['pembelajaran_id'] ?? $item['mata_pelajaran_id'] ?? uniqid();
                        $uniquePems[$keyId] = $item;
                    }
                    $row['pembelajaran'] = json_encode(array_values($uniquePems), JSON_UNESCAPED_UNICODE);
                }
            }

            DB::table($tableName)->updateOrInsert(
                [$identifierColumn => $row[$identifierColumn]],
                $row
            );
            $processed++;
        }

        return response()->json([
            'success' => true,
            'message' => 'Sinkronisasi ' . ucfirst($entity) . ' selesai.',
            'details' => $processed . ' data berhasil diproses.'
        ], 200);
    }

    // Helper identifier tetap sama
    private function getIdentifierColumn(array $columns, string $entity)
    {
        $identifiers = [
            'peserta_didik_id', 'ptk_id', 'rombongan_belajar_id',
            'pembelajaran_id', 'sekolah_id', 'pengguna_id', 'id_reg_pd'
        ];
        foreach ($identifiers as $id) {
            if (in_array($id, $columns)) return $id;
        }
        $fallbackId = Str::snake($entity) . '_id';
        if(in_array($fallbackId, $columns)) return $fallbackId;
        return $columns[0];
    }

    // Helper define column tetap sama
    private function defineColumnType($table, string $column)
    {
        if (Str::endsWith($column, '_id_str')) {
            $table->text($column)->nullable();
        } elseif (Str::endsWith($column, '_id')) {
            $table->string($column, 191)->nullable()->index();
        } elseif (str_contains($column, 'tanggal')) {
            $table->date($column)->nullable();
        } elseif (in_array($column, ['pembelajaran', 'anggota_rombel'])) {
            $table->longText($column)->nullable();
        } else {
            $table->text($column)->nullable();
        }
    }
}
