<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cria a tabela de históricos de interações.
     *
     * Esta tabela armazena o histórico de interações com clientes e leads,
     * permitindo acompanhar a evolução do relacionamento comercial.
     */
    public function up(): void
    {
        Schema::create('historicos', function (Blueprint $table) {
            // Identificador único
            $table->id()->comment('Identificador único do histórico');

            // Relacionamento polimórfico (pode ser associado a Lead ou Cliente)
            $table->morphs('historicable');

            // Relacionamento com usuário que registrou o histórico
            $table->foreignId('user_id')
                ->comment('Usuário que registrou o histórico')
                ->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            // Campos do registro de histórico
            $table->string('tipo')
                ->comment('Tipo de contato: telefone, email, whatsapp, presencial, videoconferencia, outro');
            $table->text('texto')
                ->comment('Descrição detalhada da interação');
            $table->dateTime('data')
                ->comment('Data e hora da interação');

            // Campos de planejamento
            $table->text('proxima_acao')->nullable()
                ->comment('Descrição da próxima ação planejada');
            $table->dateTime('data_proxima_acao')->nullable()
                ->comment('Data e hora programada para a próxima ação');
            $table->text('retorno')->nullable()
                ->comment('Descrição do retorno esperado');
            $table->dateTime('data_retorno')->nullable()
                ->comment('Data e hora esperada para retorno do cliente');
            $table->boolean('ativar_lembrete')->default(false)
                ->comment('Indica se deve acionar lembretes para esta ação');

            // Anexos e documentos
            $table->string('anexo')->nullable()
                ->comment('Caminho para o arquivo anexado');

            // Controle de timestamps e soft delete
            $table->timestamps();
            $table->softDeletes();

            // Índices para otimização de consultas frequentes
            $table->index('data');
            $table->index('data_proxima_acao');
            $table->index(['user_id', 'historicable_id', 'historicable_type']);
        });
    }

    /**
     * Reverte a criação da tabela de históricos.
     */
    public function down(): void
    {
        Schema::dropIfExists('historicos');
    }
};
