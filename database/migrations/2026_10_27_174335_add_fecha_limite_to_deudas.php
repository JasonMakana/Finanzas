<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deudas', function (Blueprint $table) {
            $table->date('fecha_limite')->nullable()->after('dia_pago');
        });
    }

    public function down(): void
    {
        Schema::table('deudas', function (Blueprint $table) {
            $table->dropColumn('fecha_limite');
        });
    }
};