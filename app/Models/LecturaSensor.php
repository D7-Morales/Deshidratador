<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LecturaSensor extends Model
{
    protected $table = 'lecturas_sensor';
    protected $primaryKey = 'id_lectura';

    const UPDATED_AT = null;

    protected $fillable = [
        'id_sensor',
        'id_carga',
        'temperatura',
        'humedad',
        'presion',
        'fecha_hora',
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
        'temperatura' => 'decimal:2',
        'humedad' => 'decimal:2',
        'presion' => 'decimal:2',
    ];

    /**
     * Get the sensor that owns the reading.
     */
    public function sensor(): BelongsTo
    {
        return $this->belongsTo(Sensor::class, 'id_sensor', 'id_sensor');
    }

    /**
     * Get the dehydration load associated with this reading.
     */
    public function carga(): BelongsTo
    {
        return $this->belongsTo(CargaFruta::class, 'id_carga', 'id_carga');
    }
}
