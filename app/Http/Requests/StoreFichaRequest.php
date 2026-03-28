<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFichaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            // El odontograma es un JSON, lo validamos como array estructurado
            'odontograma' => 'nullable|array',
            
            // La historia clínica
            'historia' => 'required|array',
            'historia.consulta_id' => 'nullable|exists:consultas,id',
            'historia.cita_id' => 'nullable|exists:citas,id',
            'historia.motivo_consulta' => 'nullable|string|max:500',
            'historia.sintomas' => 'nullable|string',
            'historia.observaciones' => 'nullable|string',
            'historia.diagnostico' => 'nullable|string',
            'historia.prescripciones' => 'nullable|string',
            'historia.proxima_cita' => 'nullable|date|after_or_equal:today',
        ];
    }

    public function messages(): array
    {
        return [
            'historia.proxima_cita.after_or_equal' => 'La fecha de la próxima cita no puede ser en el pasado.',
        ];
    }
}