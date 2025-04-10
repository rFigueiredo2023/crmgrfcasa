<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('arquivos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->comment('Usuário que enviou');
            $table->string('nome');
            $table->string('nome_original');
            $table->string('tipo');
            $table->string('tamanho');
            $table->string('caminho');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('arquivos');
    }
};