<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogSistema extends Model
{
    // 1. Conectamos a tu tabla exacta
    protected $table = 'logs_sistema';

    // 2. Tu tabla solo tiene 'creado_en', no tiene 'actualizado_en'
    const CREATED_AT = 'creado_en';
    const UPDATED_AT = null; 

    // 3. Los campos que permitimos guardar masivamente
    protected $fillable = [
        'usuario_id', 
        'accion', 
        'tabla_afectada', 
        'registro_id',
        'valores_anteriores', 
        'valores_nuevos', 
        'ip_address', 
        'user_agent'
    ];

    // 4. Transformación automática de JSON a Array
    protected $casts = [
        'valores_anteriores' => 'array',
        'valores_nuevos' => 'array',
    ];
}