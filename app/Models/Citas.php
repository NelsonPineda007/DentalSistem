<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cita;
use Illuminate\Support\Facades\DB;

class CitaController extends Controller
{
    // 1. Cargar datos para los <select> del modal (Pacientes y Doctores)
    public function obtenerDatosFormulario()
    {
        try {
            $pacientes = DB::table('pacientes')->select('id', 'nombre', 'apellido')->get();
            // Asumiendo que tienes una tabla de doctores o usuarios con ese rol
            $doctores = DB::table('doctores')->select('id', 'nombre', 'apellido')->get();

            return response()->json([
                'pacientes' => $pacientes,
                'doctores' => $doctores
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al cargar los datos del formulario'], 500);
        }
    }

    // 2. Obtener todas las citas (EXCLUYENDO las Canceladas por el Soft Delete)
    public function obtenerCitas()
    {
        try {
            // Hacemos un JOIN simple para traer los nombres del paciente y doctor
            $citas = DB::table('citas')
                ->join('pacientes', 'citas.paciente_id', '=', 'pacientes.id')
                ->leftJoin('doctores', 'citas.doctor_id', '=', 'doctores.id')
                ->select(
                    'citas.*', 
                    DB::raw("CONCAT(pacientes.nombre, ' ', pacientes.apellido) as paciente_nombre"),
                    DB::raw("CONCAT(doctores.nombre, ' ', doctores.apellido) as doctor_nombre")
                )
                ->where('citas.estado', '!=', 'Cancelada') // Filtro de Soft Delete
                ->orderBy('citas.fecha', 'asc')
                ->orderBy('citas.hora', 'asc')
                ->get();

            return response()->json($citas, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener las citas'], 500);
        }
    }

    // 3. Guardar una nueva cita
    public function guardarCita(Request $request)
    {
        try {
            $cita = new Cita();
            $cita->paciente_id = $request->paciente_id;
            $cita->doctor_id = $request->doctor_id;
            $cita->fecha = $request->fecha;
            $cita->hora = $request->hora;
            $cita->motivo = $request->motivo;
            $cita->estado = $request->estado ?? 'Programada';
            
            $cita->save();

            return response()->json(['message' => 'Cita guardada correctamente'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al guardar la cita'], 500);
        }
    }

    // 4. Actualizar una cita existente
    public function actualizarCita(Request $request, $id)
    {
        try {
            $cita = Cita::find($id);
            if (!$cita) {
                return response()->json(['error' => 'Cita no encontrada'], 404);
            }

            $cita->paciente_id = $request->paciente_id;
            $cita->doctor_id = $request->doctor_id;
            $cita->fecha = $request->fecha;
            $cita->hora = $request->hora;
            $cita->motivo = $request->motivo;
            $cita->estado = $request->estado;
            
            $cita->save();

            return response()->json(['message' => 'Cita actualizada correctamente'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al actualizar la cita'], 500);
        }
    }

    // 5. Eliminar Cita (SOFT DELETE: Solo cambia el estado a 'Cancelada')
    public function eliminarCita($id)
    {
        try {
            $cita = Cita::find($id);
            if (!$cita) {
                return response()->json(['error' => 'Cita no encontrada'], 404);
            }

            $cita->estado = 'Cancelada';
            $cita->save();

            return response()->json(['message' => 'Cita cancelada (archivada) correctamente'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al cancelar la cita'], 500);
        }
    }
}