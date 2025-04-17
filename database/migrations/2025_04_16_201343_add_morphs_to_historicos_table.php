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
            if (!Schema::hasColumn('historicos', 'historicable_id') && !Schema::hasColumn('historicos', 'historicable_type')) {
                $table->morphs('historicable');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('historicos', function (Blueprint $table) {
            $table->dropMorphs('historicable');
        });
    }
};
