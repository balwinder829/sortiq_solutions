<?php

// app/Notifications/UpcomingEventNotification.php
 

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\NotificationTemplate;
use App\Services\NotificationService;

class UpcomingEventNotification extends Notification
{
    use Queueable;

    protected $event;

    public function __construct($event)
    {
        $this->event = $event;
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
        // Fetch template
        $template = NotificationTemplate::where('key', 'upcoming.event')->first();

        // Replace placeholders
        $body = str_replace(
            [':name', ':date'],
            [
                $this->event->name,
                $this->event->event_date->format('d M Y')
            ],
            $template->body
        );

        return [
            'title'        => $template->title,
            'message'      => $body,
            'event_id'     => $this->event->id,
            'event_date'   => $this->event->event_date,
            'template_key' => 'upcoming.event',
        ];
    }
}
