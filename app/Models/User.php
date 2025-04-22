<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Task;
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable,HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',           // Nombre del usuario
        'email',          // Correo electrónico
        'password',       // Contraseña
        'nif',            // Número de identificación fiscal
        'is_active',      // Estado de activación del usuario
        'last_login',     // Fecha del último inicio de sesión
        'avatar',         // URL o ruta del avatar
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function tasks()
    {
        return $this->hasMany(Task::class, 'employe_id');  // Relación con las tareas asignadas al usuario
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);  // Relación con los comentarios del usuario
    }
    
    
}
