<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ExpedienteController extends Controller
{
    // =========================================================
    // 1. GUARDAR FICHA Y ODONTOGRAMA (MOMENTO 3)
    // =========================================================
    public function guardarFicha(Request $request, $paciente_id)
    {
        try {
            DB::beginTransaction();

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

                // SI ESTAMOS ACTUALIZANDO EL BORRADOR
                if (!empty($historia['consulta_id'])) {
                    $consultaGuardada = \App\Models\Consulta::find($historia['consulta_id']);
                    
                    if ($consultaGuardada) {
                        // 1. Actualizamos los textos normales
                        $consultaGuardada->update([
                            'motivo_consulta' => $historia['motivo_consulta'] ?? null,
                            'sintomas' => $historia['sintomas'] ?? null,
                            'observaciones' => $historia['observaciones'] ?? null,
                            'diagnostico' => $historia['diagnostico'] ?? null,
                            'prescripciones' => $historia['prescripciones'] ?? null,
                            'proxima_cita_recomendada' => $historia['proxima_cita'] ?? null,
                            'cita_id' => $historia['cita_id'] ?? $consultaGuardada->cita_id,
                        ]);

                        // 2. ¡FORZAMOS EL ESTADO! 
                        $consultaGuardada->estado = 'completada';
                        $consultaGuardada->save();

                        // 3. ACTUALIZAMOS LA CITA A COMPLETADA
                        if ($consultaGuardada->cita_id) {
                            \App\Models\Cita::where('id', $consultaGuardada->cita_id)
                                            ->update(['estado' => 'Completada']);
                        }
                    }
                } 
                // SI ES UNA CONSULTA LIBRE (Sin cita previa)
                else {
                    $consultaGuardada = \App\Models\Consulta::create([
                        'paciente_id' => $paciente_id,
                        'empleado_id' => 1, 
                        'cita_id' => $historia['cita_id'] ?? null,
                        'motivo_consulta' => $historia['motivo_consulta'] ?? null,
                        'sintomas' => $historia['sintomas'] ?? null,
                        'observaciones' => $historia['observaciones'] ?? null,
                        'diagnostico' => $historia['diagnostico'] ?? null,
                        'prescripciones' => $historia['prescripciones'] ?? null,
                        'proxima_cita_recomendada' => $historia['proxima_cita'] ?? null,
                    ]);

                    // Forzamos el estado también aquí
                    $consultaGuardada->estado = 'completada';
                    $consultaGuardada->save();

                    if ($consultaGuardada->cita_id) {
                        \App\Models\Cita::where('id', $consultaGuardada->cita_id)
                                        ->update(['estado' => 'Completada']);
                    }
                }
                Schema::enableForeignKeyConstraints();
            }

            DB::commit();

            return response()->json([
                'mensaje' => 'Ficha procesada correctamente', 
                'odontograma_bd' => $odontograma, 
                'consulta_bd' => $consultaGuardada
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
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
    // 3. OBTENER RECIBOS PARA LA TABLA DE FINANZAS
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
                    'estado_pago' => $f->estado_pago,
                    'cita_id' => $f->cita_id
                ];
            });

            return response()->json($datosFormateados);
        } catch (\Throwable $e) {
            return response()->json(['error_real' => $e->getMessage()], 500);
        }
    }

    // =========================================================
    // 4. GUARDAR RECIBO NUEVO (Y EXPEDIENTE)
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
            if ($abono >= $total) {
                $estado_pago = 'pagado';
            } else if ($abono > 0) {
                $estado_pago = 'parcial';
            }

            $consulta_id = null;

            // 1. Si el recibo viene de una cita, buscamos el expediente que el doctor ya llenó
            if (!empty($request->cita_id)) {
                $consultaExistente = \App\Models\Consulta::where('cita_id', $request->cita_id)->first();
                if ($consultaExistente) {
                    $consulta_id = $consultaExistente->id;
                }
            }

            // 2. Si NO hay cita (es un recibo Libre), CREAMOS un expediente administrativo para sostener los tratamientos
            if (!$consulta_id) {
                $consultaNueva = \App\Models\Consulta::create([
                    'paciente_id' => $paciente_id,
                    'empleado_id' => 1, 
                    'cita_id' => null,
                    'fecha_consulta' => $request->fecha,
                    'motivo_consulta' => 'Tratamiento Directo (Caja)', // Un nombre más limpio
                    'observaciones' => $request->observaciones_factura ?? 'Facturación directa sin consulta médica previa'
                ]);
                $consulta_id = $consultaNueva->id;
            }
            // 👆 FIN DE LA NUEVA LÓGICA 👆

            $ultimoId = \App\Models\Factura::max('id') ?? 0;
            $numeroFactura = 'FAC-' . str_pad($ultimoId + 1, 5, '0', STR_PAD_LEFT);

            $factura = \App\Models\Factura::create([
                'numero' => $numeroFactura,
                'paciente_id' => $paciente_id,
                'empleado_id' => 1, 
                'cita_id' => $request->cita_id ?? null,
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

            // Guardar Items y Tratamientos Aplicados usando el ID inteligente ($consulta_id)
            foreach ($request->tratamientos as $tratamiento) {
                
                $tratAplicado = \App\Models\TratamientoAplicado::create([
                    'consulta_id' => $consulta_id, // <-- Usamos el ID inteligente
                    'tratamiento_id' => $tratamiento['id'] ?? 1,
                    'diente' => $request->diente ?? 'General',
                    'realizado_por' => 1,
                    'notas' => 'Agregado desde facturación'
                ]);

                \App\Models\FacturaItem::create([
                    'factura_id' => $factura->id,
                    'tipo_item' => 'tratamiento',
                    'descripcion' => $tratamiento['nombre'], 
                    'cantidad' => 1,
                    'precio_unitario' => $tratamiento['precio'],
                    'total_item' => $tratamiento['precio'],
                    'tratamiento_id' => $tratamiento['id'] ?? null,
                    'tratamiento_aplicado_id' => $tratAplicado->id, 
                    'consulta_id' => $consulta_id // <-- Usamos el ID inteligente
                ]);
            }

            \App\Models\FacturaEstadoHistorial::create([
                'factura_id' => $factura->id,
                'tipo_cambio' => 'estado_general',
                'valor_nuevo' => 'emitida',
                'cuota_id' => $cuota ? $cuota->id : null, 
                'cambiado_por' => 1,
                'motivo' => 'Creación de recibo',
                'ip_address' => $request->ip()
            ]);

            Schema::enableForeignKeyConstraints();
            DB::commit(); 

            return response()->json(['mensaje' => 'Recibo y Expediente guardados con éxito']);

        } catch (\Throwable $e) {
            DB::rollBack(); 
            return response()->json(['error_real' => $e->getMessage(), 'linea' => $e->getLine()], 500);
        }
    }

    // =========================================================
    // 5. REGISTRAR UN NUEVO ABONO A UN RECIBO EXISTENTE
    // =========================================================
    public function abonarFactura(Request $request, $factura_id)
    {
        try {
            DB::beginTransaction();
            Schema::disableForeignKeyConstraints();
            
            $factura = \App\Models\Factura::findOrFail($factura_id);
            $abono = (float) $request->abono;

            if ($abono <= 0 || $abono > $factura->saldo_pendiente) {
                return response()->json(['error_real' => 'Monto de abono inválido'], 400);
            }

            $factura->saldo_pendiente -= $abono;
            $factura->estado_pago = ($factura->saldo_pendiente <= 0) ? 'pagado' : 'parcial';
            $factura->save();

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

            \App\Models\Pago::create([
                'factura_id' => $factura->id,
                'cuota_id' => $cuota ? $cuota->id : null,
                'empleado_id' => 1, 
                'monto' => $abono,
                'metodo_pago' => $request->metodo_pago,
                'estado' => 'confirmado',
                'nota' => 'Abono posterior a la emisión'
            ]);

            \App\Models\FacturaEstadoHistorial::create([
                'factura_id' => $factura->id,
                'tipo_cambio' => 'estado_pago',
                'valor_nuevo' => $factura->estado_pago,
                'cambiado_por' => 1,
                'motivo' => 'Registro de nuevo abono',
                'ip_address' => $request->ip()
            ]);

            Schema::enableForeignKeyConstraints();
            DB::commit();
            
            return response()->json(['mensaje' => 'Abono registrado correctamente']);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error_real' => $e->getMessage(), 'linea' => $e->getLine()], 500);
        }
    }

    // =========================================================
    // 6. GENERAR PDF DEL RECIBO
    // =========================================================
    public function imprimirFactura($factura_id)
    {
        try {
            $factura = \App\Models\Factura::findOrFail($factura_id);
            $paciente = \App\Models\Paciente::findOrFail($factura->paciente_id);
            $items = \App\Models\FacturaItem::where('factura_id', $factura->id)->get();
            $pagos = \App\Models\Pago::where('factura_id', $factura->id)->orderBy('fecha_pago', 'asc')->get();

            $cita = null;
            if ($factura->cita_id) {
                $cita = \App\Models\Cita::find($factura->cita_id);
            }

            $total_pagado = $pagos->sum('monto');

            $data = [
                'factura' => $factura,
                'paciente' => $paciente,
                'items' => $items,
                'pagos' => $pagos,
                'cita' => $cita,
                'total_pagado' => $total_pagado,
                'clinica' => [
                    'nombre' => 'DentalSistem Clínica Odontológica',
                    'telefono' => '+503 2222-3333',
                    'email' => 'contacto@dentalsistem.com',
                    'direccion' => 'San Salvador, El Salvador'
                ]
            ];

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.factura', $data);
            $pdf->setPaper('A4', 'portrait');

            return $pdf->stream('Recibo_' . $factura->numero . '.pdf');

        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // =========================================================
    // 7. INICIAR CONSULTA DESDE UNA CITA (MOMENTO 2)
    // =========================================================
    public function iniciarConsultaDesdeCita(Request $request, $citaId)
    {
        try {
            $cita = \App\Models\Cita::findOrFail($citaId);

            if ($cita->estado === 'En progreso') {
                $consultaBorrador = \App\Models\Consulta::where('cita_id', $cita->id)
                                            ->where('estado', 'borrador')
                                            ->orderBy('id', 'desc') 
                                            ->first();
                                            
                if ($consultaBorrador) {
                    return response()->json([
                        'mensaje' => 'Continuando con la consulta en curso.',
                        'consulta' => $consultaBorrador
                    ]);
                }
            }

            DB::beginTransaction();
            \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();

            $cita->estado = 'En progreso';
            $cita->save();

            $consulta = \App\Models\Consulta::create([
                'cita_id' => $cita->id,
                'paciente_id' => $cita->paciente_id,
                'empleado_id' => $cita->empleado_id, 
                'fecha_consulta' => now()->toDateString(),
                'motivo_consulta' => $cita->motivo_consulta,
                'estado' => 'borrador' 
            ]);

            \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();
            DB::commit();

            return response()->json([
                'mensaje' => 'Cita vinculada y borrador creado correctamente.',
                'consulta' => $consulta
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error_real' => $e->getMessage(), 'linea' => $e->getLine()], 500);
        }
    }

    // =========================================================
    // 8. OBTENER DETALLE DE RECIBO PARA EDITAR
    // =========================================================
    public function obtenerDetalleFactura($id)
    {
        try {
            $factura = \App\Models\Factura::findOrFail($id);
            $itemsDb = \App\Models\FacturaItem::where('factura_id', $id)->get();
            
            $items = $itemsDb->map(function($i) {
                return [
                    'id' => $i->tratamiento_id,
                    'nombre' => preg_replace('/\s*\(Diente:.*?\)/', '', $i->descripcion), 
                    'precio' => (float) $i->precio_unitario,
                    'codigo' => 'S/C'
                ];
            });

            // LÓGICA NUEVA: Buscar los Dientes Afectados
            $dientesAfectados = '';
            $primerItem = $itemsDb->firstWhere('tratamiento_aplicado_id', '!=', null);
            if ($primerItem) {
                $tratAplicado = \App\Models\TratamientoAplicado::find($primerItem->tratamiento_aplicado_id);
                if ($tratAplicado) {
                    $dientesAfectados = $tratAplicado->diente;
                }
            }
            
            return response()->json([
                'factura' => $factura,
                'items' => $items,
                'dientes' => $dientesAfectados // Enviamos el dato al frontend
            ]);

        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

// =========================================================
    // 9. ACTUALIZAR RECIBO (EDICIÓN DE TRATAMIENTOS)
    // =========================================================
    public function actualizarFactura(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            Schema::disableForeignKeyConstraints();

            $factura = \App\Models\Factura::findOrFail($id);
            $total = (float) $request->total;
            
            $pagos_existentes = \App\Models\Pago::where('factura_id', $factura->id)->sum('monto');
            $nuevo_saldo = $total - $pagos_existentes;
            
            $estado_pago = 'pendiente';
            if ($pagos_existentes >= $total) {
                $estado_pago = 'pagado';
            } else if ($pagos_existentes > 0) {
                $estado_pago = 'parcial';
            }

            $factura->update([
                'cita_id' => $request->cita_id ?? null,
                'fecha_emision' => $request->fecha,
                'subtotal' => $request->subtotal,
                'descuento' => $request->descuento,
                'total' => $total,
                'saldo_pendiente' => $nuevo_saldo > 0 ? $nuevo_saldo : 0,
                'estado_pago' => $estado_pago,
                'observaciones' => $request->observaciones_factura
            ]);

            // 👇 AQUI ESTÁ LA MAGIA PARA QUE NO SE PIERDA EL ID 👇
            $old_items = \App\Models\FacturaItem::where('factura_id', $factura->id)->get();
            
            $consulta_id = null;
            
            // 1. Rescatamos el ID de la consulta del primer item que ya existía
            $primerItem = $old_items->first();
            if ($primerItem && $primerItem->consulta_id) {
                $consulta_id = $primerItem->consulta_id;
            }

            // 2. Si por algún motivo estaba vacío, tratamos de buscar por la cita actual
            if (!$consulta_id && $factura->cita_id) {
                $consulta = \App\Models\Consulta::where('cita_id', $factura->cita_id)->first();
                $consulta_id = $consulta ? $consulta->id : null;
            }

            // 3. Si sigue estando vacío (caso raro), creamos una de emergencia para no dar error 500
            if (!$consulta_id) {
                $consultaNueva = \App\Models\Consulta::create([
                    'paciente_id' => $factura->paciente_id,
                    'empleado_id' => 1, 
                    'cita_id' => $factura->cita_id ?? null,
                    'fecha_consulta' => $request->fecha,
                    'motivo_consulta' => 'Tratamiento Directo (Caja)',
                    'observaciones' => 'Expediente recuperado al editar recibo'
                ]);
                $consulta_id = $consultaNueva->id;
            }

            // AHORA SÍ: Eliminamos items viejos de forma segura
            foreach($old_items as $oi) {
                if($oi->tratamiento_aplicado_id) {
                    \App\Models\TratamientoAplicado::where('id', $oi->tratamiento_aplicado_id)->delete();
                }
                $oi->delete();
            }

            // Guardamos los items corregidos
            foreach ($request->tratamientos as $tratamiento) {
                $tratAplicado = \App\Models\TratamientoAplicado::create([
                    'consulta_id' => $consulta_id, 
                    'tratamiento_id' => $tratamiento['id'] ?? 1,
                    'diente' => $request->diente ?? 'General',
                    'realizado_por' => 1,
                    'notas' => 'Actualizado tras edición de recibo'
                ]);

                \App\Models\FacturaItem::create([
                    'factura_id' => $factura->id,
                    'tipo_item' => 'tratamiento',
                    'descripcion' => $tratamiento['nombre'], 
                    'cantidad' => 1,
                    'precio_unitario' => $tratamiento['precio'],
                    'total_item' => $tratamiento['precio'],
                    'tratamiento_id' => $tratamiento['id'] ?? null,
                    'tratamiento_aplicado_id' => $tratAplicado->id, 
                    'consulta_id' => $consulta_id 
                ]);
            }

            // Actualizamos la cuota
            $cuota = \App\Models\FacturaCuota::where('factura_id', $factura->id)->first();
            if($cuota) {
                $cuota->update([
                    'monto_programado' => $total,
                    'saldo_cuota' => $nuevo_saldo > 0 ? $nuevo_saldo : 0,
                    'estado' => $nuevo_saldo <= 0 ? 'pagado_completo' : 'pagado_parcial'
                ]);
            }

            Schema::enableForeignKeyConstraints();
            DB::commit(); 
            return response()->json(['mensaje' => 'Recibo actualizado con éxito']);

        } catch (\Throwable $e) {
            DB::rollBack(); 
            return response()->json(['error_real' => $e->getMessage()], 500);
        }
    }

    // =========================================================
    // 10. GENERAR PDF DE LA FICHA CLINICA Y ODONTOGRAMA
    // =========================================================
    public function imprimirFicha($paciente_id)
    {
        try {
            $paciente = \App\Models\Paciente::findOrFail($paciente_id);
            $odontograma = \App\Models\Odontograma::where('paciente_id', $paciente_id)->first();
            $consultas = \App\Models\Consulta::where('paciente_id', $paciente_id)->orderBy('fecha_consulta', 'desc')->get();

            // Decodificamos el JSON del Odontograma de forma segura
            $estadoDientes = ['diagnostico' => [], 'operatoria' => [], 'detalles_extra' => []];
            if ($odontograma && $odontograma->estado_dientes) {
                $dec = is_string($odontograma->estado_dientes) ? json_decode($odontograma->estado_dientes, true) : $odontograma->estado_dientes;
                if (is_array($dec)) {
                    $estadoDientes = array_merge($estadoDientes, $dec);
                }
            }

            $data = [
                'paciente' => $paciente,
                'odontograma' => $estadoDientes,
                'consultas' => $consultas,
                'clinica' => [
                    'nombre' => 'DentalSistem Clínica Odontológica',
                    'telefono' => '+503 2222-3333',
                    'email' => 'contacto@dentalsistem.com',
                    'direccion' => 'San Salvador, El Salvador'
                ]
            ];

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.ficha_clinica', $data);
            
            // Ponemos el papel en horizontal (landscape) para que quepan bien los dientes
            $pdf->setPaper('A4', 'landscape');

            return $pdf->stream('Ficha_Clinica_' . $paciente->numero_expediente . '.pdf');

        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}