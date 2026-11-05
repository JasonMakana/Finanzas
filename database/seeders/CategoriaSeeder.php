<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categoria;

class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            ['nombre' => 'Sueldo Base', 'tipo' => 'ingreso'],
            ['nombre' => 'Comisiones', 'tipo' => 'ingreso'],
            ['nombre' => 'Transporte', 'tipo' => 'gasto'],
            ['nombre' => 'Suscripciones', 'tipo' => 'gasto'],
            ['nombre' => 'Gimnasio', 'tipo' => 'gasto'],
            ['nombre' => 'Cuidado Personal', 'tipo' => 'gasto'],
            ['nombre' => 'Comida', 'tipo' => 'gasto'],
            ['nombre' => 'Deudas', 'tipo' => 'gasto'],
            ['nombre' => 'Telefonía e Internet', 'tipo' => 'gasto'],
        ];

        foreach ($categorias as $categoria) {
            Categoria::create($categoria);
        }
    }
}