<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ficha Clínica - {{ $paciente->numero_expediente }}</title>
    <style>
        /* ESTILOS GLOBALES */
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #1e293b; font-size: 11px; margin: 0; padding: 15px; }
        .header { width: 100%; border-bottom: 3px solid #1e3a8a; padding-bottom: 12px; margin-bottom: 20px; }
        .clinic-name { color: #1e3a8a; font-size: 22px; font-weight: bold; margin-bottom: 4px; }
        .doc-title { text-align: right; color: #64748b; font-size: 16px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
        .patient-subtitle { font-size: 13px; color: #1e3a8a; margin-top: 5px; font-weight: bold; }

        .section-title { background-color: #f8fafc; color: #1e3a8a; padding: 8px 12px; font-weight: bold; font-size: 13px; margin-top: 25px; margin-bottom: 15px; text-transform: uppercase; border-left: 5px solid #1e3a8a; border-radius: 4px; border-top: 1px solid #e2e8f0; border-right: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0; }
        
        /* ESTILOS PARA LAS TABLAS DE DATOS */
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 11px; }
        .data-table th { background-color: #1e3a8a; color: #ffffff; padding: 8px; text-align: left; border: 1px solid #1e3a8a; font-weight: bold; text-transform: uppercase; font-size: 10px;}
        .data-table td { padding: 8px; border: 1px solid #cbd5e1; vertical-align: middle; }

        /* ESTILOS DE "CAJITAS" PARA IMITAR EL FORMULARIO WEB */
        .form-label { font-size: 10px; font-weight: bold; color: #64748b; text-transform: uppercase; margin-bottom: 4px; display: block;}
        .form-input { border: 1px solid #cbd5e1; background-color: #f1f5f9; padding: 8px 10px; border-radius: 6px; font-size: 12px; color: #1e293b; font-weight: bold; min-height: 16px;}
        .checkbox-group { border: 1px solid #cbd5e1; background-color: #f1f5f9; padding: 8px 10px; border-radius: 6px; }
        .checkbox-inline { display: inline-block; margin-right: 20px; font-weight: bold; color: #1e3a8a; font-size: 12px;}
        .check-box { display: inline-block; width: 14px; height: 14px; border: 2px solid #94a3b8; text-align: center; line-height: 14px; font-size: 12px; margin-right: 6px; background: white; border-radius: 3px; font-weight: 900;}

        /* ETIQUETAS DE COLORES */
        .badge-red { background-color: #fef2f2; color: #ef4444; padding: 3px 8px; border-radius: 4px; border: 1px solid #fca5a5; font-weight: bold; }
        .badge-blue { background-color: #eff6ff; color: #3b82f6; padding: 3px 8px; border-radius: 4px; border: 1px solid #93c5fd; font-weight: bold; }
        .badge-gray { background-color: #f8fafc; color: #475569; padding: 3px 8px; border-radius: 4px; border: 1px solid #cbd5e1; font-weight: bold; }

        /* =====================================================
           NUEVO: FILAS DE HISTORIAL POR CATEGORÍA
           ===================================================== */

        /* Urgencia / Dolor */
        .row-urgencia td { background-color: #fef2f2; border-color: #fecaca; }

        /* Facturación / Tratamiento */
        .row-facturacion td { background-color: #eff6ff; border-color: #bfdbfe; }

        /* Consulta general / default */
        .row-general td { background-color: #f8fafc; border-color: #e2e8f0; }

        /* NUEVA: Leyenda de colores del historial */
        .leyenda { display: flex; gap: 18px; margin-bottom: 10px; font-size: 10px; font-weight: bold; }
        .leyenda-item { display: flex; align-items: center; gap: 5px; }
        .leyenda-dot { width: 10px; height: 10px; border-radius: 2px; display: inline-block; }

        .footer { position: fixed; bottom: -20px; left: 0; right: 0; text-align: center; font-size: 9px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 5px; }
    </style>
</head>
<body>

@php
    // MAGIA PHP: Extraer SOLAMENTE los dientes afectados (Ignorar los sanos)
    $todas_las_piezas = [18,17,16,15,14,13,12,11, 21,22,23,24,25,26,27,28, 48,47,46,45,44,43,42,41, 31,32,33,34,35,36,37,38];
    $afectados = [];
    $caras_nombres = ['Superior', 'Derecha', 'Inferior', 'Izquierda', 'Central'];

    foreach($todas_las_piezas as $num) {
        $diag = $odontograma['diagnostico'][$num] ?? 'sano';
        $oper = $odontograma['operatoria'][$num] ?? [];

        // Traducimos las caras afectadas a texto claro
        $oper_activos = [];
        for($i=0; $i<5; $i++) {
            $estado_cara = $oper[$i] ?? 'sano';
            if ($estado_cara !== 'sano') {
                $oper_activos[] = $caras_nombres[$i] . ' (' . ucfirst($estado_cara) . ')';
            }
        }

        // Si el diente tiene diagnóstico OR caras afectadas, lo guardamos
        if ($diag !== 'sano' || count($oper_activos) > 0) {
            $afectados[$num] = [
                'diagnostico' => $diag,
                'operatoria' => count($oper_activos) > 0 ? implode(', ', $oper_activos) : 'Sin afecciones de caras'
            ];
        }
    }

    $extra = $odontograma['detalles_extra'] ?? [];

    // NUEVA FUNCIÓN: Clasifica el motivo de consulta en una categoría visual
    $clasificarConsulta = function(string $motivo): string {
        $m = strtolower($motivo);

        $palabras_urgencia = ['dolor', 'amputacion', 'urgencia', 'emergencia', 'fractura', 'trauma', 'infeccion', 'infección', 'absceso', 'hemorragia', 'sangrado'];
        $palabras_facturacion = ['factur', 'tratamiento', 'pago', 'cobro', 'caja', 'directo'];

        foreach ($palabras_urgencia as $p) {
            if (str_contains($m, $p)) return 'urgencia';
        }
        foreach ($palabras_facturacion as $p) {
            if (str_contains($m, $p)) return 'facturacion';
        }

        return 'general';
    };
@endphp

    {{-- CABECERA --}}
    <table class="header">
        <tr>
            <td style="width: 65%;">
                <div class="clinic-name">{{ $clinica['nombre'] }}</div>
                <div style="font-size: 11px; color: #64748b; font-weight: bold; text-transform: uppercase;">{{ $clinica['direccion'] }}</div>
                <div style="font-size: 11px; color: #64748b;">Teléfono: {{ $clinica['telefono'] }} | Email: {{ $clinica['email'] }}</div>
            </td>
            <td style="width: 35%;" class="doc-title">
                FICHA MÉDICA DENTAL<br>
                <div style="font-size: 14px; color: #1e3a8a;">N° {{ $paciente->numero_expediente }}</div>
                <div class="patient-subtitle">Paciente: {{ $paciente->nombre }} {{ $paciente->apellido }}</div>
            </td>
        </tr>
    </table>

    <div class="section-title">1. Odontograma (Solo Piezas Afectadas)</div>
    
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 10%; text-align: center; font-size: 12px;">Pieza</th>
                <th style="width: 25%;">Diagnóstico Visual</th>
                <th style="width: 65%;">Operatoria (Caras Afectadas)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($afectados as $num => $datos)
            <tr>
                <td style="text-align: center; font-size: 18px; font-weight: 900; color: #1e3a8a; background-color: #f8fafc;">
                    {{ $num }}
                </td>
                <td>
                    @if($datos['diagnostico'] == 'caries' || $datos['diagnostico'] == 'extraccion')
                        <span class="badge-red">{{ ucfirst($datos['diagnostico']) }}</span>
                    @elseif($datos['diagnostico'] == 'restaurado')
                        <span class="badge-blue">Restaurado</span>
                    @elseif($datos['diagnostico'] == 'ausente')
                        <span class="badge-gray">Ausente</span>
                    @else
                        <span style="color: #64748b; font-weight: bold;">Sano</span>
                    @endif
                </td>
                <td style="font-size: 11px; color: #334155; line-height: 1.5;">
                    {{ $datos['operatoria'] }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" style="text-align: center; color: #10b981; font-weight: bold; padding: 20px; font-size: 14px;">
                    🦷 Odontograma Sano - No se registran piezas dentales con afecciones.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">2. Prótesis</div>
    <table width="100%" style="margin-bottom: 15px;">
        <tr>
            <td width="33%" style="padding-right: 10px;">
                <span class="form-label">Dientes Color</span>
                <div class="form-input">{{ !empty($extra['prot_color']) ? $extra['prot_color'] : 'No especificado' }}</div>
            </td>
            <td width="33%" style="padding-right: 10px;">
                <span class="form-label">Guía</span>
                <div class="form-input">{{ !empty($extra['prot_guia']) ? $extra['prot_guia'] : 'No especificada' }}</div>
            </td>
            <td width="33%">
                <span class="form-label">Molde</span>
                <div class="form-input">{{ !empty($extra['prot_molde']) ? $extra['prot_molde'] : 'No especificado' }}</div>
            </td>
        </tr>
    </table>
    
    <span class="form-label">Materiales de Prótesis</span>
    <div class="checkbox-group" style="margin-bottom: 25px;">
        <span class="checkbox-inline">
            <span class="check-box" style="color: #2563eb;">{{ !empty($extra['prot_acrilico']) && $extra['prot_acrilico'] ? '✓' : ' ' }}</span> Acrílico
        </span>
        <span class="checkbox-inline">
            <span class="check-box" style="color: #2563eb;">{{ !empty($extra['prot_porcelana']) && $extra['prot_porcelana'] ? '✓' : ' ' }}</span> Porcelana
        </span>
    </div>

    <div class="section-title">3. Endodoncia - Cirugía</div>
    <table width="100%" style="margin-bottom: 15px;">
        <tr>
            <td width="50%" style="padding-right: 10px;">
                <span class="form-label">Diente Afectado</span>
                <div class="form-input">{{ !empty($extra['endo_diente']) ? $extra['endo_diente'] : 'Ninguno' }}</div>
            </td>
            <td width="50%">
                <span class="form-label">Vitalidad</span>
                <div class="form-input">{{ !empty($extra['endo_vitalidad']) ? $extra['endo_vitalidad'] : 'No evaluada' }}</div>
            </td>
        </tr>
    </table>
    <table width="100%" style="margin-bottom: 25px;">
        <tr>
            <td width="50%" style="padding-right: 10px;">
                <span class="form-label">Medicación Provisional</span>
                <div class="form-input">{{ !empty($extra['endo_provisional']) ? $extra['endo_provisional'] : 'No asignada' }}</div>
            </td>
            <td width="50%">
                <span class="form-label">Medicación de Trabajo</span>
                <div class="form-input">{{ !empty($extra['endo_trabajo']) ? $extra['endo_trabajo'] : 'No asignada' }}</div>
            </td>
        </tr>
    </table>

    <div class="section-title">4. Historial de Consultas Médicas</div>

    @if(count($consultas) > 0)

    {{-- LEYENDA VISUAL --}}
    <div class="leyenda">
        <span class="leyenda-item">
            <span class="leyenda-dot" style="background-color: #ef4444;"></span>
            <span style="color: #ef4444;">Urgencia / Dolor</span>
        </span>
        <span class="leyenda-item">
            <span class="leyenda-dot" style="background-color: #3b82f6;"></span>
            <span style="color: #3b82f6;">Facturación / Tratamiento</span>
        </span>
        <span class="leyenda-item">
            <span class="leyenda-dot" style="background-color: #94a3b8;"></span>
            <span style="color: #64748b;">Consulta General</span>
        </span>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 12%;">Fecha</th>
                <th style="width: 25%;">Motivo</th>
                <th style="width: 35%;">Observaciones y Síntomas</th>
                <th style="width: 28%;">Diagnóstico y Rx</th>
            </tr>
        </thead>
        <tbody>
            @foreach($consultas as $c)
            @php
                $categoria = $clasificarConsulta($c->motivo_consulta);
                $rowClass  = 'row-' . $categoria;
                
                // Definimos el color del borde según la categoría para evitar bugs en DomPDF
                $bordeColor = '#94a3b8'; // general (gris)
                if($categoria == 'urgencia') $bordeColor = '#ef4444'; // rojo
                if($categoria == 'facturacion') $bordeColor = '#3b82f6'; // azul
            @endphp
            <tr class="{{ $rowClass }}">
                {{-- LE APLICAMOS EL BORDE DIRECTO AQUÍ --}}
                <td style="font-weight: bold; color: #1e3a8a; border-left: 4px solid {{ $bordeColor }};">{{ date('d/m/Y', strtotime($c->fecha_consulta)) }}</td>
                <td><strong>{{ $c->motivo_consulta }}</strong></td>
                <td>
                    @if($c->sintomas) <div style="margin-bottom: 6px;"><strong>Sx:</strong> {{ $c->sintomas }}</div> @endif
                    @if($c->observaciones) <div><strong>Obs:</strong> {{ $c->observaciones }}</div> @endif
                </td>
                <td>
                    @if($c->diagnostico) <div style="margin-bottom: 6px; color: #ef4444; font-weight: bold;"><strong>Dx:</strong> {{ $c->diagnostico }}</div> @endif
                    @if($c->prescripciones) <div style="color: #16a34a; font-weight: bold;"><strong>Rx:</strong> {{ $c->prescripciones }}</div> @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div style="background-color: #f8fafc; padding: 15px; border: 1px dashed #cbd5e1; text-align: center; color: #64748b; font-weight: bold; border-radius: 6px;">
        No hay consultas médicas registradas en el historial de este paciente.
    </div>
    @endif

    <div class="footer">
        Ficha Clínica Oficial - {{ $clinica['nombre'] }} - Documento Médico Confidencial - Generado el {{ date('d/m/Y H:i') }}
    </div>

</body>
</html>