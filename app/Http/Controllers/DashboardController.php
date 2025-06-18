<?php

namespace App\Http\Controllers;
use App\Models\Document;
use App\Models\Approval;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $adminId = Auth::id();

        $suratMasuk = Approval::where('status', 'pending')->count();
        $suratKeluar = Approval::whereIn('status', ['approved', 'rejected'])->count();
        $incomingMessages = Message::where('receiver_id', $adminId)->count();

        // Filter jumlah hari
        $days = (int) $request->query('days', 7);
        $days = in_array($days, [7, 14, 30]) ? $days : 7;

        $dateRange = Carbon::now()->subDays($days - 1);
        $period = CarbonPeriod::create($dateRange, Carbon::now());

        $documentsPerDate = Document::where('tanggal_pengajuan', '>=', $dateRange)
            ->selectRaw('DATE(tanggal_pengajuan) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        $dates = collect();
        $counts = collect();

        foreach ($period as $date) {
            $carbonDate = Carbon::parse($date);
            $tanggal = $carbonDate->format('Y-m-d');
            $label = $carbonDate->format('d-m');

            $dates->push($label);
            $count = $documentsPerDate->firstWhere('date', $tanggal)->count ?? 0;
            $counts->push($count);
        }

        // Query List Sisa Cuti < 5
        $dataCutiKurang = DB::table('leave_balances')
            ->join('users', 'users.id', '=', 'leave_balances.user_id')
            ->join('profiles', 'profiles.user_id', '=', 'users.id')
            ->where('leave_balances.remaining_leave', '<', 5)
            ->select('profiles.name as nama_karyawan', 'leave_balances.remaining_leave')
            ->orderBy('remaining_leave', 'asc')
            ->get();

        return view('pages.dashboard', compact(
            'suratMasuk',
            'suratKeluar',
            'incomingMessages',
            'dates',
            'counts',
            'days',
            'dataCutiKurang'
        ));
    }


}