<?php

namespace App\Models\Whatsapp;

use Illuminate\Database\Eloquent\Model;

class WhatsappLogs extends Model
{
    protected $table = 'whatsapp_logs';

    protected $fillable = [
        'model',
        'model_id',
        'mobile_number',
        'message',
        'media_url',
        'name',
        'status'
    ];
}
