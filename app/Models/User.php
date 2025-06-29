<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Task;
use App\Models\Comment;
use App\Models\Log;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\BelongsToMany;
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
        'name',   
        'surname',        // Nombre del usuario
        'email',          // Correo electrónico
        'password',       // Contraseña
        'dni',            // Número de identificación fiscal
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
    public function logs()
    {
        return $this->hasMany(Log::class);  // Relación con los registros de actividad del usuario
    }
    public function scopeActive($query)
    {
        return $query->where('is_active', true);  // Alcance para filtrar usuarios activos
    }
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);  // Alcance para filtrar usuarios inactivos
    }
    public function scopeWithRole($query, $role)
    {
        return $query->whereHas('roles', function ($q) use ($role) {
            $q->where('name', $role);  // Alcance para filtrar usuarios por rol
        });
    }
        public function employees()  // jefes → empleados
    {
        return $this->belongsToMany(User::class, 'empleado_jefe',
            'boss_id', 'employe_id');
    }

    public function bosses()     // empleados → jefes
    {
        return $this->belongsToMany(User::class, 'empleado_jefe',
            'employe_id', 'boss_id');
    }
    public function createdTasks()
{
    return $this->hasMany(Task::class, 'boss_id');
}
}
