<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adiciona campos complementares à tabela clientes.
     *
     * Esta migration adiciona:
     * - telefone2: campo para telefone alternativo
     * - site: endereço do site do cliente
     * - segmento_id: relacionamento com a tabela de segmentos
     */
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            // Telefone alternativo
            $table->string('telefone2')->nullable()
                ->comment('Telefone alternativo ou secundário');

            // Site da empresa
            $table->string('site')->nullable()
                ->comment('URL do site da empresa');

            // Relação com segmento (setor de atuação)
            $table->foreignId('segmento_id')->nullable()
                ->comment('Identificador do segmento de mercado')
                ->constrained('segmentos')
                ->onUpdate('cascade')
                ->onDelete('set null');

            // Índice para melhorar performance em buscas por segmento
            $table->index('segmento_id');
        });
    }

    /**
     * Reverte as alterações, removendo os campos adicionados.
     */
    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            // Remove a constraint de chave estrangeira
            $table->dropConstrainedForeignId('segmento_id');

            // Remove os demais campos
            $table->dropColumn(['telefone2', 'site']);
        });
    }
};
