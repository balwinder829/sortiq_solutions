<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StudentLeaveAppliedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $leave;

    public function __construct($leave)
    {
        $this->leave = $leave;
    }

    public function build()
    {
        return $this->subject(
            "Student Leave: {$this->leave->student_name} (SNO: {$this->leave->sno})"
        )->view('emails.student_leave_applied');
    }
}