<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StudentDueSummaryMail extends Mailable
{
    use Queueable, SerializesModels;

    public $dueToday;
    public $overdue;

    public function __construct($dueToday, $overdue)
    {
        $this->dueToday = $dueToday;
        $this->overdue  = $overdue;
    }

    public function build()
    {
        return $this->subject('Student Due Payment Summary')
                    ->view('emails.student_due_summary');
    }
}
