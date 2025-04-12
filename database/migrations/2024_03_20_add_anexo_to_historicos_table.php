<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('historicos', function (Blueprint $table) {
            $table->string('anexo')->nullable()->after('proxima_acao');
        });
    }

    public function down()
    {
        Schema::table('historicos', function (Blueprint $table) {
            $table->dropColumn('anexo');
        });
    }
}; 