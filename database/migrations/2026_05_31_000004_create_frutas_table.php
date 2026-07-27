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
        Schema::create('frutas', function (Blueprint $table) {
            $table->id('id_fruta');
            $table->string('nombre_fruta', 50);
            $table->string('descripcion', 200);
            $table->decimal('temperatura_recomendada', 5, 2);
            $table->decimal('humedad_recomendada', 5, 2);
            $table->integer('tiempo_estimado_horas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('frutas');
    }
};
