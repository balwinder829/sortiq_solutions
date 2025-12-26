<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\NotificationTemplate;

class SalesLeadsLowPercentNotification extends Notification
{
    use Queueable;

    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $isAdmin = $notifiable->role == 1;

        $templateKey = $isAdmin
            ? 'sales.leads.low.percent.admin'
            : 'sales.leads.low.percent';

        $template = NotificationTemplate::where('key', $templateKey)->first();

        // Safety fallback
        if (! $template) {
            return [
                'title'        => 'Leads Running Low',
                'message'      => 'Leads are running low.',
                'template_key' => $templateKey,
                'meta'         => $this->data,
            ];
        }

        $body = $template->body;

        foreach ($this->data as $key => $value) {
            $body = str_replace(":{$key}", $value, $body);
        }

        return [
            'title'        => $template->title,
            'message'      => $body,
            'template_key' => $templateKey,
            'meta'         => $this->data,
        ];
    }

}
