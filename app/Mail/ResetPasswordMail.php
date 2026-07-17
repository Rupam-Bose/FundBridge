<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $resetUrl;
    public string $userEmail;

    public function __construct(string $resetUrl, string $email)
    {
        $this->resetUrl  = $resetUrl;
        $this->userEmail = $email;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'FundBridge — Reset Your Password',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'auth.emails.reset-password',
            with: [
                'resetUrl'  => $this->resetUrl,
                'userEmail' => $this->userEmail,
            ]
        );
    }
}
