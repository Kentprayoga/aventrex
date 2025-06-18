<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Approval;
use App\Models\User;
use App\Models\Document;
use App\Models\Log;
use App\Models\Template;
use App\Models\Profile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ApprovalController extends Controller
{
  
    public function getByUser($userId)
    {
        // Validasi apakah user dengan ID tersebut ada
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Ambil semua approval yang terkait dengan user tersebut
        $approvals = Approval::where('user_id', $userId)->get();
        return response()->json($approvals);
    }
    /**
     * Get approval summary for the authenticated user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */                                     
// public function history(Request $request)
// {
//     $user = $request->user();

//     $histories = Approval::where('user_id', $user->id)
//         ->with(['document.category', 'document.template'])
//         ->orderByDesc('created_at')
//         ->get()
//         ->map(function($approval) {
//             return [
//                 'document_id'   => $approval->document->id,
//                 'template_name' => optional($approval->document->template)->name,
//                 'category_name' => optional($approval->document->category)->name,
//                 'status'        => $approval->status,
//             ];
//         });

//     return response()->json($histories);
// }


//     public function show($documentId)
//     {
//         $user = Auth::user();

//         if (!$user) {
//             return response()->json(['message' => 'Unauthorized'], 401);
//         }

//         $approval = Approval::with([
//             'document.category',
//             'document.user.profile'
//         ])
//         ->where('document_id', $documentId)
//         ->where('user_id', $user->id)
//         ->latest()
//         ->firstOrFail();

//         $document = $approval->document;

//         return response()->json([
//             'nama'          => $document->user->profile->name,
//             'nip'           => $document->user->profile->nip,
//             'kategori'      => $document->category->name,
//             'nomor_dokumen' => $document->document_number,
//             'file_url'      => asset('storage/' . $document->file_path),
//             'status'        => $approval->status,
//         ]);
//     }

public function history(Request $request)
{
    $user = $request->user();

    $histories = Approval::where('user_id', $user->id)
        ->with(['document.template.category'])  // relasi lengkapnya
        ->get()
        ->map(function ($approval) {
            return [
                'id' => $approval->id,
                'category_name' => $approval->document->template->category->name ?? 'Tidak diketahui',
                'document_number' => $approval->document->document_number ?? '-',
                'status' => $approval->status,
                'tanggal_pengajuan' => $approval->document->tanggal_pengajuan ?? '-', 
            ];
        });
        Log::create([
            'user_id' => $user->id,
            'action'  => 'melihat history approval',
        ]);
    return response()->json($histories, 200);
}



    // Detail approval berdasarkan id approval
public function historyDetail($id, Request $request)
{
    $user = $request->user();

    $approval = Approval::where('id', $id)
        ->where('user_id', $user->id)
        ->with([
            'document.profile',        // data profil pemilik dokumen
            'document.template.category' // data template dan category
        ])
        ->firstOrFail();
        Log::create([
            'user_id' => $user->id,
            'action'  => 'melihat data history approval',
        ]);

    return response()->json([
        'id' => $approval->id,
        'status' => $approval->status,
        'alasan' => $approval->alasan ?? '-',
        'document_number' => $approval->document->document_number ?? '-',
        'category_name' => $approval->document->template->category->name ?? 'Tidak diketahui',
        'profile_name' => $approval->document->profile->name ?? 'Tidak diketahui',
        'profile_nip' => $approval->document->profile->nip ?? '-',
        'file_path' => $approval->document->file_path ?? null,
        'tanggal_pengajuan' => $approval->document->tanggal_pengajuan ?? null,
        // tambahan data jika perlu
    ], 200);
}

public function cancel($id, Request $request)
{
    $user = $request->user();

    $approval = Approval::where('id', $id)
        ->where('user_id', $user->id)
        ->first();

    if (!$approval) {
        return response()->json(['message' => 'Approval tidak ditemukan.'], 404);
    }

    if ($approval->status !== 'pending') {
        return response()->json(['message' => 'Approval tidak bisa dibatalkan karena status bukan pending.'], 400);
    }

    $approval->status = 'cancelled';
    $approval->save();

    return response()->json([
        'message' => 'Approval berhasil dibatalkan.',
        'data' => $approval  // opsional: bisa dihapus jika tidak perlu
    ], 200);
}

}