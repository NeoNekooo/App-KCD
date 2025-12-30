<?php

namespace App\Http\Controllers\Admin\Rombel;

use App\Http\Controllers\Controller;
use App\Models\Rombel;
use Illuminate\Http\Request;

class RombelController extends Controller
{
    /**
     * Update core display fields for a rombel (jurusan/kurikulum strings)
     */
    public function updateCore(Request $request, Rombel $rombel)
    {
        $validated = $request->validate([
            'jurusan_id_str'    => 'nullable|string|max:191',
            'kurikulum_id_str'  => 'nullable|string|max:191',
            'nama_rombel'       => 'nullable|string|max:191',
        ]);

        $rombel->update([
            'jurusan_id_str'   => $validated['jurusan_id_str'] ?? $rombel->jurusan_id_str,
            'kurikulum_id_str' => $validated['kurikulum_id_str'] ?? $rombel->kurikulum_id_str,
            'nama_rombel'      => $validated['nama_rombel'] ?? $rombel->nama_rombel,
        ]);

        return redirect()->back()->with('success', 'Data Rombel diperbarui.');
    }
}
