<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cria a tabela principal de clientes para o CRM.
     *
     * Esta tabela armazena as informações cadastrais dos clientes da empresa,
     * incluindo dados coletados via integração com a API CNPJa.
     */
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            // Identificador único
            $table->id()->comment('Identificador único do cliente');

            // Dados cadastrais principais
            $table->string('razao_social')
                ->comment('Razão social ou nome empresarial');
            $table->string('cnpj')->unique()
                ->comment('CNPJ da empresa (apenas números)');
            $table->string('inscricao_estadual')->nullable()
                ->comment('Inscrição Estadual principal');
            $table->string('telefone')
                ->comment('Telefone principal de contato');
            $table->string('contato')
                ->comment('Nome da pessoa de contato');
            $table->string('email')->nullable()
                ->comment('E-mail para contato');
            $table->string('endereco')
                ->comment('Endereço completo');
            $table->string('cep')->nullable()
                ->comment('CEP (apenas números)');
            $table->string('municipio')
                ->comment('Nome do município');
            $table->string('uf', 2)
                ->comment('Sigla do estado (UF)');
            $table->string('codigo_ibge')
                ->comment('Código IBGE do município');

            // Dados da API CNPJa
            $table->string('nome_fantasia')->nullable()
                ->comment('Nome Fantasia da empresa');
            $table->date('fundacao')->nullable()
                ->comment('Data de fundação da empresa');
            $table->string('situacao')->nullable()
                ->comment('Situação cadastral na Receita Federal');
            $table->date('data_situacao')->nullable()
                ->comment('Data da última atualização da situação');
            $table->string('natureza_juridica')->nullable()
                ->comment('Natureza jurídica da empresa');
            $table->string('porte')->nullable()
                ->comment('Porte da empresa (ME, EPP, etc)');
            $table->decimal('capital_social', 15, 2)->nullable()
                ->comment('Capital social registrado');
            $table->boolean('simples_nacional')->default(false)
                ->comment('Indica se está no Simples Nacional');
            $table->string('logradouro')->nullable()
                ->comment('Logradouro do endereço');
            $table->string('numero')->nullable()
                ->comment('Número do endereço');
            $table->string('bairro')->nullable()
                ->comment('Bairro do endereço');
            $table->string('cidade')->nullable()
                ->comment('Cidade (mesmo que município, mas do CNPJ)');
            $table->string('estado')->nullable()
                ->comment('Estado (mesmo que UF, mas do CNPJ)');
            $table->string('complemento')->nullable()
                ->comment('Complemento do endereço');
            $table->string('dominio_email')->nullable()
                ->comment('Domínio de e-mail corporativo');
            $table->string('cnae_principal')->nullable()
                ->comment('Código CNAE da atividade principal');
            $table->string('socio_principal')->nullable()
                ->comment('Nome do sócio principal');
            $table->string('funcao_socio')->nullable()
                ->comment('Função do sócio principal');
            $table->string('suframa')->nullable()
                ->comment('Código SUFRAMA');
            $table->string('status_suframa')->nullable()
                ->comment('Status do cadastro SUFRAMA');

            // Relacionamento com vendedor
            $table->foreignId('user_id')->nullable()
                ->comment('Vendedor responsável pelo cliente')
                ->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('set null');

            // Configurações fiscais
            $table->string('tipo_contribuinte')->default('Contribuinte')
                ->comment('Tipo de contribuinte: Contribuinte, Não Contribuinte ou Isento');
            $table->string('regime_tributario')->default('Simples Nacional')
                ->comment('Regime tributário: Simples Nacional, Lucro Presumido ou Lucro Real');

            // Controle de timestamps e soft delete
            $table->timestamps();
            $table->softDeletes();

            // Índices para otimização de consultas frequentes
            $table->index('razao_social');
            $table->index('municipio');
            $table->index('uf');
            $table->index('user_id');
        });
    }

    /**
     * Reverte a criação da tabela clientes.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
