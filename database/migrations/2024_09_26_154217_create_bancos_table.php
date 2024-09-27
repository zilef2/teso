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
        Schema::create('bancos', function (Blueprint $table) {
            $table->id();
            
            $table->integer('codigo_cuenta_contable');
            $table->integer('numero_cuenta_bancaria');
            $table->string('banco');
            $table->string('tipo_de_cuenta');
            $table->string('tipo_de_recursos');
            $table->string('convenio');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bancos');
    }
};
