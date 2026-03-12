<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class FacturaCuota extends Model
{
    protected $table = 'factura_cuotas';
    const CREATED_AT = 'creado_en';
    const UPDATED_AT = null;

    protected $fillable = [
        'factura_id', 'numero_cuota', 'total_cuotas', 'monto_programado', 
        'monto_abonado', 'fecha_emision', 'fecha_vencimiento', 'fecha_pago', 
        'estado', 'dias_retraso', 'observaciones'
    ];
}