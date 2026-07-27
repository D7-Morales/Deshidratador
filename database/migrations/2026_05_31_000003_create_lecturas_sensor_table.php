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
        Schema::create('lecturas_sensor', function (Blueprint $table) {
            $table->id('id_lectura');
            $table->unsignedBigInteger('id_sensor');
            $table->decimal('temperatura', 5, 2);
            $table->decimal('humedad', 5, 2);
            $table->decimal('presion', 7, 2);
            $table->dateTime('fecha_hora');
            $table->timestamps();

            $table->foreign('id_sensor')->references('id_sensor')->on('sensores')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lecturas_sensor');
    }
};
