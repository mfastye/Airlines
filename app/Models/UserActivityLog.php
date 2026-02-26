<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'action', 'description', 'ip_address',
        'user_agent', 'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function user() { return $this->belongsTo(User::class); }

    public static function log(int $userId, string $action, ?string $description = null, ?array $metadata = null): self
    {
        return static::create([
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'metadata' => $metadata,
        ]);
    }
}
