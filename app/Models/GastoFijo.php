<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GastoFijo extends Model
{
    protected $table = 'gasto_fijos';

    protected $fillable = [
        'nombre',
        'dia_limite',
        'monto',
        'estado'
    ];
}