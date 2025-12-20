<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\NotificationTemplate;

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
        return ['database'];
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
