<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'transaction_id', 'booking_id', 'payment_gateway_id', 'amount',
        'currency', 'method', 'status', 'receipt_image', 'notes',
        'rejection_reason', 'confirmed_by', 'confirmed_at', 'gateway_response',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'confirmed_at' => 'datetime',
        'gateway_response' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($payment) {
            if (empty($payment->transaction_id)) {
                $payment->transaction_id = 'TXN-' . strtoupper(Str::random(10));
            }
        });
    }

    public function booking() { return $this->belongsTo(Booking::class); }
    public function gateway() { return $this->belongsTo(PaymentGateway::class, 'payment_gateway_id'); }
    public function confirmedBy() { return $this->belongsTo(User::class, 'confirmed_by'); }
}
