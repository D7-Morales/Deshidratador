<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alerta extends Model
{
    protected $table = 'alertas';
    protected $primaryKey = 'id_alerta';

    const UPDATED_AT = null;

    protected $fillable = [
        'id_proceso',
        'id_lectura',
        'tipo_alerta',
        'mensaje',
        'atendida',
        'fecha_alerta',
    ];

    protected $casts = [
        'fecha_alerta' => 'datetime',
        'atendida' => 'boolean',
    ];

    /**
     * Get the process associated with the alert.
     */
    public function proceso(): BelongsTo
    {
        return $this->belongsTo(ProcesoDeshidratacion::class, 'id_proceso', 'id_proceso');
    }

    /**
     * Get the reading that triggered the alert.
     */
    public function lectura(): BelongsTo
    {
        return $this->belongsTo(LecturaSensor::class, 'id_lectura', 'id_lectura');
    }
}
