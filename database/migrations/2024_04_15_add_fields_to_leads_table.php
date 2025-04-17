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
            if (!Schema::hasColumn('leads', 'razao_social')) {
                $table->string('razao_social');
            }

            if (!Schema::hasColumn('leads', 'cnpj')) {
                $table->string('cnpj')->nullable();
            }

            if (!Schema::hasColumn('leads', 'ie')) {
                $table->string('ie')->nullable(); // Inscrição Estadual
            }

            if (!Schema::hasColumn('leads', 'endereco')) {
                $table->string('endereco')->nullable();
            }

            if (!Schema::hasColumn('leads', 'telefone')) {
                $table->string('telefone')->nullable();
            }

            if (!Schema::hasColumn('leads', 'contato')) {
                $table->string('contato')->nullable();
            }

            if (!Schema::hasColumn('leads', 'data_proxima_acao')) {
                $table->datetime('data_proxima_acao')->nullable();
            }

            if (!Schema::hasColumn('leads', 'data_retorno')) {
                $table->datetime('data_retorno')->nullable();
            }

            if (!Schema::hasColumn('leads', 'ativar_lembrete')) {
                $table->boolean('ativar_lembrete')->default(false);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn([
                'razao_social',
                'cnpj',
                'ie',
                'endereco',
                'telefone',
                'contato',
                'data_proxima_acao',
                'data_retorno',
                'ativar_lembrete'
            ]);
        });
    }
};