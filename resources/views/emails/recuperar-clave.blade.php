<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f8fafc;
            color: #334155;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }
        .wrapper {
            width: 100%;
            background-color: #f8fafc;
            padding: 40px 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }
        .header {
            background-color: #1e40af;
            padding: 35px 20px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 26px;
            letter-spacing: 2px;
            font-weight: 700;
        }
        .body {
            padding: 40px 35px;
        }
        .body p {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 24px;
            color: #475569;
        }
        .btn-container {
            text-align: center;
            margin: 35px 0;
        }
        .btn {
            background-color: #1e40af;
            color: #ffffff !important;
            text-decoration: none;
            padding: 16px 32px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            display: inline-block;
            box-shadow: 0 4px 6px rgba(30, 64, 175, 0.2);
        }
        .warning-text {
            font-size: 14px;
            color: #64748b;
        }
        .footer {
            background-color: #f1f5f9;
            padding: 24px;
            text-align: center;
            font-size: 13px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
        }
        .url-raw {
            font-size: 13px;
            color: #94a3b8;
            word-break: break-all;
            background-color: #f8fafc;
            padding: 15px;
            border-radius: 6px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <h1>DENTISTA</h1>
            </div>
            <div class="body">
                <p>Hola, <strong>{{ $usuario ?? 'Usuario' }}</strong>:</p>
                <p>Hemos recibido una solicitud para restablecer la contraseña de tu cuenta en el sistema clínico. Si fuiste tú, puedes configurar una nueva clave haciendo clic en el siguiente botón:</p>
                
                <div class="btn-container">
                    <a href="{{ $url }}" class="btn">Restablecer Contraseña</a>
                </div>
                
                <p class="warning-text">Este enlace de recuperación expirará por seguridad en 60 minutos.</p>
                <p class="warning-text">Si no solicitaste este cambio, no es necesario realizar ninguna acción y tu contraseña seguirá siendo la misma.</p>
                
                <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 35px 0;">
                
                <p class="url-raw">Si tienes problemas haciendo clic en el botón, copia y pega esta URL en tu navegador web:<br><br>
                <a href="{{ $url }}" style="color: #1e40af;">{{ $url }}</a></p>
            </div>
            <div class="footer">
                &copy; {{ date('Y') }} Sistema Dental. Todos los derechos reservados.<br>
                Este es un mensaje automático, por favor no respondas a este correo.
            </div>
        </div>
    </div>
</body>
</html>