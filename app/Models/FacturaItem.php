<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class FacturaItem extends Model
{
    protected $table = 'factura_items';
    const CREATED_AT = 'creado_en';
    const UPDATED_AT = null;

    protected $fillable = [
        'factura_id', 'tipo_item', 'descripcion', 'cantidad', 'precio_unitario', 
        'descuento_item', 'total_item', 'tratamiento_id', 'tratamiento_aplicado_id', 
        'consulta_id', 'nota'
    ];
}