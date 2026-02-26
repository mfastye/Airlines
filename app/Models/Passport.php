<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Passport extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id', 'passport_number', 'full_name', 'first_name', 'last_name',
        'date_of_birth', 'nationality', 'gender', 'issue_date', 'expiry_date',
        'issuing_country', 'passport_image', 'is_expired', 'ocr_data',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'is_expired' => 'boolean',
        'ocr_data' => 'array',
    ];

    public function customer() { return $this->belongsTo(Customer::class); }

    public function checkExpiry(): bool
    {
        if ($this->expiry_date && $this->expiry_date->isPast()) {
            $this->update(['is_expired' => true]);
            return true;
        }
        return false;
    }
}
