<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('asientos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_cuenta');
            $table->string('nombre_cuenta');
            $table->string('codigo');
            $table->string('documento');
            $table->string('fecha_elaboracion');
            $table->string('descripcion',1024)->nullable();
            $table->string('comprobante');
            $table->decimal('valor_debito', 15)->nullable();
            $table->decimal('valor_credito', 15)->nullable();
            $table->string('nit');
            $table->string('nombre');
            $table->string('cod_costos');
            $table->string('desc_costos');
            $table->string('codigo_interno_cuenta');
            $table->string('codigo_tercero');
            $table->string('ccostos');
            $table->string('saldo_inicial');
            $table->string('saldo_final');
            $table->string('nombre_empresa');
            $table->string('nit_empresa');
            $table->string('documento_ref');
            $table->string('consecutivo');
            $table->string('periodo');
            $table->string('plan_cuentas');
            $table->integer('numerounico')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asientos');
    }
};
