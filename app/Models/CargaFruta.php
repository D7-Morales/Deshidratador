<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CargaFruta extends Model
{
    protected $table = 'cargas_fruta';
    protected $primaryKey = 'id_carga';

    protected $fillable = [
        'numero_lote',
        'id_fruta',
        'id_usuario',
        'id_deshidratador',
        'cantidad_frutas',
        'bandeja',
        'fecha_inicio',
        'fecha_fin',
        'duracion_horas',
        'peso_inicial_gramos',
        'peso_final_gramos',
        'estado_proceso',
        'observaciones',
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'peso_inicial_gramos' => 'decimal:2',
        'peso_final_gramos' => 'decimal:2',
        'duracion_horas' => 'decimal:2',
        'cantidad_frutas' => 'integer',
        'bandeja' => 'integer',
    ];

    /**
     * Get the fruit associated with the load.
     */
    public function fruta(): BelongsTo
    {
        return $this->belongsTo(Fruta::class, 'id_fruta', 'id_fruta');
    }

    /**
     * Get the user who managed the load.
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    /**
     * Get the dehydrator associated with the load.
     */
    public function deshidratador(): BelongsTo
    {
        return $this->belongsTo(Deshidratador::class, 'id_deshidratador', 'id_deshidratador');
    }

    /**
     * Get readings for this load.
     */
    public function lecturas(): HasMany
    {
        return $this->hasMany(LecturaSensor::class, 'id_carga', 'id_carga');
    }

    /**
     * Get commands executed during this load.
     */
    public function comandos(): HasMany
    {
        return $this->hasMany(ComandoControl::class, 'id_carga', 'id_carga');
    }

    /**
     * Get automation rules for this load.
     */
    public function automatizaciones(): HasMany
    {
        return $this->hasMany(Automatizacion::class, 'id_carga', 'id_carga');
    }

    /**
     * Get alerts for this load (si tienes tabla alertas).
     */
    public function alertas(): HasMany
    {
        return $this->hasMany(Alerta::class, 'id_carga', 'id_carga');
    }
}