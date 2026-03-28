<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFacturaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'fecha' => 'required|date',
            'diente' => 'nullable|string|max:255',
            'tipo_factura' => 'required|in:contado,cuotas',
            'metodo_pago' => 'required|in:efectivo,transferencia,tarjeta_credito,tarjeta_debito,cheque,deposito,otros',
            'abono' => 'nullable|numeric|min:0',
            'descuento' => 'nullable|numeric|min:0',
            'observaciones_factura' => 'nullable|string',
            'subtotal' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'cita_id' => 'nullable|exists:citas,id',
            
            // Vigila que el carrito de tratamientos sea un Array válido
            'tratamientos' => 'required|array|min:1',
            'tratamientos.*.id' => 'required|exists:tratamientos,id',
            'tratamientos.*.precio' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'tratamientos.required' => 'Debes agregar al menos un tratamiento para generar la factura.',
            'abono.min' => 'El abono no puede ser un número negativo.',
            'total.min' => 'El total de la factura no puede ser negativo.',
        ];
    }
}