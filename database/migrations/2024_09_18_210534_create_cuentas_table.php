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
        Schema::create('cuentas', function (Blueprint $table) {
            $table->id();
            $table->softDeletes();
            
            $table->string('codigo_cuenta_contable');
            $table->string('numero_cuenta_bancaria')->nullable();
            $table->string('banco')->nullable();
            $table->string('tipo_de_cuenta')->nullable();
            $table->string('tipo_de_recurso')->nullable();
            $table->string('convenio')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cuentas');
    }
};
