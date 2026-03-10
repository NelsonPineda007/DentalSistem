<?php

namespace App\Http\Controllers;

use App\Models\Tratamiento;
use Illuminate\Http\Request;

class TratamientoController extends Controller
{
    // 1. LEER TODOS (Para que tu JS los pagine y filtre)
    public function obtenerTodos()
    {
        // El 'with' hace un JOIN automático a la base de datos
        $tratamientos = Tratamiento::with('categoria')->get();
        return response()->json($tratamientos);
    }

    // 2. CREAR NUEVO (POST)
    public function guardar(Request $request)
    {
        // Validamos que el código no se repita
        $request->validate([
            'codigo' => 'required|unique:tratamientos',
            'nombre' => 'required',
            'costo_base' => 'required|numeric'
        ]);

        $tratamiento = Tratamiento::create($request->all());
        
        return response()->json([
            'mensaje' => 'Tratamiento creado exitosamente', 
            'tratamiento' => $tratamiento
        ]);
    }

    // 3. ACTUALIZAR (PUT)
    public function actualizar(Request $request, $id)
    {
        $tratamiento = Tratamiento::find($id);
        
        if (!$tratamiento) {
            return response()->json(['mensaje' => 'Tratamiento no encontrado'], 404);
        }

        // Validamos que el código sea único, PERO ignorando el de este mismo tratamiento
        $request->validate([
            'codigo' => 'required|unique:tratamientos,codigo,' . $id,
            'nombre' => 'required',
            'costo_base' => 'required|numeric'
        ]);

        $tratamiento->update($request->all());
        
        return response()->json([
            'mensaje' => 'Tratamiento actualizado exitosamente', 
            'tratamiento' => $tratamiento
        ]);
    }

    // 4. ELIMINAR / ARCHIVAR (Soft Delete)
    public function eliminar($id)
    {
        $tratamiento = Tratamiento::find($id);
        
        if (!$tratamiento) {
            return response()->json(['mensaje' => 'Tratamiento no encontrado'], 404);
        }

        // En lugar de borrar de MySQL, lo pasamos a Inactivo
        $tratamiento->estado = 'Inactivo';
        $tratamiento->save();
        
        return response()->json(['mensaje' => 'Tratamiento archivado exitosamente']);
    }

    // NUEVA FUNCIÓN: Traer categorías para los <select>
    public function obtenerCategorias()
    {
        $categorias = \App\Models\CategoriaTratamiento::where('estado', 'Activo')->get();
        return response()->json($categorias);
    }
}