<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Comment;

use App\Models\Log;
class Task extends Model
{
   public $timestamps = false; // Desactiva las marcas de tiempo automáticas si no las necesitas
    // Asignación masiva de campos
    protected $fillable = [
        'title',
        'description',
        'employe_id',
        'boss_id',
        'create_date',
        'deadLine',
        'archivos',
        'importancia',
        'estado',
        'complete_at'
    ];

    // Relación con el empleado (quién realiza la tarea)
    

    // Relación con el jefe (quién asigna la tarea)
    public function boss()
    {
        return $this->belongsTo(User::class, 'boss_id');
    }

    // Relación con los comentarios de la tarea
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // Relación con los archivos adjuntos (si decides implementar esta funcionalidad)
    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }

    // Relación con los logs relacionados con la tarea (si decides hacer un seguimiento)
    public function logs()
    {
        return $this->hasMany(Log::class);
    }

    // Relación con el empleado asignado a la tarea
    public function assignedUsers()
    {
        return $this->belongsToMany(User::class, 'task_user'); // Tabla pivot 'task_user'
    }
    
}
