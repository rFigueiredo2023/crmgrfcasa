<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adiciona campos adicionais à tabela segmentos.
     * Esta migration foi modificada para não duplicar a criação da tabela,
     * já que existe a migration 2025_04_22_233721_create_segmentos_table.php
     */
    public function up(): void
    {
        // Verifica se a tabela existe antes de tentar modificá-la
        if (Schema::hasTable('segmentos')) {
            Schema::table('segmentos', function (Blueprint $table) {
                // Adiciona campos adicionais que seriam únicos para esta migration
                if (!Schema::hasColumn('segmentos', 'descricao')) {
                    $table->text('descricao')->nullable()->after('nome');
                }

                if (!Schema::hasColumn('segmentos', 'cor')) {
                    $table->string('cor', 7)->nullable()->after('descricao')
                        ->comment('Código de cor hexadecimal para identificação visual');
                }

                if (!Schema::hasColumn('segmentos', 'ativo')) {
                    $table->boolean('ativo')->default(true)->after('cor');
                }
            });
        }
        // Se a tabela não existir (caso a outra migration falhe), cria a tabela
        else {
            Schema::create('segmentos', function (Blueprint $table) {
                $table->id();
                $table->string('nome');
                $table->text('descricao')->nullable();
                $table->string('cor', 7)->nullable()
                    ->comment('Código de cor hexadecimal para identificação visual');
                $table->boolean('ativo')->default(true);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Se a tabela existir, remove apenas os campos adicionados nesta migration
        if (Schema::hasTable('segmentos')) {
            Schema::table('segmentos', function (Blueprint $table) {
                $table->dropColumn(['descricao', 'cor', 'ativo']);
            });
        }
    }
};
