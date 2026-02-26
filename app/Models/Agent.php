<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Agent extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'phone', 'phone_country_code', 'whatsapp', 'whatsapp_country_code',
        'email', 'provider_id', 'balance', 'currency', 'commission_amount',
        'commission_type', 'status', 'api_key', 'api_secret', 'api_enabled',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'api_enabled' => 'boolean',
    ];

    public function provider() { return $this->belongsTo(Provider::class); }
    public function users() { return $this->hasMany(User::class); }
    public function bookings() { return $this->hasMany(Booking::class); }
    public function financialAccount() { return $this->hasOne(FinancialAccount::class); }
}
