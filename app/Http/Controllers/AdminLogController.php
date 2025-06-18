<?php

namespace App\Http\Controllers;

use App\Models\Log;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminLogController extends Controller
{
    public function index(Request $request)
    {
        $query = Log::with('user.profile');

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // Ambil semua log, urut dari terbaru
        $logs = $query->orderBy('created_at', 'desc')->get();

        return view('adminlog.index', compact('logs'));
    }

    public function exportPdf(Request $request)
    {
        $query = Log::with('user.profile');

        // Jika filter tanggal digunakan
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $logs = $query->orderBy('created_at', 'desc')->take(100)->get();

        return Pdf::loadView('adminlog.pdf', [
            'logs' => $logs,
            'filterDate' => $request->date,
        ])->download('laporan-log-aktivitas.pdf');
    }
}