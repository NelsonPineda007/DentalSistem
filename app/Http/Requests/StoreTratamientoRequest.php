<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTratamientoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->input('id');
        return [
            'codigo' => 'required|string|max:20|unique:tratamientos,codigo,' . $id,
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'categoria_id' => 'nullable|exists:categorias_tratamientos,id',
            'duracion_estimada' => 'nullable|integer|min:1',
            'costo_base' => 'required|numeric|min:0',
            'requiere_cita' => 'required|boolean',
            'estado' => 'required|in:Activo,Inactivo'
        ];
    }

    public function messages(): array
    {
        return [
            'codigo.unique' => 'Este código de tratamiento ya está en uso.',
            'costo_base.numeric' => 'El costo debe ser un valor numérico.',
        ];
    }
}