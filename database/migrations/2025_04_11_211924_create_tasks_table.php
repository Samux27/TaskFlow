<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
        $table->string('title');
        $table->text('description');
        $table->foreignId('employe_id')->constrained('users');  // Relación con el empleado (usuario que realiza la tarea)
        $table->foreignId('boss_id')->constrained('users');  // Relación con el jefe (usuario que asigna la tarea)
        $table->timestamp('create_date')->useCurrent();  // Fecha de creación de la tarea
        $table->timestamp('deadLine')->nullable();  // Fecha límite de la tarea
        $table->string('archivos')->nullable();  // Ruta de archivos adjuntos
        $table->enum('importancia', ['baja', 'media', 'alta'])->default('media');  // Importancia de la tarea
        $table->enum('estado', ['no iniciado', 'en progreso', 'bloqueada', 'finalizada'])->default('no iniciado');
        $table->timestamp('complete_at')->nullable();  // Fecha de finalización
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
