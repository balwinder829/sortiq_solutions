<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\NotificationTemplate;

class SalesFollowupsMissedNotification extends Notification
{
    use Queueable;

    protected $count;

    public function __construct($count)
    {
        $this->count = $count;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $template = NotificationTemplate::where('key', 'sales.followups.missed')->first();
        $body = str_replace(':count', $this->count, $template->body);

        return [
            'title' => $template->title,
            'message' => $body,
            'pending_count' => $this->count,
            'template_key' => 'sales.followups.missed'
        ];
    }
}
