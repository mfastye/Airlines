<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Airport extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_en', 'name_ar', 'code', 'iata_code',
        'city_en', 'city_ar', 'country_en', 'country_ar',
        'timezone', 'latitude', 'longitude', 'status',
    ];

    public function departureFlights() { return $this->hasMany(Flight::class, 'departure_airport_id'); }
    public function arrivalFlights() { return $this->hasMany(Flight::class, 'arrival_airport_id'); }

    public function getNameAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->name_ar : $this->name_en;
    }

    public function getCityAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->city_ar : $this->city_en;
    }

    public function getFullNameAttribute()
    {
        $name = $this->getNameAttribute();
        $city = $this->getCityAttribute();
        return "{$name} ({$this->code}) - {$city}";
    }
}
