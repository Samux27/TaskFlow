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

class BossEmployeeController extends Controller
{
     public function index()
    {
          /* ───────────────────────────────────────────────────────
       1) Usuarios con su primer rol “plano” en ->role
    ─────────────────────────────────────────────────────── */
    $users = User::with('roles:id,name')          // 1 consulta + eager-load roles
        ->get()
        ->map(function ($u) {
            $u->role = $u->roles->first()->name ?? '';  // “jefe”, “empleado”, ...
            return $u->only(['id', 'name', 'role','dni']);    // limpiamos lo que no hace falta
        });

    /* ───────────────────────────────────────────────────────
       2) Mapping jefe-empleados
       Asume tabla pivot boss_employee (boss_id, employee_id)
    ─────────────────────────────────────────────────────── */
    $mapping = DB::table('empleado_jefe')
        ->select(
            'boss_id',
            DB::raw('GROUP_CONCAT(employe_id) as employee_ids') // 1 fila por jefe
        )
        ->groupBy('boss_id')
        ->get()
        ->map(function ($row) {
            return [
                'boss_id'       => (int) $row->boss_id,
                'employee_ids'  => array_map('intval', explode(',', $row->employee_ids)),
            ];
        });

   
 //dd($users, $mapping); // Para depurar, eliminar en producción
    return Inertia::render('Admin/Boss-Employee/ListBossEmployee', [
        'users'   => $users,
        'mapping' => $mapping,
    ]);
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
        // 1️⃣ Validar formato
        $data = $request->validate([
            'boss_id'       => ['required', 'integer', 'exists:users,id'],
            'employee_ids'  => ['array'],
            'employee_ids.*'=> ['integer', 'exists:users,id'],
        ]);

        // 2️⃣ IDs de roles
        $bossRoleId     = DB::table('roles')->where('name', 'boss')->value('id');
        $employeeRoleId = DB::table('roles')->where('name', 'employee')->value('id');

        if (! $bossRoleId || ! $employeeRoleId) {
            return back()->withErrors('Roles boss/employee no encontrados en tabla roles.');
        }

        // 3️⃣ ¿El usuario indicado es realmente un jefe?
        $isBoss = DB::table('model_has_roles')
            ->where('model_id',   $data['boss_id'])
            ->where('model_type', 'App\\Models\\User')
            ->where('role_id',    $bossRoleId)
            ->exists();

        if (! $isBoss) {
            return back()->withErrors(['boss_id' => 'El usuario indicado no tiene rol boss.']);
        }

        // 4️⃣ Filtrar employee_ids: solo los que tengan rol employee
        $validEmployeeIds = DB::table('model_has_roles')
            ->whereIn('model_id', $data['employee_ids'] ?? [])
            ->where('model_type', 'App\\Models\\User')
            ->where('role_id',    $employeeRoleId)
            ->pluck('model_id')
            ->unique()
            ->values()
            ->all();

        // 5️⃣ Sincronizar tabla pivote empleado_jefe
        DB::transaction(function () use ($data, $validEmployeeIds) {
            DB::table('empleado_jefe')
                ->where('boss_id', $data['boss_id'])
                ->delete();

            if (! empty($validEmployeeIds)) {
                $rows = collect($validEmployeeIds)->map(fn ($id) => [
                    'boss_id'     => $data['boss_id'],
                    'employe_id' => $id,
                ])->all();

                DB::table('empleado_jefe')->insert($rows);
            }
        });
        Log::create([
            'user_id' => Auth::id(), // debe ser el usuario que realiza la acción
            'action' => 'Actualizar los empleados de un jefe',
            'details' => 'El jefe '.$data['boss_id'].' ahora tiene permisos sobre '.json_encode($validEmployeeIds).'.',
            'ip_address' => request()->ip(),
        ]);
        return back()->with('success', 'Permisos actualizados');
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
