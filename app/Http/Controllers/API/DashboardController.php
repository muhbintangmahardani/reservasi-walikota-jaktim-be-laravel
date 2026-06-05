<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\LoginLog;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Mengambil data Log Aktivitas Login untuk Dashboard
     */
    public function getLoginLogs()
    {
        try {
            // Ambil 5 log terakhir beserta data usernya
            $logs = LoginLog::with('user')
                ->latest() // Sama dengan orderBy('created_at', 'desc')
                ->take(5)
                ->get()
                ->map(function ($log) {
                    return [
                        'id' => $log->id,
                        'action' => $log->action,
                        // Jika user dihapus, nama akan jadi 'Sistem / Unknown'
                        'user' => $log->user ? $log->user->name : 'Sistem / Unknown',
                        'status' => $log->status,
                        'created_at' => $log->created_at,
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'Data log berhasil ditarik',
                'data' => $logs
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data log: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }
}