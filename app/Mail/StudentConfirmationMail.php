<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class StudentConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $student;
    public $filePath;
    public $receiptPath;

    public function __construct($student, $filePath, $receiptPath)
    {
        $this->student = $student;
        $this->filePath = $filePath;
        $this->receiptPath = $receiptPath;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Student Confirmation & Payment Receipt'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.student-confirmation',
            with: ['student' => $this->student]
        );
    }

    public function attachments(): array
    {
        $attachments = [];

        if ($this->filePath && file_exists($this->filePath)) {
            $attachments[] = Attachment::fromPath($this->filePath)
                ->as('Student_Confirmation.pdf')
                ->withMime('application/pdf');
        }

        if ($this->receiptPath && file_exists($this->receiptPath)) {
            $attachments[] = Attachment::fromPath($this->receiptPath)
                ->as('Payment_Receipt.pdf')
                ->withMime('application/pdf');
        }

        return $attachments;
    }
}
