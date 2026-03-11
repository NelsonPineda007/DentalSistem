<?php

namespace App\Http\Controllers;

use App\Models\Odontograma;
use App\Models\Consulta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ExpedienteController extends Controller
{
    // =========================================================
    // 1. FUNCIÓN PARA GUARDAR O ACTUALIZAR
    // =========================================================
    public function guardarFicha(Request $request, $paciente_id)
    {
        try {
            $odontograma = Odontograma::updateOrCreate(
                ['paciente_id' => $paciente_id], 
                [
                    'estado_dientes' => $request->odontograma,
                    'observaciones_generales' => 'Actualizado desde sistema'
                ]
            );

            $historia = $request->historia;
            $consultaGuardada = null;

            if (!empty($historia['motivo_consulta']) || !empty($historia['diagnostico'])) {
                Schema::disableForeignKeyConstraints();

                // SI TRAE UN ID, ACTUALIZAMOS LA CONSULTA EXISTENTE
                if (!empty($historia['consulta_id'])) {
                    $consultaGuardada = Consulta::find($historia['consulta_id']);
                    if ($consultaGuardada) {
                        $consultaGuardada->update([
                            'motivo_consulta' => $historia['motivo_consulta'] ?? null,
                            'sintomas' => $historia['sintomas'] ?? null,
                            'observaciones' => $historia['observaciones'] ?? null,
                            'diagnostico' => $historia['diagnostico'] ?? null,
                            'prescripciones' => $historia['prescripciones'] ?? null,
                            'proxima_cita_recomendada' => $historia['proxima_cita'] ?? null,
                        ]);
                    }
                } else {
                    // SI NO TRAE ID, CREAMOS UNA NUEVA
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
                }

                Schema::enableForeignKeyConstraints();
            }

            return response()->json([
                'mensaje' => 'Ficha procesada correctamente',
                'odontograma_bd' => $odontograma,
                'consulta_bd' => $consultaGuardada
            ]);

        } catch (\Exception $e) {
            return response()->json(['error_real' => $e->getMessage(), 'linea' => $e->getLine()], 500);
        }
    }

    // =========================================================
    // 2. FUNCIÓN PARA LEER EL HISTORIAL AL ENTRAR
    // =========================================================
    public function obtenerFicha($paciente_id)
    {
        try {
            $odontograma = Odontograma::where('paciente_id', $paciente_id)->first();
            
            // NUEVO: Traemos el historial ordenado de la más reciente a la más antigua
            $consultas = Consulta::where('paciente_id', $paciente_id)
                                 ->orderBy('id', 'desc')
                                 ->get();
            
            return response()->json([
                'odontograma' => $odontograma ? $odontograma->estado_dientes : null,
                'consultas' => $consultas
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['error_real' => $e->getMessage(), 'linea' => $e->getLine()], 500);
        }
    }
}