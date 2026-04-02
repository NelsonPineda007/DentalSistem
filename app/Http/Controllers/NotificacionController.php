<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cita;
use App\Models\CalendarioEvento;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NotificacionController extends Controller
{
    public function obtenerDatos()
    {
        try {
            $hoy = Carbon::today('America/El_Salvador');
            $usuario = Auth::user();
            $rol = DB::table('roles')->where('id', $usuario->rol_id)->value('nombre');

            // 1. Citas Pendientes
            $queryCitas = Cita::with(['paciente', 'empleado'])
                ->where('fecha_cita', '>=', $hoy->toDateString())
                ->whereIn('estado', ['Programada', 'Confirmada']);

            if ($rol === 'Dentista') {
                $queryCitas->where('empleado_id', $usuario->id);
            }

            $citas = $queryCitas->orderBy('fecha_cita', 'asc')
                ->orderBy('hora_inicio', 'asc')
                ->get()
                ->map(function ($cita) {
                    $fechaCarbon = Carbon::parse($cita->fecha_cita);
                    $etiquetaFecha = $fechaCarbon->isToday() ? 'Hoy' : ($fechaCarbon->isTomorrow() ? 'Mañana' : $fechaCarbon->format('d/m/Y'));
                    
                    return [
                        'id' => $cita->id,
                        'paciente_id' => $cita->paciente_id,
                        'paciente' => $cita->paciente ? $cita->paciente->nombre . ' ' . $cita->paciente->apellido : 'Paciente',
                        'motivo' => $cita->motivo_consulta ?? 'Sin motivo',
                        'etiqueta_fecha' => $etiquetaFecha,
                        'hora' => Carbon::parse($cita->hora_inicio)->format('g:i A'),
                        
                        // Campos de alta precisión para las alertas JS
                        'fecha_cruda' => $cita->fecha_cita,
                        'hora_cruda' => $cita->hora_inicio,
                    ];
                });

            // 2. Notas Hechas (PRIVADAS)
            $notas = CalendarioEvento::where('tipo', 'Nota')
                ->where('empleado_id', $usuario->id)
                ->orderBy('fecha', 'desc')
                ->limit(20)
                ->get()
                ->map(function ($nota) {
                    return [
                        'id' => $nota->id,
                        'autor' => $nota->titulo ?? 'Nota Personal',
                        'contenido' => $nota->detalles ?? 'Sin contenido',
                        'tiempo' => $nota->creado_en ? Carbon::parse($nota->creado_en)->diffForHumans() : 'Reciente',
                        
                        // Campos de alta precisión
                        'fecha_cruda' => $nota->fecha,
                        'hora_cruda' => $nota->hora,
                    ];
                });

            // 3. Recordatorios (PRIVADOS)
            $recordatorios = CalendarioEvento::where('tipo', 'Recordatorio')
                ->where('empleado_id', $usuario->id)
                ->orderBy('fecha', 'desc')
                ->limit(20)
                ->get()
                ->map(function ($rec) {
                    $fechaCarbon = Carbon::parse($rec->fecha);
                    $etiquetaFecha = $fechaCarbon->isToday() ? 'Hoy' : ($fechaCarbon->isYesterday() ? 'Ayer' : $fechaCarbon->format('d/m/Y'));
                    
                    return [
                        'id' => $rec->id,
                        'titulo' => $rec->titulo,
                        'detalles' => $rec->detalles,
                        'tiempo' => $etiquetaFecha . ($rec->hora ? ', ' . Carbon::parse($rec->hora)->format('g:i A') : ''),
                        
                        // Campos de alta precisión
                        'fecha_cruda' => $rec->fecha,
                        'hora_cruda' => $rec->hora,
                    ];
                });

            return response()->json([
                'status' => 'success',
                'citas' => $citas,
                'notas' => $notas,
                'recordatorios' => $recordatorios
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error en BD: ' . $e->getMessage()
            ]);
        }
    }
}