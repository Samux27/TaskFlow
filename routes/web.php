<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use Inertia\Inertia;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\CommentsController;
use Illuminate\Session\Middleware\StartSession;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Controllers\BossEmployeeController;
use Illuminate\Support\Facades\Auth;
Route::middleware([
    StartSession::class,
    HandleInertiaRequests::class,
    \Illuminate\View\Middleware\ShareErrorsFromSession::class,
    \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
    \Illuminate\Cookie\Middleware\EncryptCookies::class,
])->group(function () {

    // Ruta raíz: renderiza la vista de login
    Route::get('/', function () {
        return Inertia::render('Auth/Login');
    });

    // Ruta de dashboard, solo accesible por usuarios autenticados y verificados
    Route::get('/dashboard', function () {
        // Obtener el usuario autenticado
        $user = Auth::user();
    
        // Pasar el usuario a la vista de Inertia
        return Inertia::render('Dashboard', [
            'user' => $user,  // Pasa el usuario completo, incluyendo la propiedad avatar
        ]);
    })->middleware(['auth', 'verified'])->name('dashboard');
    // Grupo de rutas que requieren autenticación
    Route::middleware('auth')->group(function () {

        // Rutas relacionadas con el perfil
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        // Grupo de rutas para los administradores
        Route::middleware(['role_or_permission:admin'])->group(function () {
            // Rutas de usuarios solo accesibles por admin
            Route::resource('/users', UserController::class);
            Route::resource('log', LogController::class);
            Route::post('/boss/permissions', [BossEmployeeController::class, 'store']);
            Route::resource('boss-employee', BossEmployeeController::class);
        });

        // Rutas relacionadas con las tareas
        Route::resource('task', TaskController::class);
        Route::get('/mis-tareas', [TaskController::class, 'ListUsersTask'])->name('task.ListUsersTask');
       

        
        
        
        // Rutas relacionadas con los comentarios
        Route::resource('comments', CommentsController::class);
    });

    // Ruta para crear un usuario, accesible solo por administradores
    
});

// Rutas de autenticación (por ejemplo, login, registro, etc.)
require __DIR__.'/auth.php';
