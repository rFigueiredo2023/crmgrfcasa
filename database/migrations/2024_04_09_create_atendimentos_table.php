<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('atendimentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('tipo');
            $table->text('descricao');
            $table->string('retorno')->nullable();
            $table->dateTime('data_retorno')->nullable();
            $table->text('proxima_acao')->nullable();
            $table->dateTime('data_proxima_acao')->nullable();
            $table->boolean('ativar_lembrete')->default(false);
            $table->string('anexo')->nullable();
            $table->string('status')->default('Aberto');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('atendimentos');
    }
}; 