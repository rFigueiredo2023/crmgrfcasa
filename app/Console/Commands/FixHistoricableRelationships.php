<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FixHistoricableRelationships extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'historicos:fix';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Corrige relacionamentos polimórficos na tabela historicos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando correção dos relacionamentos polimórficos na tabela historicos...');

        // 1. Verificar registros com historicable_type ou historicable_id NULL
        $registrosProblematicos = DB::table('historicos')
            ->whereNull('historicable_type')
            ->orWhereNull('historicable_id')
            ->get();

        $this->info("Encontrados {$registrosProblematicos->count()} registros com problemas de NULL.");

        // 2. Verificar formato incorreto para historicable_type
        $registrosFormatoIncorreto = DB::table('historicos')
            ->whereNotNull('historicable_type')
            ->whereRaw("historicable_type NOT LIKE 'App\\\\%'")
            ->get();

        $this->info("Encontrados {$registrosFormatoIncorreto->count()} registros com formato incorreto no historicable_type.");

        // 3. Verificar registros com referências inválidas
        $leadsValidos = DB::table('leads')->pluck('id')->toArray();
        $clientesValidos = DB::table('clientes')->pluck('id')->toArray();

        $leadRegistrosInvalidos = DB::table('historicos')
            ->where('historicable_type', 'App\\Models\\Lead')
            ->whereNotIn('historicable_id', $leadsValidos)
            ->get();

        $this->info("Encontrados {$leadRegistrosInvalidos->count()} registros de histórico com referências inválidas para leads.");

        $clienteRegistrosInvalidos = DB::table('historicos')
            ->where('historicable_type', 'App\\Models\\Cliente')
            ->whereNotIn('historicable_id', $clientesValidos)
            ->get();

        $this->info("Encontrados {$clienteRegistrosInvalidos->count()} registros de histórico com referências inválidas para clientes.");

        // 4. Verificar registros com o antigo formato (historicocable em vez de historicable)
        $tabelaExistente = DB::select("SHOW COLUMNS FROM `historicos` LIKE 'historicocable_id'");
        if (!empty($tabelaExistente)) {
            $this->info("Encontrado campo antigo 'historicocable_id'. Corrigindo...");
            $this->corrigirCamposAntigos();
        }

        // 5. Corrigir registros com problemas
        $this->corrigirRegistros($registrosProblematicos, $registrosFormatoIncorreto);

        // 6. Remover registros inválidos (opcional, com confirmação)
        if ($leadRegistrosInvalidos->count() > 0 || $clienteRegistrosInvalidos->count() > 0) {
            if ($this->confirm('Deseja remover registros com referências inválidas?')) {
                $this->removerRegistrosInvalidos($leadRegistrosInvalidos, $clienteRegistrosInvalidos);
            }
        }

        $this->info('Correção dos relacionamentos polimórficos concluída!');

        // Verificação final
        $historicosTotais = DB::table('historicos')->count();
        $historicosSemProblemas = DB::table('historicos')
            ->whereNotNull('historicable_type')
            ->whereNotNull('historicable_id')
            ->count();

        $this->info("Verificação final: {$historicosSemProblemas} de {$historicosTotais} registros estão corretos.");

        return 0;
    }

    private function corrigirCamposAntigos()
    {
        // Verifica se os campos existem
        $colunas = DB::select("SHOW COLUMNS FROM `historicos`");
        $colunasExistentes = collect($colunas)->pluck('Field')->toArray();

        // Se existirem os campos antigos, migrar dados para os novos
        if (in_array('historicocable_id', $colunasExistentes) && in_array('historicocable_type', $colunasExistentes)) {
            $this->info("Migrando dados de campos antigos para novos...");

            DB::statement("
                UPDATE `historicos`
                SET
                    `historicable_id` = `historicocable_id`,
                    `historicable_type` = `historicocable_type`
                WHERE
                    `historicocable_id` IS NOT NULL
                    AND `historicocable_type` IS NOT NULL
                    AND (`historicable_id` IS NULL OR `historicable_type` IS NULL)
            ");

            $this->info("Migração de dados concluída!");
        }
    }

    private function corrigirRegistros($registrosProblematicos, $registrosFormatoIncorreto)
    {
        $contador = 0;

        // Corrigir registros NULL baseados em informações de outros campos
        foreach ($registrosProblematicos as $registro) {
            // Lógica para tentar deduzir o tipo e ID corretos
            // Exemplo: se existe cliente_id (campo antigo), usar para atualizar historicable_id
            // e definir historicable_type como App\Models\Cliente

            // Este é um exemplo simplificado. Você pode precisar ajustar com base em sua estrutura específica
            if (property_exists($registro, 'cliente_id') && $registro->cliente_id) {
                DB::table('historicos')
                    ->where('id', $registro->id)
                    ->update([
                        'historicable_type' => 'App\\Models\\Cliente',
                        'historicable_id' => $registro->cliente_id
                    ]);
                $contador++;
            }
            // Caso similar para leads
            else if (property_exists($registro, 'lead_id') && $registro->lead_id) {
                DB::table('historicos')
                    ->where('id', $registro->id)
                    ->update([
                        'historicable_type' => 'App\\Models\\Lead',
                        'historicable_id' => $registro->lead_id
                    ]);
                $contador++;
            }
        }

        // Corrigir formato de historicable_type
        foreach ($registrosFormatoIncorreto as $registro) {
            // Identificar qual deveria ser o formato correto
            $tipoCorrigido = $this->corrigirFormatoTipo($registro->historicable_type);

            if ($tipoCorrigido) {
                DB::table('historicos')
                    ->where('id', $registro->id)
                    ->update([
                        'historicable_type' => $tipoCorrigido
                    ]);
                $contador++;
            }
        }

        $this->info("Total de {$contador} registros corrigidos.");
    }

    private function corrigirFormatoTipo($tipo)
    {
        // Mapeia formatos incorretos para corretos
        $mapeamento = [
            'Lead' => 'App\\Models\\Lead',
            'Cliente' => 'App\\Models\\Cliente',
            'App\Lead' => 'App\\Models\\Lead',
            'App\Cliente' => 'App\\Models\\Cliente',
            'App/Models/Lead' => 'App\\Models\\Lead',
            'App/Models/Cliente' => 'App\\Models\\Cliente',
        ];

        return $mapeamento[$tipo] ?? null;
    }

    private function removerRegistrosInvalidos($leadRegistrosInvalidos, $clienteRegistrosInvalidos)
    {
        $idsLead = $leadRegistrosInvalidos->pluck('id')->toArray();
        $idsCliente = $clienteRegistrosInvalidos->pluck('id')->toArray();

        $todosIds = array_merge($idsLead, $idsCliente);

        if (!empty($todosIds)) {
            $removidos = DB::table('historicos')
                ->whereIn('id', $todosIds)
                ->delete();

            $this->info("Removidos {$removidos} registros de histórico com referências inválidas.");
        }
    }
}
