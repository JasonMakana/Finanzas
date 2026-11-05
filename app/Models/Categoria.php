<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasMany;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    // Obligamos a Laravel a buscar la tabla con "s"
    protected $table = 'categorias';

    protected $fillable = [
        'nombre',
        'tipo'
    ];

    public function movimientos(): HasMany
    {
        return $this->hasMany(Movimiento::class);
    }
}