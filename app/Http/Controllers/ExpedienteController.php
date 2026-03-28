<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreFichaRequest;
use App\Http\Requests\StoreFacturaRequest;
use App\Http\Requests\StoreAbonoRequest;

class ExpedienteController extends Controller
{
    // =========================================================
    // 1. GUARDAR FICHA Y ODONTOGRAMA (MOMENTO 3)
    // =========================================================
    public function guardarFicha(StoreFichaRequest $request, $paciente_id)
    {
        $datos = $request->validated(); 
        $ahora = \Carbon\Carbon::now('America/El_Salvador')->format('Y-m-d H:i:s');

        try {
            DB::beginTransaction();

            $odontogramaJson = json_encode($datos['odontograma'] ?? []);
            $odontogramaExiste = DB::table('odontogramas')->where('paciente_id', $paciente_id)->first();

            if ($odontogramaExiste) {
                DB::table('odontogramas')->where('paciente_id', $paciente_id)->update([
                    'estado_dientes' => $odontogramaJson,
                    'observaciones_generales' => 'Actualizado desde sistema',
                    'actualizado_por' => Auth::id()
                ]);
            } else {
                DB::table('odontogramas')->insert([
                    'paciente_id' => $paciente_id,
                    'estado_dientes' => $odontogramaJson,
                    'observaciones_generales' => 'Actualizado desde sistema',
                    'actualizado_por' => Auth::id()
                ]);
            }

            $historia = $datos['historia'] ?? [];
            $consultaGuardadaId = null;

            if (!empty($historia['motivo_consulta']) || !empty($historia['diagnostico'])) {
                $proximaCita = !empty($historia['proxima_cita']) ? $historia['proxima_cita'] : null;
                $citaId = !empty($historia['cita_id']) ? $historia['cita_id'] : null;

                if (!empty($historia['consulta_id'])) {
                    DB::table('consultas')->where('id', $historia['consulta_id'])->update([
                        'motivo_consulta' => $historia['motivo_consulta'] ?? '',
                        'sintomas' => $historia['sintomas'] ?? '',
                        'observaciones' => $historia['observaciones'] ?? '',
                        'diagnostico' => $historia['diagnostico'] ?? '',
                        'prescripciones' => $historia['prescripciones'] ?? '',
                        'proxima_cita_recomendada' => $proximaCita,
                        'estado' => 'completada', 
                        'cita_id' => $citaId
                    ]);
                    $consultaGuardadaId = $historia['consulta_id'];
                } else {
                    $consultaGuardadaId = DB::table('consultas')->insertGetId([
                        'paciente_id' => $paciente_id,
                        'cita_id' => $citaId,
                        'empleado_id' => Auth::id(), 
                        'fecha_consulta' => $ahora, // 🔥 HORA EXACTA EL SALVADOR
                        'motivo_consulta' => $historia['motivo_consulta'] ?? '',
                        'sintomas' => $historia['sintomas'] ?? '',
                        'observaciones' => $historia['observaciones'] ?? '',
                        'diagnostico' => $historia['diagnostico'] ?? '',
                        'prescripciones' => $historia['prescripciones'] ?? '',
                        'proxima_cita_recomendada' => $proximaCita,
                        'estado' => 'completada'
                    ]);
                }
            }

            if (!empty($historia['cita_id'])) {
                DB::table('citas')->where('id', $historia['cita_id'])->update(['estado' => 'Completada']);
            }

            DB::commit();
            return response()->json(['success' => true, 'mensaje' => 'Ficha guardada', 'consulta_id' => $consultaGuardadaId]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage(), 'linea' => $e->getLine()], 500);
        }
    }

    public function obtenerFicha($paciente_id)
    {
        $odontograma = DB::table('odontogramas')->where('paciente_id', $paciente_id)->first();
        $consultas = DB::table('consultas')->where('paciente_id', $paciente_id)->orderBy('fecha_consulta', 'desc')->get();

        $estadoDientes = ['diagnostico' => [], 'operatoria' => [], 'detalles_extra' => []];
        if ($odontograma && $odontograma->estado_dientes) {
            $dec = json_decode($odontograma->estado_dientes, true);
            if (is_string($dec)) $dec = json_decode($dec, true);
            if (is_array($dec)) $estadoDientes = array_merge($estadoDientes, $dec);
        }

        return response()->json(['odontograma' => $estadoDientes, 'consultas' => $consultas]);
    }


    // =========================================================
    // 2. FACTURACIÓN Y PAGOS (MOMENTO 4)
    // =========================================================
    public function obtenerFacturas($paciente_id)
    {
        $facturas = DB::table('facturas')
            ->where('paciente_id', $paciente_id)
            ->orderBy('fecha_emision', 'desc')
            ->get();

        $resultado = [];
        foreach ($facturas as $f) {
            $tratamientosStr = DB::table('factura_items')
                ->join('tratamientos', 'factura_items.tratamiento_id', '=', 'tratamientos.id')
                ->where('factura_items.factura_id', $f->id)
                ->pluck('tratamientos.nombre')
                ->implode(', ');

            $resultado[] = [
                'id' => $f->id,
                'fecha' => date('d/m/Y', strtotime($f->fecha_emision)),
                'numero' => $f->numero, 
                'tratamiento' => $tratamientosStr,
                'valor' => $f->total,
                'saldo' => $f->saldo_pendiente,
                'estado_pago' => $f->estado_pago,
                'cita_id' => $f->cita_id
            ];
        }

        return response()->json($resultado);
    }

    public function guardarFactura(StoreFacturaRequest $request, $paciente_id)
    {
        $datos = $request->validated(); 
        // 🔥 TIEMPO EXACTO EL SALVADOR
        $ahoraObj = \Carbon\Carbon::now('America/El_Salvador');
        $ahoraCompleto = $ahoraObj->format('Y-m-d H:i:s');
        $horaExacta = $ahoraObj->format('H:i:s');

        try {
            DB::beginTransaction();

            $maxId = DB::table('facturas')->max('id') ?? 0;
            $numFactura = 'FAC-' . str_pad($maxId + 1, 5, '0', STR_PAD_LEFT);

            $abonoInicial = floatval($datos['abono'] ?? 0);
            $total = floatval($datos['total']);
            $saldoPendiente = $total - $abonoInicial;
            if ($saldoPendiente < 0) $saldoPendiente = 0;

            $estadoPago = 'pendiente';
            if ($abonoInicial >= $total) $estadoPago = 'pagado';
            elseif ($abonoInicial > 0) $estadoPago = 'parcial';

            $observacionesFinales = $datos['observaciones_factura'] ?? '';
            if (!empty($datos['diente'])) {
                $observacionesFinales .= "\n[Piezas afectadas: " . $datos['diente'] . "]";
            }

            $facturaId = DB::table('facturas')->insertGetId([
                'numero' => $numFactura,
                'paciente_id' => $paciente_id,
                'empleado_id' => Auth::id(),
                'cita_id' => $datos['cita_id'] ?? null,
                'fecha_emision' => $datos['fecha'] . ' ' . $horaExacta,
                'subtotal' => $datos['subtotal'],
                'impuestos' => 0.00,
                'descuento' => $datos['descuento'] ?? 0,
                'total' => $total,
                'saldo_pendiente' => $saldoPendiente,
                'estado_general' => 'emitida',
                'estado_pago' => $estadoPago,
                'tipo_factura' => $datos['tipo_factura'],
                'moneda' => 'USD',
                'observaciones' => $observacionesFinales,
                'creado_por' => Auth::id(),
                'creado_en' => $ahoraCompleto
            ]);

            foreach ($datos['tratamientos'] as $t) {
                $tratamientoDb = DB::table('tratamientos')->where('id', $t['id'])->first();
                $nombreTratamiento = $tratamientoDb ? $tratamientoDb->nombre : 'Tratamiento Médico';

                DB::table('factura_items')->insert([
                    'factura_id' => $facturaId,
                    'tipo_item' => 'tratamiento',
                    'descripcion' => $nombreTratamiento,
                    'cantidad' => 1,
                    'precio_unitario' => $t['precio'],
                    'descuento_item' => 0.00,
                    'total_item' => $t['precio'],
                    'tratamiento_id' => $t['id'],
                    'creado_en' => $ahoraCompleto
                ]);
            }

            if ($abonoInicial > 0) {
                DB::table('pagos')->insert([
                    'factura_id' => $facturaId,
                    'empleado_id' => Auth::id(),
                    'fecha_pago' => $ahoraCompleto, // 🔥 HORA EXACTA EL SALVADOR
                    'fecha_registro' => $ahoraCompleto,
                    'monto' => $abonoInicial,
                    'metodo_pago' => $datos['metodo_pago'],
                    'estado' => 'confirmado',
                    'registrado_por' => Auth::id(),
                    'creado_en' => $ahoraCompleto
                ]);
            }

            DB::commit();
            return response()->json(['success' => true, 'mensaje' => 'Factura generada']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function abonarFactura(StoreAbonoRequest $request, $factura_id)
    {
        $datos = $request->validated(); 
        $ahoraCompleto = \Carbon\Carbon::now('America/El_Salvador')->format('Y-m-d H:i:s');

        try {
            DB::beginTransaction();

            $factura = DB::table('facturas')->where('id', $factura_id)->first();
            $abono = floatval($datos['abono']);
            if ($abono > $factura->saldo_pendiente) $abono = $factura->saldo_pendiente;

            DB::table('pagos')->insert([
                'factura_id' => $factura->id,
                'empleado_id' => Auth::id(),
                'fecha_pago' => $ahoraCompleto, // 🔥 HORA EXACTA EL SALVADOR
                'fecha_registro' => $ahoraCompleto,
                'monto' => $abono,
                'metodo_pago' => $datos['metodo_pago'],
                'estado' => 'confirmado',
                'registrado_por' => Auth::id(),
                'creado_en' => $ahoraCompleto
            ]);

            $nuevoSaldo = $factura->saldo_pendiente - $abono;
            $estadoPago = $nuevoSaldo <= 0 ? 'pagado' : 'parcial';

            DB::table('facturas')->where('id', $factura->id)->update([
                'saldo_pendiente' => $nuevoSaldo,
                'estado_pago' => $estadoPago,
                'actualizado_por' => Auth::id(),
                'actualizado_en' => $ahoraCompleto
            ]);

            DB::commit();
            return response()->json(['success' => true, 'nuevo_saldo' => $nuevoSaldo]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function actualizarFactura(StoreFacturaRequest $request, $id)
    {
        $datos = $request->validated(); 
        $ahoraObj = \Carbon\Carbon::now('America/El_Salvador');
        $ahoraCompleto = $ahoraObj->format('Y-m-d H:i:s');
        $horaExacta = $ahoraObj->format('H:i:s');

        try {
            DB::beginTransaction();

            $pagosRealizados = DB::table('pagos')->where('factura_id', $id)->sum('monto');

            $nuevoTotal = floatval($datos['total']);
            $nuevoSaldo = $nuevoTotal - $pagosRealizados;
            if ($nuevoSaldo < 0) $nuevoSaldo = 0;

            $estadoPago = 'pendiente';
            if ($pagosRealizados >= $nuevoTotal) $estadoPago = 'pagado';
            elseif ($pagosRealizados > 0) $estadoPago = 'parcial';

            $observacionesFinales = $datos['observaciones_factura'] ?? '';
            if (!empty($datos['diente'])) {
                $observacionesFinales .= "\n[Piezas afectadas: " . $datos['diente'] . "]";
            }

            DB::table('facturas')->where('id', $id)->update([
                'fecha_emision' => $datos['fecha'] . ' ' . $horaExacta,
                'subtotal' => $datos['subtotal'],
                'descuento' => $datos['descuento'] ?? 0,
                'total' => $nuevoTotal,
                'saldo_pendiente' => $nuevoSaldo,
                'estado_pago' => $estadoPago,
                'observaciones' => $observacionesFinales,
                'cita_id' => $datos['cita_id'] ?? null,
                'actualizado_por' => Auth::id(),
                'actualizado_en' => $ahoraCompleto
            ]);

            DB::table('factura_items')->where('factura_id', $id)->delete();

            foreach ($datos['tratamientos'] as $t) {
                $tratamientoDb = DB::table('tratamientos')->where('id', $t['id'])->first();
                $nombreTratamiento = $tratamientoDb ? $tratamientoDb->nombre : 'Tratamiento Médico';

                DB::table('factura_items')->insert([
                    'factura_id' => $id,
                    'tipo_item' => 'tratamiento',
                    'descripcion' => $nombreTratamiento,
                    'cantidad' => 1,
                    'precio_unitario' => $t['precio'],
                    'descuento_item' => 0.00,
                    'total_item' => $t['precio'],
                    'tratamiento_id' => $t['id'],
                    'creado_en' => $ahoraCompleto
                ]);
            }

            DB::commit();
            return response()->json(['success' => true, 'mensaje' => 'Factura actualizada']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function obtenerDetalleFactura($id)
    {
        try {
            $factura = DB::table('facturas')->where('id', $id)->first();
            if(!$factura) return response()->json(['error' => 'Recibo no encontrado'], 404);

            $detalles = DB::table('factura_items')
                ->leftJoin('tratamientos', 'factura_items.tratamiento_id', '=', 'tratamientos.id')
                ->where('factura_items.factura_id', $id)
                ->select(
                    'factura_items.tratamiento_id as id', 
                    DB::raw("COALESCE(tratamientos.nombre, factura_items.descripcion) as nombre"), 
                    DB::raw("COALESCE(tratamientos.codigo, 'S/C') as codigo"), 
                    'factura_items.precio_unitario as precio'
                )
                ->get();

            $dientesAfectados = '';
            $observacionesLimpias = $factura->observaciones ?? '';
            
            if ($observacionesLimpias && preg_match('/\[Piezas afectadas:\s*(.*?)\]/', $observacionesLimpias, $matches)) {
                $dientesAfectados = $matches[1];
                $observacionesLimpias = str_replace("\n[Piezas afectadas: " . $dientesAfectados . "]", "", $observacionesLimpias);
                $observacionesLimpias = str_replace("[Piezas afectadas: " . $dientesAfectados . "]", "", $observacionesLimpias);
            }

            $factura->observaciones = trim($observacionesLimpias);

            return response()->json([
                'factura' => $factura,
                'items' => $detalles,
                'dientes' => $dientesAfectados
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'linea' => $e->getLine()], 500);
        }
    }

    public function iniciarConsultaDesdeCita(Request $request, $citaId)
    {
        try {
            $cita = DB::table('citas')->where('id', $citaId)->first();
            $consulta = DB::table('consultas')->where('cita_id', $citaId)->first();

            if (!$consulta) {
                $consultaId = DB::table('consultas')->insertGetId([
                    'paciente_id' => $cita->paciente_id,
                    'empleado_id' => Auth::id(),
                    'cita_id' => $cita->id,
                    'fecha_consulta' => \Carbon\Carbon::now('America/El_Salvador')->format('Y-m-d H:i:s'),
                    'motivo_consulta' => $cita->motivo_consulta,
                    'estado' => 'borrador' 
                ]);
                $consulta = DB::table('consultas')->where('id', $consultaId)->first();
            }

            if ($cita->estado !== 'En progreso') {
                DB::table('citas')->where('id', $citaId)->update(['estado' => 'En progreso']);
            }

            return response()->json(['success' => true, 'consulta' => $consulta]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function imprimirFactura($factura_id)
    {
        try {
            $factura = DB::table('facturas')->where('id', $factura_id)->first();
            if(!$factura) throw new \Exception("Factura no encontrada");

            $paciente = DB::table('pacientes')->where('id', $factura->paciente_id)->first();
            $creador = DB::table('empleados')->where('id', $factura->creado_por)->first();
            $pagos = DB::table('pagos')->where('factura_id', $factura_id)->get();
            $detalles = DB::table('factura_items')
                ->leftJoin('tratamientos', 'factura_items.tratamiento_id', '=', 'tratamientos.id')
                ->select('factura_items.*', 'tratamientos.nombre as tratamiento_nombre')
                ->where('factura_id', $factura_id)->get();

            $cita = null;
            if ($factura->cita_id) {
                $cita = DB::table('citas')->where('id', $factura->cita_id)->first();
            }

            $total_pagado = $pagos->sum('monto');

            $factura->paciente = $paciente;
            $factura->creador = $creador;
            $factura->pagos = $pagos;
            $factura->detalles = $detalles;
            $factura->cita = $cita;

            $data = [
                'factura' => $factura,
                'paciente' => $paciente,
                'creador' => $creador,
                'pagos' => $pagos,
                'detalles' => $detalles,
                'items' => $detalles, 
                'total_pagado' => $total_pagado, 
                'cita' => $cita, 
                'clinica' => [
                    'nombre' => 'DentalSistem Clínica Odontológica',
                    'telefono' => '+503 2222-3333',
                    'email' => 'contacto@dentalsistem.com',
                    'direccion' => 'San Salvador, El Salvador'
                ]
            ];

            // 🔥 AQUI CAMBIAMOS DE A5 a A4 PARA QUE VUELVA A SER TAMAÑO CARTA NORMAL
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.factura', $data);
            $pdf->setPaper('A4', 'portrait'); 

            return $pdf->stream('Recibo_' . $factura->numero . '.pdf');

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'linea' => $e->getLine()], 500);
        }
    }

    public function imprimirFicha($paciente_id)
    {
        try {
            $paciente = DB::table('pacientes')->where('id', $paciente_id)->first();
            if (!$paciente) throw new \Exception('Paciente no encontrado');

            $odontograma = DB::table('odontogramas')->where('paciente_id', $paciente_id)->first();
            $consultas = DB::table('consultas')->where('paciente_id', $paciente_id)->orderBy('fecha_consulta', 'desc')->get();

            $estadoDientes = ['diagnostico' => [], 'operatoria' => [], 'detalles_extra' => []];
            if ($odontograma && $odontograma->estado_dientes) {
                $dec = json_decode($odontograma->estado_dientes, true);
                if (is_string($dec)) $dec = json_decode($dec, true);
                if (is_array($dec)) $estadoDientes = array_merge($estadoDientes, $dec);
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
            $pdf->setPaper('A4', 'landscape');

            return $pdf->stream('Ficha_Clinica_' . $paciente->numero_expediente . '.pdf');

        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}