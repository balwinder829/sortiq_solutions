<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\NotificationTemplate;
use App\Services\NotificationService;

class AdminTodayInterviewNotification extends Notification
{
    use Queueable;

    protected $sessionId;

    public function __construct(int $count, $sessionId = null)
    {
        $this->count = $count;
        $this->sessionId = $sessionId;
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
        $template = NotificationTemplate::where('key', 'admin.interviews.today')->first();

        $message = str_replace(
            ':count',
            $this->count,
            $template->body
        );

        return [
            'title'        => $template->title,
            'message'      => $message,
            'count'        => $this->count,
            'template_key' => 'admin.interviews.today',
        ];
    }
}
