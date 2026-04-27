<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('reservations', function (Blueprint $table) {
        $table->id();
        // Relasi ke tabel users dan rooms
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->foreignId('room_id')->constrained('rooms')->onDelete('cascade');
        
        // Data Acara
        $table->string('event_name');
        $table->dateTime('start_time');
        $table->dateTime('end_time');
        $table->string('location_type')->default('ruangan_terdaftar'); 
        $table->text('other_location')->nullable(); 
        
        // Detail Pelaksana (Nullable agar bisa diisi bertahap atau diedit Pimpinan)
        $table->string('pejabat_pelaksana')->nullable();
        $table->string('pejabat_pendamping')->nullable();
        $table->string('pakaian')->nullable(); 
        $table->string('origin_unit')->nullable(); 
        
        // Status & Verifikasi
        $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending');
        $table->text('rejection_reason')->nullable(); 
        $table->string('category_label',)->nullable(); 
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
