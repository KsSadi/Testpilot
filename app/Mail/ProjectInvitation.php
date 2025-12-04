<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\ProjectShare;

class ProjectInvitation extends Mailable
{
    use Queueable, SerializesModels;

    public $share;

    /**
     * Create a new message instance.
     */
    public function __construct(ProjectShare $share)
    {
        $this->share = $share->load(['shareable', 'sharedBy', 'sharedWith']);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $shareable = $this->share->shareable;
        $shareableName = $shareable->name ?? $shareable->title ?? 'a project';
        
        return new Envelope(
            subject: 'You\'ve been invited to collaborate on ' . $shareableName,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.project-invitation',
        );
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
