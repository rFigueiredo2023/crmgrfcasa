<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('historicos', function (Blueprint $table) {
            // Remover a coluna cliente_id
            $table->dropForeign(['cliente_id']);
            $table->dropColumn('cliente_id');
            
            // Adicionar colunas polimórficas
            $table->morphs('historicoable');
            
            // Adicionar novos campos
            $table->string('retorno')->nullable()->after('texto');
            $table->dateTime('data_retorno')->nullable()->after('retorno');
            $table->boolean('ativar_lembrete')->default(false)->after('data_retorno');
        });
    }

    public function down()
    {
        Schema::table('historicos', function (Blueprint $table) {
            // Remover colunas polimórficas
            $table->dropMorphs('historicoable');
            
            // Remover novos campos
            $table->dropColumn(['retorno', 'data_retorno', 'ativar_lembrete']);
            
            // Restaurar coluna cliente_id
            $table->foreignId('cliente_id')->constrained()->onDelete('cascade');
        });
    }
}; 