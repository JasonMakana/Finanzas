<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proyeccion extends Model
{
    use HasFactory;

    // Forzamos el nombre de la tabla en español con "s"
    protected $table = 'proyecciones'; 

    protected $fillable = [
        'fecha_inicio_semana',
        'ingreso_estimado'
    ];
}