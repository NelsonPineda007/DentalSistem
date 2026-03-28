<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCitaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'paciente_id' => 'required|exists:pacientes,id',
            'empleado_id' => 'required|exists:empleados,id',
            'fecha' => 'required|date',
            'hora' => 'required',
            'hora_fin' => 'required',
            // Blindaje estricto basado en tu archivo SQL:
            'estado' => 'nullable|string|in:Programada,Confirmada,En progreso,Completada,Cancelada,No presentado',
            'motivo' => 'nullable|string|max:255',
            'notas' => 'nullable|string',
            'forzar_guardado' => 'nullable|boolean'
        ];
    }

    public function messages(): array
    {
        return [
            'paciente_id.required' => 'Debes seleccionar un paciente válido.',
            'empleado_id.required' => 'Debes asignar un doctor a la cita.',
            'fecha.date' => 'El formato de la fecha es inválido.',
            'estado.in' => 'El estado de la cita no es válido.',
        ];
    }
}