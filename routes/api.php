<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ReservationController;
use App\Http\Controllers\API\NotificationController;

// Rute Publik (Bisa diakses tanpa token)
Route::post('/login', [AuthController::class, 'login']);

// Rute Privat (Hanya bisa diakses dengan token Bearer)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);

    // Rute Reservasi & Ruangan
    Route::get('/rooms', [ReservationController::class, 'getRooms']);
    Route::get('/reservations', [ReservationController::class, 'index']);
    Route::post('/reservations', [ReservationController::class, 'store']);
    Route::put('/reservations/{id}/verify', [ReservationController::class, 'verify']);
    
    // Rute Khusus Dashboard Pimpinan/Verifikator
    Route::get('/reservations/pending', [ReservationController::class, 'getPending']);
    Route::get('/reservations/active', [ReservationController::class, 'getActive']);
    
    // ==========================================================
    // INI 2 BARIS YANG DITAMBAHKAN AGAR FITUR EDIT JADWAL BERJALAN
    // ==========================================================
    Route::get('/reservations/{id}', [ReservationController::class, 'show']); // Untuk load data ke form edit
    Route::put('/reservations/{id}', [ReservationController::class, 'update']); // Untuk simpan perubahan
    
    // Rute Riwayat & Hapus
    Route::get('/my-reservations', [ReservationController::class, 'myHistory']);
    Route::delete('/reservations/{id}', [ReservationController::class, 'destroy']);

    // Rute Notifikasi
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/mark-read', [NotificationController::class, 'markAsRead']);
});