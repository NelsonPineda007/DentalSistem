<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DentalSistem Dashboard</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    {{-- Quitamos 'static/' porque el archivo está directo en public/css/nav.css --}}
    <link rel="stylesheet" href="{{ asset('css/nav.css') }}">
    
    {{-- Quitamos 'static/' porque el archivo está directo en public/js/paginacion.js --}}
    <script src="{{ asset('js/paginacion.js') }}" defer></script>

</head>
<body class="bg-gray-100 h-screen flex overflow-hidden">