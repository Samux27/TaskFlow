<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Log;
use App\Models\Comment;
use Illuminate\Support\Facades\Storage;
use App\Models\Task;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     * Funcion de Logchange para registrar cambios en los usuarios y Reflejarlos en la tabla de logs
     */
    private function logChange($action, $details, $usuarioModificado)
{
    Log::create([
        'user_id' => Auth::id(),
        'action' => $action,
        'details' => $details . " (Afectado:".$usuarioModificado->name." ID :{$usuarioModificado->id})",
        'ip_address' => request()->ip(),
    ]);
}

    public function index()
{
    $usuarios = User::with('roles:id,name') // Eager load roles to avoid N+1 query problem
        ->get()
        ->map(function ($user) {
            $user->role = $user->roles->pluck('name')->first(); // Get the first role name
            return $user->only(['id', 'name', 'surname', 'email', 'dni', 'role', 'is_active']);
        });
    
    return Inertia::render('Admin/User/ListUsers', [
        'users' => $usuarios,
    ]);
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
{
    return Inertia::render('Admin/User/CreateUser');

}
    /**
     * Store a newly created resource in storage.
     */
 

   

     public function store(Request $request)
     {
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'dni' => 'required|string|max:255|unique:users,dni','regex:/^[0-9]{8}[A-Z]$/',
            'password' => 'required|string|confirmed',
            'role' => 'required|string|in:admin,boss,employee',
        ]);
        
     
         $user = User::create([
             'name' => $validated['name'],
             'surname' => $validated['surname'],
             'email' => $validated['email'],
             'dni' => $validated['dni'],
             'password' => Hash::make($validated['password']),
             'is_active' => true,
         ]);

         Log::create([
            'user_id' => Auth::id(),
            'action' => 'Creación de usuario',
            'details' => 'El usuario '.$user->name.' con ID ' . $user->id . ' fue creado.',
            'ip_address' => request()->ip(),
        ]);
         $user->assignRole($validated['role']);
     
         return redirect()->route('users.index')->with('success', 'Usuario creado correctamente');


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
    public function edit($id)
    {
        $user = User::findOrFail($id);
    
        // Obtener el nombre del primer rol asignado
        $user->role = $user->roles->pluck('name')->first();
    
        return Inertia::render('Admin/User/EditUser', [
            'user' => $user
        ]);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
{
    
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'surname' => 'required|string|max:255',
        'dni' => [
            'required',
            'string',
            'max:9',
            Rule::unique('users', 'dni')->ignore($user->id),
            'regex:/^[0-9]{8}[A-Z]$/',
        ],
        'email' => [
            'required',
            'email',
            Rule::unique('users', 'email')->ignore($user->id),
        ],
        'password' => 'nullable|string',
        'role' => ['required', Rule::in(['admin', 'boss', 'employee'])],
        'is_active' => 'required',
        'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    $original = $user->getOriginal();

    // Lista de campos a comparar y actualizar
    $campos = [
        'name' => 'Nombre',
        'surname' => 'Apellido',
        'email' => 'Email',
        'is_active' => 'Estado',
    ];

    foreach ($campos as $campo => $nombre) {
        if ($user->$campo != $validated[$campo]) {
            $valorAnterior = $original[$campo];
            $valorNuevo = $validated[$campo];
    
            // Si es el campo 'is_active', traducir valores booleanos a texto legible
            if ($campo === 'is_active') {
                $valorAnterior = $valorAnterior ? 'Activo' : 'Inactivo';
                $valorNuevo = $valorNuevo ? 'Activo' : 'Inactivo';
            }
    
            $this->logChange("Actualización de $nombre", "$nombre: {$valorAnterior} → {$valorNuevo}", $user);
            $user->$campo = $validated[$campo];
        }
    }

    // DNI con strtoupper
    $dni = strtoupper($validated['dni']);
    if ($user->dni !== $dni) {
        $this->logChange('Actualización de DNI', "DNI: {$original['dni']} → {$dni}", $user);
        $user->dni = $dni;
    }

    // Contraseña
    if (!empty($validated['password'])) {
        $this->logChange('Cambio de contraseña', "Contraseña actualizada", $user);
        $user->password = Hash::make($validated['password']);
    }

    // Avatar
    if ($request->hasFile('avatar')) {
        if ($user->avatar && Storage::disk('public')->exists('avatars/' . $user->avatar)) {
            Storage::disk('public')->delete('avatars/' . $user->avatar);
        }

        $avatar = $request->file('avatar');
        $avatarName = uniqid() . '.' . $avatar->getClientOriginalExtension();
        $avatar->storeAs('avatars', $avatarName, 'public');

        $this->logChange('Actualización de avatar', 'Avatar actualizado', $user);
        $user->avatar = $avatarName;
    }

    // Rol
    $currentRole = $user->roles->first()?->name;
    if ($currentRole !== $validated['role']) {
        $this->logChange('Cambio de rol', "Rol: {$currentRole} → {$validated['role']}", $user);
        $user->syncRoles([$validated['role']]);
    }

    $user->save();

    return redirect()->route('users.index')->with('success', 'Usuario actualizado correctamente.');
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
{
    $user = User::findOrFail($id);

    // No permitir que un usuario se elimine a sí mismo
    if (Auth::id() === $user->id) {
        return redirect()->back()->with('error', 'No puedes eliminar tu propio usuario.');
    }

    // Registrar la acción ANTES de eliminar los logs del usuario
    Log::create([
        'user_id' => Auth::id(), // debe ser el usuario que realiza la acción
        'action' => 'Eliminación de usuario',
        'details' => 'El usuario '.$user->name.' con ID ' . $user->id . ' fue eliminado con sus tareas, permisos y logs.',
        'ip_address' => request()->ip(),
    ]);

    // Eliminar relaciones
    $user->tasks()->delete();
    $user->logs()->delete();
    $user->roles()->detach();
    $user->permissions()->detach();

    $user->delete();

    return redirect()->back()->with('success', 'Usuario y sus datos relacionados fueron eliminados correctamente.');
}

}
