<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Room;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ==========================================
        // 1. SEEDER PENGGUNA (USERS)
        // Semua password disetel menjadi: password
        // ==========================================

        User::create([
            'name' => 'Admin Kominfotik',
            'email' => 'admin@kominfotik.jt',
            'password' => Hash::make('password'),
            'unit_name' => 'Kominfotik',
            'role' => 'admin_kominfotik',
        ]);

        User::create([
            'name' => 'Bapak Walikota',
            'email' => 'walikota@jt.go.id',
            'password' => Hash::make('password'),
            'unit_name' => 'Pimpinan',
            'role' => 'pimpinan',
        ]);

        User::create([
            'name' => 'Asisten Perekonomian',
            'email' => 'asisten@jt.go.id',
            'password' => Hash::make('password'),
            'unit_name' => 'Sekretariat Kota',
            'role' => 'asisten',
        ]);

        User::create([
            'name' => 'User Bagian Umum',
            'email' => 'umum@jt.go.id',
            'password' => Hash::make('password'),
            'unit_name' => 'Bagian Umum dan Protokol',
            'role' => 'user_bagian',
        ]);

        User::create([
            'name' => 'User Bagian Kesra',
            'email' => 'kesra@jt.go.id',
            'password' => Hash::make('password'),
            'unit_name' => 'Bagian Kesejahteraan Rakyat',
            'role' => 'user_bagian',
        ]);

        // ==========================================
        // 2. SEEDER RUANGAN (ROOMS)
        // ==========================================

        Room::create([
            'room_name' => 'Ruang Pola',
            'capacity' => 150,
            'facilities' => 'Proyektor, AC, Sound System, Panggung, Podium',
            'is_active' => true,
        ]);

        Room::create([
            'room_name' => 'Ruang Rapat Utama (RRU)',
            'capacity' => 50,
            'facilities' => 'Proyektor, AC, Mic Meja, LED TV',
            'is_active' => true,
        ]);

        Room::create([
            'room_name' => 'Ruang Rapat Khusus Walikota',
            'capacity' => 15,
            'facilities' => 'Smart Board, AC, Sofa, Minibar',
            'is_active' => true,
        ]);
    }
}