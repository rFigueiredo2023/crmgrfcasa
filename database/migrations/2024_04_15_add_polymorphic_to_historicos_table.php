<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('historicos', function (Blueprint $table) {
            if (!Schema::hasColumn('historicos', 'historicoable_id')) {
                $table->unsignedBigInteger('historicoable_id');
            }

            if (!Schema::hasColumn('historicos', 'historicoable_type')) {
                $table->string('historicoable_type');
            }

            // Removendo as antigas foreign keys se existirem
            if (Schema::hasColumn('historicos', 'cliente_id')) {
                $table->dropForeign(['cliente_id']);
                $table->dropColumn('cliente_id');
            }

            if (Schema::hasColumn('historicos', 'lead_id')) {
                $table->dropForeign(['lead_id']);
                $table->dropColumn('lead_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('historicos', function (Blueprint $table) {
            // Recriando as colunas antigas
            $table->unsignedBigInteger('cliente_id')->nullable();
            $table->unsignedBigInteger('lead_id')->nullable();

            // Recriando as foreign keys
            $table->foreign('cliente_id')->references('id')->on('clientes');
            $table->foreign('lead_id')->references('id')->on('leads');

            // Removendo as colunas polimórficas
            $table->dropColumn(['historicoable_id', 'historicoable_type']);
        });
    }
};