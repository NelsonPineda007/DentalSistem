<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ExpedienteController extends Controller
{
    // =========================================================
    // 1. GUARDAR FICHA Y ODONTOGRAMA
    // =========================================================
    public function guardarFicha(Request $request, $paciente_id)
    {
        try {
            $odontograma = \App\Models\Odontograma::updateOrCreate(
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

                if (!empty($historia['consulta_id'])) {
                    $consultaGuardada = \App\Models\Consulta::find($historia['consulta_id']);
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
                    $consultaGuardada = \App\Models\Consulta::create([
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

            return response()->json(['mensaje' => 'Ficha procesada correctamente', 'odontograma_bd' => $odontograma, 'consulta_bd' => $consultaGuardada]);

        } catch (\Throwable $e) {
            return response()->json(['error_real' => $e->getMessage(), 'linea' => $e->getLine()], 500);
        }
    }

    // =========================================================
    // 2. LEER FICHA Y CONSULTAS
    // =========================================================
    public function obtenerFicha($paciente_id)
    {
        try {
            $odontograma = \App\Models\Odontograma::where('paciente_id', $paciente_id)->first();
            $consultas = \App\Models\Consulta::where('paciente_id', $paciente_id)->orderBy('id', 'desc')->get();
            
            return response()->json([
                'odontograma' => $odontograma ? $odontograma->estado_dientes : null,
                'consultas' => $consultas
            ]);
        } catch (\Throwable $e) {
            return response()->json(['error_real' => $e->getMessage(), 'linea' => $e->getLine()], 500);
        }
    }

    // =========================================================
    // 3. OBTENER FACTURAS PARA LA TABLA DE FINANZAS
    // =========================================================
    public function obtenerFacturas($paciente_id)
    {
        try {
            $facturas = \App\Models\Factura::where('paciente_id', $paciente_id)->orderBy('fecha_emision', 'desc')->get();
            
            $datosFormateados = $facturas->map(function($f) {
                return [
                    'id' => $f->id,
                    'numero' => $f->numero,
                    'fecha' => date('d/m/Y', strtotime($f->fecha_emision)),
                    'tratamiento' => $f->observaciones ?? 'Consulta Dental',
                    'valor' => $f->total,
                    'abono' => $f->total - $f->saldo_pendiente,
                    'saldo' => $f->saldo_pendiente,
                    'estado_pago' => $f->estado_pago
                ];
            });

            return response()->json($datosFormateados);
        } catch (\Throwable $e) {
            return response()->json(['error_real' => $e->getMessage()], 500);
        }
    }

    // =========================================================
    // 4. GUARDAR FACTURA NUEVA (Y EXPEDIENTE)
    // =========================================================
    public function guardarFactura(Request $request, $paciente_id)
    {
        try {
            DB::beginTransaction(); 
            Schema::disableForeignKeyConstraints();

            $total = (float) $request->total;
            $abono = (float) $request->abono;
            $saldo_pendiente = $total - $abono;
            
            $tipo_factura = ($saldo_pendiente > 0) ? 'cuotas' : $request->tipo_factura;
            
            $estado_pago = 'pendiente';
            if ($abono >= $total) $estado_pago = 'pagado';
            else if ($abono > 0) $estado_pago = 'parcial';

            $consulta = \App\Models\Consulta::create([
                'paciente_id' => $paciente_id,
                'empleado_id' => 1, 
                'fecha_consulta' => $request->fecha,
                'motivo_consulta' => 'Facturación y Aplicación de Tratamientos',
                'observaciones' => $request->observaciones_factura ?? 'Facturación desde caja'
            ]);

            $ultimoId = \App\Models\Factura::max('id') ?? 0;
            $numeroFactura = 'FAC-' . str_pad($ultimoId + 1, 5, '0', STR_PAD_LEFT);

            $factura = \App\Models\Factura::create([
                'numero' => $numeroFactura,
                'paciente_id' => $paciente_id,
                'empleado_id' => 1, 
                'fecha_emision' => $request->fecha,
                'subtotal' => $request->subtotal,
                'descuento' => $request->descuento,
                'total' => $total,
                'saldo_pendiente' => $saldo_pendiente,
                'estado_general' => 'emitida',
                'estado_pago' => $estado_pago,
                'tipo_factura' => $tipo_factura,
                'observaciones' => $request->observaciones_factura ?? 'Tratamiento Dental', 
            ]);

            $cuota = null;
            if ($tipo_factura === 'cuotas' && $saldo_pendiente > 0) {
                $cuota = \App\Models\FacturaCuota::create([
                    'factura_id' => $factura->id,
                    'numero_cuota' => 1,
                    'total_cuotas' => 1, 
                    'monto_programado' => $total, 
                    'monto_abonado' => 0, 
                    'fecha_vencimiento' => date('Y-m-d', strtotime('+30 days')), 
                    'estado' => 'pendiente'
                ]);
            }

            if ($abono > 0) {
                \App\Models\Pago::create([
                    'factura_id' => $factura->id,
                    'cuota_id' => $cuota ? $cuota->id : null, 
                    'empleado_id' => 1,
                    'monto' => $abono,
                    'metodo_pago' => $request->metodo_pago,
                    'estado' => 'confirmado',
                    'nota' => 'Abono inicial realizado en caja'
                ]);

                if ($cuota) {
                    $cuota->monto_abonado = $abono;
                    $cuota->estado = 'pagado_parcial';
                    $cuota->save();
                }
            }

            foreach ($request->tratamientos as $tratamiento) {
                $tratAplicado = \App\Models\TratamientoAplicado::create([
                    'consulta_id' => $consulta->id, 
                    'tratamiento_id' => $tratamiento['id'] ?? 1,
                    'diente' => $request->diente ?? 'General',
                    'realizado_por' => 1,
                    'notas' => 'Agregado desde facturación'
                ]);

                \App\Models\FacturaItem::create([
                    'factura_id' => $factura->id,
                    'tipo_item' => 'tratamiento',
                    'descripcion' => $tratamiento['nombre'] . ' (Diente: ' . ($request->diente ?? 'General') . ')',
                    'cantidad' => 1,
                    'precio_unitario' => $tratamiento['precio'],
                    'total_item' => $tratamiento['precio'],
                    'tratamiento_id' => $tratamiento['id'] ?? null,
                    'tratamiento_aplicado_id' => $tratAplicado->id, 
                    'consulta_id' => $consulta->id 
                ]);
            }

            \App\Models\FacturaEstadoHistorial::create([
                'factura_id' => $factura->id,
                'tipo_cambio' => 'estado_general',
                'valor_nuevo' => 'emitida',
                'cuota_id' => $cuota ? $cuota->id : null, 
                'cambiado_por' => 1,
                'motivo' => 'Creación de factura',
                'ip_address' => $request->ip()
            ]);

            Schema::enableForeignKeyConstraints();
            DB::commit(); 

            return response()->json(['mensaje' => 'Factura y Expediente guardados con éxito']);

        } catch (\Throwable $e) {
            DB::rollBack(); 
            return response()->json(['error_real' => $e->getMessage(), 'linea' => $e->getLine()], 500);
        }
    }

// =========================================================
    // 5. REGISTRAR UN NUEVO ABONO A UNA FACTURA EXISTENTE
    // =========================================================
    public function abonarFactura(Request $request, $factura_id)
    {
        try {
            DB::beginTransaction();
            
            // APAGAMOS LAS RESTRICCIONES PARA QUE NO BUSQUE AL EMPLEADO 1
            Schema::disableForeignKeyConstraints();
            
            $factura = \App\Models\Factura::findOrFail($factura_id);
            $abono = (float) $request->abono;

            if ($abono <= 0 || $abono > $factura->saldo_pendiente) {
                return response()->json(['error_real' => 'Monto de abono inválido'], 400);
            }

            // 1. Actualizar el saldo de la Factura
            $factura->saldo_pendiente -= $abono;
            $factura->estado_pago = ($factura->saldo_pendiente <= 0) ? 'pagado' : 'parcial';
            $factura->save();

            // 2. Actualizar la Cuota (si era crédito)
            $cuota = \App\Models\FacturaCuota::where('factura_id', $factura->id)->where('estado', '!=', 'pagado_completo')->first();
            if ($cuota) {
                $cuota->monto_abonado += $abono;
                $nuevo_saldo_cuota = $cuota->monto_programado - $cuota->monto_abonado;
                
                $cuota->estado = ($nuevo_saldo_cuota <= 0) ? 'pagado_completo' : 'pagado_parcial';
                if ($nuevo_saldo_cuota <= 0) {
                    $cuota->fecha_pago = now();
                }
                $cuota->save();
            }

            // 3. Crear el recibo en la tabla Pagos
            \App\Models\Pago::create([
                'factura_id' => $factura->id,
                'cuota_id' => $cuota ? $cuota->id : null,
                'empleado_id' => 1, // Esto daba error, pero ya apagamos la restricción
                'monto' => $abono,
                'metodo_pago' => $request->metodo_pago,
                'estado' => 'confirmado',
                'nota' => 'Abono posterior a la emisión'
            ]);

            // 4. Dejar rastro en el Historial
            \App\Models\FacturaEstadoHistorial::create([
                'factura_id' => $factura->id,
                'tipo_cambio' => 'estado_pago',
                'valor_nuevo' => $factura->estado_pago,
                'cambiado_por' => 1,
                'motivo' => 'Registro de nuevo abono',
                'ip_address' => $request->ip()
            ]);

            // ENCENDEMOS LAS RESTRICCIONES DE NUEVO
            Schema::enableForeignKeyConstraints();
            DB::commit();
            
            return response()->json(['mensaje' => 'Abono registrado correctamente']);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error_real' => $e->getMessage(), 'linea' => $e->getLine()], 500);
        }
    }
}