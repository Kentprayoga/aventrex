<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Log;

class LogController extends Controller
{
    public function getUserLogs(Request $request)
    {
        $user = $request->user();

        $logs = Log::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get(['action', 'detail', 'created_at']);

        return response()->json($logs);
    }
}