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

// Esto permite entrar a /expediente/1, /expediente/2, etc.
Route::get('/expediente/{id}', function ($id) {
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