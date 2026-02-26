<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'first_name', 'last_name', 'email', 'phone',
        'phone_country_code', 'whatsapp', 'whatsapp_country_code',
        'date_of_birth', 'gender', 'nationality',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function passports() { return $this->hasMany(Passport::class); }
    public function bookings() { return $this->hasMany(Booking::class); }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getActivePassport()
    {
        return $this->passports()->where('is_expired', false)->latest()->first();
    }
}
