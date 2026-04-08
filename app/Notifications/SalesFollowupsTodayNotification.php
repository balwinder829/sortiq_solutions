<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\NotificationTemplate;
use App\Services\NotificationService;


class SalesFollowupsTodayNotification extends Notification
{
    use Queueable;

    protected $count;

    public function __construct($count)
    {
        $this->count = $count;
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
        $template = NotificationTemplate::where('key', 'sales.followups.today')->first();
        $body = str_replace(':count', $this->count, $template->body);

        return [
            'title' => $template->title,
            'message' => $body,
            'pending_count' => $this->count,
            'template_key' => 'sales.followups.today'
        ];
    }
}
