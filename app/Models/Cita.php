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
}