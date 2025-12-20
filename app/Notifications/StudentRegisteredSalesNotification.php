<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\NotificationTemplate;

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
        return ['database'];
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
