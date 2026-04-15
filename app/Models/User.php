<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'empleados';
    const CREATED_AT = 'creado_en';
    const UPDATED_AT = 'actualizado_en';

    protected $fillable = [
        'usuario', 'email', 'password_hash', 'nombre', 'apellido', 
        'telefono', 'especialidad', 'rol_id', 'estado'
    ];

    protected $hidden = ['password_hash', 'remember_token'];

    // Solo una declaración de esta función:
    public function getAuthPassword()
    {
        return $this->password_hash;
    }
}