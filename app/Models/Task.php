<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
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

    // Relación con los archivos adjuntos de la tarea (si decides agregar esta funcionalidad)
    public function attachments()
    {
        return $this->hasMany(Attachment::class);  // Suponiendo que tengas una tabla 'attachments'
    }

    // Relación con los logs relacionados con la tarea (si decides hacer un seguimiento)
    public function logs()
    {
        return $this->hasMany(Log::class);
    }

    // Si no estás usando created_at y updated_at, desactívalos
    public $timestamps = true;  // Esto es opcional si no usas las fechas de creación y actualización

    // Si deseas usar validación, puedes agregar reglas personalizadas aquí
    // public static function rules() {
    //     return [
    //         'title' => 'required|string|max:255',
    //         'description' => 'required|string',
    //         // Otras reglas de validación aquí...
    //     ];
    // }
}
