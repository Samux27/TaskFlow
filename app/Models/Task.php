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
    public function employee()
    {
        return $this->belongsTo(User::class, 'employe_id');
    }

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

    // Si no estás usando created_at y updated_at, desactívalos
      // Esto es opcional si no usas las fechas de creación y actualización
    public function assignedUsers()
    {
        return $this->belongsToMany(User::class, 'task_user'); // Tabla pivot 'task_user'
    }
    // Si deseas usar validación, puedes agregar reglas personalizadas aquí
    // public static function rules() {
    //     return [
    //         'title' => 'required|string|max:255',
    //         'description' => 'required|string',
    //         // Otras reglas de validación aquí...
    //     ];
    // }
}
