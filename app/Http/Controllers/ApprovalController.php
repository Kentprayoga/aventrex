<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Approval;
use App\Models\LeaveBalance;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request as GlobalRequest; 

class ApprovalController extends Controller
{
    public function index()
    {
        $cutiTahunanApprovals = Approval::with([
            'document.template.category',
            'document.user.profile',
            'document.user.leaveBalance'   // <-- ini ditambahkan
        ])
        ->where('status', 'pending')
        ->whereHas('document.template.category', function($query) {
            $query->where('name', 'cuti');
        })
        ->get();


        $otherApprovals = Approval::with(['document.template.category', 'document.user.profile'])
            ->where('status', 'pending')
            ->whereHas('document.template.category', function($query) {
                $query->where('name', '!=', 'cuti');
            })
            ->get();

        return view('approvals.index', compact('cutiTahunanApprovals', 'otherApprovals'));
    }

    public function approve($id, Request $request)
    {
        $approval = Approval::with('document.template.category')->findOrFail($id);
        $approval->status = 'approved';
        $approval->save();

        // Jika kategori cuti tahunan, update leave balance
        if ($approval->document->template->category->name === 'cuti') {
            $userId = $approval->document->user_id;
            $leaveBalance = LeaveBalance::where('user_id', $userId)->first();

            if ($leaveBalance) {
                $cutiDiambil = $approval->document->lama_hari ?? 1;

                $leaveBalance->used_leave += $cutiDiambil;
                $leaveBalance->remaining_leave -= $cutiDiambil;

                if ($leaveBalance->remaining_leave < 0) {
                    $leaveBalance->remaining_leave = 0;
                }

                $leaveBalance->save();
            }
        }

    Log::create([
        'user_id'    => Auth::user()->id, // user admin yang sedang login
        'action'     => 'confirm_approval',
        'detail'     => "Admin mengonfirmasi pengajuan ID ". $approval->id,
        'ip_address' => $request->ip(),
        'user_agent' => $request->userAgent(),
    ]);
        return redirect()->route('approvals.index')->with('success', 'Dokumen disetujui dan cuti diperbarui.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'alasan' => 'required|string',
        ]);

        $approval = Approval::findOrFail($id);
        $approval->status = 'rejected';
        $approval->alasan = $request->alasan;
        $approval->save();

    Log::create([
        'user_id'    => Auth::user()->id, // user admin yang sedang login
        'action'     => 'reject_approval',
        'detail'     => "Admin menolak pengajuan ID ". $approval->id,
        'ip_address' => $request->ip(),
        'user_agent' => $request->userAgent(),
    ]);
        return redirect()->route('approvals.index')->with('success', 'Dokumen ditolak dengan alasan.');
    }
}