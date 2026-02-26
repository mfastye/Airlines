<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentGateway extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name_en', 'name_ar', 'logo', 'instructions_en', 'instructions_ar',
        'type', 'api_endpoint', 'api_key', 'api_secret', 'config',
        'status', 'balance',
    ];

    protected $casts = [
        'config' => 'array',
        'balance' => 'decimal:2',
    ];

    protected $hidden = ['api_key', 'api_secret'];

    public function payments() { return $this->hasMany(Payment::class); }
    public function financialAccount() { return $this->hasOne(FinancialAccount::class); }

    public function getNameAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->name_ar : $this->name_en;
    }
}
