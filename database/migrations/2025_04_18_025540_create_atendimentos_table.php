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
        Schema::create('atendimentos', function (Blueprint $table) {
            $table->id();
            $table->morphs('atendivel'); // Relacionamento polimórfico com leads e clientes
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('tipo_contato');
            $table->text('descricao');
            $table->text('retorno')->nullable();
            $table->dateTime('data_retorno')->nullable();
            $table->text('proxima_acao')->nullable();
            $table->dateTime('data_proxima_acao')->nullable();
            $table->enum('status', ['aberto', 'em_andamento', 'concluido'])->default('aberto');
            $table->timestamps();
            $table->softDeletes();

            // Índices adicionais
            $table->index('atendivel_id');
            $table->index('atendivel_type');
            $table->index('user_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('atendimentos');
    }
};
