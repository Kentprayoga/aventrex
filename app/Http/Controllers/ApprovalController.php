<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Approval;
use App\Models\LeaveBalance;
use App\Models\Log;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request as GlobalRequest; 
use Illuminate\Support\Facades\Storage;
use ZipArchive;


class ApprovalController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->category;

        $cutiTahunanApprovals = Approval::with([
            'document.template.category',
            'document.user.profile',
            'document.user.leaveBalance'
        ])
        ->where('status', 'pending')
        ->whereHas('document.template.category', function($query) use ($category) {
            $query->where('name', $category ?? 'cuti');
        })
        ->get();

        $otherApprovals = Approval::with(['document.template.category', 'document.user.profile'])
            ->where('status', 'pending')
            ->whereHas('document.template.category', function($query) use ($category) {
                $query->where('name', '!=', $category ?? 'cuti');
            })
            ->get();

        return view('approvals.index', compact('cutiTahunanApprovals', 'otherApprovals', 'category'));
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

        $category = $approval->document->template->category->name ?? 'cuti';

        Log::create([
            'user_id'    => Auth::id(),
            'action'     => 'confirm_approval',
            'detail'     => "Admin menyetujui dokumen nomor: {$approval->document->document_number}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('approvals.index', ['category' => $category])
                        ->with('success', 'Dokumen disetujui dan cuti diperbarui.');
    }
    public function uploadSigned(Request $request, $id)
    {
        $request->validate([
            'file_signed' => 'required|file|mimes:pdf|max:20480',
        ]);

        $approval = Approval::with('document.template.category')->findOrFail($id);
        $document = $approval->document;

        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $filename = 'surat_' . $document->id . '_' . time() . '.pdf';
        $path = $request->file('file_signed')->storeAs('documents', $filename, 'public');

        $document->file_path = $path;
        $document->save();

        Log::create([
            'user_id'    => Auth::id(),
            'action'     => 'upload_signed_file',
            'detail' => "Admin mengunggah file TTD untuk dokumen nomor: {$document->document_number}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Ambil nama kategori
        $kategori = $document->template->category->name ?? 'cuti';

        return redirect()->route('approvals.index', ['category' => $kategori])
                        ->with('success', 'File berhasil diunggah dan diperbarui.');
    }
    public function categoryList()
    {
        $categories = Category::with('templates.documents.approval')->get();

        foreach ($categories as $category) {
            $count = 0;

            foreach ($category->templates as $template) {
                foreach ($template->documents as $document) {
                    if ($document->approval && $document->approval->status === 'pending') {
                        $count++;
                    }
                }
            }

            $category->pending_count = $count;
        }

        return view('approvals.category_list', compact('categories'));
    }
    public function reject(Request $request, $id)
    {
        $request->validate([
            'alasan' => 'required|string',
        ]);

        $approval = Approval::with('document.template.category')->findOrFail($id);
        $approval->status = 'rejected';
        $approval->alasan = $request->alasan;
        $approval->save();

        $category = $approval->document->template->category->name ?? 'cuti';

        Log::create([
            'user_id'    => Auth::id(),
            'action'     => 'reject_approval',
            'detail'     => "Admin menolak dokumen nomor: {$approval->document->document_number}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('approvals.index', ['category' => $category])
                        ->with('success', 'Dokumen ditolak dengan alasan.');
    }
    public function history(Request $request)
    {
        $query = Approval::with([
            'document.template.category',
            'document.user.profile'
        ])->latest();

        // Filter status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter kategori dari nama category di template
        if ($request->has('category') && $request->category !== 'all') {
            $query->whereHas('document.template.category', function ($q) use ($request) {
                $q->where('name', $request->category);
            });
        }

        // Filter berdasarkan tanggal pengajuan dokumen
        if ($request->filled('from')) {
            $query->whereHas('document', function ($q) use ($request) {
                $q->whereDate('tanggal_pengajuan', '>=', Carbon::parse($request->from));
            });
        }

        if ($request->filled('to')) {
            $query->whereHas('document', function ($q) use ($request) {
                $q->whereDate('tanggal_pengajuan', '<=', Carbon::parse($request->to));
            });
        }

        $approvals = $query->get(); // bisa juga pakai paginate jika perlu
        $categories = Category::whereHas('templates.documents.approval')->get();

        return view('history.history', compact('approvals', 'categories'));
    }
    public function downloadAll(Request $request)
{
    $approvals = Approval::with('document')
        ->when($request->status && $request->status !== 'all', function ($q) use ($request) {
            $q->where('status', $request->status);
        })
        ->get();

    $zipFileName = 'arsip_dokumen_' . now()->format('Ymd_His') . '.zip';
    $zipPath = storage_path('app/public/' . $zipFileName);

    $zip = new ZipArchive;
    if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
        foreach ($approvals as $approval) {
            if ($approval->document && $approval->document->file_path && Storage::disk('public')->exists($approval->document->file_path)) {
                $realPath = storage_path('app/public/' . $approval->document->file_path);
                $fileNameInZip = 'surat_' . $approval->document->document_number . '.pdf';
                $zip->addFile($realPath, $fileNameInZip);
            }
        }
        $zip->close();
    } else {
        return redirect()->back()->with('error', 'Gagal membuat file ZIP.');
    }

    return response()->download($zipPath)->deleteFileAfterSend(true);
    }
    public function downloadFiltered(Request $request)
    {
        $query = Approval::with('document.template.category');

        // Filter kategori
        if ($request->filled('category') && $request->category !== 'all') {
            $query->whereHas('document.template.category', function ($q) use ($request) {
                $q->where('name', $request->category);
            });
        }

        // Filter tanggal dari
        if ($request->filled('from')) {
            $query->whereHas('document', function ($q) use ($request) {
                $q->whereDate('tanggal_pengajuan', '>=', Carbon::parse($request->from));
            });
        }

        // Filter tanggal sampai
        if ($request->filled('to')) {
            $query->whereHas('document', function ($q) use ($request) {
                $q->whereDate('tanggal_pengajuan', '<=', Carbon::parse($request->to));
            });
        }

        // Ambil data
        $approvals = $query->get();

        if ($approvals->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada file sesuai filter.');
        }

        $zipFileName = 'filtered_documents_' . now()->format('Ymd_His') . '.zip';
        $zipPath = storage_path('app/public/' . $zipFileName);
        $zip = new ZipArchive;

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($approvals as $approval) {
                $document = $approval->document;
                if ($document && $document->file_path && Storage::disk('public')->exists($document->file_path)) {
                    $filePath = storage_path('app/public/' . $document->file_path);
                    $zip->addFile($filePath, 'surat_' . $document->document_number . '.pdf');
                }
            }
            $zip->close();
        } else {
            return redirect()->back()->with('error', 'Gagal membuat ZIP file.');
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }
}