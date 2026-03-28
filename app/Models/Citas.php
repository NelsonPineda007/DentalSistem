<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Cita extends Model
{
    use HasFactory, Auditable; // <-- Aquí activamos el rastreador de logs
    
    // Le decimos a Laravel exactamente qué tabla usar
    protected $table = 'citas';
    
    // Le indicamos que las columnas de tiempo están en español
    const CREATED_AT = 'creado_en';
    const UPDATED_AT = 'actualizado_en';
    
    // El guarded vacío permite guardar datos masivamente sin que Laravel nos bloquee
    protected $guarded = [];
}