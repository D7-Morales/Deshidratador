<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComandoControl extends Model
{
    protected $table = 'comandos_control';
    protected $primaryKey = 'id_comando';

    protected $fillable = [
        'id_dispositivo',
        'id_usuario',
        'id_carga',
        'accion',
        'origen',
        'estado_ejecucion',
        'fecha_envio',
        'fecha_respuesta'
    ];

    // Relación con Dispositivo
    public function dispositivo()
    {
        return $this->belongsTo(DispositivoControl::class, 'id_dispositivo', 'id_dispositivo');
    }

    // Relación con Usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    // Relación con CargaFruta
    public function carga()
    {
        return $this->belongsTo(CargaFruta::class, 'id_carga', 'id_carga');
    }
}