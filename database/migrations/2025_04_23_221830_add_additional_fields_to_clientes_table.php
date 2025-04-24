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
            // Adicionando novos campos para armazenar dados adicionais da API CNPJa
            if (!Schema::hasColumn('clientes', 'cnaes_secundarios')) {
                $table->json('cnaes_secundarios')->nullable();
            }

            if (!Schema::hasColumn('clientes', 'idade_socio')) {
                $table->string('idade_socio')->nullable();
            }

            if (!Schema::hasColumn('clientes', 'lista_socios')) {
                $table->json('lista_socios')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn(['cnaes_secundarios', 'idade_socio', 'lista_socios']);
        });
    }
};
