<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cita;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function obtenerDatos()
    {
        $hoy = Carbon::now('America/El_Salvador')->toDateString();
        $usuario = Auth::user();
        $rol = DB::table('roles')->where('id', $usuario->rol_id)->value('nombre');
        $esDentista = ($rol === 'Dentista');
        
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

            // Exitosas
            $qCitas = Cita::where('fecha_cita', $fechaStr)->whereNotIn('estado', ['Cancelada', 'No presentado']);
            if($esDentista) $qCitas->where('empleado_id', $usuario->id);
            $citasExitosas = $qCitas->count();
            
            $sparkCitas[] = $citasExitosas;
            $movimientoData[] = $citasExitosas;
            
            // No asistidas
            $qNoAsis = Cita::where('fecha_cita', $fechaStr)->where('estado', 'No presentado');
            if($esDentista) $qNoAsis->where('empleado_id', $usuario->id);
            $sparkNoAsistidas[] = $qNoAsis->count();

            // Canceladas
            $qCanc = Cita::where('fecha_cita', $fechaStr)->where('estado', 'Cancelada');
            if($esDentista) $qCanc->where('empleado_id', $usuario->id);
            $sparkCanceladas[] = $qCanc->count();
        }

        $citasHoy = end($sparkCitas);
        $noAsistidasHoy = end($sparkNoAsistidas);
        $canceladasHoy = end($sparkCanceladas);

        // Completadas
        $qComp = Cita::where('fecha_cita', $hoy)->where('estado', 'Completada');
        if($esDentista) $qComp->where('empleado_id', $usuario->id);
        $completadasHoy = $qComp->count();

        // Total hoy
        $qTot = Cita::where('fecha_cita', $hoy)->whereNotIn('estado', ['Cancelada']);
        if($esDentista) $qTot->where('empleado_id', $usuario->id);
        $totalCitasHoy = $qTot->count();
                             
        $tasaPorcentaje = $totalCitasHoy > 0 ? round(($completadasHoy / $totalCitasHoy) * 100) : 0;

        // Top Tratamientos (Filtrado por el doctor si aplica)
        $qTrat = DB::table('tratamientos_aplicados')
            ->join('tratamientos', 'tratamientos_aplicados.tratamiento_id', '=', 'tratamientos.id')
            ->select('tratamientos.nombre', DB::raw('count(*) as cantidad'))
            ->groupBy('tratamientos.nombre')
            ->orderByDesc('cantidad')
            ->limit(4);
        if($esDentista) $qTrat->where('tratamientos_aplicados.realizado_por', $usuario->id);
        $tratamientosTop = $qTrat->get();

        // Lista de Citas para hoy
        $qNotiHoy = Cita::join('pacientes', 'citas.paciente_id', '=', 'pacientes.id')
            ->where('citas.fecha_cita', $hoy)
            ->whereIn('citas.estado', ['Programada', 'Confirmada', 'En progreso'])
            ->orderBy('citas.hora_inicio')
            ->select('citas.id', 'citas.hora_inicio', 'pacientes.nombre', 'pacientes.apellido')
            ->limit(20);
        if($esDentista) $qNotiHoy->where('citas.empleado_id', $usuario->id);
        
        $notificacionesHoy = $qNotiHoy->get()->map(function($cita) {
            return [
                'id' => $cita->id,
                'paciente' => $cita->nombre . ' ' . $cita->apellido,
                'hora' => Carbon::parse($cita->hora_inicio)->format('g:i A')
            ];
        });

        // Lista de Citas Próximas
        $qNotiProx = Cita::join('pacientes', 'citas.paciente_id', '=', 'pacientes.id')
            ->where('citas.fecha_cita', '>', $hoy)
            ->whereIn('citas.estado', ['Programada', 'Confirmada'])
            ->orderBy('citas.fecha_cita')
            ->orderBy('citas.hora_inicio')
            ->select('citas.id', 'citas.fecha_cita', 'citas.hora_inicio', 'pacientes.nombre', 'pacientes.apellido')
            ->limit(20);
        if($esDentista) $qNotiProx->where('citas.empleado_id', $usuario->id);

        $notificacionesProximas = $qNotiProx->get()->map(function($cita) {
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