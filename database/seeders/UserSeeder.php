<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User; // Importa o Model User
use Illuminate\Support\Facades\Hash; // Importa Hash para a senha

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cria usuário Admin
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'), // Hash da senha
            'role' => 'admin',
        ]);

        // Cria usuário Vendas
        User::create([
            'name' => 'Vendas User',
            'email' => 'vendas@example.com',
            'password' => Hash::make('password'),
            'role' => 'vendas',
        ]);

        // Cria usuário Financial
        User::create([
            'name' => 'Financial User',
            'email' => 'financial@example.com',
            'password' => Hash::make('password'),
            'role' => 'financial',
        ]);
    }
}
