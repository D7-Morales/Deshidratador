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

    /**
     * Get the sensors associated with this dehydrator.
     */
    public function sensores(): HasMany
    {
        return $this->hasMany(Sensor::class, 'id_deshidratador', 'id_deshidratador');
    }

    /**
     * Get the control devices (fan, resistor, etc.) for this dehydrator.
     */
    public function dispositivosControl(): HasMany
    {
        return $this->hasMany(DispositivoControl::class, 'id_deshidratador', 'id_deshidratador');
    }

    /**
     * Get the dehydration processes associated with this dehydrator.
     */
    public function procesos(): HasMany
    {
        return $this->hasMany(ProcesoDeshidratacion::class, 'id_deshidratador', 'id_deshidratador');
    }
}