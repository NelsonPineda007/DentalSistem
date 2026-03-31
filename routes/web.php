<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\TratamientoController;
use App\Http\Controllers\ExpedienteController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\CalendarioController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\PerfilController;

// ==========================================
// 1. RUTAS PÚBLICAS Y AUTENTICACIÓN
// ==========================================

// Ruta raíz: Si está logueado va al dashboard, si no, va al login
Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/dashboard');
    }
    return view('login');
})->name('login'); 

// Procesar el login y logout
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ==========================================
// 2. RECUPERACIÓN DE CONTRASEÑA
// ==========================================
Route::get('/olvide-mi-password', [PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/olvide-mi-password', [PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/resetear-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/resetear-password', [PasswordResetController::class, 'reset'])->name('password.update');

// ==========================================
// 3. RUTAS DE INSTALACIÓN / SETUP (Solo usar una vez para crear los usuarios por defecto)
// ==========================================
Route::get('/crear-admin-secreto', function () {
    $rolId = DB::table('roles')->where('nombre', 'Admin')->value('id');
    if (!$rolId) {
        $rolId = DB::table('roles')->insertGetId(['nombre' => 'Admin', 'estado' => 'Activo']);
    }
    $existe = User::where('email', 'jnlopezjr36@gmail.com')->first();
    if ($existe) return "El usuario ya existe.";
    User::create([
        'usuario' => 'Jaime Nelson', 'nombre' => 'Jaime Nelson', 'apellido' => 'Lopez Salinas',
        'email' => 'jnlopezjr36@gmail.com', 'password_hash' => Hash::make('12345678'), 
        'rol_id' => $rolId, 'estado' => 'Activo'
    ]);
    return "✅ Administrador creado.";
});

Route::get('/crear-recepcionista', function () {
    $rolId = DB::table('roles')->where('nombre', 'Recepcionista')->value('id');
    if (!$rolId) {
        $rolId = DB::table('roles')->insertGetId(['nombre' => 'Recepcionista', 'estado' => 'Activo']);
    }
    $existe = User::where('email', 'recepcion@dentalsistem.com')->first();
    if ($existe) return "El usuario ya existe.";
    User::create([
        'usuario' => 'recepcion_01', 'nombre' => 'Ana', 'apellido' => 'García',
        'email' => 'recepcion@dentalsistem.com', 'password_hash' => Hash::make('12345678'), 
        'rol_id' => $rolId, 'estado' => 'Activo'
    ]);
    return "✅ Recepcionista creada.";
});

Route::get('/crear-dentista', function () {
    $rolId = DB::table('roles')->where('nombre', 'Dentista')->value('id');
    if (!$rolId) {
        $rolId = DB::table('roles')->insertGetId(['nombre' => 'Dentista', 'estado' => 'Activo']);
    }
    $existe = User::where('email', 'doctor@dentalsistem.com')->first();
    if ($existe) return "El usuario Dentista ya existe.";
    User::create([
        'usuario' => 'dr_prueba', 
        'nombre' => 'Carlos', 
        'apellido' => 'Méndez',
        'email' => 'doctor@dentalsistem.com', 
        'password_hash' => Hash::make('12345678'), 
        'especialidad' => 'Ortodoncia General',
        'rol_id' => $rolId, 
        'estado' => 'Activo'
    ]);
    return "✅ Dentista creado. (Email: doctor@dentalsistem.com / Clave: 12345678)";
});


// ==========================================
// 4. RUTAS PROTEGIDAS (Solo accesibles si estás logueado)
// ==========================================
Route::middleware(['auth'])->group(function () {

    // --- DASHBOARD ---
    // Solo Administradores y Dentistas pueden ver el Dashboard
    Route::middleware(['role:Admin,Dentista'])->group(function () {
        Route::get('/dashboard', function () { return view('dashboard'); });
        Route::get('/api/dashboard', [DashboardController::class, 'obtenerDatos']);
    });

    // --- VISTAS DEL SISTEMA (Blade) ---
    Route::get('/pacientes', function () { return view('pacientes'); });
    Route::get('/citas', function () { return view('citas'); });
    Route::get('/calendar', function () { return view('calendar'); });
    Route::get('/expediente', function () { return view('expediente'); });
    Route::get('/tratamiento', function () { return view('tratamiento'); });
    Route::get('/perfil', function () { return view('perfil'); });
    Route::get('/notificaciones', function () { return view('notificaciones'); });
    
    // --- API: PERFIL DE USUARIO ---
    Route::get('/api/perfil/datos', [PerfilController::class, 'obtenerDatos']);
    Route::put('/api/perfil/actualizar', [PerfilController::class, 'actualizarPerfil']);
    Route::post('/api/perfil/solicitar-password', [PerfilController::class, 'solicitarCambioPassword']);

    // --- API: NOTIFICACIONES ---
    Route::get('/api/notificaciones/datos', [NotificacionController::class, 'obtenerDatos']);

    // --- API: PACIENTES ---
    Route::get('/api/obtener-pacientes', [PacienteController::class, 'obtenerTodos']);
    Route::get('/api/pacientes/{id}', [PacienteController::class, 'obtenerUno']);
    Route::post('/api/guardar-paciente', [PacienteController::class, 'guardar']);
    Route::put('/api/pacientes/{id}', [PacienteController::class, 'actualizar']);
    Route::delete('/api/pacientes/{id}', [PacienteController::class, 'eliminar']);
    Route::get('/api/pacientes/{id}/pdf', [PacienteController::class, 'imprimirExpediente']); // Imprimir PDF del paciente

    // --- API: TRATAMIENTOS (Catálogo) ---
    Route::get('/api/categorias-tratamientos', [TratamientoController::class, 'obtenerCategorias']);
    Route::get('/api/obtener-tratamientos', [TratamientoController::class, 'obtenerTodos']);
    Route::post('/api/guardar-tratamiento', [TratamientoController::class, 'guardar']);
    Route::put('/api/tratamientos/{id}', [TratamientoController::class, 'actualizar']);
    Route::delete('/api/tratamientos/{id}', [TratamientoController::class, 'eliminar']);

    // --- API: EXPEDIENTE CLÍNICO Y FACTURACIÓN ---
    Route::post('/api/expediente/{paciente_id}/guardar', [ExpedienteController::class, 'guardarFicha']); // Guardar Ficha (Alergias, etc)
    Route::get('/api/expediente/{paciente_id}', [ExpedienteController::class, 'obtenerFicha']); // Traer la ficha guardada
    Route::get('/api/expediente/{paciente_id}/ficha/pdf', [ExpedienteController::class, 'imprimirFicha']); // PDF Ficha clínica
    
    // Facturación dentro del expediente
    Route::get('/api/expediente/{paciente_id}/facturas', [ExpedienteController::class, 'obtenerFacturas']);
    Route::post('/api/expediente/{paciente_id}/facturas', [ExpedienteController::class, 'guardarFactura']);
    Route::post('/api/expediente/facturas/{factura_id}/abonar', [ExpedienteController::class, 'abonarFactura']);
    Route::get('/api/expediente/facturas/{factura_id}/pdf', [ExpedienteController::class, 'imprimirFactura']); // PDF Recibo
    Route::get('/api/expediente/facturas/detalle/{id}', [ExpedienteController::class, 'obtenerDetalleFactura']);
    Route::put('/api/expediente/facturas/{id}', [ExpedienteController::class, 'actualizarFactura']);

    // --- API: CITAS ---
    Route::get('/api/citas/datos-formulario', [CitaController::class, 'obtenerDatosFormulario']); // Llena el select de doctores y pacientes
    Route::get('/api/citas', [CitaController::class, 'obtenerCitas']); // Lista global de citas
    Route::post('/api/citas', [CitaController::class, 'guardarCita']);
    Route::put('/api/citas/{id}', [CitaController::class, 'actualizarCita']); 
    Route::delete('/api/citas/{id}', [CitaController::class, 'eliminarCita']);
    Route::get('/api/pacientes/{id}/citas', [CitaController::class, 'obtenerCitasPaciente']); // Citas de 1 solo paciente
    Route::post('/citas/{citaId}/iniciar-consulta', [ExpedienteController::class, 'iniciarConsultaDesdeCita']); // Botón verde "Iniciar Consulta"

    // --- API: CALENDARIO (Notas y Recordatorios) ---
    Route::get('/api/calendario', [CalendarioController::class, 'obtenerEventos']);
    Route::post('/api/calendario', [CalendarioController::class, 'guardarEvento']);
    Route::put('/api/calendario/{id}', [CalendarioController::class, 'actualizarEvento']); 
    Route::delete('/api/calendario/{id}', [CalendarioController::class, 'eliminarEvento']);

});