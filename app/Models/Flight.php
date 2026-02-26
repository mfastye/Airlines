<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Flight extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'flight_number', 'airline_id', 'departure_airport_id', 'arrival_airport_id',
        'departure_time', 'arrival_time', 'duration_minutes', 'aircraft_type',
        'stops', 'stop_details', 'status', 'provider_id', 'created_by',
    ];

    protected $casts = [
        'departure_time' => 'datetime',
        'arrival_time' => 'datetime',
    ];

    public function airline() { return $this->belongsTo(Airline::class); }
    public function departureAirport() { return $this->belongsTo(Airport::class, 'departure_airport_id'); }
    public function arrivalAirport() { return $this->belongsTo(Airport::class, 'arrival_airport_id'); }
    public function seats() { return $this->hasMany(FlightSeat::class); }
    public function bookings() { return $this->hasMany(Booking::class); }
    public function provider() { return $this->belongsTo(Provider::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }

    public function getDurationFormattedAttribute(): string
    {
        if (!$this->duration_minutes) return '';
        $hours = intdiv($this->duration_minutes, 60);
        $minutes = $this->duration_minutes % 60;
        return "{$hours}h {$minutes}m";
    }

    public function getAvailableSeats(string $class = 'economy'): int
    {
        $seat = $this->seats()->where('class', $class)->where('status', 'available')->first();
        return $seat ? $seat->available_seats : 0;
    }

    public function getLowestPrice(string $class = 'economy'): float
    {
        $seat = $this->seats()->where('class', $class)->where('status', 'available')->first();
        return $seat ? (float)$seat->adult_price : 0;
    }
}
