<?php

namespace App\Http\Controllers;

use App\Models\Odontograma;
use App\Models\Consulta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema; // <-- IMPORTANTE PARA EL TRUCO

class ExpedienteController extends Controller
{
    // =========================================================
    // 1. FUNCIÓN PARA GUARDAR (La que ya tenías perfecta)
    // =========================================================
    public function guardarFicha(Request $request, $paciente_id)
    {
        try {
            // 1. GUARDAR ODONTOGRAMA
            $odontograma = Odontograma::updateOrCreate(
                ['paciente_id' => $paciente_id], 
                [
                    'estado_dientes' => $request->odontograma,
                    'observaciones_generales' => 'Actualizado desde sistema'
                ]
            );

            // 2. GUARDAR HISTORIA DE CONSULTA
            $historia = $request->historia;
            $consultaGuardada = null;

            if (!empty($historia['motivo_consulta']) || !empty($historia['diagnostico'])) {
                
                Schema::disableForeignKeyConstraints();

                $consultaGuardada = Consulta::create([
                    'paciente_id' => $paciente_id,
                    'empleado_id' => 1, 
                    'motivo_consulta' => $historia['motivo_consulta'] ?? null,
                    'sintomas' => $historia['sintomas'] ?? null,
                    'observaciones' => $historia['observaciones'] ?? null,
                    'diagnostico' => $historia['diagnostico'] ?? null,
                    'prescripciones' => $historia['prescripciones'] ?? null,
                    'proxima_cita_recomendada' => $historia['proxima_cita'] ?? null,
                ]);

                Schema::enableForeignKeyConstraints();
            }

            return response()->json([
                'mensaje' => 'Ficha procesada correctamente',
                'odontograma_bd' => $odontograma,
                'consulta_bd' => $consultaGuardada
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error_real' => $e->getMessage(),
                'linea' => $e->getLine()
            ], 500);
        }
    }

    // =========================================================
    // 2. FUNCIÓN PARA LEER (¡Esta es la que faltaba!)
    // =========================================================
    public function obtenerFicha($paciente_id)
    {
        try {
            $odontograma = Odontograma::where('paciente_id', $paciente_id)->first();
            
            return response()->json([
                'odontograma' => $odontograma ? $odontograma->estado_dientes : null
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error_real' => $e->getMessage(),
                'linea' => $e->getLine()
            ], 500);
        }
    }
}