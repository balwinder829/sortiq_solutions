<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\NotificationTemplate;
use App\Services\NotificationService;

class StudentRegisteredSalesNotification extends Notification
{
    use Queueable;

    protected $student;

    public function __construct($student)
    {
        $this->student = $student;
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
        $template = NotificationTemplate::where('key', 'student.registered.sales')->first();

        $body = str_replace(':name', $this->student->student_name, $template->body);

        return [
            'title'        => $template->title,
            'message'      => $body,
            'student_id'   => $this->student->id,
            'template_key' => 'student.registered.sales'
        ];
    }
}
