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
            $table->string('numero_documento', 512);
            $table->string('numero_cheque', 512);
            $table->dateTime('fecha_elaboracion');
            $table->string('consecutivo', 512);
            $table->string('codigo_cuenta', 512);
            $table->string('nombre_cuenta', 512);
            $table->string('ccostos', 512);
            $table->string('nit', 512);
            $table->string('nombre', 512);
            $table->decimal('valor_debito', 15, 2);
            $table->decimal('valor_credito', 15, 2);
            $table->string('codigo_asiento', 512);
            $table->string('documento_ref', 512);
            $table->string('plan_cuentas', 512);
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
