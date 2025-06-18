<?php

use App\Http\Controllers\AdminLogController;
use App\Http\Controllers\AuthController;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\DashboardController;
use App\Http\Middleware\AdminOnly;

// Rute untuk login/logout
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Group routes with auth middleware
Route::middleware([AdminOnly::class])->group(function () {

    // Menampilkan riwayat dokumen dengan filter
    Route::get('/documents', [DocumentController::class, 'index'])->name('history.index');

    // Cetak riwayat dokumen yang difilter (semua)
    Route::get('/documents/cetak', [DocumentController::class, 'cetakHistory'])->name('history.cetak');

    // Cetak satu dokumen berdasarkan ID
    Route::get('/documents/{id}/cetak', [DocumentController::class, 'cetakSatu'])->name('history.cetak.satu');

    // Admin routes under '/admin' prefix
    Route::prefix('admin')->group(function () {
        Route::get('/approvals', [ApprovalController::class, 'index'])->name('approvals.index');
        Route::post('/approvals/{id}/approve', [ApprovalController::class, 'approve'])->name('approvals.approve');
        Route::post('/approvals/{id}/reject', [ApprovalController::class, 'reject'])->name('approvals.reject');
    });
    
    Route::get('/template', [TemplateController::class, 'index'])->name('template.index');
    Route::post('/template', [TemplateController::class, 'store'])->name('template.store');
    Route::get('/template/{id}/edit', [TemplateController::class, 'edit'])->name('template.edit');
    Route::put('/template/{id}', [TemplateController::class, 'update'])->name('template.update');
    Route::delete('/template/{id}', [TemplateController::class, 'destroy'])->name('template.destroy');

    // Route untuk kategori
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    // Route untuk template

    // Route untuk posisi dan divisi
    Route::put('/position/{id}', [PositionController::class, 'update'])->name('position.update');
    Route::delete('/position/{id}', [PositionController::class, 'destroy'])->name('position.destroy');
    Route::put('/division/{id}', [DivisionController::class, 'update'])->name('division.update');
    Route::delete('/division/{id}', [DivisionController::class, 'destroy'])->name('division.destroy');
    // Route untuk mengelola user
    Route::get('/user', [UserController::class, 'index'])->name('user.index');
    Route::post('/user', [UserController::class, 'store'])->name('user.store');
    Route::get('/user/{id}/edit', [UserController::class, 'edit'])->name('user.edit');
    Route::put('/user/{id}', [UserController::class, 'update'])->name('user.update');
    Route::delete('/user/{id}', [UserController::class, 'destroy'])->name('user.destroy');
    Route::get('/user/create', [UserController::class, 'create'])->name('user.create');
    Route::post('/division/store', [DivisionController::class, 'store'])->name('division.store');
    Route::post('/position/store', [PositionController::class, 'store'])->name('position.store');

    // Route untuk mengaktifkan dan menonaktifkan pengguna
    Route::post('/user/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('user.toggleStatus');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('pages.dashboard');
    Route::get('/admin/chat', [MessageController::class, 'index'])->name('admin.chat');
    Route::post('/admin/chat', [MessageController::class, 'index']);
    Route::delete('/admin/chat/message/{id}', [MessageController::class, 'deleteMessage'])->name('admin.chat.delete');
    Route::delete('/admin/chat/{userId}/clear', [MessageController::class, 'clearMessages'])->name('admin.chat.clear');
    Route::get('/admin/logs', [AdminLogController::class, 'index'])->name('adminlog.index');
    Route::get('/admin/logs/export/pdf', [AdminLogController::class, 'exportPdf'])->name('adminlog.pdf');

});

// Route untuk dashboard, dengan middleware 'auth'
Route::get('/', function () {
    return view('auth.login'); // ini file splash.blade.php
});