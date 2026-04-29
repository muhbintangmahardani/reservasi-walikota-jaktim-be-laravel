<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Notification;
use App\Models\LoginLog; // 🔥 WAJIB IMPORT: Untuk mencatat log aktivitas
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 2. Cari user berdasarkan email
        $user = User::where('email', $request->email)->first();

        // 3. Cek apakah user ada dan password cocok
        if (!$user || !Hash::check($request->password, $user->password)) {
            
            // 🔥 CATAT LOG: GAGAL LOGIN (Hanya jika emailnya memang ada di database tapi sandi salah)
            if ($user) {
                LoginLog::create([
                    'user_id' => $user->id,
                    'action' => 'Gagal Login (Sandi Salah)',
                    'status' => 'error',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]);
            }

            return response()->json([
                'message' => 'Email atau Password salah.'
            ], 401); 
        }

        // 🔥 CATAT LOG: OTORISASI BERHASIL
        LoginLog::create([
            'user_id' => $user->id,
            'action' => 'Otorisasi Berhasil',
            'status' => 'success',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        // 4. Buat Token (Sanctum)
        $token = $user->createToken('auth_token')->plainTextToken;

        // 5. Kembalikan response sukses beserta data user & token
        return response()->json([
            'message' => 'Login berhasil',
            'token' => $token, 
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'unit_name' => $user->unit_name,
                'role' => $user->role, 
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        // 🔥 CATAT LOG: SESI BERAKHIR (LOGOUT)
        if ($user) {
            LoginLog::create([
                'user_id' => $user->id,
                'action' => 'Sesi Berakhir (Logout)',
                'status' => 'warning', // Pakai warning agar di UI warnanya oranye/kuning
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
        }

        // Hapus token yang sedang digunakan
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil'
        ]);
    }

    // =========================================================================
    // FUNGSI BARU: REQUEST RESET PASSWORD UNTUK PIMPINAN
    // =========================================================================
    public function requestPasswordReset(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // 1. Cek apakah email ada di database
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'Email tidak terdaftar dalam sistem.'], 404);
        }

        // 2. Cari semua akun Admin
        $admins = User::where('role', 'admin_kominfotik')->get();

        if ($admins->isEmpty()) {
            return response()->json(['message' => 'Sistem tidak menemukan Administrator aktif.'], 500);
        }

        // 🔥 CATAT LOG: MEMINTA RESET PASSWORD (Opsional tapi keren untuk tracking)
        LoginLog::create([
            'user_id' => $user->id,
            'action' => 'Meminta Reset Password',
            'status' => 'warning',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        // 3. Kirim notifikasi ke semua Admin
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'title' => 'Permintaan Reset Password 🔐',
                'type' => 'warning',
                'message' => "Pengguna {$user->name} ({$user->email}) meminta bantuan untuk mereset password. Silakan hubungi yang bersangkutan dan ubah passwordnya melalui menu Manajemen Pengguna."
            ]);
        }

        return response()->json(['message' => 'Permintaan berhasil dikirim ke Admin.']);
    }
}