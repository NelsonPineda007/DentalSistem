<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura {{ $factura->numero }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; font-size: 14px; margin: 0; padding: 20px; }
        .header { width: 100%; border-bottom: 2px solid #1e3a8a; padding-bottom: 20px; margin-bottom: 30px; }
        .header table { width: 100%; }
        .clinic-info { text-align: left; }
        .invoice-info { text-align: right; }
        .clinic-name { color: #1e3a8a; font-size: 24px; font-weight: bold; margin-bottom: 5px; }
        .title { font-size: 20px; font-weight: bold; color: #333; margin-bottom: 5px; text-transform: uppercase; }
        .badge { display: inline-block; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; text-transform: uppercase; color: white; }
        .bg-pagado { background-color: #10b981; }
        .bg-parcial { background-color: #f59e0b; }
        .bg-pendiente { background-color: #ef4444; }
        
        .patient-box { background-color: #f8fafc; padding: 15px; border-radius: 8px; margin-bottom: 30px; border: 1px solid #e2e8f0; }
        .patient-box p { margin: 5px 0; }
        
        table.items { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        table.items th { background-color: #1e3a8a; color: white; padding: 10px; text-align: left; font-size: 12px; }
        table.items td { padding: 10px; border-bottom: 1px solid #e2e8f0; font-size: 13px; }
        
        .totals-container { width: 100%; }
        .totals-table { width: 40%; float: right; border-collapse: collapse; }
        .totals-table th { text-align: left; padding: 5px 10px; border-bottom: 1px solid #e2e8f0; color: #64748b; }
        .totals-table td { text-align: right; padding: 5px 10px; border-bottom: 1px solid #e2e8f0; font-weight: bold; }
        .totals-table .grand-total th, .totals-table .grand-total td { font-size: 18px; color: #1e3a8a; border-bottom: 2px solid #1e3a8a; }
        .totals-table .balance th, .totals-table .balance td { color: #ef4444; }
        .clear { clear: both; }

        .payments-box { margin-top: 40px; border-top: 1px solid #e2e8f0; padding-top: 20px; }
        .payments-box h4 { margin-top: 0; color: #475569; }
        table.payments { width: 60%; border-collapse: collapse; font-size: 12px; }
        table.payments th, table.payments td { padding: 6px; border-bottom: 1px dashed #e2e8f0; text-align: left; }

        .footer { position: fixed; bottom: -30px; left: 0; right: 0; text-align: center; font-size: 11px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 10px; }
    </style>
</head>
<body>

    <div class="header">
        <table>
            <tr>
                <td class="clinic-info">
                    <div class="clinic-name">{{ $clinica['nombre'] }}</div>
                    <div>{{ $clinica['direccion'] }}</div>
                    <div>Tel: {{ $clinica['telefono'] }} | Email: {{ $clinica['email'] }}</div>
                </td>
                <td class="invoice-info">
                    <div class="title">DOCUMENTO DE COBRO</div>
                    <div><strong>N° Documento:</strong> {{ $factura->numero }}</div>
                    <div><strong>Fecha Emisión:</strong> {{ date('d/m/Y', strtotime($factura->fecha_emision)) }}</div>
                    <div style="margin-top: 10px;">
                        ESTADO: 
                        @if($factura->estado_pago == 'pagado') <span class="badge bg-pagado">PAGADO</span>
                        @elseif($factura->estado_pago == 'parcial') <span class="badge bg-parcial">ABONO PARCIAL</span>
                        @else <span class="badge bg-pendiente">PENDIENTE</span> @endif
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="patient-box">
        <strong>Datos del Paciente:</strong>
        <p>Nombre: {{ $paciente->nombre }} {{ $paciente->apellido }} (Exp: {{ $paciente->numero_expediente }})</p>
        <p>Teléfono: {{ $paciente->telefono ?? 'N/A' }} | Email: {{ $paciente->email ?? 'N/A' }}</p>
        
        @if($cita)
        <div style="margin-top: 10px; padding-top: 8px; border-top: 1px dashed #cbd5e1;">
            <strong>Cita Vinculada:</strong> {{ date('d/m/Y', strtotime($cita->fecha_cita)) }} a las {{ date('H:i', strtotime($cita->hora_inicio)) }} hrs.
            <p style="color: #64748b; font-size: 12px; margin-top: 3px;">Motivo: {{ $cita->motivo_consulta ?? 'No especificado' }}</p>
        </div>
        @endif
    </div>

    <table class="items">
        <thead>
            <tr>
                <th>CANT.</th>
                <th>DESCRIPCIÓN DEL TRATAMIENTO</th>
                <th style="text-align: right;">PRECIO UNIT.</th>
                <th style="text-align: right;">SUBTOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td>{{ number_format($item->cantidad, 0) }}</td>
                @php
                    // Limpiamos el texto usando tu lógica: quitamos la lista de dientes solo para el PDF
                    $nombreLimpio = preg_replace('/\s*\(Diente:.*?\)/', '', $item->descripcion);
                    $nombreLimpio = preg_replace('/\s*\([\d,\s]+\)/', '', $nombreLimpio); // Por si lee las facturas más viejitas
                @endphp
                <td>{{ $nombreLimpio }}</td>
                <td style="text-align: right;">${{ number_format($item->precio_unitario, 2) }}</td>
                <td style="text-align: right;">${{ number_format($item->total_item, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals-container">
        <table class="totals-table">
            <tr>
                <th>Subtotal:</th>
                <td>${{ number_format($factura->subtotal, 2) }}</td>
            </tr>
            @if($factura->descuento > 0)
            <tr>
                <th>Descuento Aplicado:</th>
                <td style="color: #10b981;">-${{ number_format($factura->descuento, 2) }}</td>
            </tr>
            @endif
            <tr class="grand-total">
                <th>TOTAL:</th>
                <td>${{ number_format($factura->total, 2) }}</td>
            </tr>
            <tr>
                <th>Abonado hasta la fecha:</th>
                <td>${{ number_format($total_pagado, 2) }}</td>
            </tr>
            <tr class="balance">
                <th>SALDO PENDIENTE:</th>
                <td>${{ number_format($factura->saldo_pendiente, 2) }}</td>
            </tr>
        </table>
        <div class="clear"></div>
    </div>

    @if(count($pagos) > 0)
    <div class="payments-box">
        <h4>Registro de Abonos Recibidos</h4>
        <table class="payments">
            <tr>
                <th>Fecha</th>
                <th>Método de Pago</th>
                <th>Monto Abonado</th>
            </tr>
            @foreach($pagos as $pago)
            <tr>
                <td>{{ date('d/m/Y H:i', strtotime($pago->fecha_pago)) }}</td>
                <td style="text-transform: capitalize;">{{ str_replace('_', ' ', $pago->metodo_pago) }}</td>
                <td>${{ number_format($pago->monto, 2) }}</td>
            </tr>
            @endforeach
        </table>
    </div>
    @endif

    <div class="footer">
        Este documento es un comprobante de servicio clínico interno. Gracias por confiar su salud dental con nosotros.
    </div>

</body>
</html>