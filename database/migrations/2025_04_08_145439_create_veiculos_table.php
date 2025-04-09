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
            $table->id();
            $table->string('motorista');
            $table->string('marca');
            $table->string('modelo');
            $table->integer('ano_fabricacao');
            $table->string('mes_licenca');
            $table->string('local');
            $table->string('placa')->unique();
            $table->string('uf');
            $table->decimal('tara', 10, 2);
            $table->decimal('capacidade_kg', 10, 2);
            $table->decimal('capacidade_m3', 10, 2);
            $table->enum('tipo_rodagem', ['truck', 'toco', 'cavalo_mecanico', 'van', 'utilitarios', 'outros']);
            $table->enum('tipo_carroceria', ['aberta', 'bau', 'outros', 'slider']);
            $table->string('renavam');
            $table->string('cpf_cnpj_proprietario');
            $table->string('proprietario');
            $table->string('uf_proprietario');
            $table->string('tipo_proprietario');
            $table->text('detalhes')->nullable();
            $table->foreignId('user_id')->constrained('users')->comment('Usuário que cadastrou');
            $table->timestamps();
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
