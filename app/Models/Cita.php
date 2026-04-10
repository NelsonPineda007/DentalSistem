<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Cita extends Model
{
    use Auditable;
    use HasFactory;
    
    protected $table = 'citas';
    const CREATED_AT = 'creado_en';
    const UPDATED_AT = 'actualizado_en';
    protected $guarded = [];

    // ==========================================
    // RELACIONES PARA EL CENTRO DE NOTIFICACIONES
    // ==========================================

    // 1. Relación: Una Cita pertenece a un Paciente
    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    // 2. Relación: Una Cita pertenece a un Doctor (Empleado/Usuario)
    public function empleado()
    {
        // Revisando el árbol de tus carpetas, veo que tu modelo de personal se llama User
        // Si tienes un modelo llamado Empleado, cambia "User::class" por "Empleado::class"
        return $this->belongsTo(User::class, 'empleado_id');
    }
}