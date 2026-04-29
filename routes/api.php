<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ReservationController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\ReportController;
use App\Http\Controllers\Api\DashboardController;

// Rute Publik (Bisa diakses tanpa token)
Route::post('/login', [AuthController::class, 'login']);

Route::post('/request-reset-password', [AuthController::class, 'requestPasswordReset']);
Route::delete('/notifications/{id}', function($id) {
        \App\Models\Notification::destroy($id);
        return response()->json(['message' => 'Notifikasi ditandai sudah dibaca (dihapus).']);
    });

// Rute Privat (Hanya bisa diakses dengan token Bearer)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);

    // Endpoint untuk narik data ke Dashboard
    Route::get('/login-logs', [DashboardController::class, 'getLoginLogs']);

    // 👇👇👇 INI BARIS YANG HILANG SEBELUMNYA 👇👇👇
    // Rute Khusus Kelola User (Admin)
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
    // 👆👆👆 ================================= 👆👆👆

    // Rute Reservasi & Ruangan
    Route::get('/rooms', [ReservationController::class, 'getRooms']);
    Route::get('/reservations', [ReservationController::class, 'index']);
    Route::post('/reservations', [ReservationController::class, 'store']);
    Route::put('/reservations/{id}/verify', [ReservationController::class, 'verify']);
    
    // Rute Khusus Dashboard Pimpinan/Verifikator
    Route::get('/reservations/pending', [ReservationController::class, 'getPending']);
    Route::get('/reservations/active', [ReservationController::class, 'getActive']);
    
    // Rute Riwayat, Hapus, & Edit Jadwal
    Route::get('/reservations/{id}', [ReservationController::class, 'show']); 
    Route::put('/reservations/{id}', [ReservationController::class, 'update']); 
    Route::get('/my-reservations', [ReservationController::class, 'myHistory']);
    Route::delete('/reservations/{id}', [ReservationController::class, 'destroy']);

    // Rute Laporan PDF
    Route::get('/laporan-ruangan/pdf', [ReportController::class, 'generatePdf']);

    // Rute Laporan Excel
    Route::get('/laporan-ruangan/excel', [ReportController::class, 'exportExcel']);

    // Rute Notifikasi
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/mark-read', [NotificationController::class, 'markAsRead']);
});