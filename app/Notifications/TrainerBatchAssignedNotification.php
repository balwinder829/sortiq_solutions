<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\NotificationTemplate;
use App\Services\NotificationService;

class TrainerBatchAssignedNotification extends Notification
{
    use Queueable;

    protected $batch;

    public function __construct($batch)
    {
        $this->batch = $batch;
    }

    public function via($notifiable)
    {
        NotificationService::send(
            $notifiable,
            static::class,
            $this->toDatabase($notifiable),
            $this->sessionId ?? null
        );

        return []; // stop Laravel default DB insert
    }

    public function toDatabase($notifiable)
    {
        $template = NotificationTemplate::where('key', 'batch.assigned')->first();

        $body = str_replace(':batch_name', $this->batch->batch_name, $template->body);
        
        return [
            'title' => $template->title,
            'message' => $body,
            'batch_id' => $this->batch->id,
            'template_key' => 'batch.assigned'
        ];
    }
}
