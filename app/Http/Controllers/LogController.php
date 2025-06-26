<?php

namespace App\Http\Controllers;

use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class LogController extends Controller
{
    /**
     * Menampilkan riwayat aktivitas pengguna (user).
     */
    public function userActivity(Request $request)
    {
    $query = Log::with('user.profile')
        ->whereHas('user', function ($q) {
            $q->where('role_id', 2);
        });

    if ($request->filled('action') && $request->action !== 'all') {
        $query->where('action', $request->action);
    }

    if ($request->filled('tanggal')) {
        $query->whereDate('created_at', Carbon::parse($request->tanggal));
    }

    $logs = $query->latest()->get();

    $actions = Log::select('action')
        ->whereHas('user', fn($q) => $q->where('role_id', 2))
        ->distinct()
        ->pluck('action');

    return view('logs.user', compact('logs', 'actions'));
    }
    public function exportUserPdf(Request $request)
    {
        $logs = $this->getFilteredLogs(2, $request); // role_id 2 = user

        $pdf = Pdf::loadView('exports.log_pdf', ['logs' => $logs]);

        return $pdf->download('log_user.pdf');
    }

    public function adminActivity(Request $request)
    {
        $query = Log::with('user.profile')
            ->whereHas('user', function ($q) {
                $q->where('role_id', 1);
            });

        if ($request->filled('action') && $request->action !== 'all') {
            $query->where('action', $request->action);
        }

        // 🔍 Filter 1 hari saja
        if ($request->filled('tanggal')) {
            $query->whereDate('created_at', Carbon::parse($request->tanggal));
        }

        $logs = $query->latest()->get();

        $actions = Log::select('action')
            ->whereHas('user', fn($q) => $q->where('role_id', 1))
            ->distinct()
            ->pluck('action');

        return view('logs.admin', compact('logs', 'actions'));
    }
    public function exportAdminPdf(Request $request)
    {
        $logs = $this->getFilteredLogs(1, $request);

        $pdf = Pdf::loadView('exports.log_pdf', ['logs' => $logs]);

        return $pdf->download('log_admin.pdf');
    }

    private function getFilteredLogs($roleId, Request $request)
    {
        $query = Log::with('user.profile')
            ->whereHas('user', fn($q) => $q->where('role_id', $roleId));

        if ($request->filled('action') && $request->action !== 'all') {
            $query->where('action', $request->action);
        }

        if ($request->filled('tanggal')) {
            $query->whereDate('created_at', $request->tanggal);
        }

        return $query->latest()->get();
    }

    public function select()
    {
        return view('logs.index'); // view untuk memilih log admin / user
    }

}