<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fruta extends Model
{
    protected $table = 'frutas';
    protected $primaryKey = 'id_fruta';

    protected $fillable = [
        'nombre_fruta',
        'temperatura_recomendada',
        'humedad_recomendada',
        'porcentaje_humedad_final',
        'tiempo_estimado_horas',
        'observaciones',
    ];

    protected $casts = [
        'temperatura_recomendada' => 'decimal:2',
        'humedad_recomendada' => 'decimal:2',
        'porcentaje_humedad_final' => 'decimal:2',
        'tiempo_estimado_horas' => 'integer',
    ];

    /**
     * Get the dehydration loads for this fruit.
     */
    public function cargas(): HasMany
    {
        return $this->hasMany(CargaFruta::class, 'id_fruta', 'id_fruta');
    }

    /**
     * Scope para buscar frutas por nombre (opcional pero útil).
     */
    public function scopeBuscar($query, $nombre)
    {
        return $query->where('nombre_fruta', 'like', "%{$nombre}%");
    }

    /**
     * Obtener tiempo estimado formateado (opcional).
     */
    public function getTiempoEstimadoFormateadoAttribute(): string
    {
        $horas = $this->tiempo_estimado_horas;
        
        if ($horas >= 24) {
            $dias = floor($horas / 24);
            $horasRestantes = $horas % 24;
            return "{$dias} día(s) y {$horasRestantes} hora(s)";
        }
        
        return "{$horas} hora(s)";
    }
}