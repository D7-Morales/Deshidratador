<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sensor extends Model
{
    protected $table = 'sensores';
    protected $primaryKey = 'id_sensor';

    protected $fillable = [
        'id_deshidratador',
        'id_tipo',
        'nombre_sensor',
        'modelo',
        'ubicacion_sensor',
        'estado_sensor',
    ];

    /**
     * Accessor for backward compatibility with views using $sensor->ubicacion.
     */
    public function getUbicacionAttribute()
    {
        return $this->ubicacion_sensor;
    }

    /**
     * Accessor for backward compatibility with views using $sensor->estado.
     */
    public function getEstadoAttribute()
    {
        return $this->estado_sensor;
    }

    /**
     * Get the dehydrator this sensor belongs to.
     */
    public function deshidratador(): BelongsTo
    {
        return $this->belongsTo(Deshidratador::class, 'id_deshidratador', 'id_deshidratador');
    }

    /**
     * Get the sensor type definition.
     */
    public function tipoSensor(): BelongsTo
    {
        return $this->belongsTo(TipoSensor::class, 'id_tipo', 'id_tipo');
    }

    /**
     * Get the readings for this sensor.
     */
    public function lecturas(): HasMany
    {
        return $this->hasMany(LecturaSensor::class, 'id_sensor', 'id_sensor');
    }
}
