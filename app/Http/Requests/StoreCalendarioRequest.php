<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCalendarioRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'titulo' => 'required|string|max:255',
            'fecha' => 'required|date',
            'hora' => 'nullable',
            'detalles' => 'nullable|string',
            'color' => 'nullable|string|max:50',
            'tipo' => 'required|in:Nota,Recordatorio'
        ];
    }

    public function messages(): array
    {
        return [
            'titulo.required' => 'El título del evento es obligatorio.',
            'tipo.in' => 'El tipo de evento debe ser una Nota o un Recordatorio.',
        ];
    }
}