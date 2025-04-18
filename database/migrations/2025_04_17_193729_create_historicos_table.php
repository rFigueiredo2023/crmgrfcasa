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
        Schema::create('historicos', function (Blueprint $table) {
            // Chave primária
            $table->id();

            // Relacionamento polimórfico (Lead ou Cliente)
            $table->morphs('historicable');

            // Relacionamento com usuário
            $table->foreignId('user_id')->constrained('users')->onUpdate('cascade')->onDelete('restrict');

            // Campos do histórico
            $table->string('tipo'); // Ligação, WhatsApp, E-mail, Visita, Reunião, Outro
            $table->text('texto'); // Descrição do atendimento
            $table->text('proxima_acao')->nullable();
            $table->dateTime('data_proxima_acao')->nullable();
            $table->text('retorno')->nullable();
            $table->dateTime('data_retorno')->nullable();
            $table->boolean('ativar_lembrete')->default(false);
            $table->string('anexo')->nullable(); // Caminho para o arquivo

            // Extras
            $table->timestamps();
            $table->softDeletes();

            // Índices adicionais
            $table->index(['user_id', 'historicable_id', 'historicable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historicos');
    }
};
