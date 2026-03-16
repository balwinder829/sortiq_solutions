<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LoginNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $actorName;
    public string $ipAddress;
    public string $loginTime;
    public string $guard;

    public function __construct(string $actorName, string $ipAddress, string $loginTime, string $guard = 'web')
    {
        $this->actorName = $actorName;
        $this->ipAddress = $ipAddress;
        $this->loginTime = $loginTime;
        $this->guard = $guard;
    }

    public function build()
    {
        return $this->subject('Login: ' . $this->actorName . ' – ' . config('app.name'))
            ->view('emails.login-notification');
    }
}
