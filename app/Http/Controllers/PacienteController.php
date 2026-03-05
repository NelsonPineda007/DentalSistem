<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use Illuminate\Http\Request;

class PacienteController extends Controller
{
    // 1. Esta trae a TODOS los pacientes (Para tu pantalla de tabla general)
    public function obtenerTodos()
    {
        $pacientes = Paciente::where('estado', 'Activo')->get();
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
}