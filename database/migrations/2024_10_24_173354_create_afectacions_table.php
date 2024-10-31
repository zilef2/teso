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
        Schema::create('afectacions', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->decimal('valor_debito', 15, 2)->nullable();
            $table->decimal('valor_credito', 15, 2)->nullable();
            $table->string('codigo_cuenta');
            $table->string('codigo_asiento');
            $table->string('tipo');
            $table->string('codigo');
            $table->date('fecha_elaboracion');
            $table->string('consecutivo');
            $table->string('descripcion',1024);
            $table->string('descripcion_concepto');
            $table->string('codigo_banco');
            $table->string('otros');
            $table->string('taquilla');
            $table->string('consecutivo2');
            $table->string('nombre_empresa');
            $table->string('nombre_dependencia');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('afectacions');
    }
};
