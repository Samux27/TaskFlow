<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\Task;
use Illuminate\Support\Facades\DB;
class BossController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }
public function createTaskForEmployee(User $empleado)
{
    $boss = auth()->user();

    $empleados = $boss->employees()->with('roles')->get();

    return inertia('Boss/CreateTaskForEmployee', [
        'empleado' => $empleado,
        'employees' => $empleados,
        'bossId' => $boss->id,
        'userRole' => 'boss',
    ]);
}

public function storeTaskForEmployee(Request $request, User $empleado)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'importancia' => 'required|in:alta,media,baja',
    ]);

    $task = Task::create([
        'title' => $request->title,
        'description' => $request->description,
        'importancia' => $request->importancia,
        'estado' => 'pendiente',
        'boss_id' => auth()->id(),
    ]);

    $task->assignedUsers()->attach($empleado->id);

    return redirect()->route('boss.empleado.tareas', $empleado->id)->with('success', 'Tarea creada correctamente');
}
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
    
public function empleadosAsignados()
{
    $bossId = auth()->id();

    $empleados = User::whereIn('id', function ($query) use ($bossId) {
        $query->select('employe_id')
              ->from('empleado_jefe')
              ->where('boss_id', $bossId);
    })->get();

    return inertia('Boss/ListEmployee', [
        'empleados' => $empleados,
        'userRole' => auth()->user()->getRoleNames()->first()
    ]);
}
public function verTareasEmpleado(User $empleado)
{
    $bossId = auth()->id();

    // Verifica que el jefe tenga acceso a este empleado
    $tienePermiso = DB::table('empleado_jefe')
        ->where('boss_id', $bossId)
        ->where('employe_id', $empleado->id)
        ->exists();

    if (!$tienePermiso) {
        abort(403);
    }

    $tareas = Task::where('boss_id', $bossId)
        ->whereHas('assignedUsers', fn($q) => $q->where('user_id', $empleado->id))
        ->orderByDesc('create_date')
        ->get();

    return inertia('Boss/EmpleadoTareas', [
        'empleado' => $empleado,
        'tareas' => $tareas,
        'userRole' => auth()->user()->getRoleNames()->first()
    ]);
}
}
