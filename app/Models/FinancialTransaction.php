<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'financial_account_id', 'type', 'amount', 'balance_after',
        'currency', 'reference_type', 'reference_id',
        'description_en', 'description_ar', 'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_after' => 'decimal:2',
    ];

    public function account() { return $this->belongsTo(FinancialAccount::class, 'financial_account_id'); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }

    public function getDescriptionAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->description_ar : $this->description_en;
    }
}
