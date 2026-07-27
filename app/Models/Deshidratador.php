<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Deshidratador extends Model
{
    protected $table = 'deshidratadores';
    protected $primaryKey = 'id_deshidratador';

    protected $fillable = [
        'nombre',
        'ubicacion',
        'capacidad_kg',
        'panel_solar',
        'bateria',
        'estado',
        'fecha_registro'
    ];

    // Relación con Sensores
    public function sensores(): HasMany
    {
        return $this->hasMany(Sensor::class, 'id_deshidratador', 'id_deshidratador');
    }

    // Relación con Dispositivos de Control (Ventilador, Resistencia, etc.)
    public function dispositivosControl(): HasMany
    {
        return $this->hasMany(DispositivoControl::class, 'id_deshidratador', 'id_deshidratador');
    }

    // Relación con Cargas/Procesos de deshidratación
    public function cargas(): HasMany
    {
        return $this->hasMany(CargaFruta::class, 'id_deshidratador', 'id_deshidratador');
    }
}