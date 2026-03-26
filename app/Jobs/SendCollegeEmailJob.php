<?php

namespace App\Jobs;

use App\Models\EmailRecipient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;

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
            |------------------------------------------------------------------
            | Dynamic SMTP Config
            |------------------------------------------------------------------
            */
            config([
                'mail.mailers.smtp.host' => $sender->host,
                'mail.mailers.smtp.port' => $sender->port,
                'mail.mailers.smtp.encryption' => $sender->encryption,
                'mail.mailers.smtp.username' => $sender->username,
                'mail.mailers.smtp.password' => $sender->password,
            ]);

            /*
            |------------------------------------------------------------------
            | Resolve Template
            |------------------------------------------------------------------
            */
            // $template = $campaign->meta['template'] ?? 'college_emails.college_visit';
            $template = data_get($recipient->meta, 'template');
            // dd($template);
            if (!$template || !view()->exists($template)) {
                $template = 'college_emails.college_visit';
            }


            /*
            |------------------------------------------------------------------
            | Render HTML (IMPORTANT)
            |------------------------------------------------------------------
            */
            $html = View::make($template, [
                'campaign' => $campaign,
                'recipient' => $recipient,
                'sender' => $sender,
                'body' => $campaign->body,
            ])->render();
            // dd($html);
            /*
            |------------------------------------------------------------------
            | Save rendered email
            |------------------------------------------------------------------
            */
            $recipient->update([
                'rendered_body' => $html,
                'template_name' => $template
            ]);

            /*
            |------------------------------------------------------------------
            | Send Email
            |------------------------------------------------------------------
            */
            Mail::mailer('smtp')
                ->to($recipient->email)
                ->send(new CollegeEmailMailable($campaign, $recipient, $sender, $html));

            /*
            |------------------------------------------------------------------
            | Update Status
            |------------------------------------------------------------------
            */
            $recipient->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            $recipient->refresh();

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