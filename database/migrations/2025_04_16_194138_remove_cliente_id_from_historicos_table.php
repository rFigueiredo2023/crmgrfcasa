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
        // O campo já foi removido por outra migration
        if (Schema::hasColumn('historicos', 'cliente_id')) {
            Schema::table('historicos', function (Blueprint $table) {
                $table->dropColumn('cliente_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasColumn('historicos', 'cliente_id')) {
            Schema::table('historicos', function (Blueprint $table) {
                $table->unsignedBigInteger('cliente_id')->nullable()->after('id');
            });
        }
    }
};
