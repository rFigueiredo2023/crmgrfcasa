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

            // Novos campos da API CNPJa
            $table->string('nome_fantasia')->nullable();
            $table->date('fundacao')->nullable();
            $table->string('situacao')->nullable();
            $table->date('data_situacao')->nullable();
            $table->string('natureza_juridica')->nullable();
            $table->string('porte')->nullable();
            $table->decimal('capital_social', 15, 2)->nullable();
            $table->boolean('simples_nacional')->default(false);
            $table->string('logradouro')->nullable();
            $table->string('numero')->nullable();
            $table->string('bairro')->nullable();
            $table->string('cidade')->nullable();
            $table->string('estado')->nullable();
            $table->string('complemento')->nullable();
            $table->string('dominio_email')->nullable();
            $table->string('cnae_principal')->nullable();
            $table->string('socio_principal')->nullable();
            $table->string('funcao_socio')->nullable();
            $table->string('suframa')->nullable();
            $table->string('status_suframa')->nullable();

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
