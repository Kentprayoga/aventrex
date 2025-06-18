<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Approval;
use App\Models\Document;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    // public function index()
    // {
    //     $user = Auth::user();

    //     if (!$user) {
    //         Log::error('User not authenticated');
    //         return response()->json(['message' => 'Unauthorized'], 401);
    //     }

    //     $approved = $user->approvals()->where('status', 'approved')->count();
    //     $pending = $user->approvals()->where('status', 'pending')->count();
    //     $rejected = $user->approvals()->where('status', 'rejected')->count();

    //     return response()->json([
    //         'name' => optional($user->profile)->name ?? $user->email,
    //         'approved' => $approved,
    //         'pending' => $pending,
    //         'rejected' => $rejected,
    //     ]);
    // }


    public function index()
{
    $user = Auth::user();

    if (!$user) {
        Log::error('User not authenticated');
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    $approved = Approval::where('user_id', $user->id)->where('status', 'approved')->count();
    $pending = Approval::where('user_id', $user->id)->where('status', 'pending')->count();
    $rejected = Approval::where('user_id', $user->id)->where('status', 'rejected')->count();

    return response()->json([
        'name' => optional($user->profile)->name ?? $user->email,
        'approved' => $approved,
        'pending' => $pending,
        'rejected' => $rejected,
    ],202);
}

}