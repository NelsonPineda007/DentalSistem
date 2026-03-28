<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePacienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        $id = $this->input('id');

        return [
            'numero_expediente' => 'required|unique:pacientes,numero_expediente,' . $id,
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'email' => 'nullable|email:rfc,dns|max:100',
            'telefono' => ['required', 'regex:/^(\+\d{1}\s\d{3}-\d{3}-\d{4}|\d{4}-\d{4})$/'],
            'DUI' => ['nullable', 'regex:/^\d{8}-\d{1}$/'],
            'estado' => 'required|in:Activo,Inactivo',
            'fecha_nacimiento' => 'nullable|date',
            'genero' => 'nullable|string',
            'grupo_sanguineo' => 'nullable|string',
            'es_menor' => 'nullable|boolean',
            'responsable_legal' => 'nullable|string|max:150',
            'direccion' => 'nullable|string',
            'ciudad' => 'nullable|string|max:50',
            'codigo_postal' => 'nullable|string|max:10',
            'empresa' => 'nullable|string|max:150',
            'preferencia_contacto' => 'nullable|string',
            'contacto_emergencia_nombre' => 'nullable|string|max:100',
            'contacto_emergencia_telefono' => ['nullable', 'regex:/^(\+\d{1}\s\d{3}-\d{3}-\d{4}|\d{4}-\d{4})$/'],
            'seguro_medico' => 'nullable|string|max:100',
            'alergias' => 'nullable|string',
            'enfermedades_cronicas' => 'nullable|string',
            'medicamentos_actuales' => 'nullable|string',
            'notas_medicas' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'email.email' => 'El formato del correo es inválido.',
            'email.dns' => 'El dominio del correo electrónico no existe (Ej: @dominio-falso.com).',
            'telefono.regex' => 'El teléfono debe ser Local (0000-0000) o USA (+1 000-000-0000).',
            'contacto_emergencia_telefono.regex' => 'El teléfono de emergencia debe ser Local (0000-0000) o USA (+1 000-000-0000).',
            'DUI.regex' => 'El formato del DUI debe ser exacto: 00000000-0.',
            'numero_expediente.unique' => 'Este número de expediente ya está asignado a otro paciente.',
        ];
    }
}