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
        Schema::create('empleado_jefe', function (Blueprint $table) {
            $table->id();
            $table->foreignId('boss_id')->constrained('users');  // Relación con el jefe
            $table->foreignId('employe_id')->constrained('users');  // Relación con el empleado
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empleado_jefe');
    }
};
