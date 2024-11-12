<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
* php artisan migrate --path=database/migrations/2024_11_12_create_users_table.php
* php artisan migrate --path=database/migrations/2024_11_08_192827_create_cesinafectacions_table.php
 */
    public function up(): void
    {
        Schema::create('cesinafectacions', function (Blueprint $table) {
            $table->id();
            $table->string('valor_debito');
            $table->string('valor_credito');
            $table->string('codigo_cuenta');
            $table->string('codigo_asiento');
            $table->string('tipo');
            $table->string('codigo');
            $table->string('fecha_elaboracion');
            $table->string('consecutivo');
            $table->string('descripcion',1200);
            $table->string('descripcion_concepto',1200);
            $table->string('codigo_banco');
            $table->string('otros');
            $table->string('taquilla');
            $table->string('consecutivo2');
            $table->string('nombre_empresa');
            $table->string('nombre_dependencia');
            $table->string('descripcion2', 1200);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cesinafectacions');
    }
};
