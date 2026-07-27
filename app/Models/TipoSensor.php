<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoSensor extends Model
{
    protected $table = 'tipos_sensor';
    protected $primaryKey = 'id_tipo';

    protected $fillable = [
        'nombre_tipo',
        'fabricante',
        'interfaz',
        'precision_temperatura',
        'precision_humedad',
        'voltaje_operacion',
        'descripcion'
    ];

    public function sensores(): HasMany
    {
        return $this->hasMany(Sensor::class, 'id_tipo', 'id_tipo');
    }
}
