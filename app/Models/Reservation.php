<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'room_id',
        'event_name',
        'start_time',
        'end_time',
        'location_type',
        'other_location',
        'pejabat_pelaksana',
        'pejabat_pendamping',
        'pakaian',
        'origin_unit',
        'status',
        'rejection_reason',
        'category_label',
    ];

    // Relasi ke tabel users
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke tabel rooms
    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}