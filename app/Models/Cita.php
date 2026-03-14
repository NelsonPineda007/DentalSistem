<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    use HasFactory;
    protected $table = 'citas';
    const CREATED_AT = 'creado_en';
    const UPDATED_AT = 'actualizado_en';
    protected $guarded = [];
}