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
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id('id_usuario');
            $table->string('ci', 20)->unique();
            $table->string('nombre_completo', 100);
            $table->string('usuario', 50)->unique();
            $table->char('password', 64);
            $table->enum('rol', ['ADMINISTRADOR', 'OPERADOR']);
            $table->enum('estado', ['ACTIVO', 'INACTIVO']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
