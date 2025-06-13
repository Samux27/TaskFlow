<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;

class NotificacionCorreo extends Mailable
{
    use Queueable, SerializesModels;
    public $task; // Variable para almacenar el nombre del usuario
    /**
     * Create a new message instance.
     */
    public function __construct($task)
    {
        $this->task = $task; // Asignar el nombre del usuario a la variable
        
    }
    


    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Notificacion Correo',
        );
    }
    public function build()
    {
$path = public_path('images/Logo_TaskFlow.jpg');
$image = base64_encode(file_get_contents($path));
        return $this->view('emails.notification')  // Usamos la vista 'emails.task_created'
                    ->subject('Nueva tarea asignada') // Asunto del correo
                    ->with([
                        'base64Image' => $image,
                        'task' => $this->task,
                        'boss' => $this->task->boss,  // Pasamos la tarea completa a la vista
                    ]);
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.notification',
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
