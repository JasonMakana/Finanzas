<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deudas', function (Blueprint $table) {
            // Agregamos el día del mes en que se debe pagar (ej. 15)
            $table->integer('dia_pago')->nullable()->after('monto_sugerido');
        });
    }

    public function down(): void
    {
        Schema::table('deudas', function (Blueprint $table) {
            $table->dropColumn('dia_pago');
        });
    }
};