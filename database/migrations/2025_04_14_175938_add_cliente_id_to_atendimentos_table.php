<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('atendimentos', function (Blueprint $table) {
            // Primeiro removemos o valor default
            $table->string('status')->default(null)->change();

            // Atualizamos os registros existentes
            DB::statement("UPDATE atendimentos SET status = 'pendente' WHERE status = 'Pendente'");
            DB::statement("UPDATE atendimentos SET status = 'em_andamento' WHERE status = 'Em Andamento'");
            DB::statement("UPDATE atendimentos SET status = 'concluido' WHERE status = 'Concluído'");

            // Adicionamos o novo valor default
            $table->string('status')->default('pendente')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('atendimentos', function (Blueprint $table) {
            $table->string('status')->default('Pendente')->change();
        });
    }
};
