<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CollegeEmailMailable extends Mailable
{
    use SerializesModels;

    public $campaign;
    public $recipient;
    public $sender;

    public function __construct($campaign, $recipient, $sender)
    {
        $this->campaign = $campaign;
        $this->recipient = $recipient;
        $this->sender = $sender;
    }

    public function build()
    {
        return $this->from($this->sender->email, $this->sender->name ?? 'Team')
            ->subject($this->campaign->subject)
            ->view('college_emails.college_visit')
            ->with([
                // 'body' => $this->campaign->body,
                // 'recipient' => $this->recipient
            ]);
    }
}