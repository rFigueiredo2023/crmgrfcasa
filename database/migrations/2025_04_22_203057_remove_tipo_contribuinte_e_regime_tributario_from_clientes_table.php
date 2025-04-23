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
            $table->dropColumn('tipo_contribuinte');
            $table->dropColumn('regime_tributario');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->enum('tipo_contribuinte', ['Contribuinte', 'Não Contribuinte', 'Isento'])->default('Contribuinte')->after('user_id');
            $table->enum('regime_tributario', ['Simples Nacional', 'Lucro Presumido', 'Lucro Real'])->default('Simples Nacional')->after('tipo_contribuinte');
        });
    }
};
