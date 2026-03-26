<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable; // 1. Lo importas

class Tratamiento extends Model
{
    use Auditable;
    // Relación: Un tratamiento pertenece a una categoría
    public function categoria()
    {
        return $this->belongsTo(CategoriaTratamiento::class, 'categoria_id');
    }
    // 1. Le decimos la tabla exacta
    protected $table = 'tratamientos';

    // 2. Protegemos los campos rellenables
    protected $fillable = [
        'codigo', 'nombre', 'descripcion', 'categoria_id', 'costo_base',
        'duracion_estimada', 'requiere_cita', 'estado', 'creado_por', 'actualizado_por'
    ];

    
    // 3. Traducimos timestamps (como hicimos con pacientes)
    const CREATED_AT = 'creado_en';
    const UPDATED_AT = 'actualizado_en';
}