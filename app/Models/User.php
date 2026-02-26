<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name', 'email', 'password', 'username', 'role', 'phone',
        'phone_country_code', 'whatsapp', 'whatsapp_country_code',
        'avatar', 'google_id', 'status', 'language', 'dark_mode',
        'provider_id', 'agent_id', 'permissions', 'last_login_at', 'last_login_ip',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_login_at' => 'datetime',
        'dark_mode' => 'boolean',
        'permissions' => 'array',
    ];

    public function isAdmin(): bool { return $this->role === 'admin'; }
    public function isProvider(): bool { return $this->role === 'provider'; }
    public function isAgent(): bool { return $this->role === 'agent'; }
    public function isEmployee(): bool { return $this->role === 'employee'; }
    public function isCustomer(): bool { return $this->role === 'customer'; }

    public function provider() { return $this->belongsTo(Provider::class); }
    public function agent() { return $this->belongsTo(Agent::class); }
    public function employee() { return $this->hasOne(Employee::class); }
    public function customer() { return $this->hasOne(Customer::class); }
    public function activityLogs() { return $this->hasMany(UserActivityLog::class); }
}
