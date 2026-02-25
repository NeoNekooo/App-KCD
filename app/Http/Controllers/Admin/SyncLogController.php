<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SyncLogController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        // Tarik data log, filter kalau ada pencarian, urutkan paling baru
        $logs = DB::table('sync_logs')
            ->when($search, function ($query, $search) {
                return $query->where('nama_sekolah', 'like', "%{$search}%")
                             ->orWhere('npsn', 'like', "%{$search}%");
            })
            ->orderBy('updated_at', 'desc')
            ->paginate(50);

        return view('admin.monitoring-sync.index', compact('logs'));
    }
}