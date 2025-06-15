<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeNotication extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $user; // Variable para almacenar la tarea
    public function __construct($user)
    {
        $this->user = $user; // Asignar el usuario a la variable
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome Notification',
        );
    }
    public function build()
    {

        return $this->view('emails.Welcomenotification')  // Usamos la vista 'emails.task_created'
                    ->subject('Bienvenido a TaskFlow') // Asunto del correo
                    ->with([
                        'user' => $this->user, // Pasamos el usuario completo a la vista
                        
                    ]);
    }
    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.Welcomenotification',
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
