<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LeaveAppliedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $leave;

    public function __construct($leave)
    {
        $this->leave = $leave;
    }

    public function build()
    {
        return $this->subject('Leave Request - ' . $this->leave->emp_name . ' (' . $this->leave->emp_code . ')')
                    ->view('emails.leave_applied');
    }
}