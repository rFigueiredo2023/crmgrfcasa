<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cria a tabela de segmentos para categorização de clientes.
     *
     * Esta tabela armazena os diferentes segmentos de mercado dos clientes,
     * permitindo agrupar e filtrar clientes por área de atuação.
     */
    public function up(): void
    {
        Schema::create('segmentos', function (Blueprint $table) {
            // Identificador único
            $table->id()->comment('Identificador único do segmento');

            // Campos principais
            $table->string('nome')->unique()
                ->comment('Nome do segmento de mercado (ex: Varejo, Indústria, Serviços)');

            // Controle de datas de criação e atualização
            $table->timestamps();

            // Índices
            $table->index('nome');
        });
    }

    /**
     * Reverte a criação da tabela de segmentos.
     */
    public function down(): void
    {
        Schema::dropIfExists('segmentos');
    }
};
