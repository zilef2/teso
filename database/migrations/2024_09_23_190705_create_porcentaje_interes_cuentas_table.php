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
        Schema::create('porcentaje_interes_cuentas', function (Blueprint $table) {
            $table->id();
            $table->Datetime('Mes');
            $table->decimal('valor', 4);
            $table->unsignedBigInteger('cuenta_id');
            $table->foreign('cuenta_id')
                ->references('id')
                ->on('cuentas')
                ->onDelete('restrict'); //cascade | set null
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('porcentaje_interes_cuentas');
    }
};
