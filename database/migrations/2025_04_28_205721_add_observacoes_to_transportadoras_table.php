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
        Schema::table('transportadoras', function (Blueprint $table) {
            // Adicionar campo de observações após o campo email
            $table->text('observacoes')->nullable()->after('email')->comment('Observações adicionais sobre a transportadora');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transportadoras', function (Blueprint $table) {
            // Remover o campo de observações
            $table->dropColumn('observacoes');
        });
    }
};
