<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Movimiento extends Model
{
    protected $table = 'movimientos';

    protected $fillable = [
        'categoria_id',
        'deuda_id',
        'cuenta_id',
        'transferencia_id',
        'fecha',
        'tipo',
        'descripcion',
        'monto',
        'mes_pago_tdc'
    ];

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    public function deuda(): BelongsTo
    {
        return $this->belongsTo(Deuda::class);
    }

    public function cuenta()
    {
        return $this->belongsTo(Cuenta::class);
    }
}