<?php

namespace App\Mail;

use App\Models\Quote;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QuoteShareLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Quote $quote,
        public string $subjectText,
        public string $messageText,
        public string $shareUrl,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subjectText,
            replyTo: $this->quote->salesperson_email ? [$this->quote->salesperson_email] : [],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.quote-share-link',
        );
    }
}
