<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Pago extends Model
{
    use Auditable;
    
    protected $table = 'pagos';
    const CREATED_AT = 'creado_en';
    const UPDATED_AT = null;

    protected $fillable = [
        'factura_id', 'cuota_id', 'empleado_id', 'fecha_pago', 'fecha_registro', 
        'monto', 'metodo_pago', 'referencia', 'estado', 'nota', 'registrado_por'
    ];
}