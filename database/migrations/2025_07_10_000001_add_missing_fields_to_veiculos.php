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
        Schema::table('veiculos', function (Blueprint $table) {
            // Adicionar campos novos para veículos
            if (!Schema::hasColumn('veiculos', 'chassi')) {
                $table->string('chassi')->nullable()->after('uf');
            }

            if (!Schema::hasColumn('veiculos', 'km_oleo')) {
                $table->integer('km_oleo')->nullable()->after('ano_fabricacao');
            }

            if (!Schema::hasColumn('veiculos', 'km_correia')) {
                $table->integer('km_correia')->nullable()->after('km_oleo');
            }

            if (!Schema::hasColumn('veiculos', 'segurado_ate')) {
                $table->date('segurado_ate')->nullable()->after('km_correia');
            }

            if (!Schema::hasColumn('veiculos', 'limite_km_mes')) {
                $table->integer('limite_km_mes')->nullable()->after('segurado_ate');
            }

            if (!Schema::hasColumn('veiculos', 'responsavel_atual')) {
                $table->string('responsavel_atual')->nullable()->after('renavam');
            }

            if (!Schema::hasColumn('veiculos', 'antt_rntrc')) {
                $table->string('antt_rntrc')->nullable()->after('proprietario');
            }

            if (!Schema::hasColumn('veiculos', 'ie_proprietario')) {
                $table->string('ie_proprietario')->nullable()->after('uf_proprietario');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('veiculos', function (Blueprint $table) {
            $columns = [
                'chassi',
                'km_oleo',
                'km_correia',
                'segurado_ate',
                'limite_km_mes',
                'responsavel_atual',
                'antt_rntrc',
                'ie_proprietario'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('veiculos', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
