<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use HasFactory;

class LecturaSensor extends Model
{
    
    protected $table = 'lecturas_sensor';
    protected $primaryKey = 'id_lectura';
    public $timestamps = true;

    protected $fillable = [
        'id_sensor',
        'id_proceso',
        'temperatura',
        'humedad',
        'presion',
         'estado_ventilador',
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
     * Get the dehydration process associated with this reading.
     */
    public function proceso(): BelongsTo
    {
        return $this->belongsTo(ProcesoDeshidratacion::class, 'id_proceso', 'id_proceso');
    }

    public function alertas()
    {
        return $this->hasMany(Alerta::class, 'id_lectura', 'id_lectura');
    }
}
