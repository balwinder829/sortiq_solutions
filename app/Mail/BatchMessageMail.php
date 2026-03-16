<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BatchMessageMail extends Mailable
{
    public $messageText;
    public $batchName;
    public $trainerName;
    public $subjectText;

    public function __construct($subjectText, $messageText, $batchName, $trainerName)
    {
        $this->subjectText = $subjectText;
        $this->messageText = $messageText;
        $this->batchName = $batchName;
        $this->trainerName = $trainerName;
    }

    public function build()
    {
        return $this->subject($this->subjectText)
                    ->view('emails.batch_message');
    }
}