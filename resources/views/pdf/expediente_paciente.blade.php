<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Expediente - {{ $paciente->numero_expediente }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; font-size: 13px; margin: 0; padding: 20px; }
        .header { width: 100%; border-bottom: 3px solid #1e3a8a; padding-bottom: 15px; margin-bottom: 20px; }
        .clinic-name { color: #1e3a8a; font-size: 24px; font-weight: bold; margin-bottom: 5px; }
        .doc-title { text-align: right; color: #64748b; font-size: 18px; font-weight: bold; text-transform: uppercase; }
        
        .section-title { background-color: #1e3a8a; color: white; padding: 6px 12px; font-weight: bold; font-size: 12px; margin-top: 20px; margin-bottom: 10px; text-transform: uppercase; }
        
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .info-table td { padding: 6px 0; vertical-align: top; }
        .info-table .label { font-weight: bold; color: #64748b; width: 15%; font-size: 11px; text-transform: uppercase;}
        .info-table .value { width: 35%; color: #1e293b; font-weight: bold; border-bottom: 1px dotted #cbd5e1; padding-bottom: 2px;}
        
        .medical-alert { background-color: #fef2f2; border-left: 4px solid #ef4444; padding: 10px; margin-bottom: 15px; }
        .medical-alert strong { color: #b91c1c; }

        .data-table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 11px; }
        .data-table th { background-color: #f1f5f9; color: #334155; padding: 8px; text-align: left; border: 1px solid #cbd5e1; font-weight: bold; }
        .data-table td { padding: 8px; border: 1px solid #cbd5e1; vertical-align: top; }

        .debt-box { margin-top: 20px; padding: 15px; background-color: #f8fafc; border: 1px solid #e2e8f0; text-align: right; font-size: 14px;}
        .debt-box span { font-size: 18px; font-weight: bold; color: #ef4444; }

        .footer { position: fixed; bottom: -30px; left: 0; right: 0; text-align: center; font-size: 10px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 10px; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>

    <table class="header">
        <tr>
            <td style="width: 50%;">
                <div class="clinic-name">{{ $clinica['nombre'] }}</div>
                <div style="font-size: 11px; color: #64748b;">{{ $clinica['direccion'] }} | Tel: {{ $clinica['telefono'] }}</div>
            </td>
            <td style="width: 50%;" class="doc-title">
                EXPEDIENTE CLÍNICO<br>
                <span style="font-size: 14px; color: #1e3a8a;">N° {{ $paciente->numero_expediente }}</span>
            </td>
        </tr>
    </table>

    <div class="section-title">1. Identificación del Paciente</div>
    <table class="info-table">
        <tr>
            <td class="label">Nombres:</td> <td class="value">{{ $paciente->nombre }}</td>
            <td class="label">Apellidos:</td> <td class="value">{{ $paciente->apellido }}</td>
        </tr>
        <tr>
            <td class="label">Edad:</td> <td class="value">{{ $edad }}</td>
            <td class="label">Sexo:</td> <td class="value">{{ $paciente->genero ?? 'No especificado' }}</td>
        </tr>
        <tr>
            <td class="label">DUI:</td> <td class="value">{{ $paciente->DUI ?? 'No registrado' }}</td>
            <td class="label">Teléfono:</td> <td class="value">{{ $paciente->telefono ?? 'No registrado' }}</td>
        </tr>
        <tr>
            <td class="label">Dirección:</td> <td class="value" colspan="3">{{ $paciente->direccion ?? 'No registrada' }}, {{ $paciente->ciudad ?? '' }}</td>
        </tr>
    </table>

    @if($paciente->alergias || $paciente->enfermedades_cronicas)
    <div class="medical-alert">
        <strong>⚠️ ALERTA MÉDICA IMPORTANTE</strong><br>
        @if($paciente->alergias) <div>Alergias: {{ $paciente->alergias }}</div> @endif
        @if($paciente->enfermedades_cronicas) <div style="margin-top: 5px;">Enf. Crónicas: {{ $paciente->enfermedades_cronicas }}</div> @endif
    </div>
    @endif

    <div class="section-title">2. Antecedentes Médicos</div>
    <table class="info-table" style="width: 100%;">
        <tr>
            <td class="label" style="width: 20%;">Grupo Sanguíneo:</td> <td class="value" style="width: 30%;">{{ $paciente->grupo_sanguineo ?? 'Desconocido' }}</td>
            <td class="label" style="width: 20%;">Seguro Médico:</td> <td class="value" style="width: 30%;">{{ $paciente->seguro_medico ?? 'Particular' }}</td>
        </tr>
        <tr>
            <td class="label" style="width: 20%;">Medicamentos:</td> <td class="value" colspan="3">{{ $paciente->medicamentos_actuales ?? 'Ninguno reportado' }}</td>
        </tr>
        @if($paciente->notas_medicas)
        <tr>
            <td class="label" style="width: 20%;">Notas Especiales:</td> <td class="value" colspan="3" style="color: #ea580c;">{{ $paciente->notas_medicas }}</td>
        </tr>
        @endif
    </table>

    <div class="section-title">3. Contacto de Emergencia</div>
    <table class="info-table">
        <tr>
            <td class="label" style="width: 20%;">Nombre:</td> <td class="value" style="width: 30%;">{{ $paciente->contacto_emergencia_nombre ?? 'No asignado' }}</td>
            <td class="label" style="width: 20%;">Teléfono:</td> <td class="value" style="width: 30%;">{{ $paciente->contacto_emergencia_telefono ?? 'No asignado' }}</td>
        </tr>
    </table>

    @if(count($consultas) > 0)
    <div class="page-break"></div>
    <div class="section-title">4. Historial de Consultas Clínicas</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 15%;">Fecha</th>
                <th style="width: 25%;">Motivo</th>
                <th style="width: 35%;">Observaciones / Diagnóstico</th>
                <th style="width: 25%;">Prescripciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($consultas as $consulta)
            <tr>
                <td>{{ date('d/m/Y', strtotime($consulta->fecha_consulta)) }}</td>
                <td><strong>{{ $consulta->motivo_consulta }}</strong></td>
                <td>
                    @if($consulta->sintomas) <div><em>Sx:</em> {{ $consulta->sintomas }}</div> @endif
                    @if($consulta->observaciones) <div style="margin-top:3px;"><em>Obs:</em> {{ $consulta->observaciones }}</div> @endif
                    @if($consulta->diagnostico) <div style="margin-top:3px; color: #1e3a8a;"><em>Dx:</em> {{ $consulta->diagnostico }}</div> @endif
                </td>
                <td>{{ $consulta->prescripciones ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if(count($facturas) > 0)
    <div class="section-title" style="margin-top: 30px;">5. Resumen Financiero y Recibos</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 15%;">Fecha</th>
                <th style="width: 15%;">N° Recibo</th>
                <th style="width: 40%;">Concepto</th>
                <th style="width: 15%;">Total</th>
                <th style="width: 15%;">Saldo Pendiente</th>
            </tr>
        </thead>
        <tbody>
            @foreach($facturas as $factura)
            <tr>
                <td>{{ date('d/m/Y', strtotime($factura->fecha_emision)) }}</td>
                <td>{{ $factura->numero }}</td>
                <td>{{ $factura->observaciones ?? 'Servicios dentales' }}</td>
                <td>${{ number_format($factura->total, 2) }}</td>
                <td style="color: {{ $factura->saldo_pendiente > 0 ? '#ef4444' : '#10b981' }}; font-weight: bold;">
                    ${{ number_format($factura->saldo_pendiente, 2) }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if($deudaTotal > 0)
    <div class="debt-box">
        Deuda Total Activa del Paciente: <span>${{ number_format($deudaTotal, 2) }}</span>
    </div>
    @endif
    @endif

    <div class="footer">
        Documento Médico Confidencial - Impreso el {{ date('d/m/Y H:i') }} - Sistema DentalSistem
    </div>

</body>
</html>