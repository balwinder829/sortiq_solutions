<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

// app/Mail/StudentJoinedMail.php
class StudentJoinedMail extends Mailable
{
    public $student;

    public function __construct($student)
    {
        $this->student = $student;
    }

    public function build()
    {
        return $this->subject('New Student Joined')
            ->view('emails.student_joined');
    }
}

