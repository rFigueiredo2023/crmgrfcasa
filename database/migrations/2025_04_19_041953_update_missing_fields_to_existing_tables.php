<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adiciona e ajusta campos faltantes em várias tabelas do sistema.
     *
     * Esta migration faz ajustes em múltiplas tabelas para garantir a consistência
     * do banco de dados, adicionando campos necessários e renomeando outros para
     * seguir o padrão estabelecido.
     */
    public function up(): void
    {
        // Ajustes na tabela clientes
        $this->modificarTabelaClientes();

        // Ajustes na tabela leads
        $this->modificarTabelaLeads();

        // Ajustes na tabela transportadoras
        $this->modificarTabelaTransportadoras();

        // Ajustes na tabela veiculos
        $this->modificarTabelaVeiculos();
    }

    /**
     * Reverte as alterações.
     *
     * Não remove campos ou altera nomes de volta para evitar perda de dados.
     */
    public function down(): void
    {
        // Não revertemos mudanças para evitar perda de dados
    }

    /**
     * Ajusta campos na tabela clientes
     */
    private function modificarTabelaClientes(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            // Renomear ie para inscricao_estadual caso exista
            if (Schema::hasColumn('clientes', 'ie') && !Schema::hasColumn('clientes', 'inscricao_estadual')) {
                $table->renameColumn('ie', 'inscricao_estadual');
            }
            // Caso não tenha nenhum desses campos, criar inscricao_estadual
            elseif (!Schema::hasColumn('clientes', 'ie') && !Schema::hasColumn('clientes', 'inscricao_estadual')) {
                $table->string('inscricao_estadual')->nullable()
                    ->comment('Inscrição Estadual principal');
            }

            // Adicionar campos faltantes se não existirem
            if (!Schema::hasColumn('clientes', 'endereco')) {
                $table->string('endereco')
                    ->comment('Endereço completo');
            }

            if (!Schema::hasColumn('clientes', 'codigo_ibge')) {
                $table->string('codigo_ibge')
                    ->comment('Código IBGE do município');
            }

            if (!Schema::hasColumn('clientes', 'email')) {
                $table->string('email')->nullable()
                    ->comment('E-mail para contato');
            }

            // Adicionar o campo segmento
            if (!Schema::hasColumn('clientes', 'segmento')) {
                $table->string('segmento')->nullable()
                    ->comment('Segmento de mercado (texto)');
            }
        });
    }

    /**
     * Ajusta campos na tabela leads
     */
    private function modificarTabelaLeads(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            // Adicionar campos faltantes se não existirem
            if (!Schema::hasColumn('leads', 'endereco')) {
                $table->string('endereco')->nullable()
                    ->comment('Endereço completo');
            }

            if (!Schema::hasColumn('leads', 'codigo_ibge')) {
                $table->string('codigo_ibge')->nullable()
                    ->comment('Código IBGE do município');
            }

            if (!Schema::hasColumn('leads', 'email')) {
                $table->string('email')->nullable()
                    ->comment('E-mail para contato');
            }

            if (!Schema::hasColumn('leads', 'status')) {
                $table->string('status')->default('novo')
                    ->comment('Status do lead: novo, frio, morno, quente, convertido, perdido');

                // Adiciona índice para o campo status para otimizar consultas
                $table->index('status');
            }
        });
    }

    /**
     * Ajusta campos na tabela transportadoras
     */
    private function modificarTabelaTransportadoras(): void
    {
        Schema::table('transportadoras', function (Blueprint $table) {
            // Renomear ie para inscricao_estadual caso exista
            if (Schema::hasColumn('transportadoras', 'ie') && !Schema::hasColumn('transportadoras', 'inscricao_estadual')) {
                $table->renameColumn('ie', 'inscricao_estadual');
            }
            // Caso não tenha nenhum desses campos, criar inscricao_estadual
            elseif (!Schema::hasColumn('transportadoras', 'ie') && !Schema::hasColumn('transportadoras', 'inscricao_estadual')) {
                $table->string('inscricao_estadual')->nullable()
                    ->comment('Inscrição Estadual');
            }

            // Adicionar campos faltantes se não existirem
            if (!Schema::hasColumn('transportadoras', 'celular')) {
                $table->string('celular')->nullable()
                    ->comment('Número de celular para contato');
            }

            if (!Schema::hasColumn('transportadoras', 'email')) {
                $table->string('email')
                    ->comment('E-mail para contato');
            }
        });
    }

    /**
     * Ajusta campos na tabela veiculos
     */
    private function modificarTabelaVeiculos(): void
    {
        Schema::table('veiculos', function (Blueprint $table) {
            // Adicionar campos faltantes se não existirem
            if (!Schema::hasColumn('veiculos', 'cpf_cnpj_proprietario')) {
                $table->string('cpf_cnpj_proprietario')
                    ->comment('CPF ou CNPJ do proprietário do veículo');
            }

            if (!Schema::hasColumn('veiculos', 'uf_proprietario')) {
                $table->string('uf_proprietario')
                    ->comment('UF do proprietário do veículo');
            }

            if (!Schema::hasColumn('veiculos', 'tipo_proprietario')) {
                $table->string('tipo_proprietario')
                    ->comment('Tipo do proprietário (PF ou PJ)');
            }

            // Adicionar campos novos para veículos
            if (!Schema::hasColumn('veiculos', 'chassi')) {
                $table->string('chassi')->nullable()
                    ->comment('Número do chassi do veículo');
            }

            if (!Schema::hasColumn('veiculos', 'km_oleo')) {
                $table->integer('km_oleo')->nullable()
                    ->comment('Quilometragem da última troca de óleo');
            }

            if (!Schema::hasColumn('veiculos', 'km_correia')) {
                $table->integer('km_correia')->nullable()
                    ->comment('Quilometragem da última troca de correia');
            }

            if (!Schema::hasColumn('veiculos', 'segurado_ate')) {
                $table->date('segurado_ate')->nullable()
                    ->comment('Data de vencimento do seguro');
            }

            if (!Schema::hasColumn('veiculos', 'limite_km_mes')) {
                $table->integer('limite_km_mes')->nullable()
                    ->comment('Limite de quilometragem mensal');
            }

            if (!Schema::hasColumn('veiculos', 'responsavel_atual')) {
                $table->string('responsavel_atual')->nullable()
                    ->comment('Nome do responsável atual pelo veículo');
            }

            if (!Schema::hasColumn('veiculos', 'antt_rntrc')) {
                $table->string('antt_rntrc')->nullable()
                    ->comment('Registro Nacional de Transportadores Rodoviários de Cargas');
            }

            if (!Schema::hasColumn('veiculos', 'ie_proprietario')) {
                $table->string('ie_proprietario')->nullable()
                    ->comment('Inscrição Estadual do proprietário');
            }
        });
    }
};
