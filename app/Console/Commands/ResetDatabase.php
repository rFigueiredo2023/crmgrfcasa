<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ResetDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Zera completamente o banco de dados';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->confirm('Isso vai APAGAR TODOS OS DADOS do banco. Tem certeza?')) {
            $this->info('Operação cancelada.');
            return;
        }

        // Desabilitar verificação de chaves estrangeiras para evitar erros
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Buscar o nome de todas as tabelas no banco
        $this->info('Buscando tabelas...');
        $tables = DB::select('SHOW TABLES');

        // Extrair o nome da tabela do resultado
        $databaseName = config('database.connections.mysql.database');
        $tableKey = 'Tables_in_' . $databaseName;

        // Truncar cada tabela encontrada
        foreach ($tables as $table) {
            $tableName = $table->$tableKey;

            // Pular tabela de migrações
            if ($tableName === 'migrations') {
                continue;
            }

            $this->info("Truncando tabela: {$tableName}");
            DB::table($tableName)->truncate();
        }

        // Reabilitar verificação de chaves estrangeiras
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Reiniciar as migrações apenas para migrations e users
        $this->info('Recriando tabela de usuários...');

        // Verificar se a coluna 'role' existe na tabela users
        $userColumns = Schema::getColumnListing('users');
        $hasRoleColumn = in_array('role', $userColumns);

        // Criar usuário admin com base nas colunas existentes
        $adminData = [
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'created_at' => now(),
            'updated_at' => now()
        ];

        // Adiciona role apenas se a coluna existir
        if ($hasRoleColumn) {
            $adminData['role'] = 'admin';
        }

        DB::table('users')->insert($adminData);

        // Criar usuário vendas
        $vendasData = [
            'name' => 'Vendas',
            'email' => 'vendas@example.com',
            'password' => bcrypt('password'),
            'created_at' => now(),
            'updated_at' => now()
        ];

        // Adiciona role apenas se a coluna existir
        if ($hasRoleColumn) {
            $vendasData['role'] = 'vendas';
        }

        DB::table('users')->insert($vendasData);

        // Criar mais usuários para teste
        // Usuário financeiro
        $financeiroData = [
            'name' => 'Financeiro',
            'email' => 'financeiro@example.com',
            'password' => bcrypt('password'),
            'created_at' => now(),
            'updated_at' => now()
        ];

        if ($hasRoleColumn) {
            $financeiroData['role'] = 'financeiro';
        }

        DB::table('users')->insert($financeiroData);

        // Usuário vendedor 1
        $vendedor1Data = [
            'name' => 'Maria Silva',
            'email' => 'maria@example.com',
            'password' => bcrypt('password'),
            'created_at' => now(),
            'updated_at' => now()
        ];

        if ($hasRoleColumn) {
            $vendedor1Data['role'] = 'vendas';
        }

        $vendedor1Id = DB::table('users')->insertGetId($vendedor1Data);

        // Usuário vendedor 2
        $vendedor2Data = [
            'name' => 'João Souza',
            'email' => 'joao@example.com',
            'password' => bcrypt('password'),
            'created_at' => now(),
            'updated_at' => now()
        ];

        if ($hasRoleColumn) {
            $vendedor2Data['role'] = 'vendas';
        }

        $vendedor2Id = DB::table('users')->insertGetId($vendedor2Data);

        $this->info('Criando tabelas principais...');

        // Certificar-se de que as tabelas principais estão criadas
        $this->createLeadsTable();
        $this->createClientesTable();
        $this->createHistoricosTable();

        // Criar alguns dados de teste
        $this->criarLeadsTeste($vendedor1Id, $vendedor2Id);
        $this->criarClientesTeste($vendedor1Id, $vendedor2Id);

        $this->info('Banco de dados zerado com sucesso!');

        return 0;
    }

    private function createLeadsTable()
    {
        if (!Schema::hasTable('leads')) {
            $this->info('Criando tabela leads...');
            Schema::create('leads', function ($table) {
                $table->id();
                $table->string('razao_social');
                $table->string('cnpj')->nullable();
                $table->string('ie')->nullable();
                $table->string('endereco')->nullable();
                $table->string('telefone');
                $table->string('contato')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->foreign('user_id')->references('id')->on('users');
                $table->dateTime('data_proxima_acao')->nullable();
                $table->dateTime('data_retorno')->nullable();
                $table->boolean('ativar_lembrete')->default(false);
                $table->timestamps();
            });
        }
    }

    private function createClientesTable()
    {
        if (!Schema::hasTable('clientes')) {
            $this->info('Criando tabela clientes...');
            Schema::create('clientes', function ($table) {
                $table->id();
                $table->string('razao_social');
                $table->string('cnpj')->nullable();
                $table->string('ie')->nullable();
                $table->string('endereco')->nullable();
                $table->string('telefone');
                $table->string('contato')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->foreign('user_id')->references('id')->on('users');
                $table->timestamps();
            });
        }
    }

    private function createHistoricosTable()
    {
        if (!Schema::hasTable('historicos')) {
            $this->info('Criando tabela historicos...');
            Schema::create('historicos', function ($table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->foreign('user_id')->references('id')->on('users');
                $table->morphs('historicable');
                $table->dateTime('data');
                $table->string('tipo');
                $table->text('texto');
                $table->text('proxima_acao')->nullable();
                $table->dateTime('data_proxima_acao')->nullable();
                $table->text('retorno')->nullable();
                $table->dateTime('data_retorno')->nullable();
                $table->boolean('ativar_lembrete')->default(false);
                $table->string('anexo')->nullable();
                $table->timestamps();
            });
        }
    }

    private function criarLeadsTeste($vendedor1Id, $vendedor2Id)
    {
        if (Schema::hasTable('leads')) {
            $this->info('Criando leads de teste...');

            // Lead 1
            $lead1Id = DB::table('leads')->insertGetId([
                'razao_social' => 'Empresa ABC Ltda',
                'cnpj' => '12.345.678/0001-90',
                'telefone' => '(11) 98765-4321',
                'contato' => 'Carlos Silva',
                'endereco' => 'Av. Paulista, 1000, São Paulo-SP',
                'user_id' => $vendedor1Id,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Lead 2
            $lead2Id = DB::table('leads')->insertGetId([
                'razao_social' => 'Distribuidora XYZ',
                'cnpj' => '45.678.901/0001-23',
                'telefone' => '(21) 97654-3210',
                'contato' => 'Ana Souza',
                'endereco' => 'Rua das Flores, 200, Rio de Janeiro-RJ',
                'user_id' => $vendedor1Id,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Lead 3
            $lead3Id = DB::table('leads')->insertGetId([
                'razao_social' => 'Indústria 123',
                'cnpj' => '78.901.234/0001-56',
                'telefone' => '(31) 96543-2109',
                'contato' => 'José Santos',
                'endereco' => 'Av. Industrial, 500, Belo Horizonte-MG',
                'user_id' => $vendedor2Id,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Lead 4
            $lead4Id = DB::table('leads')->insertGetId([
                'razao_social' => 'Comércio Rápido',
                'cnpj' => '90.123.456/0001-78',
                'telefone' => '(41) 95432-1098',
                'contato' => 'Mariana Oliveira',
                'endereco' => 'Rua do Comércio, 300, Curitiba-PR',
                'user_id' => $vendedor2Id,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Adicionar históricos para os leads
            if (Schema::hasTable('historicos')) {
                // Histórico Lead 1
                DB::table('historicos')->insert([
                    'user_id' => $vendedor1Id,
                    'historicable_id' => $lead1Id,
                    'historicable_type' => 'App\\Models\\Lead',
                    'data' => now()->subDays(7),
                    'tipo' => 'Ligação',
                    'texto' => 'Primeiro contato com o lead, apresentando os serviços da empresa',
                    'proxima_acao' => 'Agendar reunião',
                    'data_proxima_acao' => now()->addDays(3),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                DB::table('historicos')->insert([
                    'user_id' => $vendedor1Id,
                    'historicable_id' => $lead1Id,
                    'historicable_type' => 'App\\Models\\Lead',
                    'data' => now()->subDays(3),
                    'tipo' => 'E-mail',
                    'texto' => 'Envio de material institucional conforme solicitado',
                    'proxima_acao' => 'Follow-up por telefone',
                    'data_proxima_acao' => now()->addDays(2),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // Histórico Lead 2
                DB::table('historicos')->insert([
                    'user_id' => $vendedor1Id,
                    'historicable_id' => $lead2Id,
                    'historicable_type' => 'App\\Models\\Lead',
                    'data' => now()->subDays(5),
                    'tipo' => 'WhatsApp',
                    'texto' => 'Cliente interessado em produtos da linha premium',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // Histórico Lead 3
                DB::table('historicos')->insert([
                    'user_id' => $vendedor2Id,
                    'historicable_id' => $lead3Id,
                    'historicable_type' => 'App\\Models\\Lead',
                    'data' => now()->subDays(2),
                    'tipo' => 'Visita',
                    'texto' => 'Visita presencial para demonstração do produto',
                    'retorno' => 'Cliente solicitou orçamento detalhado',
                    'data_retorno' => now()->subDay(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // Não adicionamos histórico para o Lead 4 intencionalmente,
                // para testar casos sem histórico
            }

            $this->info('4 leads de teste criados com sucesso, incluindo históricos!');
        }
    }

    private function criarClientesTeste($vendedor1Id, $vendedor2Id)
    {
        if (Schema::hasTable('clientes')) {
            $this->info('Criando clientes de teste...');

            // Cliente 1
            $cliente1Id = DB::table('clientes')->insertGetId([
                'razao_social' => 'Supermercado Grande',
                'cnpj' => '12.345.678/0001-90',
                'telefone' => '(11) 98765-4321',
                'contato' => 'Roberto Almeida',
                'endereco' => 'Av. Principal, 1000, São Paulo-SP',
                'user_id' => $vendedor1Id,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Cliente 2
            $cliente2Id = DB::table('clientes')->insertGetId([
                'razao_social' => 'Farmácia Saúde',
                'cnpj' => '23.456.789/0001-01',
                'telefone' => '(21) 97654-3210',
                'contato' => 'Carla Mendes',
                'endereco' => 'Rua das Farmácias, 200, Rio de Janeiro-RJ',
                'user_id' => $vendedor2Id,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Criar históricos para os clientes
            if (Schema::hasTable('historicos')) {
                // Histórico cliente 1
                DB::table('historicos')->insert([
                    'user_id' => $vendedor1Id,
                    'historicable_id' => $cliente1Id,
                    'historicable_type' => 'App\\Models\\Cliente',
                    'data' => now()->subDays(5),
                    'tipo' => 'Ligação',
                    'texto' => 'Cliente solicitou orçamento para 10 unidades do produto A',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // Outro histórico cliente 1
                DB::table('historicos')->insert([
                    'user_id' => $vendedor1Id,
                    'historicable_id' => $cliente1Id,
                    'historicable_type' => 'App\\Models\\Cliente',
                    'data' => now()->subDays(2),
                    'tipo' => 'E-mail',
                    'texto' => 'Enviei proposta comercial com desconto de 5% para pagamento à vista',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // Histórico cliente 2
                DB::table('historicos')->insert([
                    'user_id' => $vendedor2Id,
                    'historicable_id' => $cliente2Id,
                    'historicable_type' => 'App\\Models\\Cliente',
                    'data' => now()->subDays(3),
                    'tipo' => 'Visita',
                    'texto' => 'Reunião para apresentação dos novos produtos da linha X',
                    'proxima_acao' => 'Enviar catálogo atualizado',
                    'data_proxima_acao' => now()->addDays(7),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            $this->info('2 clientes de teste com históricos criados com sucesso!');
        }
    }
}
