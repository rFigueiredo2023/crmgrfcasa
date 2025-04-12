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
        Schema::table('leads', function (Blueprint $table) {
            // Removendo campos antigos
            $table->dropColumn(['email', 'origem', 'status', 'observacoes']);
            
            // Adicionando novos campos
            $table->string('nome_empresa')->nullable();
            $table->string('cnpj')->nullable();
            $table->string('ie')->nullable(); // Inscrição Estadual
            $table->string('endereco')->nullable();
            $table->string('codigo_ibge')->nullable();
            $table->dateTime('data_proxima_acao')->nullable();
            $table->dateTime('data_retorno')->nullable();
            $table->boolean('ativar_lembrete')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            // Removendo novos campos
            $table->dropColumn([
                'nome_empresa',
                'cnpj',
                'ie',
                'endereco',
                'codigo_ibge',
                'data_proxima_acao',
                'data_retorno',
                'ativar_lembrete'
            ]);
            
            // Restaurando campos antigos
            $table->string('email')->nullable();
            $table->string('origem')->nullable();
            $table->string('status')->nullable();
            $table->text('observacoes')->nullable();
        });
    }
};
