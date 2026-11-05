<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cuentas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); // Ej: BBVA Débito, Efectivo, Nu...
            $table->string('tipo'); // 'efectivo', 'debito', 'credito'
            $table->decimal('limite_credito', 10, 2)->nullable();
            $table->integer('dia_corte')->nullable();
            $table->integer('dia_pago')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuentas');
    }
};