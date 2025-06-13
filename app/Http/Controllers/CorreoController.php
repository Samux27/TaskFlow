<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task; // Asegúrate de que la ruta del modelo Task es correcta
use App\Models\Log; // Asegúrate de que la ruta del modelo Log es correcta
use Illuminate\Support\Facades\Auth; // Importa la clase Auth
use App\Mail\NotificacionCorreo; // Asegúrate de que la ruta del Mail es correcta
use Illuminate\Support\Facades\Mail; // Importa la clase Mail
use App\Models\User; // Importa el modelo User si es necesario
class CorreoController extends Controller
{
    /**
     * Enviar un correo a los usuarios asignados cuando se crea una tarea.
     *
     * @param \App\Models\Task $task  // Cambia el tipo aquí
     * @return void
     */
    public function sendTaskCreatedEmail(Task $task)
    {
        // Obtener los usuarios asignados a la tarea
        $assignedUsers = $task->assignedUsers;
        
        // Enviar el correo a cada usuario asignado
        foreach ($assignedUsers as $user) {
            Mail::to($user->email)->send(new NotificacionCorreo($task));
             Log::create([
        'user_id'    => Auth::id(),
        'action'     => 'Mandar Correo ',
        'details'    => '' . 'usuario: ' . Auth::user()->name .  ' ha enviado un correo de la tarea: ' . $task->title . ' (ID: ' . $task->id . ')' . ' - Correo enviado a: ' . $user->email . ' con asunto: Nueva tarea asignada' ,
        
        'ip_address' => request()->ip(),
    ]);
        }
    }
}
