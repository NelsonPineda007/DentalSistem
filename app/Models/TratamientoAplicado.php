<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TratamientoAplicado extends Model {
    protected $table = 'tratamientos_aplicados';
    const CREATED_AT = 'fecha_aplicacion';
    const UPDATED_AT = null;
    protected $fillable = ['consulta_id', 'tratamiento_id', 'diente', 'caras_diente', 'notas', 'realizado_por'];
}