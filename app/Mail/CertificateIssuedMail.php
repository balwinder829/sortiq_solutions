<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class CertificateIssuedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $student;
    public $filePath;

    public function __construct($student, $filePath)
    {
        $this->student = $student;
        $this->filePath = $filePath;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Certificate Issued',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.student_certificate',
            with: ['student' => $this->student]
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->filePath)
        ];
    }
}
