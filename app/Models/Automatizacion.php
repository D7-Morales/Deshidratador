<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Automatizacion extends Model
{
    protected $table = 'automatizacion';
    protected $primaryKey = 'id_regla';

    protected $fillable = [
        'id_proceso',
        'temperatura_umbral',
        'humedad_umbral',
        'tiempo_estimado_restante',
        'porcentaje_secado',
        'accion_recomendada',
        'fecha_generacion'
    ];

    /**
     * Get the process associated with this automation rule.
     */
    public function proceso(): BelongsTo
    {
        return $this->belongsTo(ProcesoDeshidratacion::class, 'id_proceso', 'id_proceso');
    }
}