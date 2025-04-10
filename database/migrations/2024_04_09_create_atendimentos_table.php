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
            $table->foreignId('cliente_id')->constrained('clientes');
            $table->foreignId('vendedor_id')->constrained('users');
            $table->dateTime('data_atendimento');
            $table->string('tipo_atendimento');
            $table->text('descricao');
            $table->string('status');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('atendimentos');
    }
}; 