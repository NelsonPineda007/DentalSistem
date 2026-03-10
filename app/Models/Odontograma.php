<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Odontograma extends Model
{
    protected $table = 'odontogramas';

    protected $fillable = [
        'paciente_id', 'estado_dientes', 'observaciones_generales', 'actualizado_por'
    ];

    // Le decimos a Laravel que este campo es un JSON y lo convierta a Array automáticamente
    protected $casts = [
        'estado_dientes' => 'array'
    ];

    // Tu tabla no tiene 'created_at', solo 'ultima_actualizacion'
    const CREATED_AT = null;
    const UPDATED_AT = 'ultima_actualizacion';
}