# ⚙️ Smart Room API - Backend (Kominfotik Jakarta Timur)

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white)
![Sanctum](https://img.shields.io/badge/Sanctum-Security-green?style=for-the-badge)

Ini adalah repositori *Backend* untuk **Sistem Informasi Smart Room Kominfotik Jakarta Timur**. API ini bertugas sebagai sistem pusat yang menangani logika bisnis, autentikasi berlapis, validasi jadwal, dan menjaga keamanan data menggunakan arsitektur RESTful API.

## 🚀 Fitur Utama Backend

*   **Role-Based Access Control (RBAC)**: Menggunakan Laravel Sanctum untuk manajemen token yang membedakan hak akses antara **Admin**, **Sekretariat Pimpinan (VIP)**, dan **User Bagian**.
*   **Reservation Engine**: Algoritma validasi cerdas untuk mencegah bentrok jadwal (*overlapping*) pada ruangan yang sama di waktu yang sama.
*   **Live Audit Trail (Security Log)**: Sistem pencatatan log komprehensif yang secara otomatis memantau dan mencatat aktivitas *login*, *logout*, serta kegagalan otorisasi secara *real-time*.
*   **Strict Rate Limiting**: Perlindungan ketat dari serangan *Brute Force* menggunakan *throttle* bawaan Laravel pada semua *endpoint* autentikasi.

## 🛠️ Tech Stack
*   **Framework**: Laravel 13
*   **Authentication**: Laravel Sanctum (Token-based)
*   **Database**: MySQL
*   **Tools**: Laragon, Postman

## 🔧 Panduan Instalasi (Localhost)

**Prasyarat**: PHP >= 8.3, Composer, dan MySQL Server berjalan.

1. **Install Dependensi PHP**
   Jalankan perintah ini di dalam folder `backend`:
   \`\`\`bash
   composer install
   \`\`\`

2. **Konfigurasi Environment**
   Salin file `.env.example` menjadi `.env` lalu sesuaikan konfigurasi database Anda.
   ```bash
    cp .env.example .env
    php artisan key:generate
   ```

   *Contoh isi .env:*
   ```bash
   env
   APP_NAME="Smart Room API"
   APP_ENV=local
   APP_DEBUG=true
   APP_URL=http://localhost:8000
   
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=smart_room_db
   DB_USERNAME=root
   DB_PASSWORD=
   
   FRONTEND_URL=http://localhost:3000
   ```

4. **Migrasi Database & Seeder**
   *(Seeder akan membuatkan tabel beserta akun Admin dan role default)*
   ```bash
   php artisan migrate --seed
   ```

5. **Jalankan Server Lokal**
   ```bash
   php artisan serve
   ```
   *API akan berjalan di `http://localhost:8000`*

## 📝 Beberapa Penambahan Commit
*   `feat(api):` Penambahan *endpoint* atau fitur logika baru.
*   `security(api):` Penambalan celah keamanan atau optimasi validasi.
*   `fix(api):` Perbaikan *bug* atau *error* pada *controller/model*.
