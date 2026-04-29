<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\Notification; 
use Illuminate\Support\Facades\Schema;

class ReservationController extends Controller
{
    // =========================================================================
    // 1. FUNGSI UNTUK USER BAGIAN (KALENDER & DROPDOWN)
    // =========================================================================
    
    public function index()
    {
        // 🛠️ DIPERBAIKI: Menghapus batasan kolom agar aman dari error 500
        $reservations = Reservation::with(['user', 'room'])
            ->orderBy('start_time', 'asc')
            ->get();
        return response()->json($reservations);
    }

    public function getRooms()
    {
        $rooms = Room::where('is_active', true)->get();
        return response()->json($rooms);
    }

    // =========================================================================
    // 2. FUNGSI KHUSUS DASHBOARD PIMPINAN / ASISTEN (SEKPIM)
    // =========================================================================

    public function getPending()
    {
        // 🛠️ DIPERBAIKI: Menghapus batasan kolom agar aman dari error 500
        $reservations = Reservation::with(['user', 'room'])
            ->where('status', 'pending')
            ->orderBy('start_time', 'asc')
            ->get();
        return response()->json(['data' => $reservations]);
    }

    public function getActive()
    {
        // 🛠️ DIPERBAIKI: Menghapus batasan kolom agar aman dari error 500
        $reservations = Reservation::with(['user', 'room'])
            ->where('status', 'verified')
            ->orderBy('start_time', 'asc')
            ->get();
        return response()->json(['data' => $reservations]);
    }

    public function show($id)
    {
        $reservation = Reservation::with(['user', 'room'])->findOrFail($id);
        return response()->json(['data' => $reservation]);
    }


    // =========================================================================
    // 3. FUNGSI CRUD INTI (TAMBAH, VERIFIKASI, EDIT, HAPUS)
    // =========================================================================

    public function store(Request $request)
    {
        $request->validate([
            'event_name' => 'required|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'location_type' => 'required|string',
            'room_id' => 'nullable|exists:rooms,id',
            'origin_unit' => 'nullable|string',
            'pejabat_pelaksana' => 'nullable|string',
            'pejabat_pendamping' => 'nullable|string',
            'pakaian' => 'nullable|string',
            'other_location' => 'nullable|string',
            'category_label' => 'nullable|string',
            'status' => 'nullable|string'
        ]);

        if ($request->location_type === 'ruangan_terdaftar' && $request->room_id) {
            $isConflict = Reservation::where('room_id', $request->room_id)
                ->whereIn('status', ['pending', 'verified']) 
                ->where(function ($query) use ($request) {
                    $query->where('start_time', '<', $request->end_time)
                        ->where('end_time', '>', $request->start_time);
                })->exists();

            if ($isConflict) {
                return response()->json([
                    'message' => 'Maaf, ruangan sudah terisi pada waktu tersebut. Silakan pilih waktu atau ruangan lain.'
                ], 422);
            }
        }

        try {
            $data = $request->all();
            
            if ($request->user()) {
                $data['user_id'] = $request->user()->id;
            }

            if (!isset($data['status'])) {
                $data['status'] = 'pending';
            }

            $reservation = Reservation::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Reservasi/Jadwal berhasil disimpan.',
                'data' => $reservation
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menyimpan ke database: ' . $e->getMessage()
            ], 500);
        }
    }

    public function verify(Request $request, $id)
    {
        try {
            $reservation = Reservation::findOrFail($id);
            $reservation->status = $request->status;
            
            if ($request->status === 'rejected' && Schema::hasColumn('reservations', 'rejection_reason')) {
                $reservation->rejection_reason = $request->rejection_reason;
            }

            if ($request->status === 'verified' && Schema::hasColumn('reservations', 'category_label')) {
                $reservation->category_label = $request->category_label;
            }
            
            $reservation->save();

            if ($reservation->user_id) {
                $isVerified = $request->status === 'verified';
                Notification::create([
                    'user_id' => $reservation->user_id,
                    'title' => $isVerified ? 'Reservasi Diterima!' : 'Reservasi Ditolak',
                    'type' => $isVerified ? 'success' : 'error',
                    'message' => $isVerified 
                        ? "Pengajuan '{$reservation->event_name}' telah disetujui."
                        : "Maaf, pengajuan '{$reservation->event_name}' ditolak. Alasan: " . ($request->rejection_reason ?? 'Tidak ada alasan khusus.')
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Status reservasi berhasil diupdate.',
                'data' => $reservation
            ]);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Error Backend: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $reservation = Reservation::findOrFail($id);
            $updateData = $request->except(['user_id']); 
            $reservation->update($updateData);

            if ($reservation->user_id) {
                Notification::create([
                    'user_id' => $reservation->user_id,
                    'title' => 'Jadwal Anda Diperbarui',
                    'type' => 'info',
                    'message' => "Detail pengajuan '{$reservation->event_name}' telah diubah atau disesuaikan oleh Sekretariat Pimpinan."
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Perubahan berhasil disimpan!',
                'data' => $reservation
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    // =========================================================================
    // 4. FUNGSI RIWAYAT & HAPUS (USER BAGIAN)
    // =========================================================================

    public function myHistory(Request $request)
    {
        // 🛠️ DIPERBAIKI: Menghapus batasan kolom room:id,room_name menjadi room saja
        $reservations = Reservation::where('user_id', $request->user()->id)
            ->with(['room'])
            ->orderBy('start_time', 'desc')
            ->get();

        return response()->json($reservations);
    }

    public function destroy(Request $request, $id)
    {
        try {
            $reservation = Reservation::findOrFail($id);

            if ($reservation->user_id !== $request->user()->id) {
                return response()->json(['message' => 'Akses ditolak. Ini bukan pengajuan Anda.'], 403);
            }

            if ($reservation->status !== 'pending') {
                return response()->json(['message' => 'Hanya pengajuan dengan status Menunggu Verifikasi yang bisa dibatalkan.'], 400);
            }

            $reservation->delete();
            return response()->json(['message' => 'Pengajuan berhasil dibatalkan/dihapus.']);
            
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menghapus data: ' . $e->getMessage()], 500);
        }
    }
}