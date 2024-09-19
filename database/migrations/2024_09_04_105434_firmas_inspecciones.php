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
        Schema::create('firmas', function (Blueprint $table) {
            $table->id();
            $table->softDeletes();
            $table->string('nombre')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('restrict'); //cascade | set null
            $table->unsignedBigInteger('inspeccion_id');
                        $table->foreign('inspeccion_id')
                            ->references('id')
                            ->on('inspeccions')
                            ->onDelete('restrict'); //cascade | set null
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
