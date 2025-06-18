<?php

use App\Http\Controllers\Api\ApprovalController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\TemplateController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\MessageController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::post('/login', [AuthController::class, 'login']);
// Route::middleware('auth:sanctum')->post('/logout', function (Request $request) {
//     // Menghapus token yang digunakan oleh pengguna
//     $request->user()->tokens->each(function ($token) {
//         $token->delete();
//     });

//     return response()->json(['message' => 'Successfully logged out']);
// });
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/messages/{userId}', [MessageController::class, 'getMessagesWithUser']);
    Route::post('/messages', [MessageController::class, 'store']);
    Route::get('/profile-basic', [MessageController::class, 'getUserProfile']);
    Route::post('/approvals/{id}/cancel', [ApprovalController::class, 'cancel']);
    Route::post('/documents', [DocumentController::class, 'store']);

    Route::get('/approvals/history', [ApprovalController::class, 'history']);
    Route::get('approvals/history/{id}', [ApprovalController::class, 'historyDetail']);
    Route::get('/home-summary', [HomeController::class, 'index']);
    Route::get('/profile', [ProfileController::class, 'getProfile']);
    Route::post('/update-password', [ProfileController::class, 'updatePassword']);
});



// routes/api.php

Route::get('/templates', [TemplateController::class, 'index']);
Route::get('/template', [TemplateController::class, 'listTemplates']);