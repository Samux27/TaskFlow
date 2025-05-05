<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    $usuarios = User::all();
    
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
        // Validar los datos del formulario
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')],
            'dni' => ['required', 'string', Rule::unique('users')],
            'password' => 'required|string|min:8|confirmed',
            'is_active' => 'nullable|boolean',  // Verifica si es un campo opcional
            'role' => 'required|in:admin,user,boss', // Asegúrate de que el rol sea válido
        ]);

        // Crear el usuario
       $user= User::create([
            'name' => $validated['name'],
            'surname' => $validated['surname'],
            'email' => $validated['email'],
            'dni' => $validated['dni'],
            'password' => Hash::make($validated['password']),
            'is_active' => $validated['is_active'] ?? false,
        ]);
        $user->assignRole($validated['role']); 
        return Inertia::render('Admin/User/ListUsers', [
            'success' => 'Usuario creado correctamente',
        ]);
        
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
    public function destroy($id)
{
    $user = User::findOrFail($id);
    $user->delete();

    return redirect()->back()->with('success', 'Usuario eliminado correctamente');
}
}
