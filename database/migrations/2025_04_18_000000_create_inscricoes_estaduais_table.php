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
        Schema::create('inscricoes_estaduais', function (Blueprint $table) {
            // Chave primária
            $table->id();

            // Relacionamento com clientes
            $table->foreignId('cliente_id')->constrained('clientes')->onUpdate('cascade')->onDelete('cascade');

            // Campos principais
            $table->string('estado');
            $table->string('numero_ie');
            $table->string('tipo_ie');
            $table->string('status_ie');
            $table->date('data_status_ie')->nullable();

            // Timestamps
            $table->timestamps();

            // Índices
            $table->index('cliente_id');
            $table->index('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inscricoes_estaduais');
    }
};
