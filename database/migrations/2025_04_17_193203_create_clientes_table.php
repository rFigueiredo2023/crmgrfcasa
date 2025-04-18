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
        Schema::create('clientes', function (Blueprint $table) {
            // Chave primária
            $table->id();

            // Campos principais
            $table->string('razao_social');
            $table->string('cnpj')->unique();
            $table->string('inscricao_estadual')->nullable();
            $table->string('telefone');
            $table->string('contato');
            $table->string('email')->nullable();
            $table->string('endereco');
            $table->string('cep')->nullable();
            $table->string('municipio');
            $table->string('uf', 2); // Apenas sigla do estado (2 caracteres)
            $table->string('codigo_ibge');

            // Relacionamento
            $table->foreignId('user_id')->nullable()->constrained('users')->onUpdate('cascade')->onDelete('set null');

            // Informações para emissão de nota
            $table->enum('tipo_contribuinte', ['Contribuinte', 'Não Contribuinte', 'Isento'])->default('Contribuinte');
            $table->enum('regime_tributario', ['Simples Nacional', 'Lucro Presumido', 'Lucro Real'])->default('Simples Nacional');

            // Extras
            $table->timestamps();
            $table->softDeletes();

            // Índices adicionais
            $table->index('razao_social');
            $table->index('municipio');
            $table->index('uf');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
