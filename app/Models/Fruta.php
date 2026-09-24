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
     * Get the dehydration processes for this fruit.
     */
    public function procesos(): HasMany
    {
        return $this->hasMany(ProcesoDeshidratacion::class, 'id_fruta', 'id_fruta');
    }

    /**
     * Scope to search fruits by name.
     */
    public function scopeBuscar($query, $nombre)
    {
        return $query->where('nombre_fruta', 'like', "%{$nombre}%");
    }

    /**
     * Get formatted estimated time.
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