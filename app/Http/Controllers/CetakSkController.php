<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PengajuanSekolah;
use App\Models\TipeSurat;
use App\Models\Instansi; // <-- IMPORT MODEL INSTANSI BRE!
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
        if (!in_array(strtolower($data->status), ['acc', 'selesai', 'selesai (acc)'])) {
            abort(403, 'Dokumen belum disetujui (ACC).');
        }
        
        // Security Check: Role Sekolah (Hanya boleh cetak pengajuan sendiri)
        $user = Auth::user();
        if ($user && $user->role == 'sekolah' && $data->sekolah_id != $user->sekolah_id) {
            abort(403, 'Unauthorized. Anda tidak memiliki akses ke dokumen ini.');
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
                
                if (strpos($openingTag, 'border-collapse') === false) {
                    $openingTag = str_replace('<table', '<table style="border-collapse: collapse; width: 100%;"', $openingTag);
                }

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

        // 🔥 AMBIL DATA INSTANSI KCD 🔥
        $kcd = Instansi::first();

        // 🔥 LOGIC RENDER FOTO TANDA TANGAN UNTUK DOMPDF 🔥
        $tandaTanganHtml = '';
        if ($kcd && $kcd->tanda_tangan) {
            // Gunakan Base64 agar DomPDF 100% bisa render tanpa error path
            // Asumsi file tersimpan di storage/app/public/ (via php artisan storage:link)
            $imagePath = public_path('storage/' . $kcd->tanda_tangan);
            
            if (file_exists($imagePath)) {
                $ext = pathinfo($imagePath, PATHINFO_EXTENSION);
                $imgBase64 = base64_encode(file_get_contents($imagePath));
                $tandaTanganHtml = '<img src="data:image/'.$ext.';base64,'.$imgBase64.'" style="max-height: 120px; width: auto;" alt="Tanda Tangan">';
            } else {
                // Fallback jika pakai path URL asset biasa
                $tandaTanganHtml = '<img src="'.asset('storage/' . $kcd->tanda_tangan).'" style="max-height: 120px; width: auto;" alt="Tanda Tangan">';
            }
        }

        // 4. SIAPKAN DATA REPLACE (Mail Merge)
        $replacements = [
            // Data Global
            '[NAMA_GURU]'    => Str::title(strtolower($data->nama_guru)), 
            '[NAMA_SEKOLAH]' => $data->nama_sekolah,
            '[JUDUL]'        => strtoupper($data->judul),
            '[KATEGORI]'     => ucwords(str_replace('-', ' ', $data->kategori)),
            '[NOMOR_SURAT]'  => $data->nomor_sk ?? '...',
            '[TANGGAL_ACC]'  => $data->tgl_selesai ? Carbon::parse($data->tgl_selesai)->isoFormat('D MMMM Y') : date('d F Y'),
            
            // Variabel Dasar Template
            '{{no_surat}}'   => $data->nomor_sk ?? '-',
            '{{tanggal}}'    => $data->tgl_selesai ? Carbon::parse($data->tgl_selesai)->isoFormat('D MMMM Y') : date('d F Y'),
            '{{sekolah}}'    => $data->nama_sekolah,
            '{{tahun_ajaran}}'=> '2023/2024', 

            // 🔥 VARIABEL INSTANSI KCD 🔥
            '{{nama_instansi}}'   => $kcd->nama_instansi ?? 'CABANG DINAS PENDIDIKAN',
            '{{nama_brand}}'      => $kcd->nama_brand ?? '',
            '{{nama_kepala}}'     => $kcd->nama_kepala ?? 'Nama Kepala KCD',
            '{{nip_kepala}}'      => $kcd->nip_kepala ?? '-',
            '{{alamat_instansi}}' => $kcd->alamat ?? '-',
            '{{email_instansi}}'  => $kcd->email ?? '-',
            '{{telepon_instansi}}'=> $kcd->telepon ?? '-',
            '{{website_instansi}}'=> $kcd->website ?? '-',
            '{{tanda_tangan}}'    => $tandaTanganHtml, // Hasil render tag <img> dari atas
        ];

        // 🔥 DECODE JSON DARI KOLOM data_siswa_json 🔥
        $jsonData = $data->data_siswa_json;
        $parsedData = is_string($jsonData) ? json_decode($jsonData) : (object) $jsonData;

        if ($parsedData) {
            // --- A. LOGIKA UNTUK PESERTA DIDIK (PD) ---
            if ($data->tipe_pengaju === 'PD') {
                $replacements['{{nama}}'] = $parsedData->nama ?? $replacements['[NAMA_GURU]']; 
                $replacements['{{nisn}}'] = $parsedData->nisn ?? '-';
                $replacements['{{nipd}}'] = $parsedData->nipd ?? '-';
                $replacements['{{nik}}'] = $parsedData->nik ?? '-';
                $replacements['{{kelas}}'] = $parsedData->nama_rombel ?? '-';

                $tempatLahir = $parsedData->tempat_lahir ?? '-';
                $tanggalLahir = isset($parsedData->tanggal_lahir) ? Carbon::parse($parsedData->tanggal_lahir)->isoFormat('D MMMM Y') : '-';
                $replacements['{{ttl}}'] = $tempatLahir . ', ' . $tanggalLahir;
                $replacements['{{jk}}'] = $parsedData->jenis_kelamin ?? '-';
                $replacements['{{agama}}'] = $parsedData->agama_id_str ?? '-';

                // Construct full address
                $alamatParts = [];
                if (!empty($parsedData->alamat_jalan)) $alamatParts[] = $parsedData->alamat_jalan;
                if (!empty($parsedData->rt) && !empty($parsedData->rw)) $alamatParts[] = 'RT ' . $parsedData->rt . '/RW ' . $parsedData->rw;
                if (!empty($parsedData->desa_kelurahan)) $alamatParts[] = $parsedData->desa_kelurahan;
                if (!empty($parsedData->kecamatan)) $alamatParts[] = $parsedData->kecamatan;
                if (!empty($parsedData->kabupaten_kota)) $alamatParts[] = $parsedData->kabupaten_kota;
                if (!empty($parsedData->provinsi)) $alamatParts[] = $parsedData->provinsi;
                if (!empty($parsedData->kode_pos)) $alamatParts[] = ' (' . $parsedData->kode_pos . ')';
                $replacements['{{alamat}}'] = implode(', ', $alamatParts) ?: '-';

                $replacements['{{nama_ayah}}'] = $parsedData->nama_ayah ?? '-';
                $replacements['{{nama_ibu}}'] = $parsedData->nama_ibu ?? '-';
                $replacements['{{pekerjaan_ayah}}'] = $parsedData->pekerjaan_ayah_id_str ?? '-';
                $replacements['{{sekolah_asal}}'] = $parsedData->sekolah_asal ?? '-';
                $replacements['{{no_kk}}'] = $parsedData->no_kk ?? '-';
                $replacements['{{no_hp}}'] = $parsedData->nomor_telepon_seluler ?? '-';
                $replacements['{{no_wa}}'] = $parsedData->no_wa ?? '-';
                $replacements['{{nama_wali}}'] = $parsedData->nama_wali ?? '-';
                $replacements['{{pekerjaan_wali}}'] = $parsedData->pekerjaan_wali_id_str ?? '-';
                $replacements['{{tinggi_badan}}'] = $parsedData->tinggi_badan ?? '-';
                $replacements['{{berat_badan}}'] = $parsedData->berat_badan ?? '-';
                $replacements['{{kurikulum}}'] = $parsedData->kurikulum_id_str ?? '-';
                $replacements['{{npsn_sekolah_asal}}'] = $parsedData->npsn_sekolah_asal ?? '-';
                $replacements['{{no_seri_ijazah}}'] = $parsedData->no_seri_ijazah ?? '-';
                $replacements['{{no_seri_skhun}}'] = $parsedData->no_seri_skhun ?? '-';
                $replacements['{{pendidikan_ayah}}'] = $parsedData->pendidikan_ayah_id_str ?? '-';
                $replacements['{{penghasilan_ayah}}'] = $parsedData->penghasilan_ayah_id_str ?? '-';
                $replacements['{{pendidikan_ibu}}'] = $parsedData->pendidikan_ibu_id_str ?? '-';
                $replacements['{{penghasilan_ibu}}'] = $parsedData->penghasilan_ibu_id_str ?? '-';
                $replacements['{{alat_transportasi}}'] = $parsedData->alat_transportasi_id_str ?? '-';
                $replacements['{{jenis_tinggal}}'] = $parsedData->jenis_tinggal_id_str ?? '-';
                $replacements['{{jarak_sekolah}}'] = $parsedData->jarak_rumah_ke_sekolah_km ?? '-';
                $replacements['{{waktu_tempuh}}'] = $parsedData->waktu_tempuh_menit ?? '-';
                $replacements['{{jumlah_saudara}}'] = $parsedData->jumlah_saudara_kandung ?? '-';
                $replacements['{{hobi}}'] = $parsedData->hobi ?? '-';
                $replacements['{{cita_cita}}'] = $parsedData->cita_cita ?? '-';
            } 
            
            // --- B. LOGIKA UNTUK PEGAWAI / GTK ---
            elseif ($data->tipe_pengaju === 'GTK') {
                $replacements['{{nama}}'] = $parsedData->nama ?? $replacements['[NAMA_GURU]'];
                $replacements['{{nip}}'] = $parsedData->nip ?? '-';
                $replacements['{{nik}}'] = $parsedData->nik ?? '-';
                $replacements['{{nuptk}}'] = $parsedData->nuptk ?? '-';
                
                $replacements['{{tempat_lahir}}'] = $parsedData->tempat_lahir ?? '-';
                $replacements['{{tanggal_lahir}}'] = isset($parsedData->tanggal_lahir) ? Carbon::parse($parsedData->tanggal_lahir)->isoFormat('D MMMM Y') : '-';
                $replacements['{{jenis_kelamin}}'] = $parsedData->jenis_kelamin ?? '-'; 
                
                $replacements['{{agama_id_str}}'] = $parsedData->agama_id_str ?? '-';
                $replacements['{{jenis_ptk_id_str}}'] = $parsedData->jenis_ptk_id_str ?? '-';
                $replacements['{{jabatan_ptk_id_str}}'] = $parsedData->jabatan_ptk_id_str ?? '-';
                $replacements['{{status_kepegawaian_id_str}}'] = $parsedData->status_kepegawaian_id_str ?? '-';
                
                $replacements['{{pendidikan_terakhir}}'] = $parsedData->pendidikan_terakhir ?? '-';
                $replacements['{{bidang_studi_terakhir}}'] = $parsedData->bidang_studi_terakhir ?? '-';
                $replacements['{{pangkat_golongan_terakhir}}'] = $parsedData->pangkat_golongan_terakhir ?? '-';
                
                $replacements['{{sk_pengangkatan}}'] = $parsedData->sk_pengangkatan ?? '-';
                $replacements['{{tmt_pengangkatan}}'] = $parsedData->tmt_pengangkatan ?? '-';
                
                $replacements['{{no_hp}}'] = $parsedData->no_hp ?? '-';
                $replacements['{{email}}'] = $parsedData->email ?? '-';
                
                // Construct Address
                $alamatJalan = $parsedData->alamat_jalan ?? '';
                $rt = $parsedData->rt ?? '';
                $rw = $parsedData->rw ?? '';
                $desa = $parsedData->desa_kelurahan ?? '';
                $kec = $parsedData->kecamatan ?? '';
                $kab = $parsedData->kabupaten_kota ?? '';
                
                $alamatLengkap = trim("$alamatJalan RT $rt/RW $rw, $desa, Kec. $kec, $kab", ' ,/');
                $replacements['{{alamat_jalan}}'] = $alamatLengkap ?: '-';
            }
        }

        // 5. EXECUTE REPLACE ALL VARIABLES
        $finalContent = $isiSuratRaw;
        foreach ($replacements as $key => $val) {
            $finalContent = str_ireplace($key, $val, $finalContent);
        }

        // 6. CLEANUP (Hapus Enter Kosong Berlebih)
        $finalContent = preg_replace('/^(<p>(&nbsp;|\s|<br>)*<\/p>\s*)+/i', '', $finalContent);
        $finalContent = preg_replace('/(<p>(&nbsp;|\s|<br>)*<\/p>\s*)+$/i', '', $finalContent);

        // 7. CONFIG KERTAS & MARGIN
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

        // 8. RENDER HTML WRAPPER
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

        // 9. STREAM PDF
        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper($paperSize, 'portrait');

        return $pdf->stream("SK_{$data->nama_guru}.pdf");
    }
}