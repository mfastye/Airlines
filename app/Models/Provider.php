<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Provider extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'business_name_en', 'business_name_ar', 'contact_person',
        'phone', 'phone_country_code', 'whatsapp', 'whatsapp_country_code',
        'email', 'address', 'logo', 'commission_amount', 'commission_type',
        'allowed_airlines', 'permissions', 'status', 'balance', 'currency',
    ];

    protected $casts = [
        'allowed_airlines' => 'array',
        'permissions' => 'array',
        'balance' => 'decimal:2',
        'commission_amount' => 'decimal:2',
    ];

    public function users() { return $this->hasMany(User::class); }
    public function agents() { return $this->hasMany(Agent::class); }
    public function flights() { return $this->hasMany(Flight::class); }
    public function flightSeats() { return $this->hasMany(FlightSeat::class); }
    public function bookings() { return $this->hasMany(Booking::class); }
    public function financialAccount() { return $this->hasOne(FinancialAccount::class); }
    public function financialTransactions()
    {
        return $this->hasManyThrough(FinancialTransaction::class, FinancialAccount::class);
    }

    public function getBusinessNameAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->business_name_ar : $this->business_name_en;
    }
}
