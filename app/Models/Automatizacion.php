<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Automatizacion extends Model
{
    protected $table = 'automatizacion';
    protected $primaryKey = 'id_regla';

    protected $fillable = [
        'id_carga',
        'temperatura_umbral',
        'humedad_umbral',
        'tiempo_estimado_restante',
        'porcentaje_secado',
        'accion_recomendada',
        'fecha_generacion'
    ];

    // Relación con CargaFruta
    public function carga()
    {
        return $this->belongsTo(CargaFruta::class, 'id_carga', 'id_carga');
    }
}