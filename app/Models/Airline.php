<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Airline extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name_en', 'name_ar', 'code', 'iata_code', 'logo',
        'country_en', 'country_ar', 'description_en', 'description_ar',
        'website', 'phone', 'email', 'status',
    ];

    public function flights() { return $this->hasMany(Flight::class); }

    public function getNameAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->name_ar : $this->name_en;
    }

    public function getCountryAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->country_ar : $this->country_en;
    }
}
