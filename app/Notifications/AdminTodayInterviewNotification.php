<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\NotificationTemplate;

class AdminTodayInterviewNotification extends Notification
{
    use Queueable;

    protected int $count;

    public function __construct(int $count)
    {
        $this->count = $count;
    }

    public function via($notifiable)
    {
        return ['database'];
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
