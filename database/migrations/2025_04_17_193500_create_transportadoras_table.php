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
        Schema::create('transportadoras', function (Blueprint $table) {
            // Chave primária
            $table->id();

            // Campos principais
            $table->string('razao_social');
            $table->string('cnpj')->unique();
            $table->string('inscricao_estadual')->nullable();
            $table->string('endereco');
            $table->string('codigo_ibge');
            $table->string('telefone');
            $table->string('celular')->nullable();
            $table->string('email');
            $table->string('contato');

            // Relacionamento
            $table->foreignId('user_id')->nullable()->constrained('users')->onUpdate('cascade')->onDelete('set null');

            // Extras
            $table->timestamps();
            $table->softDeletes();

            // Índices adicionais
            $table->index('razao_social');
            $table->index('cnpj');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transportadoras');
    }
};
