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

// ==========================================
// PACIENTES
// ==========================================
use App\Http\Controllers\PacienteController;
Route::get('/api/obtener-pacientes', [PacienteController::class, 'obtenerTodos']);
Route::get('/api/pacientes/{id}', [PacienteController::class, 'obtenerUno']);
Route::post('/api/guardar-paciente', [PacienteController::class, 'guardar']);
Route::put('/api/pacientes/{id}', [PacienteController::class, 'actualizar']);
Route::delete('/api/pacientes/{id}', [PacienteController::class, 'eliminar']);


// ==========================================
// TRATAMIENTOS
// ==========================================
use App\Http\Controllers\TratamientoController;
Route::get('/api/categorias-tratamientos', [TratamientoController::class, 'obtenerCategorias']);
Route::get('/api/obtener-tratamientos', [TratamientoController::class, 'obtenerTodos']);
Route::post('/api/guardar-tratamiento', [TratamientoController::class, 'guardar']);
Route::put('/api/tratamientos/{id}', [TratamientoController::class, 'actualizar']);
Route::delete('/api/tratamientos/{id}', [TratamientoController::class, 'eliminar']);


// ==========================================
// CITAS
// ==========================================
use App\Http\Controllers\CitaController; 
Route::get('/api/citas/datos-formulario', [CitaController::class, 'obtenerDatosFormulario']);
Route::get('/api/citas', [CitaController::class, 'obtenerCitas']);
Route::post('/api/citas', [CitaController::class, 'guardarCita']);

Route::put('/api/citas/{id}', [CitaController::class, 'actualizarCita']); 
Route::delete('/api/citas/{id}', [CitaController::class, 'eliminarCita']);