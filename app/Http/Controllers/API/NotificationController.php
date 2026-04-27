<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
    // Mengambil notifikasi user yang sedang login
    public function index(Request $request)
    {
        $notifications = Notification::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->take(10) // Ambil 10 terbaru agar tidak berat
            ->get()
            ->map(function ($notif) {
                return [
                    'id' => $notif->id,
                    'title' => $notif->title,
                    'message' => $notif->message,
                    'type' => $notif->type,
                    'is_read' => (bool) $notif->is_read,
                    // diffForHumans akan menghasilkan tulisan "2 hours ago", "just now"
                    'time' => $notif->created_at->diffForHumans() 
                ];
            });

        return response()->json($notifications);
    }

    // Menandai semua notifikasi sudah dibaca
    public function markAsRead(Request $request)
    {
        Notification::where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['message' => 'Semua notifikasi telah ditandai dibaca.']);
    }
}