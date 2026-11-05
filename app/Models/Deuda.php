<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasMany;
use Illuminate\Database\Eloquent\Model;

class Deuda extends Model
{
    protected $table = 'deudas';

    protected $fillable = [
        'nombre',
        'monto_inicial',
        'saldo_restante',
        'monto_sugerido',
        'dia_pago',
        'fecha_limite',
        'oculta'
    ];

    protected $casts = [
        'oculta' => 'boolean',
    ];

    public function movimientos(): HasMany
    {
        return $this->hasMany(Movimiento::class);
    }
}