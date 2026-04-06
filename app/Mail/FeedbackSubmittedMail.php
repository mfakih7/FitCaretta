<?php

namespace App\Mail;

use App\Models\FeedbackSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FeedbackSubmittedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly FeedbackSubmission $submission)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Feedback - #' . $this->submission->id . ' - ' . $this->submission->subject
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.feedback.submitted',
            with: ['submission' => $this->submission]
        );
    }
}

