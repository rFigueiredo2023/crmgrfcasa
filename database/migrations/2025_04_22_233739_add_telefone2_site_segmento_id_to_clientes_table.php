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
        Schema::table('clientes', function (Blueprint $table) {
            $table->string('telefone2')->nullable();
            $table->string('site')->nullable();
            $table->unsignedBigInteger('segmento_id')->nullable();

            $table->foreign('segmento_id')->references('id')->on('segmentos')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropForeign(['segmento_id']);
            $table->dropColumn(['telefone2', 'site', 'segmento_id']);
        });
    }
};
