<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComandoControl extends Model
{
    protected $table = 'comandos_control';
    
    // ✅ FORZAR a Laravel a usar esta columna como clave primaria
    protected $primaryKey = 'id_comando';
    
    // ✅ Decirle explícitamente que es auto-incremental y de tipo entero
    public $incrementing = true;
    protected $keyType = 'int';

    // ✅ CORREGIDO: 'estado' en lugar de 'estado_ejecucion'
    protected $fillable = [
        'id_dispositivo',
        'id_usuario',
        'id_proceso',
        'accion',
        'origen',
        'estado', 
        'fecha_envio',
        'fecha_respuesta',
        'fecha_ejecucion'
    ];

    public function dispositivo(): BelongsTo
    {
        return $this->belongsTo(DispositivoControl::class, 'id_dispositivo', 'id_dispositivo');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    public function proceso(): BelongsTo
    {
        return $this->belongsTo(ProcesoDeshidratacion::class, 'id_proceso', 'id_proceso');
    }
}