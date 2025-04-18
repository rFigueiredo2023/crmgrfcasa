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
        Schema::create('leads', function (Blueprint $table) {
            // Chave primária
            $table->id();

            // Campos principais
            $table->string('razao_social');
            $table->string('cnpj')->unique()->nullable();
            $table->string('inscricao_estadual')->nullable();
            $table->string('telefone');
            $table->string('contato');
            $table->string('email')->nullable();
            $table->string('endereco')->nullable();
            $table->string('cep')->nullable();
            $table->string('municipio')->nullable();
            $table->string('uf', 2)->nullable(); // Apenas sigla do estado (2 caracteres)
            $table->string('codigo_ibge')->nullable();

            // Relacionamento
            $table->foreignId('user_id')->constrained('users')->onUpdate('cascade')->onDelete('restrict');

            // Extras
            $table->timestamps();
            $table->softDeletes();

            // Índices adicionais
            $table->index('razao_social');
            $table->index('telefone');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
