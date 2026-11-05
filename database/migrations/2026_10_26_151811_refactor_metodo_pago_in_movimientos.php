<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Agregar la nueva columna foránea
        Schema::table('movimientos', function (Blueprint $table) {
            $table->foreignId('cuenta_id')->nullable()->after('categoria_id')->constrained('cuentas');
        });

        // 2. Crear las 3 cuentas base automáticamente
        $efectivoId = DB::table('cuentas')->insertGetId([
            'nombre' => 'Efectivo Físico', 'tipo' => 'efectivo', 'created_at' => now(), 'updated_at' => now()
        ]);
        $debitoId = DB::table('cuentas')->insertGetId([
            'nombre' => 'Cuenta Débito', 'tipo' => 'debito', 'created_at' => now(), 'updated_at' => now()
        ]);
        $tdcId = DB::table('cuentas')->insertGetId([
            'nombre' => 'TDC BBVA', 'tipo' => 'credito', 'limite_credito' => 20000.00, 'created_at' => now(), 'updated_at' => now()
        ]);

        // 3. Transferir el historial existente a las nuevas cuentas
        DB::table('movimientos')->where('metodo_pago', 'Efectivo')->update(['cuenta_id' => $efectivoId]);
        DB::table('movimientos')->where('metodo_pago', 'Débito')->update(['cuenta_id' => $debitoId]);
        DB::table('movimientos')->where('metodo_pago', 'TDC BBVA')->update(['cuenta_id' => $tdcId]);

        // 4. Eliminar la columna vieja
        Schema::table('movimientos', function (Blueprint $table) {
            $table->dropColumn('metodo_pago');
        });
    }

    public function down(): void
    {
        Schema::table('movimientos', function (Blueprint $table) {
            $table->string('metodo_pago')->nullable();
        });
        Schema::table('movimientos', function (Blueprint $table) {
            $table->dropForeign(['cuenta_id']);
            $table->dropColumn('cuenta_id');
        });
    }
};