<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('cuentas')->where('nombre', 'TDC BBVA')->update([
            'dia_corte' => 17,
            'dia_pago' => 7,
            // Asignamos un límite de crédito temporal de $20,000 si está en nulo. Lo puedes editar después.
            'limite_credito' => DB::raw('COALESCE(limite_credito, 20000)') 
        ]);
    }

    public function down(): void
    {
        DB::table('cuentas')->where('nombre', 'TDC BBVA')->update([
            'dia_corte' => null,
            'dia_pago' => null
        ]);
    }
};