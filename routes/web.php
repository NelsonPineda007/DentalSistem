<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\TratamientoController;
use App\Http\Controllers\CitaController; 
use App\Http\Controllers\ExpedienteController;

// ==========================================
// RUTAS DE VISTAS (NAVEGACIÓN)
// ==========================================
Route::get('/', function () {
    return view('login');
});
Route::get('/dashboard', function () {
    return view('dashboard');
});
Route::get('/pacientes', function () {
    return view('pacientes');
});
Route::get('/citas', function () {
    return view('citas');
});
Route::get('/calendar', function () {
    return view('calendar');
});
Route::get('/expediente', function () {
    return view('expediente'); 
});
Route::get('/tratamiento', function () {
    return view('tratamiento');
});
Route::get('/perfil', function () {
    return view('perfil');
});

// ==========================================
// API: PACIENTES
// ==========================================
Route::get('/api/obtener-pacientes', [PacienteController::class, 'obtenerTodos']);
Route::get('/api/pacientes/{id}', [PacienteController::class, 'obtenerUno']);
Route::post('/api/guardar-paciente', [PacienteController::class, 'guardar']);
Route::put('/api/pacientes/{id}', [PacienteController::class, 'actualizar']);
Route::delete('/api/pacientes/{id}', [PacienteController::class, 'eliminar']);

// ==========================================
// API: TRATAMIENTOS
// ==========================================
Route::get('/api/categorias-tratamientos', [TratamientoController::class, 'obtenerCategorias']);
Route::get('/api/obtener-tratamientos', [TratamientoController::class, 'obtenerTodos']);
Route::post('/api/guardar-tratamiento', [TratamientoController::class, 'guardar']);
Route::put('/api/tratamientos/{id}', [TratamientoController::class, 'actualizar']);
Route::delete('/api/tratamientos/{id}', [TratamientoController::class, 'eliminar']);

// ==========================================
// API: CITAS
// ==========================================
Route::get('/api/citas/datos-formulario', [CitaController::class, 'obtenerDatosFormulario']);
Route::get('/api/citas', [CitaController::class, 'obtenerCitas']);
Route::post('/api/citas', [CitaController::class, 'guardarCita']);
Route::put('/api/citas/{id}', [CitaController::class, 'actualizarCita']); 
Route::delete('/api/citas/{id}', [CitaController::class, 'eliminarCita']);

// ==========================================
// API: EXPEDIENTE
// ==========================================
Route::post('/api/expediente/{paciente_id}/guardar', [ExpedienteController::class, 'guardarFicha']);
Route::get('/api/expediente/{paciente_id}', [ExpedienteController::class, 'obtenerFicha']);