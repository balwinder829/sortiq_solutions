<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\NotificationTemplate;

class FeePendingSummaryNotification extends Notification
{
    use Queueable;

    protected $count;
    protected $sessionId;

    public function __construct($count, $sessionId)
    {
        $this->count = $count;
        $this->sessionId = $sessionId;
    }

    // REQUIRED METHOD → Without this, Laravel throws error
    public function via($notifiable)
    {
        return ['database']; 
    }

    public function toDatabase($notifiable)
    {
        $template = NotificationTemplate::where('key', 'fee.pending.summary')->first();

        $body = str_replace(':count', $this->count, $template->body);

        return [
            'title'        => $template->title,
            'message'      => $body,
            'count'        => $this->count,
            'session_id'   => $this->sessionId,
            'template_key' => 'fee.pending.summary'
        ];
    }
}
