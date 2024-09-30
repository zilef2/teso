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
        Schema::create('transaccions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->softDeletes();

            $table->string('codigo_cuenta_contable')->nullable();
            $table->string('nombre_cuenta')->nullable();
            $table->string('codigo')->nullable();
            $table->integer('documento')->nullable();
            $table->date('fecha_elaboracion')->nullable();
            $table->text('descripcion')->nullable();
            $table->string('comprobante')->nullable();
            $table->decimal('valor_debito', 15, 2)->nullable();
            $table->decimal('valor_credito', 15, 2)->nullable();
            $table->string('nit')->nullable(); //1. ´´
            $table->string('nombre')->nullable(); //1 . dif entre nombre y nombre_empresa
            $table->string('cod_costos')->nullable();
            $table->string('desc_costos')->nullable();
            $table->string('codigo_interno_cuenta')->nullable();
            $table->string('codigo_tercero')->nullable();
            $table->string('ccostos')->nullable();
            $table->decimal('saldo_inicial', 15)->nullable();
            $table->decimal('saldo_final', 15)->nullable();
            $table->string('nombre_empresa')->nullable();
            $table->string('nit_empresa')->nullable();
            $table->string('documento_ref')->nullable();
            $table->integer('consecutivo')->nullable();
            $table->string('periodo')->nullable();
            $table->string('plan_cuentas')->nullable();

            $table->string('contrapartida_CI')->nullable();
            $table->string('concepto_flujo_homologación')->nullable();
            $table->string('n_contrapartidas')->nullable();


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaccions');
    }
};
