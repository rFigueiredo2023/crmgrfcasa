<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cria a tabela de atendimentos para registro de interações.
     *
     * Esta tabela registra os atendimentos realizados com clientes e leads,
     * incluindo detalhes da interação, próximas ações e acompanhamento.
     *
     * Nota: Esta tabela usa um relacionamento polimórfico semelhante à tabela
     * de históricos, porém com diferente estrutura de campos.
     */
    public function up(): void
    {
        Schema::create('atendimentos', function (Blueprint $table) {
            // Identificador único
            $table->id()->comment('Identificador único do atendimento');

            // Relacionamento polimórfico (pode ser associado a Lead ou Cliente)
            $table->morphs('atendivel');

            // Responsável pelo atendimento
            $table->foreignId('user_id')->nullable()
                ->comment('Usuário responsável pelo atendimento')
                ->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('set null');

            // Campos do atendimento
            $table->string('tipo_contato')
                ->comment('Tipo de contato (telefone, email, whatsapp, presencial, videoconferencia, outro)');

            $table->text('descricao')
                ->comment('Descrição detalhada do atendimento');

            $table->text('retorno')->nullable()
                ->comment('Anotações sobre o retorno esperado');

            $table->dateTime('data_retorno')->nullable()
                ->comment('Data prevista para retorno');

            $table->text('proxima_acao')->nullable()
                ->comment('Descrição da próxima ação planejada');

            $table->dateTime('data_proxima_acao')->nullable()
                ->comment('Data programada para próxima ação');

            $table->dateTime('data_atendimento')->nullable()
                ->comment('Data e hora em que o atendimento foi realizado');

            $table->string('status')->default('aberto')
                ->comment('Status do atendimento: aberto, em_andamento, concluido');

            $table->string('anexo')->nullable()
                ->comment('Caminho para arquivo anexado ao atendimento');

            // Controle de timestamps e soft delete
            $table->timestamps();
            $table->softDeletes();

            // Índices para otimização de consultas frequentes
            $table->index('atendivel_id');
            $table->index('atendivel_type');
            $table->index('user_id');
            $table->index('status');
            $table->index('data_proxima_acao');
            $table->index('created_at');
        });
    }

    /**
     * Reverte a criação da tabela de atendimentos.
     */
    public function down(): void
    {
        Schema::dropIfExists('atendimentos');
    }
};
