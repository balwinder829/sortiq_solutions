<?php

namespace App\Services;

use App\Models\Notification;
use Illuminate\Support\Str;

class NotificationService
{
    public static function send($notifiable, $type, $data, $sessionId = null)
    {
        return Notification::create([
            'id' => (string) Str::uuid(),
            'type' => $type,
            'notifiable_id' => $notifiable->id,
            'notifiable_type' => get_class($notifiable),
            'data' => $data,
            'session_id' => $sessionId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}