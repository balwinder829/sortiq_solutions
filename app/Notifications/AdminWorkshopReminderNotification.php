<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\NotificationTemplate;

class AdminWorkshopReminderNotification extends Notification
{
    use Queueable;

    protected $workshop;
    protected $templateKey;

    public function __construct($workshop, $templateKey)
    {
        $this->workshop = $workshop;
        $this->templateKey = $templateKey;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $template = NotificationTemplate::where('key', $this->templateKey)->first();

        $message = str_replace(
            [':title', ':date'],
            [
                $this->workshop->title,
                optional($this->workshop->date)->format('d M Y')
            ],
            $template->body
        );

        return [
            'title'        => $template->title,
            'message'      => $message,
            'workshop_id'  => $this->workshop->id,
            'template_key' => $this->templateKey,
        ];
    }
}