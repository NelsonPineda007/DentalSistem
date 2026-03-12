<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Consulta extends Model
{
    protected $table = 'consultas';

    protected $fillable = [
        'cita_id', 'paciente_id', 'empleado_id', 'fecha_consulta', 
        'motivo_consulta', 'sintomas', 'diagnostico', 'observaciones', 
        'prescripciones', 'proxima_cita_recomendada'
    ];

    // Tu tabla tiene 'creado_en' pero no tiene fecha de actualización
    const CREATED_AT = 'creado_en';
    const UPDATED_AT = null;
}