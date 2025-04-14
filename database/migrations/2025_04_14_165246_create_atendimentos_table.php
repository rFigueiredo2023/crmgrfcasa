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
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->onDelete('set null');
            $table->foreignId('lead_id')->nullable()->constrained('leads')->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('tipo_contato'); // Visita, Telefone, Email, WhatsApp
            $table->text('descricao')->nullable();
            $table->dateTime('data_atendimento');
            $table->dateTime('data_proxima_acao')->nullable();
            $table->boolean('ativar_lembrete')->default(false);
            $table->string('status')->default('pendente'); // pendente, concluido, cancelado
            $table->timestamps();
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
