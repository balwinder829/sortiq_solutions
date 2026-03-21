<?php

namespace App\Jobs;

use App\Models\EmailRecipient;
use App\Models\EmailCampaign;
use App\Models\EmailSender;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\Mail;
use App\Mail\CollegeEmailMailable;

class SendCollegeEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $recipient;

    public function __construct(EmailRecipient $recipient)
    {
        $this->recipient = $recipient;
    }

    public function handle()
    {
        try {

            $recipient = EmailRecipient::with('campaign.sender')->find($this->recipient->id);

            if (!$recipient) return;

            $campaign = $recipient->campaign;
            $sender = $campaign->sender;

            /*
            |--------------------------------------------------------------------------
            | Dynamic SMTP Config (Gmail)
            |--------------------------------------------------------------------------
            */
            config([
                'mail.mailers.smtp.host' => $sender->host,
                'mail.mailers.smtp.port' => $sender->port,
                'mail.mailers.smtp.encryption' => $sender->encryption,
                'mail.mailers.smtp.username' => $sender->username,
                'mail.mailers.smtp.password' => $sender->password,
            ]);

            /*
            |--------------------------------------------------------------------------
            | Send Email
            |--------------------------------------------------------------------------
            */
            Mail::mailer('smtp')
                ->to($recipient->email)
                ->send(new CollegeEmailMailable($campaign, $recipient, $sender));

            /*
            |--------------------------------------------------------------------------
            | Update Status
            |--------------------------------------------------------------------------
            */
            $recipient->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            // increment campaign sent count
            $campaign->increment('sent_count');

        } catch (\Exception $e) {

            $recipient->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            $recipient->campaign->increment('failed_count');
        }
    }
}