<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StudentDueReminderMail extends Mailable
{
    public $student;
    public $type; // due_today | overdue

    public function __construct($student, $type)
    {
        $this->student = $student;
        $this->type = $type;
    }

    public function build()
    {
        return $this->subject('Fee Payment Reminder')
            ->view('emails.student_due_reminder');
    }
}
