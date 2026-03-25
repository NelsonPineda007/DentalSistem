<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cita;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function obtenerDatos()
    {
        $hoy = Carbon::now('America/El_Salvador')->toDateString();
        
        // Arrays para las Sparklines (Tendencias de los últimos 7 días)
        $sparkCitas = [];
        $sparkNoAsistidas = [];
        $sparkCanceladas = [];
        $labelsMovimiento = [];
        $movimientoData = [];

        for ($i = 6; $i >= 0; $i--) {
            $fecha = Carbon::now('America/El_Salvador')->subDays($i);
            $fechaStr = $fecha->toDateString();
            
            $labelsMovimiento[] = ucfirst($fecha->locale('es')->isoFormat('ddd'));

            $citasExitosas = Cita::where('fecha_cita', $fechaStr)
                                 ->whereNotIn('estado', ['Cancelada', 'No presentado'])
                                 ->count();
            
            $sparkCitas[] = $citasExitosas;
            $movimientoData[] = $citasExitosas;
            
            $sparkNoAsistidas[] = Cita::where('fecha_cita', $fechaStr)
                                      ->where('estado', 'No presentado')
                                      ->count();

            $sparkCanceladas[] = Cita::where('fecha_cita', $fechaStr)
                                     ->where('estado', 'Cancelada')
                                     ->count();
        }

        $citasHoy = end($sparkCitas);
        $noAsistidasHoy = end($sparkNoAsistidas);
        $canceladasHoy = end($sparkCanceladas);

        // NUEVO: Citas Completadas HOY
        $completadasHoy = Cita::where('fecha_cita', $hoy)
                              ->where('estado', 'Completada')
                              ->count();

        // Tasa de completación (Completadas vs Total agendadas hoy)
        $totalCitasHoy = Cita::where('fecha_cita', $hoy)
                             ->whereNotIn('estado', ['Cancelada'])
                             ->count();
                             
        $tasaPorcentaje = $totalCitasHoy > 0 ? round(($completadasHoy / $totalCitasHoy) * 100) : 0;

        // Gráfica de Tratamientos
        $hace7Dias = Carbon::now('America/El_Salvador')->subDays(6)->toDateString();
        $tratamientosTop = DB::table('tratamientos_aplicados')
            ->join('tratamientos', 'tratamientos_aplicados.tratamiento_id', '=', 'tratamientos.id')
            ->where('tratamientos_aplicados.fecha_aplicacion', '>=', $hace7Dias . ' 00:00:00')
            ->select('tratamientos.nombre', DB::raw('count(*) as cantidad'))
            ->groupBy('tratamientos.nombre')
            ->orderByDesc('cantidad')
            ->limit(4)
            ->get();

        // NUEVO: Separar Citas Pendientes de HOY
        $notificacionesHoy = Cita::join('pacientes', 'citas.paciente_id', '=', 'pacientes.id')
            ->where('citas.fecha_cita', $hoy)
            ->whereIn('citas.estado', ['Programada', 'Confirmada', 'En progreso'])
            ->orderBy('citas.hora_inicio')
            ->select('citas.hora_inicio', 'pacientes.nombre', 'pacientes.apellido')
            ->limit(5)
            ->get()
            ->map(function($cita) {
                return [
                    'paciente' => $cita->nombre . ' ' . $cita->apellido,
                    'hora' => Carbon::parse($cita->hora_inicio)->format('g:i A')
                ];
            });

        // NUEVO: Separar Citas Próximas (Mañana en adelante)
        $notificacionesProximas = Cita::join('pacientes', 'citas.paciente_id', '=', 'pacientes.id')
            ->where('citas.fecha_cita', '>', $hoy)
            ->whereIn('citas.estado', ['Programada', 'Confirmada'])
            ->orderBy('citas.fecha_cita')
            ->orderBy('citas.hora_inicio')
            ->select('citas.fecha_cita', 'citas.hora_inicio', 'pacientes.nombre', 'pacientes.apellido')
            ->limit(5)
            ->get()
            ->map(function($cita) {
                return [
                    'paciente' => $cita->nombre . ' ' . $cita->apellido,
                    'fecha' => Carbon::parse($cita->fecha_cita)->format('d/m/Y'),
                    'hora' => Carbon::parse($cita->hora_inicio)->format('g:i A')
                ];
            });

        return response()->json([
            'stats' => [
                'citasHoy' => $citasHoy,
                'noAsistidasHoy' => $noAsistidasHoy,
                'canceladasHoy' => $canceladasHoy,
                'completadasHoy' => $completadasHoy,
                'tasaPorcentaje' => $tasaPorcentaje,
                'sparkCitas' => $sparkCitas,
                'sparkNoAsistidas' => $sparkNoAsistidas,
                'sparkCanceladas' => $sparkCanceladas
            ],
            'graficaMovimiento' => [
                'labels' => $labelsMovimiento,
                'data' => $movimientoData
            ],
            'graficaTratamientos' => $tratamientosTop,
            'notificacionesHoy' => $notificacionesHoy,
            'notificacionesProximas' => $notificacionesProximas
        ]);
    }
}