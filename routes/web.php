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
    return view('expediente'); 
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
//EXPEDIENTE PDF PACIENTES
Route::get('/api/pacientes/{id}/pdf', [PacienteController::class, 'imprimirExpediente']);

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
// EXPEDIENTE Y RECIBOS (FACTURACIÓN)
// ==========================================
use App\Http\Controllers\ExpedienteController;
Route::post('/api/expediente/{paciente_id}/guardar', [ExpedienteController::class, 'guardarFicha']);
Route::get('/api/expediente/{paciente_id}', [ExpedienteController::class, 'obtenerFicha']); 

// Rutas Originales
Route::get('/api/expediente/{paciente_id}/facturas', [ExpedienteController::class, 'obtenerFacturas']);
Route::post('/api/expediente/{paciente_id}/facturas', [ExpedienteController::class, 'guardarFactura']);
Route::post('/api/expediente/facturas/{factura_id}/abonar', [ExpedienteController::class, 'abonarFactura']);
Route::get('/api/expediente/facturas/{factura_id}/pdf', [ExpedienteController::class, 'imprimirFactura']);
Route::post('/citas/{citaId}/iniciar-consulta', [ExpedienteController::class, 'iniciarConsultaDesdeCita']);

// Nuevas Rutas para Editar Recibos
Route::get('/api/expediente/facturas/detalle/{id}', [ExpedienteController::class, 'obtenerDetalleFactura']);
Route::put('/api/expediente/facturas/{id}', [ExpedienteController::class, 'actualizarFactura']);

// ==========================================
// CITAS
// ==========================================
use App\Http\Controllers\CitaController; 
Route::get('/api/citas/datos-formulario', [CitaController::class, 'obtenerDatosFormulario']);
Route::get('/api/citas', [CitaController::class, 'obtenerCitas']);
Route::post('/api/citas', [CitaController::class, 'guardarCita']);
Route::put('/api/citas/{id}', [CitaController::class, 'actualizarCita']); 
Route::delete('/api/citas/{id}', [CitaController::class, 'eliminarCita']);
Route::get('/api/pacientes/{id}/citas', [CitaController::class, 'obtenerCitasPaciente']);

// ==========================================
// CALENDARIO
// ==========================================
use App\Http\Controllers\CalendarioController; 
Route::get('/api/calendario', [CalendarioController::class, 'obtenerEventos']);
Route::post('/api/calendario', [CalendarioController::class, 'guardarEvento']);
Route::put('/api/calendario/{id}', [CalendarioController::class, 'actualizarEvento']); 
Route::delete('/api/calendario/{id}', [CalendarioController::class, 'eliminarEvento']);

// ==========================================
// DASHBOARD
// ==========================================
use App\Http\Controllers\DashboardController; 
Route::get('/api/dashboard', [DashboardController::class, 'obtenerDatos']);