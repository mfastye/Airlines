<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlightSeat extends Model
{
    use HasFactory;

    protected $fillable = [
        'flight_id', 'class', 'total_seats', 'available_seats',
        'adult_price', 'child_price', 'infant_price',
        'baggage_allowance_kg', 'hand_baggage_kg',
        'meal_included', 'wifi_available', 'entertainment_available',
        'extra_services', 'provider_id', 'status',
    ];

    protected $casts = [
        'adult_price' => 'decimal:2',
        'child_price' => 'decimal:2',
        'infant_price' => 'decimal:2',
        'meal_included' => 'boolean',
        'wifi_available' => 'boolean',
        'entertainment_available' => 'boolean',
    ];

    public function flight() { return $this->belongsTo(Flight::class); }
    public function provider() { return $this->belongsTo(Provider::class); }
    public function bookings() { return $this->hasMany(Booking::class); }

    public function decrementSeats(int $count = 1): bool
    {
        if ($this->available_seats < $count) return false;
        $this->decrement('available_seats', $count);
        if ($this->available_seats <= 0) {
            $this->update(['status' => 'sold_out']);
        }
        return true;
    }
}
