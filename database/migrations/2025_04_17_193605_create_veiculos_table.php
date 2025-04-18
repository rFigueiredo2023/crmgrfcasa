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
        Schema::create('veiculos', function (Blueprint $table) {
            // Chave primária
            $table->id();

            // Campos principais
            $table->string('motorista');
            $table->string('marca');
            $table->string('modelo');
            $table->integer('ano_fabricacao');
            $table->string('mes_licenca');
            $table->string('local');
            $table->string('placa')->unique();
            $table->string('uf', 2);
            $table->decimal('tara', 10, 2);
            $table->decimal('capacidade_kg', 10, 2);
            $table->decimal('capacidade_m3', 10, 2);
            $table->string('tipo_rodagem');
            $table->string('tipo_carroceria');
            $table->string('renavam');
            $table->string('cpf_cnpj_proprietario');
            $table->string('proprietario');
            $table->string('uf_proprietario');
            $table->string('tipo_propriedade');
            $table->text('detalhes')->nullable();

            // Relacionamento
            $table->foreignId('transportadora_id')->nullable()->constrained('transportadoras')
                  ->onUpdate('cascade')->onDelete('set null');

            // Extras
            $table->timestamps();
            $table->softDeletes();

            // Índices adicionais
            $table->index('placa');
            $table->index('motorista');
            $table->index('transportadora_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('veiculos');
    }
};
