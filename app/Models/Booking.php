<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'booking_reference', 'customer_id', 'flight_id', 'flight_seat_id',
        'return_flight_id', 'return_flight_seat_id', 'trip_type', 'class',
        'adults', 'children', 'infants', 'total_price', 'commission_amount',
        'net_price', 'currency', 'status', 'payment_status', 'has_visa',
        'special_requests', 'admin_notes', 'provider_id', 'agent_id',
        'booked_by', 'ticket_number', 'ticket_image',
        'confirmed_at', 'cancelled_at', 'cancellation_reason',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'net_price' => 'decimal:2',
        'has_visa' => 'boolean',
        'confirmed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($booking) {
            if (empty($booking->booking_reference)) {
                $booking->booking_reference = 'BK-' . strtoupper(Str::random(8));
            }
        });
    }

    public function customer() { return $this->belongsTo(Customer::class); }
    public function flight() { return $this->belongsTo(Flight::class); }
    public function flightSeat() { return $this->belongsTo(FlightSeat::class); }
    public function returnFlight() { return $this->belongsTo(Flight::class, 'return_flight_id'); }
    public function returnFlightSeat() { return $this->belongsTo(FlightSeat::class, 'return_flight_seat_id'); }
    public function provider() { return $this->belongsTo(Provider::class); }
    public function agent() { return $this->belongsTo(Agent::class); }
    public function bookedBy() { return $this->belongsTo(User::class, 'booked_by'); }
    public function passengers() { return $this->hasMany(BookingPassenger::class); }
    public function payments() { return $this->hasMany(Payment::class); }

    public function getTotalPassengers(): int
    {
        return $this->adults + $this->children + $this->infants;
    }

    public function isPending(): bool { return $this->status === 'pending'; }
    public function isConfirmed(): bool { return $this->status === 'confirmed'; }
    public function isCancelled(): bool { return $this->status === 'cancelled'; }
}
