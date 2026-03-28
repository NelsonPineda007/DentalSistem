<?php

namespace App\Http\Controllers;

use App\Models\Tratamiento;
use App\Http\Requests\StoreTratamientoRequest;

class TratamientoController extends Controller
{
    public function obtenerTodos()
    {
        $tratamientos = Tratamiento::with('categoria')->get();
        return response()->json($tratamientos);
    }

    public function guardar(StoreTratamientoRequest $request)
    {
        $tratamiento = Tratamiento::create($request->validated());
        return response()->json(['mensaje' => 'Tratamiento creado exitosamente', 'tratamiento' => $tratamiento]);
    }

    public function actualizar(StoreTratamientoRequest $request, $id)
    {
        $tratamiento = Tratamiento::find($id);
        if (!$tratamiento) return response()->json(['mensaje' => 'Tratamiento no encontrado'], 404);

        $tratamiento->update($request->validated());
        return response()->json(['mensaje' => 'Tratamiento actualizado exitosamente', 'tratamiento' => $tratamiento]);
    }

    public function eliminar($id)
    {
        $tratamiento = Tratamiento::find($id);
        if (!$tratamiento) return response()->json(['mensaje' => 'Tratamiento no encontrado'], 404);

        $tratamiento->estado = 'Inactivo';
        $tratamiento->save();
        return response()->json(['mensaje' => 'Tratamiento archivado exitosamente']);
    }

    public function obtenerCategorias()
    {
        $categorias = \App\Models\CategoriaTratamiento::where('estado', 'Activo')->get();
        return response()->json($categorias);
    }
}