<?php

use Illuminate\Support\Facades\Route;

// Pantalla de Login
Route::get('/', function () {
    return view('login');
});

// Dashboard Principal
Route::get('/dashboard', function () {
    return view('dashboard');
});

// Gestión de Pacientes
Route::get('/pacientes', function () {
    return view('pacientes');
});

// RUTAS DE CITAS
Route::get('/citas', function () {
    return view('citas');
});

Route::get('/calendar', function () {
    return view('calendar');
});

Route::get('/expediente', function () {
    return view('expediente'); // Carga resources/views/expediente.blade.php
});

// Gestión de Catálogo de Tratamientos
Route::get('/tratamiento', function () {
    return view('tratamiento');
});

// Perfil del usuario
Route::get('/perfil', function () {
    return view('perfil');
});

use App\Http\Controllers\PacienteController;

// Rutas que ya tenías para leer:
Route::get('/api/obtener-pacientes', [PacienteController::class, 'obtenerTodos']);
Route::get('/api/pacientes/{id}', [PacienteController::class, 'obtenerUno']);

//guardar
Route::post('/api/guardar-paciente', [PacienteController::class, 'guardar']);
//actualizar
Route::put('/api/pacientes/{id}', [PacienteController::class, 'actualizar']);