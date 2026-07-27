<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DispositivoControl extends Model
{
    protected $table = 'dispositivos_control';
    protected $primaryKey = 'id_dispositivo';

    protected $fillable = [
        'id_deshidratador',
        'nombre_dispositivo',
        'tipo_dispositivo',
        'pin_control',
        'estado_actual',
        'fecha_registro'
    ];

    // Relación con Deshidratador
    public function deshidratador()
    {
        return $this->belongsTo(Deshidratador::class, 'id_deshidratador', 'id_deshidratador');
    }

    // Relación con Comandos
    public function comandos()
    {
        return $this->hasMany(ComandoControl::class, 'id_dispositivo', 'id_dispositivo');
    }
}