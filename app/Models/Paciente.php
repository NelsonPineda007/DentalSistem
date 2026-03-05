<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{
    // 1. Le decimos exactamente qué tabla usar
    protected $table = 'pacientes';

    // 2. Configuramos las fechas (Timestamps) a tu idioma
    const CREATED_AT = 'creado_en';
    const UPDATED_AT = 'actualizado_en';

    // 3. Protegemos la tabla: Solo estos campos pueden ser llenados desde un formulario
    protected $fillable = [
        'numero_expediente', 'nombre', 'apellido', 'email', 'telefono', 
        'telefono_emergencia', 'fecha_nacimiento', 'genero', 'grupo_sanguineo', 
        'direccion', 'ciudad', 'codigo_postal', 'alergias', 'enfermedades_cronicas', 
        'medicamentos_actuales', 'seguro_medico', 'contacto_emergencia_nombre', 
        'contacto_emergencia_telefono', 'notas_medicas', 'estado', 
        'preferencia_contacto', 'es_menor', 'responsable_legal', 'empresa', 'DUI',
        'creado_por', 'actualizado_por'
    ];
}