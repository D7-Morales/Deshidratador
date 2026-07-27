<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Usuario extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The table associated with the model.
     */
    protected $table = 'usuarios';

    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'id_usuario';

    /**
     * Laravel standard timestamps are created_at and updated_at.
     * In our database, these are named fecha_registro and fecha_actualizacion.
     */
    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'ci_usuario',
        'nombres_usuario',
        'apellidos_usuario',
        'email_usuario',
        'password_hash',
        'id_rol',
        'estado_usuario',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password_hash',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password_hash' => 'hashed',
        ];
    }

    /**
     * Get the role associated with the user.
     */
    public function rol(): BelongsTo
    {
        return $this->belongsTo(Rol::class, 'id_rol', 'id_rol');
    }

    /**
     * Get the loads managed by the user.
     */
    public function cargas(): HasMany
    {
        return $this->hasMany(CargaFruta::class, 'id_usuario', 'id_usuario');
    }

    /**
     * Get the full name of the user.
     */
    public function getNombreCompletoAttribute(): string
    {
        return $this->nombres_usuario . ' ' . $this->apellidos_usuario;
    }

    /**
     * Check if user is admin.
     */
    public function esAdmin(): bool
    {
        return $this->rol && $this->rol->nombre_rol === 'admin';
    }

    /**
     * Check if user is active.
     */
    public function estaActivo(): bool
    {
        return $this->estado_usuario === 'activo';
    }
}