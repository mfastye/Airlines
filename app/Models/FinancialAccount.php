<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_name_en', 'account_name_ar', 'account_type',
        'provider_id', 'agent_id', 'payment_gateway_id',
        'balance', 'currency', 'status',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
    ];

    public function provider() { return $this->belongsTo(Provider::class); }
    public function agent() { return $this->belongsTo(Agent::class); }
    public function paymentGateway() { return $this->belongsTo(PaymentGateway::class); }
    public function transactions() { return $this->hasMany(FinancialTransaction::class); }

    public function credit(float $amount, string $descEn, string $descAr, ?int $userId = null, ?string $refType = null, ?int $refId = null): FinancialTransaction
    {
        $this->increment('balance', $amount);
        return $this->transactions()->create([
            'type' => 'credit',
            'amount' => $amount,
            'balance_after' => $this->fresh()->balance,
            'currency' => $this->currency,
            'description_en' => $descEn,
            'description_ar' => $descAr,
            'created_by' => $userId,
            'reference_type' => $refType,
            'reference_id' => $refId,
        ]);
    }

    public function debit(float $amount, string $descEn, string $descAr, ?int $userId = null, ?string $refType = null, ?int $refId = null): FinancialTransaction
    {
        $this->decrement('balance', $amount);
        return $this->transactions()->create([
            'type' => 'debit',
            'amount' => $amount,
            'balance_after' => $this->fresh()->balance,
            'currency' => $this->currency,
            'description_en' => $descEn,
            'description_ar' => $descAr,
            'created_by' => $userId,
            'reference_type' => $refType,
            'reference_id' => $refId,
        ]);
    }

    public function getNameAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->account_name_ar : $this->account_name_en;
    }
}
