<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CreateVendasUserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Vendas',
            'email' => 'vendas@vendas.com',
            'password' => Hash::make('123456'),
            'role' => 'vendas'
        ]);
    }
}