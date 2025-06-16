<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TareaUrgente extends Mailable
{
    use Queueable, SerializesModels;
    public $tarea;
    /**
     * Create a new message instance.
     */
    public function __construct($tarea)
    {
       $this->tarea = $tarea;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tarea Urgente',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.tarea_urgente',
        );
    }

     public function build()
    {
        return $this->subject('⚠️ Tarea urgente por vencer')
                    ->markdown('emails.tarea_urgente');
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
