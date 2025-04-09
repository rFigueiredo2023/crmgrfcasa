<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   */
  public function run(): void
  {
    // User::factory(10)->create(); // Comentado para usar apenas os usuários do UserSeeder por enquanto

    // User::factory()->create([ // Comentado
    //   'name' => 'Test User',
    //   'email' => 'test@example.com',
    // ]);

    // Chama o seeder de usuários específicos
    $this->call([
        UserSeeder::class,
        // Você pode adicionar outros seeders aqui se necessário
    ]);
  }
}
