<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsappMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'whatsapp_gateway_id', 'recipient_phone', 'recipient_name',
        'message', 'media_url', 'type', 'status', 'direction',
        'related_type', 'related_id', 'response_data',
    ];

    protected $casts = [
        'response_data' => 'array',
    ];

    public function gateway() { return $this->belongsTo(WhatsappGateway::class, 'whatsapp_gateway_id'); }
}
