<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalendarioEvento extends Model
{
    use HasFactory;

    protected $table = 'calendario_eventos';
    public $timestamps = false; 

    protected $fillable = [
        'titulo', 'fecha', 'hora', 'detalles', 'color', 'tipo', 'estado'
    ];
}