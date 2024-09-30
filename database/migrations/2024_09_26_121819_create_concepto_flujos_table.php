<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('concepto_flujos', function (Blueprint $table) {
            $table->id();
            $table->string('cuenta_contable',);
            $table->string('concepto_flujo',);
            $table->string('ingresos_o_egresos');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('concepto_flujos');
    }
};
