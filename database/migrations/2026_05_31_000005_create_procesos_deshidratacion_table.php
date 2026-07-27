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
        Schema::create('procesos_deshidratacion', function (Blueprint $table) {
            $table->id('id_proceso');
            $table->unsignedBigInteger('id_fruta');
            $table->dateTime('fecha_inicio');
            $table->dateTime('fecha_fin')->nullable();
            $table->decimal('peso_inicial', 8, 2);
            $table->decimal('peso_final', 8, 2)->nullable();
            $table->text('observaciones')->nullable();
            $table->enum('estado_proceso', ['EN_PROCESO', 'FINALIZADO']);
            $table->timestamps();

            $table->foreign('id_fruta')->references('id_fruta')->on('frutas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procesos_deshidratacion');
    }
};
