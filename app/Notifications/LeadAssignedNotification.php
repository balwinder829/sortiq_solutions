<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\NotificationTemplate;
use App\Services\NotificationService;

class LeadAssignedNotification extends Notification
{
    use Queueable;

    protected $lead;

    public function __construct($lead)
    {
        $this->lead = $lead;
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
        // Load template
        $template = NotificationTemplate::where('key', 'lead.assigned')->first();

        // Replace placeholders
        $body = str_replace(':lead_name', $this->lead->name, $template->body);

        return [
            'title' => $template->title,
            'message' => $body,
            'lead_id' => $this->lead->id,
            'template_key' => 'lead.assigned',
        ];
    }
}
