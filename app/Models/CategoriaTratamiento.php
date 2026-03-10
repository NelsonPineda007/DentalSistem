<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriaTratamiento extends Model
{
    protected $table = 'categorias_tratamientos';
    
    // Le decimos a Laravel que aquí se llaman diferente los timestamps
    const CREATED_AT = 'creado_en';
    const UPDATED_AT = 'actualizado_en';
}