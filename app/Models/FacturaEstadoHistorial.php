<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class FacturaEstadoHistorial extends Model
{
    protected $table = 'factura_estado_historial';
    const CREATED_AT = 'fecha'; // Tu BD usa 'fecha' aquí en lugar de 'creado_en'
    const UPDATED_AT = null;

    protected $fillable = [
        'factura_id', 'tipo_cambio', 'valor_anterior', 'valor_nuevo', 
        'cuota_id', 'cambiado_por', 'motivo', 'ip_address'
    ];
}