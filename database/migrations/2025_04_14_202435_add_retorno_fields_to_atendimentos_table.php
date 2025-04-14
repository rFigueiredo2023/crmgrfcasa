<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('atendimentos', function (Blueprint $table) {
            $table->text('retorno')->nullable()->after('descricao');
            $table->dateTime('data_retorno')->nullable()->after('retorno');
            $table->text('proxima_acao')->nullable()->after('data_retorno');
        });
    }

    public function down(): void
    {
        Schema::table('atendimentos', function (Blueprint $table) {
            $table->dropColumn(['retorno', 'data_retorno', 'proxima_acao']);
        });
    }
};
