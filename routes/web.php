<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use Inertia\Inertia;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\CommentController;
use Illuminate\Session\Middleware\StartSession;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Controllers\BossEmployeeController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\BossController;
use App\Http\Controllers\AttachmentController;
 use App\Http\Controllers\ExportTaskController;
 use App\Http\Controllers\CorreoController;

Route::middleware([
    StartSession::class,
    HandleInertiaRequests::class,
    \Illuminate\View\Middleware\ShareErrorsFromSession::class,
    \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
    \Illuminate\Cookie\Middleware\EncryptCookies::class,
])->group(function () {
    Route::get('/tareas-urgentes', [TaskController::class, 'tareasUrgentes'])
    
    ->name('tareas.urgentes');
   Route::get('/send-welcome-email/{name}', [CorreoController::class, 'sendWelcomeEmail']);
        
        Route::post('/task/{task}/comments', [CommentController::class, 'sendComment']);

  
    Route::get('/', function () {
        return Inertia::render('Auth/Login');
    });

    // Ruta de dashboard, solo accesible por usuarios autenticados y verificados
    Route::get('/dashboard', function () {
        $user = Auth::user();
       if ($user->hasRole('boss') || $user->hasRole('employee')) {
        app(TaskController::class)->revisarTareasUrgentes();
    }
        
        
    
        
        return Inertia::render('Dashboard', [
            'user' => $user,  
        ]);
    })->middleware(['auth', 'verified'])->name('dashboard');
    
    Route::middleware('auth')->group(function () {
       
         Route::post('mis-tareas', [TaskController::class, 'storeSelf'])
                      ->name('employee.tasks.store');
         Route::get('mis-tareas', [TaskController::class, 'employeeIndex'])
                      ->name('employee.tasks.index');
        //!!!!!! NO PONGAS CREATE ABAJO DE SHOW PORQUE NO FUNCIONA EL CREAR 
        Route::get('task/create', [TaskController::class, 'create'])->name('task.create');

        Route::post('task', [TaskController::class, 'store'])->name('task.store');
        //permite que todos los usuarios identificados puedan ver la tarea. da igual si son administradores o empleados

        Route::get('task/{task}', [TaskController::class, 'show'])->name('task.show');

        Route::get('task', [TaskController::class, 'index'])->name('task.index');
        
        
        // Rutas relacionadas con el perfil
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


        
                      
        //Rutas para los administradores
        
        Route::middleware(['role_or_permission:admin'])->group(function () {
        Route::get('/empleado/{empleado}/tareas', [BossController::class, 'verTareasEmpleado'])   ->name('admin.empleado.tareas');
        Route::get('/export-users', [ExportTaskController::class, 'exportUsers'])->name('users.export');
        Route::get('/export-tasks', [ExportTaskController::class, 'export'])->name('tasks.export');

            Route::resource('/users', UserController::class);

            Route::resource('log', LogController::class);

            Route::post('/boss/permissions', [BossEmployeeController::class, 'store']);
            Route::resource('boss-employee', BossEmployeeController::class);

            //estos son todas las rutas de tareas por que tengo que hacer que show se pueda acceder desde cualquier usuario autenticado
            // y las demás solo por administradores
            
           
                      
            Route::get('task/{task}/edit', [TaskController::class, 'edit'])->name('task.edit');
            Route::put('task/{task}', [TaskController::class, 'update'])->name('task.update');
            Route::patch('task/{task}', [TaskController::class, 'update']);
            Route::delete('task/{task}', [TaskController::class, 'destroy'])->name('task.destroy');
        });

        //Rutas de empleados 
        Route::middleware(['role_or_permission:employee'])->group(function () {
            //ruta especifica para que los empleados puedan actualizar el estado de una tarea
        Route::patch('/task/{task}/status', [TaskController::class, 'updateStatus'])->name('task.updateStatus');
             


         

});
       
        
               Route::middleware(['auth', 'role:boss'])->group(function () {
                 Route::get('/mis-empleados', [BossController::class, 'empleadosAsignados'])->name('boss.empleados');
});
Route::get('/mis-empleados/{empleado}/tareas', [BossController::class, 'verTareasEmpleado'])
    
    ->name('boss.empleado.tareas');
         
Route::get('task/{task}/edit', [TaskController::class, 'edit'])->name('task.edit');
            Route::put('task/{task}', [TaskController::class, 'update'])->name('task.update');
            Route::patch('task/{task}', [TaskController::class, 'update']);
            Route::delete('task/{task}', [TaskController::class, 'destroy'])->name('task.destroy');
Route::get('/mis-empleados/{empleado}/tareas/crear', [BossController::class, 'createTaskForEmployee'])->name('boss.empleado.tasks.create');
Route::post('/mis-empleados/{empleado}/tareas', [BossController::class, 'storeTaskForEmployee'])->name('boss.empleado.tasks.store');


        
        
        
        
        
    });

    
    
});

// Rutas de autenticación (por ejemplo, login, registro, etc.)
require __DIR__.'/auth.php';
