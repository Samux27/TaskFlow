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
            'details' => 'La Tarea '.$task->title.' con Descripcion' . $task->description . ' fue creada.',
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
