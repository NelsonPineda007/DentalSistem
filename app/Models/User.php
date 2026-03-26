<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\CustomResetPassword; // <-- Importación del nuevo correo

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // 1. Apuntamos a tu tabla real
    protected $table = 'empleados';

    // Le enseñamos a Laravel cómo se llaman tus columnas de tiempo en español
    const CREATED_AT = 'creado_en';
    const UPDATED_AT = 'actualizado_en';

    // 2. Las columnas exactas de tu SQL
    protected $fillable = [
        'usuario',
        'email',
        'password_hash',
        'nombre',
        'apellido',
        'rol_id',
        'estado',
    ];

// 3. Ocultamos las credenciales de seguridad
    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    // 4. Le enseñamos a Laravel cuál es la columna de la clave
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    // 5. INTERCEPTOR: Le indicamos a Laravel que use nuestra plantilla de correo
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPassword($token));
    }
}