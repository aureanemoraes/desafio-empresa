<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;

class SimpleMailNotification extends Mailable
{
    use Queueable, SerializesModels;

    public string $subjectData;
    public string $viewRelativePath;
    public array $contentData;

    /**
     * Create a new message instance.
     *
     * @param string $subject
     * @param string $view
     * @param array $data
     */
    public function __construct(string $subject, string $view, array $data = [])
    {
        $this->subjectData = $subject;
        $this->viewRelativePath = $view;
        $this->contentData = $data;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            subject: $this->subjectData,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: $this->viewRelativePath,
            with: $this->contentData,
        );
    }

    /**
     * Build the message.
     *
     * This method is required if you need to further customize the message.
     */
    public function build()
    {
        return $this->view($this->viewRelativePath)
                    ->with($this->contentData);
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
