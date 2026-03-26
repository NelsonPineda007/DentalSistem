<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Factura extends Model
{
    use Auditable;
    
    protected $table = 'facturas';
    const CREATED_AT = 'creado_en';
    const UPDATED_AT = 'actualizado_en';

    protected $fillable = [
        'numero', 'paciente_id', 'empleado_id', 'cita_id', 'fecha_emision', 
        'fecha_vencimiento', 'subtotal', 'impuestos', 'descuento', 'total', 
        'saldo_pendiente', 'estado_general', 'estado_pago', 'tipo_factura', 
        'moneda', 'observaciones', 'terminos_condiciones', 'creado_por', 'actualizado_por'
    ];
}