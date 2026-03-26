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
    public $html;

    public function __construct($campaign, $recipient, $sender, $html)
    {
        $this->campaign = $campaign;
        $this->recipient = $recipient;
        $this->sender = $sender;
        $this->html = $html;
    }

    public function build()
    {
        // dd($this);
        $mail = $this->from($this->sender->email, $this->sender->name ?? 'Team')
            ->subject($this->campaign->subject)
            ->html($this->html);

        /*
        |------------------------------------------------------
        | Future Attachment Support
        |------------------------------------------------------
        */
        if (!empty($this->campaign->meta['attachments'] ?? null)) {
            foreach ($this->campaign->meta['attachments'] as $file) {
                $mail->attach(storage_path('app/' . $file));
            }
        }

        return $mail;
    }
}