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
        'assigned_users'  => 'required|array|min:1',
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

    /* 6️⃣  Redirección */
    return redirect()
           ->route('task.index')      // <- listado plural de Route::resource
           ->with('success', 'Tarea creada con éxito');
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
    
    // Carga la tarea con usuarios asignados
    $task = Task::with('assignedUsers')->findOrFail($id);

    // Carga los empleados (usuarios con rol 'empleado')
    $employees = User::with('roles')->get();

    // ID del jefe autenticado, por ejemplo
    $bossId = auth()->id();

    // Retorna vista con datos (puede ser Inertia o Blade)
    return inertia('Admin/Task/EditTask', [
        'task'      => $task,
        'employees' => $employees,
        'bossId'    => $bossId,
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

    return redirect()->route('task.index')
                     ->with('success', 'Tarea actualizada correctamente.');
}
     
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
{
    $task = Task::findOrFail($id);

    Log::create([
        'user_id'    => Auth::id(),
        'action'     => 'Eliminación de tarea',
        'details'    => "La tarea {$task->title} (ID {$task->id}) fue eliminada.",
        'ip_address' => request()->ip(),
    ]);

    $task->delete();

    // 👇  Con Route::resource('tasks', ...) la ruta index se llama tasks.index
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
}
}
