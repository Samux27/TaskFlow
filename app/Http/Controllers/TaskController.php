<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\User;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log as FacadesLog;
use Illuminate\Support\Str;
use App\Models\Comment;
use App\Models\Attachment;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Mail\NotificacionCorreo;
use App\Mail\TareaUrgente;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\CorreoController; // Asegúrate de que la ruta del controlador es correcta
// Asegúrate de que la ruta del Request es correcta

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         $tareas = Task::with(['comments.user'])->get();
       
        return Inertia::render('Admin/Task/ListTasks', [
            'tasks' => $tareas,
            'userRole' => auth()->user()->getRoleNames()->first(),
            
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
        // Obtener todos los usuarios con sus roles
        $employees = User::with('roles')->get();  // Usando la relación 'roles' de Spatie
        
        return Inertia::render('Admin/Task/CreateTask', [
            'employees' => $employees,
            'bossId' => auth()->id(),  // Id del jefe autenticado
            'userRole' => auth()->user()->getRoleNames()->first(), // Obtener el rol del usuario autenticado
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */

public function store(Request $request)
{
    /* 1️⃣  Validación */
    $request->validate([
        'title'           => 'required|string|max:255',
        'description'     => 'required|string',
        'assigned_users'  => 'required|array|min:0',
        'assigned_users.*'=> 'exists:users,id',
        'create_date'     => 'required|date',
        'deadLine'        => 'nullable|date|after_or_equal:create_date',
        'complete_at'     => 'nullable|date|after_or_equal:create_date',
        'importancia'     => 'required|in:baja,media,alta',
        'estado'          => 'required|in:pendiente,en progreso,bloqueada,finalizada',
        'archivos'        => 'nullable|array',
        'archivos.*'      => 'file|mimes:jpg,jpeg,png,pdf,docx',
    ]);

    /* 2️⃣  Determinar la fecha de finalización */
    $completeAt = null;

    if ($request->estado === 'finalizada') {
        // Usa la fecha proporcionada o, si viene vacía, marca ahora
        $completeAt = $request->complete_at
            ? Carbon::parse($request->complete_at)
            : Carbon::now();
    }

    /* 3️⃣  Crear la tarea */
    $task = Task::create([
        'title'        => $request->title,
        'description'  => $request->description,
        'create_date'  => $request->create_date,
        'deadLine'     => $request->deadLine,       // tu columna camelCase
        'importancia'  => $request->importancia,
        'estado'       => $request->estado,
        'complete_at'  => $completeAt,              // ← guarda NULL o la fecha
        'boss_id'      => Auth::id(),
    ]);

    /* 4️⃣  Relación muchos-a-muchos */
    $task->assignedUsers()->sync($request->assigned_users);
    // obtiene los correos de los usuarios asignados y les manda el correo de que tienen una tarea nueva 
    $correoController = new CorreoController();
    $correoController->sendTaskCreatedEmail($task); 
    /* 5️⃣  Log */
    Log::create([
        'user_id'    => Auth::id(),
        'action'     => 'Crear tarea',
        'details'    => sprintf(
            "Tarea '%s' (ID %d) creada. Estado: %s. Fecha fin: %s. Usuarios: %s",
            $task->title,
            $task->id,
            $task->estado,
            $completeAt ? $completeAt->toDateString() : '—',
            implode(', ', $request->assigned_users)
        ),
        'ip_address' => $request->ip(),
    ]);
    $user = Auth::user();
    /* 6️⃣  Redirección */
   if ($user->hasRole('admin')) {
    return redirect()->route('task.index')
        ->with('success', 'Tarea guardada correctamente');
} else {
    return redirect()->route('employee.tasks.index')
        ->with('success', 'Tarea guardada correctamente');
}
}



    /**
     * Display the specified resource.
     */
   public function show(string $id)
{
    /* 1. Tarea + relaciones reales */
    $task = Task::with([
    'boss:id,name',           // jefe asignador
    'assignedUsers:id,name',  // usuarios asignados (muchos a muchos)
    'comments.user:id,name',  // comentarios de la tarea y usuario de cada comentario
])->findOrFail($id);

    

    /* 3. Payload compacto */
    
    return Inertia::render('Admin/Task/ShowTask', [
        'task' => $task,
        'userRole' => auth()->user()->getRoleNames()->first(),
    ]);
}

    /**
     * Show the form for editing the specified resource.
     */
  public function edit($id)
{
    $task = Task::with('assignedUsers')->findOrFail($id);
    $user = auth()->user();
    $bossId = $user->id;

    // Obtener solo los empleados asignados si es jefe, o todos si es admin
    if ($user->hasRole('boss')) {
        // Relación con empleados asignados al jefe (por ejemplo: empleados()->with('roles'))
        $employees = $user->employees()->with('roles')->get();
    } else {
        // Admin ve todos
        $employees = User::with('roles')->get();
    }

    return inertia('Admin/Task/EditTask', [
        'task'      => $task,
        'employees' => $employees,
        'bossId'    => $bossId,
        'userRole'  => $user->getRoleNames()->first(), // opcional
    ]);
}


    /**
     * Update the specified resource in storage.
     */
    
    
    public function update(Request $request, $id)
{
    $task = Task::findOrFail($id);

    /* 1. Validación */
    $validated = $request->validate([
        'title'           => 'required|string|max:255',
        'description'     => 'required|string',
        'assigned_users'  => 'required|array|min:1',
        'assigned_users.*'=> 'exists:users,id',
        'create_date'     => 'required|date',
        'dead_line'       => 'nullable|date|after_or_equal:create_date',
        'complete_at'     => 'nullable|date|after_or_equal:create_date',
        'importancia'     => 'required|in:baja,media,alta',
        'estado'          => 'required|in:pendiente,en progreso,bloqueada,finalizada',
        'archivos'        => 'nullable|array',
        'archivos.*'      => 'file|mimes:jpg,jpeg,png,pdf,docx',
    ]);

    /* 2. Lógica de complete_at
         - Si llega desde el form, úsalo.
         - Si estado = finalizada y no viene, pon ahora().
         - En cualquier otro caso, null.                        */
$completeAt = $validated['complete_at']
    ? Carbon::parse($validated['complete_at'])          // ← convierte a Carbon
    : ($validated['estado'] === 'finalizada' ? Carbon::now() : null);

    /* 3. Actualizar */
    $task->update([
        'title'        => $validated['title'],
        'description'  => $validated['description'],
        'create_date'  => $validated['create_date'],
        'dead_line'    => $validated['dead_line'] ?? null,
        'importancia'  => $validated['importancia'],
        'estado'       => $validated['estado'],
        'complete_at'  => $completeAt,
    ]);

    /* 4. Sincronizar usuarios asignados */
    $task->assignedUsers()->sync($validated['assigned_users']);

    /* 5. Log */
    Log::create([
        'user_id'    => Auth::id(),
        'action'     => 'Actualización de tarea',
        'details'    => sprintf(
            "Tarea '%s' (ID %d) actualizada. Estado: %s. Fecha fin: %s. Usuarios: %s",
            $task->title,
            $task->id,
            $task->estado,
            $completeAt ? $completeAt->toDateString() : '—',
            implode(', ', $validated['assigned_users'])
        ),
        'ip_address' => $request->ip(),
    ]);
$user = Auth::user();
    /* 6️⃣  Redirección */
   if ($user->hasRole('admin')) {
    return redirect()->route('task.index')
        ->with('success', 'Tarea guardada correctamente');
} else {
    return redirect()->route('employee.tasks.index')
        ->with('success', 'Tarea guardada correctamente');
}
}
     
    

    /**
     * Remove the specified resource from storage.
     */
public function destroy(string $id)
{
    $task = Task::findOrFail($id);

    // Eliminar comentarios relacionados
    $task->comments()->delete();

    // Eliminar relaciones de usuarios asignados
    $task->assignedUsers()->detach();

    // Guardar log antes de eliminar la tarea
    Log::create([
        'user_id'    => Auth::id(),
        'action'     => 'Eliminación de tarea',
        'details'    => "La tarea {$task->title} (ID {$task->id}) fue eliminada junto con sus relaciones.",
        'ip_address' => request()->ip(),
    ]);

    // Eliminar la tarea
    $task->delete();

    return redirect()->route('task.index')
                     ->with('success', 'La tarea fue eliminada correctamente.');
}

public function employeeIndex(Request $request)
{
    $userId = auth()->id();

    $tasks = Task::with('boss') // si quieres el jefe que creó cada tarea
        ->where(function($q) use ($userId) {
            // 1) Tareas creadas por mí como boss
            $q->where('boss_id', $userId)
              // 2) O tareas en las que estoy apuntado en task_user
              ->orWhereHas('assignedUsers', function($q2) use ($userId) {
                  $q2->where('user_id', $userId);
              });
        })
        ->orderBy('create_date', 'desc')->get();  // tu timestamp real :contentReference[oaicite:1]{index=1}
                        

    return inertia('Admin/Task/ListTasks', [
        'tasks' => $tasks,
        'userRole' => auth()->user()->getRoleNames()->first(),
        
    ]);
}
public function updateStatus(Request $request, Task $task)
{
   
    $request->validate([
        'estado' => 'required|in:pendiente,en progreso,finalizada'
    ]);
    
    // Solo los empleados asignados pueden cambiar el estado (opcional, según permisos)
    

    $task->estado = $request->estado;
    $task->save();

    return redirect()->back()->with('success', 'Estado de la tarea actualizado.');
}public function revisarTareasUrgentes()
{
    $mañana = now()->addDay();

    $tareas = Task::where('estado', '!=', 'finalizada')
        ->whereDate('deadLine', '<=', $mañana)
        ->whereDate('deadLine', '>=', now())
        ->get();

    foreach ($tareas as $tarea) {
        // Saltar si ya se notificó hace menos de 6 horas
        if ($tarea->last_notified_at && $tarea->last_notified_at->diffInHours(now()) < 6) {
            continue;
        }

        // Enviar correos a los usuarios asignados
        foreach ($tarea->assignedUsers as $user) {
            Mail::to($user->email)->send(new TareaUrgente($tarea));

            Log::create([
                'user_id' => Auth::id(),
                'action' => 'Revisión de tareas urgentes',
                'details' => 'Se ha notificado a ' . $user->name .
                    ' (ID: ' . $user->id . ') sobre la tarea: ' . $tarea->title .
                    ' (ID: ' . $tarea->id . ')',
                'ip_address' => request()->ip(),
            ]);
        }

        // ✅ Actualizar solo si se notificó
        $tarea->last_notified_at = now();
        $tarea->save();
    }
}

public function tareasUrgentes()
{
    $mañana = now()->addDay();
    $usuario = auth()->user();
    $rol = $usuario->getRoleNames()->first();

    
    $consulta = Task::where('estado', '!=', 'finalizada')
        ->whereBetween('deadLine', [now(), $mañana]);

   
    if ($rol !== 'admin') {
        $consulta->whereHas('assignedUsers', function ($q) use ($usuario) {
            $q->where('user_id', $usuario->id);
        });
    }

    $tareas = $consulta->orderBy('deadLine', 'asc')->get();

    return Inertia::render('Admin/User/TaskUrgente', [
        'tareas' => $tareas,
        'userRole' => $rol,
    ]);
}





}
