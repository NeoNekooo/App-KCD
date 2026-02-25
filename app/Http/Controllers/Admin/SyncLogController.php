<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SyncLogController extends Controller
{
    public function index()
    {
        // Tarik data log, urutkan dari yang barusan sync paling atas, kasih pagination 50 per halaman
        $logs = DB::table('sync_logs')
                  ->orderBy('updated_at', 'desc')
                  ->paginate(50);

        return view('admin.monitoring-sync.index', compact('logs'));
    }
}