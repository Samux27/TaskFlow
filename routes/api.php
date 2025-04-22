<php 
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Aquí puedes registrar las rutas de tu API. Estas rutas están cargadas
| por el grupo de middleware "api". ¡Construye algo increíble!
|
*/




// Obtener todos los usuarios
Route::get('/users', [UserController::class, 'index']);

// Obtener un usuario específico por ID
Route::get('/users/{id}', [UserController::class, 'show']);

// Crear un nuevo usuario
Route::post('/users', [UserController::class, 'store']);

// Actualizar un usuario existente
Route::put('/users/{id}', [UserController::class, 'update']);

// Eliminar un usuario
Route::delete('/users/{id}', [UserController::class, 'destroy']);

    

