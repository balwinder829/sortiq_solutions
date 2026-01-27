<?php

namespace App\Mail;

use App\Models\InternshipRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InternshipRegistrationMail extends Mailable
{
    use Queueable, SerializesModels;

    public InternshipRegistration $registration;

    public function __construct(InternshipRegistration $registration)
    {
        $this->registration = $registration;
    }

    public function build()
    {
        return $this->subject('New Internship Registration')
            ->view('emails.internship_registration');
    }
}
