<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('historicos', function (Blueprint $table) {
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('tipo');
            $table->text('texto');
            $table->text('retorno')->nullable();
            $table->dateTime('data_retorno')->nullable();
            $table->text('proxima_acao')->nullable();
            $table->dateTime('data_proxima_acao')->nullable();
            $table->boolean('ativar_lembrete')->default(false);
            $table->string('anexo')->nullable();
            $table->dateTime('data');
        });
    }

    public function down(): void
    {
        Schema::table('historicos', function (Blueprint $table) {
            $table->dropForeign(['cliente_id']);
            $table->dropForeign(['user_id']);
            $table->dropColumn([
                'cliente_id',
                'user_id',
                'tipo',
                'texto',
                'retorno',
                'data_retorno',
                'proxima_acao',
                'data_proxima_acao',
                'ativar_lembrete',
                'anexo',
                'data'
            ]);
        });
    }
};