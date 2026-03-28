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
        
        $sparkCitas = [];
        $sparkNoAsistidas = [];
        $sparkCanceladas = [];
        $labelsMovimiento = [];
        $movimientoData = [];

        // Gráfica de últimos 7 días de citas
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

        $completadasHoy = Cita::where('fecha_cita', $hoy)
                              ->where('estado', 'Completada')
                              ->count();

        $totalCitasHoy = Cita::where('fecha_cita', $hoy)
                             ->whereNotIn('estado', ['Cancelada'])
                             ->count();
                             
        $tasaPorcentaje = $totalCitasHoy > 0 ? round(($completadasHoy / $totalCitasHoy) * 100) : 0;

        // NUEVO: Quitamos el límite de 7 días. Ahora trae el Top 4 histórico.
        $tratamientosTop = DB::table('tratamientos_aplicados')
            ->join('tratamientos', 'tratamientos_aplicados.tratamiento_id', '=', 'tratamientos.id')
            ->select('tratamientos.nombre', DB::raw('count(*) as cantidad'))
            ->groupBy('tratamientos.nombre')
            ->orderByDesc('cantidad')
            ->limit(4)
            ->get();

        // Lista de Citas para hoy (Incluye el ID)
        $notificacionesHoy = Cita::join('pacientes', 'citas.paciente_id', '=', 'pacientes.id')
            ->where('citas.fecha_cita', $hoy)
            ->whereIn('citas.estado', ['Programada', 'Confirmada', 'En progreso'])
            ->orderBy('citas.hora_inicio')
            ->select('citas.id', 'citas.hora_inicio', 'pacientes.nombre', 'pacientes.apellido')
            ->limit(20)
            ->get()
            ->map(function($cita) {
                return [
                    'id' => $cita->id,
                    'paciente' => $cita->nombre . ' ' . $cita->apellido,
                    'hora' => Carbon::parse($cita->hora_inicio)->format('g:i A')
                ];
            });

        // Lista de Citas Próximas (Incluye el ID)
        $notificacionesProximas = Cita::join('pacientes', 'citas.paciente_id', '=', 'pacientes.id')
            ->where('citas.fecha_cita', '>', $hoy)
            ->whereIn('citas.estado', ['Programada', 'Confirmada'])
            ->orderBy('citas.fecha_cita')
            ->orderBy('citas.hora_inicio')
            ->select('citas.id', 'citas.fecha_cita', 'citas.hora_inicio', 'pacientes.nombre', 'pacientes.apellido')
            ->limit(20)
            ->get()
            ->map(function($cita) {
                return [
                    'id' => $cita->id,
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