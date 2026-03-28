<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAbonoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'abono' => 'required|numeric|min:0.01',
            'metodo_pago' => 'required|in:efectivo,transferencia,tarjeta_credito,tarjeta_debito,cheque,deposito,otros',
        ];
    }

    public function messages(): array
    {
        return [
            'abono.required' => 'Debes ingresar el monto que estás recibiendo.',
            'abono.min' => 'El abono debe ser mayor a cero.',
            'metodo_pago.in' => 'Selecciona un método de pago válido.',
        ];
    }
}