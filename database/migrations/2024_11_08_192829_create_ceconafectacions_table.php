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
        Schema::create('ceconafectacions', function (Blueprint $table) {
            $table->id();

            $table->integer('consecutivo');
            $table->integer('no_op');
            $table->string('numero_cheque');
            $table->string('valor_egreso');
            $table->string('valor_total');
            $table->string('nombre');
            $table->string('numero_cuenta');
            $table->string('codigo_resumido');
            $table->string('nombre_proyecto');
            $table->string('nit');
            $table->string('nombre2');
            $table->string('saldo_rubro');
            $table->string('rubro');
            $table->string('nombre_empresa');
            $table->string('nombre_dependencia');
            $table->date('fecha_elaboracion');
            $table->string('estado');
            $table->string('descripcion',1200);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ceconafectacions');
    }
};
