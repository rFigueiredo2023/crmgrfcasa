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
        // Certificar-se de que a tabela leads existe
        if (!Schema::hasTable('leads')) {
            Schema::create('leads', function (Blueprint $table) {
                $table->id();
                $table->string('razao_social');
                $table->string('cnpj')->nullable();
                $table->string('ie')->nullable();
                $table->string('endereco')->nullable();
                $table->string('codigo_ibge')->nullable();
                $table->string('telefone');
                $table->string('contato')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->foreign('user_id')->references('id')->on('users');
                $table->dateTime('data_proxima_acao')->nullable();
                $table->dateTime('data_retorno')->nullable();
                $table->boolean('ativar_lembrete')->default(false);
                $table->timestamps();
            });
        }

        // Certificar-se de que a tabela clientes existe
        if (!Schema::hasTable('clientes')) {
            Schema::create('clientes', function (Blueprint $table) {
                $table->id();
                $table->string('razao_social');
                $table->string('cnpj')->nullable();
                $table->string('ie')->nullable();
                $table->string('endereco')->nullable();
                $table->string('codigo_ibge')->nullable();
                $table->string('telefone');
                $table->string('contato')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->foreign('user_id')->references('id')->on('users');
                $table->timestamps();
            });
        }

        // Certificar-se de que a tabela historicos existe
        if (!Schema::hasTable('historicos')) {
            Schema::create('historicos', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->foreign('user_id')->references('id')->on('users');
                $table->morphs('historicable');
                $table->dateTime('data');
                $table->string('tipo');
                $table->text('texto');
                $table->text('proxima_acao')->nullable();
                $table->dateTime('data_proxima_acao')->nullable();
                $table->text('retorno')->nullable();
                $table->dateTime('data_retorno')->nullable();
                $table->boolean('ativar_lembrete')->default(false);
                $table->string('anexo')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Como esta é uma migração de correção, não faremos nada no método down
        // para evitar perda acidental de dados
    }
};
