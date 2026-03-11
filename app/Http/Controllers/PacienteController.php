<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use Illuminate\Http\Request;

class PacienteController extends Controller
{
    // 1. Esta trae a TODOS los pacientes (Para tu pantalla de tabla general)
    public function obtenerTodos()
    {
        // En lugar de filtrar por 'Activo', usamos all() para traer el historial completo
        $pacientes = Paciente::all(); 
        
        return response()->json($pacientes);
    }

    // 2. Esta trae a UN SOLO paciente por su ID (Para la cabecera del Expediente)
    public function obtenerUno($id)
    {
        $paciente = Paciente::find($id);

        if (!$paciente) {
            return response()->json(['mensaje' => 'Paciente no encontrado'], 404);
        }

        return response()->json($paciente);
    }

    // NUEVA FUNCIÓN: Recibe los datos del JS y los guarda en MySQL
    public function guardar(Request $request)
    {
        // 1. Validamos que el expediente no exista ya en la base de datos para evitar errores de MySQL
        $request->validate([
            'nombre' => 'required',
            'apellido' => 'required',
            'numero_expediente' => 'required|unique:pacientes'
        ]);

        // 2. Le decimos al Modelo que cree el registro con todos los datos que llegaron
        $paciente = Paciente::create($request->all());

        // 3. Le respondemos a tu JavaScript (api.js) que todo salió perfecto (Status 200)
        return response()->json([
            'mensaje' => 'Paciente creado exitosamente',
            'paciente' => $paciente
        ]);
    }

    // NUEVA FUNCIÓN: Recibe los datos nuevos y actualiza el registro en MySQL
    public function actualizar(Request $request, $id)
    {
        // 1. Buscamos al paciente por su ID
        $paciente = Paciente::find($id);

        if (!$paciente) {
            return response()->json(['mensaje' => 'Paciente no encontrado'], 404);
        }

        // 2. Validamos (Le decimos que el expediente es único, pero que ignore el ID actual)
        $request->validate([
            'nombre' => 'required',
            'apellido' => 'required',
            'numero_expediente' => 'required|unique:pacientes,numero_expediente,' . $id
        ]);

        // 3. Actualizamos el registro con los datos que llegaron del JS
        $paciente->update($request->all());

        // 4. Le avisamos a tu JS que todo fue un éxito
        return response()->json([
            'mensaje' => 'Paciente actualizado exitosamente',
            'paciente' => $paciente
        ]);
    }

    // NUEVA FUNCIÓN: Borrado Lógico (Soft Delete)
    public function eliminar($id)
    {
        $paciente = Paciente::find($id);

        if (!$paciente) {
            return response()->json(['mensaje' => 'Paciente no encontrado'], 404);
        }

        // En lugar de usar $paciente->delete(), hacemos el borrado lógico
        $paciente->estado = 'Inactivo';
        $paciente->save();

        return response()->json([
            'mensaje' => 'Paciente archivado con éxito'
        ]);
    }
}