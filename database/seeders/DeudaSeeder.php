<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Deuda;

class DeudaSeeder extends Seeder
{
    public function run(): void
    {
        $deudas = [
            [
                'nombre' => 'Liverpool',
                'monto_inicial' => 3500.00,
                'saldo_restante' => 2901.00,
                'monto_sugerido' => 580.00 // Ajusta esta cuota según lo que planees pagar mensual
            ],
            [
                'nombre' => 'Perfumes',
                'monto_inicial' => 3300.00,
                'saldo_restante' => 2100.00,
                'monto_sugerido' => 200.00 // $100 a la semana, como indicaste
            ]
        ];

        foreach ($deudas as $deuda) {
            Deuda::create($deuda);
        }
    }
}