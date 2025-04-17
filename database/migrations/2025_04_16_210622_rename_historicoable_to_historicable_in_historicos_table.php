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
        Schema::table('historicos', function (Blueprint $table) {
            if (Schema::hasColumn('historicos', 'historicoable_id') && !Schema::hasColumn('historicos', 'historicable_id')) {
                $table->renameColumn('historicoable_id', 'historicable_id');
            }

            if (Schema::hasColumn('historicos', 'historicoable_type') && !Schema::hasColumn('historicos', 'historicable_type')) {
                $table->renameColumn('historicoable_type', 'historicable_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('historicos', function (Blueprint $table) {
            if (Schema::hasColumn('historicos', 'historicable_id') && !Schema::hasColumn('historicos', 'historicoable_id')) {
                $table->renameColumn('historicable_id', 'historicoable_id');
            }

            if (Schema::hasColumn('historicos', 'historicable_type') && !Schema::hasColumn('historicos', 'historicoable_type')) {
                $table->renameColumn('historicable_type', 'historicoable_type');
            }
        });
    }
};
