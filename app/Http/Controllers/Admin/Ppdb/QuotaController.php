<?php

namespace App\Http\Controllers\Admin\Ppdb;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\QuotaPendaftaran;
use App\Models\TahunPelajaran;
use App\Models\KompetensiPendaftaran;
use App\Models\TingkatPendaftaran;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class QuotaController extends Controller
{
    public function index()
    {
        $tahunPpdb = TahunPelajaran::where('is_active', 1)->first();
        $tingkat   = TingkatPendaftaran::where('is_active', 1)->first();

        $quotas = collect();

        if ($tahunPpdb && $tingkat) {
            $quotas = QuotaPendaftaran::where('tahunPelajaran_id', $tahunPpdb->id)
                ->where('tingkat', $tingkat->tingkat)
                ->get();
        }

        $jurusans = KompetensiPendaftaran::all();

        // Cek apakah quota sudah ada (untuk disable tombol tambah)
        $quotaExists = in_array($tingkat->tingkat, [1, 7])
            ? $quotas->count() > 0
            : false;

        return view('admin.ppdb.quota_pendaftaran', compact(
            'quotas',
            'tahunPpdb',
            'jurusans',
            'tingkat',
            'quotaExists'
        ));
    }

    public function store(Request $request)
    {
        try {
            $tingkat = TingkatPendaftaran::where('is_active', 1)->first();

            if (!$tingkat) {
                return redirect()->back()->with('danger', 'Tidak ada tingkat yang aktif.');
            }

            $rules = [
                'tahun_id'     => 'required|exists:tahun_pelajarans,id',
                'jumlah_kelas' => 'required|integer|min:1',
                'quota'        => 'required|integer|min:1',
            ];

            // 🔒 Kalau tingkat 1 atau 7 → hanya boleh 1 data
            if (in_array($tingkat->tingkat, [1, 7])) {
                $existing = QuotaPendaftaran::where('tahunPelajaran_id', $request->tahun_id)
                    ->where('tingkat', $tingkat->tingkat)
                    ->first();

                if ($existing) {
                    return redirect()->back()
                        ->with('danger', 'Quota untuk tingkat ' . $tingkat->tingkat . ' sudah ada, tidak bisa menambah lagi.')
                        ->withInput();
                }
            }

            // 🔧 Wajib isi keahlian hanya untuk tingkat 10
            if ($tingkat->tingkat == 10) {
                $rules['keahlian'] = [
                    'required',
                    'string',
                    Rule::unique('quota_pendaftarans', 'keahlian')
                        ->where('tahunPelajaran_id', $request->tahun_id)
                        ->where('tingkat', $tingkat->tingkat),
                ];
            }

            $request->validate($rules);

            QuotaPendaftaran::create([
                'tahunPelajaran_id' => $request->tahun_id,
                'tingkat'           => $tingkat->tingkat,
                'keahlian'          => $tingkat->tingkat == 10 ? $request->keahlian : '-',
                'jumlah_kelas'      => $request->jumlah_kelas,
                'quota'             => $request->quota,
            ]);

            return redirect()->back()->with('success', 'Quota berhasil ditambahkan.');
        } catch (ValidationException $e) {
            return redirect()->back()
                ->with('danger', implode('<br>', $e->validator->errors()->all()))
                ->withInput();
        } catch (\Throwable $th) {
            return redirect()->back()
                ->with('danger', 'Gagal menambahkan quota: ' . $th->getMessage())
                ->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $quota = QuotaPendaftaran::findOrFail($id);
            $tingkat = $quota->tingkat;

            $rules = [
                'jumlah_kelas' => 'required|integer|min:1',
                'quota'        => 'required|integer|min:1',
            ];

            if ($tingkat == 10) {
                $rules['keahlian'] = [
                    'required',
                    'string',
                    Rule::unique('quota_pendaftarans', 'keahlian')
                        ->where('tahunPelajaran_id', $quota->tahunPelajaran_id)
                        ->where('tingkat', $tingkat)
                        ->ignore($quota->id),
                ];
            }

            $request->validate($rules);

            $quota->update([
                'keahlian'      => $tingkat == 10 ? $request->keahlian : '-',
                'jumlah_kelas'  => $request->jumlah_kelas,
                'quota'         => $request->quota,
            ]);

            return redirect()->back()->with('success', 'Quota berhasil diupdate.');
        } catch (ValidationException $e) {
            return redirect()->back()
                ->with('danger', implode('<br>', $e->validator->errors()->all()))
                ->withInput();
        } catch (\Throwable $th) {
            return redirect()->back()
                ->with('danger', 'Gagal memperbarui quota: ' . $th->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $quota = QuotaPendaftaran::findOrFail($id);
            $quota->delete();

            return redirect()->back()->with('success', 'Quota berhasil dihapus.');
        } catch (\Throwable $th) {
            return redirect()->back()
                ->with('danger', 'Gagal menghapus quota: ' . $th->getMessage());
        }
    }
}