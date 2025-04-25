<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cria a tabela de leads (oportunidades de negócio).
     *
     * Esta tabela armazena potenciais clientes que ainda não foram convertidos,
     * incluindo dados básicos para contato e acompanhamento comercial.
     */
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            // Identificador único
            $table->id()->comment('Identificador único do lead');

            // Dados cadastrais básicos
            $table->string('razao_social')
                ->comment('Nome da empresa ou razão social');
            $table->string('cnpj')->nullable()->unique()
                ->comment('CNPJ da empresa (apenas números)');
            $table->string('inscricao_estadual')->nullable()
                ->comment('Inscrição Estadual');
            $table->string('telefone')
                ->comment('Telefone de contato');
            $table->string('contato')->nullable()
                ->comment('Nome da pessoa de contato');
            $table->string('email')->nullable()
                ->comment('E-mail para contato');
            $table->string('endereco')->nullable()
                ->comment('Endereço completo');
            $table->string('cep')->nullable()
                ->comment('CEP (apenas números)');
            $table->string('municipio')->nullable()
                ->comment('Nome do município');
            $table->string('uf', 2)->nullable()
                ->comment('Sigla do estado (UF)');
            $table->string('codigo_ibge')->nullable()
                ->comment('Código IBGE do município');

            // Campos de acompanhamento
            $table->string('status')->default('novo')
                ->comment('Status do lead: novo, frio, morno, quente, convertido, perdido');
            $table->dateTime('data_proxima_acao')->nullable()
                ->comment('Data programada para próxima ação');
            $table->dateTime('data_retorno')->nullable()
                ->comment('Data prevista para retorno do cliente');
            $table->boolean('ativar_lembrete')->default(false)
                ->comment('Indica se deve acionar lembretes para este lead');
            $table->foreignId('segmento_id')->nullable()
                ->comment('Segmento de mercado do lead')
                ->constrained('segmentos')
                ->onUpdate('cascade')
                ->onDelete('set null');

            // Relacionamento com vendedor responsável
            $table->foreignId('user_id')
                ->comment('Vendedor responsável pelo lead')
                ->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            // Controle de timestamps e soft delete
            $table->timestamps();
            $table->softDeletes();

            // Índices para otimização de consultas frequentes
            $table->index('razao_social');
            $table->index('telefone');
            $table->index('user_id');
            $table->index('status');
            $table->index('data_proxima_acao');
        });
    }

    /**
     * Reverte a criação da tabela leads.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
