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
        Schema::table('users', function (Blueprint $table) {
            // Adiciona a coluna role se ela não existir
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('vendas')->after('password');
            }
        });

        // Atualizar os usuários existentes
        DB::table('users')->where('email', 'admin@example.com')->update(['role' => 'admin']);
        DB::table('users')->where('email', 'vendas@example.com')->update(['role' => 'vendas']);
        DB::table('users')->where('email', 'financeiro@example.com')->update(['role' => 'financeiro']);
        DB::table('users')->where('email', 'maria@example.com')->update(['role' => 'vendas']);
        DB::table('users')->where('email', 'joao@example.com')->update(['role' => 'vendas']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
