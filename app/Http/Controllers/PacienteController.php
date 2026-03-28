<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // <-- Inyectamos la clase de Autenticación
use App\Http\Requests\StorePacienteRequest; // <-- Importamos a nuestro "Guardia"

class PacienteController extends Controller
{
    public function obtenerTodos()
    {
        $pacientes = Paciente::all(); 
        return response()->json($pacientes);
    }

    public function obtenerUno($id)
    {
        $paciente = Paciente::find($id);
        if (!$paciente) {
            return response()->json(['mensaje' => 'Paciente no encontrado'], 404);
        }
        return response()->json($paciente);
    }

    // USAMOS EL FORM REQUEST AQUÍ (StorePacienteRequest en lugar de Request)
    public function guardar(StorePacienteRequest $request)
    {
        // 1. Extraemos todos los datos validados y limpios
        $datos = $request->validated();
        
        // 2. Inyectamos silenciosamente quién está creando este registro
        $datos['creado_por'] = Auth::id();

        // 3. Creamos el paciente
        $paciente = Paciente::create($datos);

        return response()->json([
            'mensaje' => 'Paciente creado exitosamente',
            'paciente' => $paciente
        ]);
    }

    // USAMOS EL FORM REQUEST AQUÍ TAMBIÉN
    public function actualizar(StorePacienteRequest $request, $id)
    {
        $paciente = Paciente::find($id);

        if (!$paciente) {
            return response()->json(['mensaje' => 'Paciente no encontrado'], 404);
        }

        // 1. Extraemos los datos validados
        $datos = $request->validated();
        
        // 2. Inyectamos silenciosamente quién está actualizando
        $datos['actualizado_por'] = Auth::id();

        // 3. Actualizamos
        $paciente->update($datos);

        return response()->json([
            'mensaje' => 'Paciente actualizado exitosamente',
            'paciente' => $paciente
        ]);
    }

    public function eliminar($id)
    {
        $paciente = Paciente::find($id);

        if (!$paciente) {
            return response()->json(['mensaje' => 'Paciente no encontrado'], 404);
        }

        $paciente->estado = 'Inactivo';
        $paciente->actualizado_por = Auth::id(); // Guardamos quién lo eliminó lógicamente
        $paciente->save();

        return response()->json([
            'mensaje' => 'Paciente archivado con éxito'
        ]);
    }

    public function imprimirExpediente($id)
    {
        try {
            $paciente = \App\Models\Paciente::findOrFail($id);
            $consultas = \App\Models\Consulta::where('paciente_id', $id)->orderBy('fecha_consulta', 'desc')->get();
            $facturas = \App\Models\Factura::where('paciente_id', $id)->orderBy('fecha_emision', 'desc')->get();
            
            $deudaTotal = $facturas->sum('saldo_pendiente');
            $edad = 'N/A';
            if($paciente->fecha_nacimiento) {
                $edad = \Carbon\Carbon::parse($paciente->fecha_nacimiento)->age . ' años';
            }

            $data = [
                'paciente' => $paciente,
                'consultas' => $consultas,
                'facturas' => $facturas,
                'edad' => $edad,
                'deudaTotal' => $deudaTotal,
                'clinica' => [
                    'nombre' => 'DentalSistem Clínica Odontológica',
                    'telefono' => '+503 2222-3333',
                    'email' => 'contacto@dentalsistem.com',
                    'direccion' => 'San Salvador, El Salvador'
                ]
            ];

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.expediente_paciente', $data);
            $pdf->setPaper('A4', 'portrait');

            return $pdf->stream('Expediente_' . $paciente->numero_expediente . '.pdf');

        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}