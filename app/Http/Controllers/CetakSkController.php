<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PengajuanSekolah;
use App\Models\TipeSurat;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class CetakSkController extends Controller
{
    /**
     * GENERATE PDF SK (Langsung Stream HTML tanpa View Blade)
     */
    public function cetakSk($uuid)
    {
        // 1. AMBIL DATA PENGAJUAN
        $data = PengajuanSekolah::where('uuid', $uuid)->firstOrFail();

        // Security Check: Status Harus ACC
        if ($data->status != 'ACC') {
            abort(403, 'Dokumen belum disetujui (ACC).');
        }
        
        // Security Check: Role Sekolah
        // Sekolah hanya boleh cetak punya sendiri
        $user = Auth::user();
        if ($user && $user->role == 'sekolah' && $data->sekolah_id != $user->sekolah_id) {
            abort(403, 'Unauthorized');
        }

        // 2. [STRICT] AMBIL TEMPLATE SESUAI PILIHAN KEPALA
        if (!$data->template_id) {
            return back()->with('error', 'Format SK belum dipilih oleh Kepala KCD saat Approval. Silakan hubungi admin.');
        }

        $template = TipeSurat::find($data->template_id);

        if (!$template) {
            return back()->with('error', 'Template surat tidak ditemukan atau telah dihapus.');
        }

        $isiSuratRaw = $template->template_isi;

        // 3. FIX TABLE WIDTH & BORDER (Regex Magic untuk DomPDF)
        if (strpos($isiSuratRaw, '<table') !== false) {
            $isiSuratRaw = preg_replace_callback('/(<table[^>]*>)(.*?)<\/table>/is', function($tableMatch) {
                $openingTag = $tableMatch[1];
                $tableContent = $tableMatch[2];
                
                // Paksa border collapse
                if (strpos($openingTag, 'border-collapse') === false) {
                    $openingTag = str_replace('<table', '<table style="border-collapse: collapse; width: 100%;"', $openingTag);
                }

                // Fix width column
                if (preg_match_all('/<col [^>]*style="width:\s*([^;%"]+)%[^"]*"[^>]*>/i', $tableContent, $colMatches)) {
                    $widths = $colMatches[1];
                    $tableContent = preg_replace_callback('/<tr[^>]*>(.*?)<\/tr>/is', function($trMatch) use ($widths) {
                        $cells = $trMatch[1];
                        $cellIndex = 0;
                        return '<tr>' . preg_replace_callback('/<(td|th)([^>]*)>/i', function($tdMatch) use ($widths, &$cellIndex) {
                            $tag = $tdMatch[1];
                            $attr = $tdMatch[2];
                            if (isset($widths[$cellIndex])) {
                                if (strpos($attr, 'style=') !== false) {
                                    $attr = preg_replace('/style="/i', 'style="width:' . $widths[$cellIndex] . '%;', $attr);
                                } else {
                                    $attr .= ' style="width:' . $widths[$cellIndex] . '%;"';
                                }
                            }
                            $cellIndex++;
                            return "<$tag$attr>";
                        }, $cells) . '</tr>';
                    }, $tableContent);
                }
                return $openingTag . $tableContent . '</table>';
            }, $isiSuratRaw);
        }

        // 4. SIAPKAN DATA REPLACE (Mail Merge)
        $replacements = [
            // Basic data from PengajuanSekolah model
            '[NAMA_GURU]'    => Str::title(strtolower($data->nama_guru)), // Still using this for compatibility
            '[NAMA_SEKOLAH]' => $data->nama_sekolah,
            '[JUDUL]'        => strtoupper($data->judul),
            '[KATEGORI]'     => ucwords($data->kategori),
            '[NOMOR_SURAT]'  => $data->nomor_sk ?? '...',
            '[TANGGAL_ACC]'  => $data->tgl_selesai ? Carbon::parse($data->tgl_selesai)->isoFormat('D MMMM Y') : date('d F Y'),
            '{{no_surat}}'   => $data->nomor_sk,
            '{{sekolah}}'    => $data->nama_sekolah,
        ];

        // Add student-specific data from data_siswa_json if available
        if ($data->data_siswa_json && is_array($data->data_siswa_json)) {
            $siswa = (object) $data->data_siswa_json; // Cast to object for easier access

            $replacements['{{nama}}'] = $siswa->nama ?? $replacements['{{nama}}']; // Prioritize siswa->nama
            $replacements['{{nisn}}'] = $siswa->nisn ?? '-';
            $replacements['{{nipd}}'] = $siswa->nipd ?? '-';
            $replacements['{{nik}}'] = $siswa->nik ?? '-';
            $replacements['{{kelas}}'] = $siswa->nama_rombel ?? '-';

            // Composite fields
            $tempatLahir = $siswa->tempat_lahir ?? '-';
            $tanggalLahir = isset($siswa->tanggal_lahir) ? Carbon::parse($siswa->tanggal_lahir)->isoFormat('D MMMM Y') : '-';
            $replacements['{{ttl}}'] = $tempatLahir . ', ' . $tanggalLahir;

            $replacements['{{jk}}'] = $siswa->jenis_kelamin ?? '-';
            $replacements['{{agama}}'] = $siswa->agama_id_str ?? '-';

            // Construct full address
            $alamatParts = [];
            if (!empty($siswa->alamat_jalan)) $alamatParts[] = $siswa->alamat_jalan;
            if (!empty($siswa->rt) && !empty($siswa->rw)) $alamatParts[] = 'RT ' . $siswa->rt . '/RW ' . $siswa->rw;
            if (!empty($siswa->desa_kelurahan)) $alamatParts[] = $siswa->desa_kelurahan;
            if (!empty($siswa->kecamatan)) $alamatParts[] = $siswa->kecamatan;
            if (!empty($siswa->kabupaten_kota)) $alamatParts[] = $siswa->kabupaten_kota;
            if (!empty($siswa->provinsi)) $alamatParts[] = $siswa->provinsi;
            if (!empty($siswa->kode_pos)) $alamatParts[] = ' (' . $siswa->kode_pos . ')';
            $replacements['{{alamat}}'] = implode(', ', $alamatParts);

            $replacements['{{nama_ayah}}'] = $siswa->nama_ayah ?? '-';
            $replacements['{{nama_ibu}}'] = $siswa->nama_ibu ?? '-';
            $replacements['{{pekerjaan_ayah}}'] = $siswa->pekerjaan_ayah_id_str ?? '-';
            
            // New additional student fields
            $replacements['{{sekolah_asal}}'] = $siswa->sekolah_asal ?? '-';
            $replacements['{{no_kk}}'] = $siswa->no_kk ?? '-';
            $replacements['{{no_hp}}'] = $siswa->nomor_telepon_seluler ?? '-';
            $replacements['{{no_wa}}'] = $siswa->no_wa ?? '-';
            $replacements['{{nama_wali}}'] = $siswa->nama_wali ?? '-';
            $replacements['{{pekerjaan_wali}}'] = $siswa->pekerjaan_wali_id_str ?? '-';
            $replacements['{{tinggi_badan}}'] = $siswa->tinggi_badan ?? '-';
            $replacements['{{berat_badan}}'] = $siswa->berat_badan ?? '-';
            $replacements['{{kurikulum}}'] = $siswa->kurikulum_id_str ?? '-';
            $replacements['{{npsn_sekolah_asal}}'] = $siswa->npsn_sekolah_asal ?? '-';
            $replacements['{{no_seri_ijazah}}'] = $siswa->no_seri_ijazah ?? '-';
            $replacements['{{no_seri_skhun}}'] = $siswa->no_seri_skhun ?? '-';
            $replacements['{{pendidikan_ayah}}'] = $siswa->pendidikan_ayah_id_str ?? '-';
            $replacements['{{penghasilan_ayah}}'] = $siswa->penghasilan_ayah_id_str ?? '-';
            $replacements['{{pendidikan_ibu}}'] = $siswa->pendidikan_ibu_id_str ?? '-';
            $replacements['{{penghasilan_ibu}}'] = $siswa->penghasilan_ibu_id_str ?? '-';
            $replacements['{{alat_transportasi}}'] = $siswa->alat_transportasi_id_str ?? '-';
            $replacements['{{jenis_tinggal}}'] = $siswa->jenis_tinggal_id_str ?? '-';
            $replacements['{{jarak_sekolah}}'] = $siswa->jarak_rumah_ke_sekolah_km ?? '-';
            $replacements['{{waktu_tempuh}}'] = $siswa->waktu_tempuh_menit ?? '-';
            $replacements['{{jumlah_saudara}}'] = $siswa->jumlah_saudara_kandung ?? '-';
            $replacements['{{hobi}}'] = $siswa->hobi ?? '-';
            $replacements['{{cita_cita}}'] = $siswa->cita_cita ?? '-';        }

        $finalContent = $isiSuratRaw;
        foreach ($replacements as $key => $val) {
            $finalContent = str_ireplace($key, $val, $finalContent);
        }

        // 5. CLEANUP (Hapus Enter Kosong Berlebih)
        $finalContent = preg_replace('/^(<p>(&nbsp;|\s|<br>)*<\/p>\s*)+/i', '', $finalContent);
        $finalContent = preg_replace('/(<p>(&nbsp;|\s|<br>)*<\/p>\s*)+$/i', '', $finalContent);

        // 6. CONFIG KERTAS & MARGIN
        $paperMap = [
            'A4'     => [0, 0, 595.28, 841.89],
            'F4'     => [0, 0, 609.45, 935.43], 
            'Legal'  => [0, 0, 612.00, 1008.00],
            'Letter' => [0, 0, 612.00, 792.00],
        ];
        
        $uk = $template->ukuran_kertas ?? 'A4';
        $paperSize = $paperMap[$uk] ?? $paperMap['A4'];

        $mt = ($template->margin_top ?? 20) . 'mm';
        $mr = ($template->margin_right ?? 25) . 'mm';
        $mb = ($template->margin_bottom ?? 20) . 'mm';
        $ml = ($template->margin_left ?? 25) . 'mm';

        // 7. RENDER HTML WRAPPER
        $html = '
        <!DOCTYPE html>
        <html lang="id">
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
            <title>SK - '.$data->nama_guru.'</title>
            <style>
                @page { margin: 0px; } 
                body { 
                    margin-top: '.$mt.'; 
                    margin-right: '.$mr.'; 
                    margin-bottom: '.$mb.'; 
                    margin-left: '.$ml.'; 
                    font-family: "Times New Roman", serif; 
                    font-size: 12pt; 
                    line-height: 1.5; 
                    color: #000;
                }
                .mce-pagebreak { page-break-before: always !important; display: block !important; height: 0px !important; visibility: hidden; }
                .mce-pagebreak:first-child { page-break-before: avoid !important; }
                table { border-collapse: collapse; width: 100%; }
                td, th { vertical-align: top; padding: 2px; }
                p { margin-top: 0; margin-bottom: 0.8rem; text-align: justify; }
                .text-center { text-align: center; }
                .text-bold { font-weight: bold; }
                .text-right { text-align: right; }
            </style>
        </head>
        <body>'.$finalContent.'</body>
        </html>';

        // 8. STREAM PDF
        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper($paperSize, 'portrait');

        // Nama file saat didownload/stream
        return $pdf->stream("SK_{$data->nama_guru}.pdf");
    }
}