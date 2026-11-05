<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cuenta extends Model
{
    // protected $table = 'cuentas';

    use HasFactory;

    protected $fillable = [
        'nombre',
        'tipo',
        'limite_credito',
        'dia_corte',
        'dia_pago'
    ];

    public function movimientos()
    {
        return $this->hasMany(Movimiento::class);
    }
}