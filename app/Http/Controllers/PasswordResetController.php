<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\User;
use App\Mail\ResetPasswordMail;
use Carbon\Carbon;

class PasswordResetController extends Controller
{
    // 1. Fungsi Mengirim Email Link Reset
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // Cek apakah email ada di database
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'Email tidak terdaftar dalam sistem kami.'], 404);
        }

        // Buat Token Acak 60 Karakter
        $token = Str::random(60);

        // Hapus token lama jika ada, lalu simpan token baru (Tabel default Laravel 10/11)
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();
        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        // Kirim Email
        Mail::to($request->email)->send(new ResetPasswordMail($token, $request->email, $user->name));

        return response()->json(['message' => 'Tautan reset berhasil dikirim!']);
    }

    // 2. Fungsi Eksekusi Ganti Sandi Baru
    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|min:8|confirmed',
        ]);

        // Cari token di database
        $resetRequest = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$resetRequest) {
            return response()->json(['message' => 'Token reset tidak valid atau salah.'], 400);
        }

        // Cek Kedaluwarsa Token (Misal: Batas waktu 60 menit)
        if (Carbon::parse($resetRequest->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return response()->json(['message' => 'Tautan reset sudah kedaluwarsa. Silakan minta ulang.'], 400);
        }

        // Jika lolos semua, Update Sandi User
        $user = User::where('email', $request->email)->first();
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        // Hapus token agar tidak bisa dipakai lagi
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json(['message' => 'Kata sandi berhasil diperbarui!']);
    }
}