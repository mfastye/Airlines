<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsappGateway extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'api_url', 'api_key', 'session_name',
        'phone_number', 'status', 'config',
    ];

    protected $casts = [
        'config' => 'array',
    ];

    protected $hidden = ['api_key'];

    public function messages() { return $this->hasMany(WhatsappMessage::class); }
}
