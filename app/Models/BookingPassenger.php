<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingPassenger extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id', 'passport_id', 'first_name', 'last_name',
        'passport_number', 'date_of_birth', 'gender', 'nationality',
        'type', 'seat_number',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function booking() { return $this->belongsTo(Booking::class); }
    public function passport() { return $this->belongsTo(Passport::class); }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
