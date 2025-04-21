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
        // Modificação na tabela clientes
        Schema::table('clientes', function (Blueprint $table) {
            // Renomear ie para inscricao_estadual caso exista
            if (Schema::hasColumn('clientes', 'ie') && !Schema::hasColumn('clientes', 'inscricao_estadual')) {
                $table->renameColumn('ie', 'inscricao_estadual');
            }
            // Caso não tenha nenhum desses campos, criar inscricao_estadual
            elseif (!Schema::hasColumn('clientes', 'ie') && !Schema::hasColumn('clientes', 'inscricao_estadual')) {
                $table->string('inscricao_estadual')->nullable();
            }

            // Adicionar campos faltantes se não existirem
            if (!Schema::hasColumn('clientes', 'endereco')) {
                $table->string('endereco');
            }
            if (!Schema::hasColumn('clientes', 'codigo_ibge')) {
                $table->string('codigo_ibge');
            }
            if (!Schema::hasColumn('clientes', 'email')) {
                $table->string('email')->nullable();
            }
        });

        // Modificação na tabela leads
        Schema::table('leads', function (Blueprint $table) {
            // Adicionar campos faltantes se não existirem
            if (!Schema::hasColumn('leads', 'endereco')) {
                $table->string('endereco')->nullable();
            }
            if (!Schema::hasColumn('leads', 'codigo_ibge')) {
                $table->string('codigo_ibge')->nullable();
            }
            if (!Schema::hasColumn('leads', 'email')) {
                $table->string('email')->nullable();
            }
            if (!Schema::hasColumn('leads', 'status')) {
                $table->string('status')->default('Frio');
            }
        });

        // Modificação na tabela transportadoras
        Schema::table('transportadoras', function (Blueprint $table) {
            // Renomear ie para inscricao_estadual caso exista
            if (Schema::hasColumn('transportadoras', 'ie') && !Schema::hasColumn('transportadoras', 'inscricao_estadual')) {
                $table->renameColumn('ie', 'inscricao_estadual');
            }
            // Caso não tenha nenhum desses campos, criar inscricao_estadual
            elseif (!Schema::hasColumn('transportadoras', 'ie') && !Schema::hasColumn('transportadoras', 'inscricao_estadual')) {
                $table->string('inscricao_estadual')->nullable();
            }

            // Adicionar campos faltantes se não existirem
            if (!Schema::hasColumn('transportadoras', 'celular')) {
                $table->string('celular')->nullable();
            }
            if (!Schema::hasColumn('transportadoras', 'email')) {
                $table->string('email');
            }
        });

        // Modificação na tabela veiculos
        Schema::table('veiculos', function (Blueprint $table) {
            // Adicionar campos faltantes se não existirem
            if (!Schema::hasColumn('veiculos', 'cpf_cnpj_proprietario')) {
                $table->string('cpf_cnpj_proprietario');
            }
            if (!Schema::hasColumn('veiculos', 'uf_proprietario')) {
                $table->string('uf_proprietario');
            }
            if (!Schema::hasColumn('veiculos', 'tipo_proprietario')) {
                $table->string('tipo_proprietario');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Como estamos apenas adicionando colunas ou renomeando, não vamos removê-las no rollback
        // Isso evita perda de dados, caso seja necessário fazer rollback
    }
};
