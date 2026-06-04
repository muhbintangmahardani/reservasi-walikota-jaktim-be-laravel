<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8fafc; padding: 40px 20px; margin: 0; }
        .card { max-width: 500px; margin: 0 auto; background: #ffffff; border-radius: 16px; padding: 32px; text-align: center; border: 1px solid #e2e8f0; }
        .title { font-size: 22px; font-weight: 800; color: #0f172a; margin-bottom: 8px; }
        .text { font-size: 14px; color: #475569; line-height: 1.6; margin-bottom: 24px; text-align: left;}
        .btn { display: inline-block; padding: 14px 28px; background-color: #2563eb; color: #ffffff !important; text-decoration: none; border-radius: 12px; font-weight: bold; font-size: 14px; margin-bottom: 24px;}
        .footer { font-size: 12px; color: #94a3b8; margin-top: 32px; border-top: 1px solid #f1f5f9; padding-top: 16px; }
    </style>
</head>
<body>
    <div class="card">
        <h2 class="title">Pemulihan Kata Sandi</h2>
        <p class="text">
            Halo <b>{{ $name }}</b>,<br><br>
            Kami menerima permintaan untuk mengatur ulang kata sandi akun Smart Room Anda. Jika Anda merasa meminta ini, silakan klik tombol di bawah untuk membuat sandi baru (tautan berlaku selama 60 menit).
        </p>
        
        <a href="{{ $resetUrl }}" class="btn">Ganti Kata Sandi Saya</a>

        <p class="text" style="font-size: 12.5px;">
            Jika Anda tidak meminta perubahan kata sandi, abaikan saja email ini. Akun Anda tetap aman.
        </p>

        <div class="footer">
            &copy; {{ date('Y') }} Suku Dinas Kominfotik Jakarta Timur.<br>Sistem Manajemen Reservasi.
        </div>
    </div>
</body>
</html>