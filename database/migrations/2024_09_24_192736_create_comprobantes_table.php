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
        Schema::create('comprobantes', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 512);
            $table->string('descripcion', 512);
            $table->string('comprobante', 512);
            $table->string('descripcion2', 512);
            $table->string('notas', 512);
            $table->string('numero_documento');
            $table->string('numero_cheque');
            $table->dateTime('fecha_elaboracion');
            $table->string('consecutivo');
            $table->string('codigo_cuenta');
            $table->string('nombre_cuenta');
            $table->string('ccostos');
            $table->string('nit');
            $table->string('nombre');
            $table->decimal('valor_debito', 15, 2);
            $table->decimal('valor_credito', 15, 2);
            $table->string('codigo_asiento');
            $table->string('documento_ref');
            $table->string('plan_cuentas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comprobantes');
    }
};
