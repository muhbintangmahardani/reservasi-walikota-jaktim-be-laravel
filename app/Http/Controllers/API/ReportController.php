<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservation;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{
    // --- FUNGSI PDF DENGAN LOGO ---
    public function generatePdf(Request $request)
    {
        $type = $request->query('type');
        $periode = $request->query('periode');
        $user = $request->user();

        Carbon::setLocale('id');
        date_default_timezone_set('Asia/Jakarta');

        try {
            $query = Reservation::with('room');
            if ($user && $user->role === 'user_bagian') {
                $query->where('user_id', $user->id);
            }

            if ($type === 'bulanan') {
                $date = Carbon::createFromFormat('Y-m', $periode, 'Asia/Jakarta');
                $start = $date->copy()->startOfMonth()->startOfDay();
                $end = $date->copy()->endOfMonth()->endOfDay();
                $title = "Laporan Bulanan: " . $date->translatedFormat('F Y');
            } else {
                $parts = explode('-W', $periode);
                $date = Carbon::now('Asia/Jakarta')->setISODate($parts[0], $parts[1]);
                $start = $date->copy()->startOfWeek()->startOfDay();
                $end = $date->copy()->endOfWeek()->endOfDay();
                $title = "Laporan Mingguan: " . $start->translatedFormat('d M Y') . " - " . $end->translatedFormat('d M Y');
            }

            $reservations = $query->whereBetween('start_time', [$start, $end])->orderBy('start_time', 'asc')->get();

            // 🔥 LOGIKA LOGO: Mengubah gambar ke Base64 agar aman di PDF
            $path = public_path('img/Lambang_Kota_Jakarta_Timur.png'); // Pastikan file ada di sini
            $logoBase64 = '';
            if (file_exists($path)) {
                $typeImg = pathinfo($path, PATHINFO_EXTENSION);
                $dataImg = file_get_contents($path);
                $logoBase64 = 'data:image/' . $typeImg . ';base64,' . base64_encode($dataImg);
            }

            $pdf = Pdf::loadView('pdf.laporan-ruangan', [
                'reservations' => $reservations,
                'title' => $title,
                'logo' => $logoBase64 // Kirim ke blade
            ]);

            $pdf->setPaper('A4', 'landscape');
            return response()->json([
                'status' => 'success',
                'file_data' => base64_encode($pdf->output()),
                'filename' => "Laporan_PDF_{$periode}.pdf"
            ]);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    // --- FUNGSI EXCEL (CSV) ---
    public function exportExcel(Request $request)
    {
        $type = $request->query('type');
        $periode = $request->query('periode');
        $user = $request->user();

        try {
            $query = Reservation::with('room');
            if ($user && $user->role === 'user_bagian') {
                $query->where('user_id', $user->id);
            }

            // (Logika tanggal sama dengan PDF...)
            if ($type === 'bulanan') {
                $date = Carbon::createFromFormat('Y-m', $periode);
                $start = $date->copy()->startOfMonth()->startOfDay();
                $end = $date->copy()->endOfMonth()->endOfDay();
            } else {
                $parts = explode('-W', $periode);
                $date = Carbon::now()->setISODate($parts[0], $parts[1]);
                $start = $date->copy()->startOfWeek()->startOfDay();
                $end = $date->copy()->endOfWeek()->endOfDay();
            }

            $reservations = $query->whereBetween('start_time', [$start, $end])->get();

            // Membuat konten CSV (Excel bisa baca CSV dengan baik)
            $csvHeader = ["No", "Nama Kegiatan", "Unit", "Waktu", "Ruangan", "Status"];
            $csvData = [];
            foreach ($reservations as $i => $r) {
                $csvData[] = [
                    $i + 1,
                    $r->event_name,
                    $r->origin_unit,
                    $r->start_time,
                    $r->room ? $r->room->room_name : $r->other_location,
                    $r->status
                ];
            }

            return response()->json([
                'status' => 'success',
                'data' => $csvData,
                'header' => $csvHeader,
                'filename' => "Laporan_Excel_{$periode}.csv"
            ]);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}