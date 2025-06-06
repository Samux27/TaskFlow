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

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tareas = Task::all();
        
        return Inertia::render('Admin/Task/ListTasks', [
            'tasks' => $tareas,
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
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validación de los datos
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'assigned_users' => 'required|array',  // Debe ser un array de IDs de usuarios
            'assigned_users.*' => 'exists:users,id', // Verificar que los usuarios existan
            'create_date' => 'required|date',
            'deadLine' => 'nullable|date',
            'importancia' => 'required|in:baja,media,alta',
            'estado' => 'required|in:pendiente,en progreso,bloqueada,finalizada',
            'archivos' => 'nullable|array', // Archivos pueden ser opcionales
            'archivos.*' => 'file|mimes:jpg,jpeg,png,pdf,docx' // Tipos de archivo permitidos
        ]);

        // Crear la tarea
        $task = new Task();
$task->title = $request->title;
$task->description = $request->description;
$task->create_date = $request->create_date;
$task->deadLine = $request->deadLine;
$task->importancia = $request->importancia;
$task->estado = $request->estado;
$task->boss_id = Auth::id(); // El jefe autenticado
$task->save();

// Asociar los usuarios asignados a la tarea usando la tabla pivot
$task->assignedUsers()->sync($request->assigned_users); // Relación muchos a muchos con 'task_user'

        Log::create([
            'user_id' => Auth::id(), // debe ser el usuario que realiza la acción
            'action' => 'Crear tarea',
            'details' => 'La Tarea '.$task->title.' con Descripcion ' . $task->description . ' fue creada.',
            'ip_address' => request()->ip(),
        ]);
        
       

        // Subir los archivos si existen
        if ($request->hasFile('archivos')) {
            foreach ($request->file('archivos') as $file) {
                $path = $file->store('tasks', 'public');
                $task->files()->create(['path' => $path]);
            }
        }

        // Redirigir a la página de tareas con éxito
        return redirect()->route('task.index')->with('success', 'Tarea creada con éxito');
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $task = Task::findOrFail($id);
        return Inertia::render('Admin/Task/ShowTask', [    
            'task' => $task,
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
     
         $validated = $request->validate([
             'title' => 'required|string|max:255',
             'description' => 'required|string',
             'assigned_users' => 'required|array|min:1',
             'assigned_users.*' => 'exists:users,id',
             'create_date' => 'required|date',
             'dead_line' => 'nullable|date|after_or_equal:create_date',
             'importancia' => 'required|in:baja,media,alta',
             'estado' => 'required|in:pendiente,en progreso,bloqueada,finalizada',
             'archivos' => 'nullable|array',
             'archivos.*' => 'file|mimes:jpg,jpeg,png,pdf,docx',
         ]);
         
         // Actualizar datos básicos con el array validado
         $task->update([
             'title'       => $validated['title'],
             'description' => $validated['description'],
             'create_date' => $validated['create_date'],
             'dead_line'   => $validated['dead_line'] ?? null,
             'importancia' => $validated['importancia'],
             'estado'      => $validated['estado'],
         ]);
    
         // Sincronizar usuarios asignados
         $task->assignedUsers()->sync($validated['assigned_users']);
     
         // (Opcional) Manejo de archivos adjuntos si es necesario
     
         // Registrar log
         
         Log::create([
             'user_id'    => Auth::id(),
             'action'     => 'Actualización de tarea',
             'details'    => "La tarea '{$task->title}' (ID {$task->id}) fue actualizada. Usuarios asignados: " . implode(', ', $validated['assigned_users']),
             'ip_address' => request()->ip(),
         ]);
         
         return redirect()->route('task.index')->with('success', 'Tarea actualizada correctamente.');
     }
     
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        
        $task = Task::findOrFail($id);
        Log::create([
            'user_id'   => Auth::id(),           // usuario que realiza la acción
            'action'    => 'Eliminación de tarea',
            'details'   => "La tarea {$task->title} (ID {$task->id}) fue eliminada junto con sus relaciones.",
            'ip_address'=> request()->ip(),
        ]);
        
        // $task->comments()->delete(); // esto en algun momento se usara por que eliminamos los comentarios de los chat de cada tarea 
        $task->delete();
        return redirect()->back()->with('success', 'La tarea y sus datos relacionados fueron eliminados correctamente.');
    }
}
