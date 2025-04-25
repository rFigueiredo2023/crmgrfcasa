<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use ReflectionClass;
use ReflectionMethod;

class CheckCadastroFluxoCommand extends Command
{
    /**
     * O nome e a assinatura do comando no console.
     *
     * @var string
     */
    protected $signature = 'check:cadastro-fluxo';

    /**
     * A descrição do comando no console.
     *
     * @var string
     */
    protected $description = 'Audita o fluxo de cadastro de clientes entre API, formulário e banco de dados';

    /**
     * Armazena os campos encontrados
     */
    protected $apiFields = [];
    protected $formFields = [];
    protected $dbFields = [];
    protected $requiredDbFields = [];

    /**
     * Contadores para o resumo
     */
    protected $totalFields = 0;
    protected $consistentFields = 0;
    protected $apiMissingInForm = 0;
    protected $requiredMissingInForm = 0;
    protected $formOnlyFields = 0;

    /**
     * Caminho do arquivo de log
     */
    protected $logFile;

    /**
     * Executa o comando no console.
     */
    public function handle()
    {
        $this->info('Iniciando auditoria do fluxo de cadastro de clientes...');
        $this->logFile = storage_path('logs/check-cadastro-fluxo-clientes.log');

        // Cria o diretório de logs se não existir
        if (!File::exists(dirname($this->logFile))) {
            File::makeDirectory(dirname($this->logFile), 0755, true);
        }

        // Inicializa o arquivo de log
        File::put($this->logFile, "# Auditoria do Fluxo de Cadastro de Clientes\n\n");
        File::append($this->logFile, "Data: " . date('Y-m-d H:i:s') . "\n\n");

        try {
            // Obtém campos da API
            $this->extractApiFields();

            // Obtém campos do formulário
            $this->extractFormFields();

            // Obtém campos da migration
            $this->extractDatabaseFields();

            // Realiza a validação cruzada
            $this->validateFlows();

            // Gera resumo
            $this->generateSummary();

        } catch (\Exception $e) {
            $this->error('Erro durante a execução: ' . $e->getMessage());
            File::append($this->logFile, "ERRO: " . $e->getMessage() . "\n");
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * Extrai campos da API no ClienteService
     */
    protected function extractApiFields()
    {
        $this->info('Analisando campos da API...');

        try {
            // Procura pelo ClienteService
            $clienteServiceFile = $this->findFile('ClienteService.php');

            if (!$clienteServiceFile) {
                throw new \Exception('Arquivo ClienteService.php não encontrado');
            }

            $content = File::get($clienteServiceFile);

            // Procura pelo método consultarCnpj
            if (!preg_match('/function\s+consultarCnpj\s*\(\s*string\s+\$cnpj\s*\)/i', $content)) {
                throw new \Exception('Método consultarCnpj não encontrado em ClienteService');
            }

            // Extrai os campos da resposta da API
            // Estratégia: procurar por atribuições do tipo $data['campo'] ou $response['campo']
            preg_match_all('/[\$](?:data|response)\s*\[\s*[\'"]([^\'"]+)[\'"]\s*\]/', $content, $matches);

            if (!empty($matches[1])) {
                $this->apiFields = array_unique($matches[1]);
                $this->line('Campos encontrados na API: ' . count($this->apiFields));
                File::append($this->logFile, "## Campos da API\n" . implode(", ", $this->apiFields) . "\n\n");
            } else {
                $this->warn('Nenhum campo da API identificado');
                File::append($this->logFile, "## Campos da API\nNenhum campo identificado\n\n");
            }

        } catch (\Exception $e) {
            $this->warn('Erro ao extrair campos da API: ' . $e->getMessage());
            File::append($this->logFile, "AVISO: Erro ao extrair campos da API: " . $e->getMessage() . "\n");
        }
    }

    /**
     * Extrai campos do formulário
     */
    protected function extractFormFields()
    {
        $this->info('Analisando campos do formulário...');

        try {
            // Procura pelo arquivo do formulário
            $formFile = resource_path('views/components/form-cliente.blade.php');

            if (!File::exists($formFile)) {
                throw new \Exception('Arquivo form-cliente.blade.php não encontrado');
            }

            $content = File::get($formFile);

            // Extrai os campos do formulário (inputs, selects, textareas)
            preg_match_all('/name\s*=\s*[\'"]([^\'"]+)[\'"]/i', $content, $matches);

            if (!empty($matches[1])) {
                // Remove campos com array notation (ex: name="foo[]")
                $fields = array_map(function($field) {
                    return preg_replace('/\[\]$/', '', $field);
                }, $matches[1]);

                $this->formFields = array_unique($fields);
                $this->line('Campos encontrados no formulário: ' . count($this->formFields));
                File::append($this->logFile, "## Campos do Formulário\n" . implode(", ", $this->formFields) . "\n\n");
            } else {
                $this->warn('Nenhum campo do formulário identificado');
                File::append($this->logFile, "## Campos do Formulário\nNenhum campo identificado\n\n");
            }

        } catch (\Exception $e) {
            $this->warn('Erro ao extrair campos do formulário: ' . $e->getMessage());
            File::append($this->logFile, "AVISO: Erro ao extrair campos do formulário: " . $e->getMessage() . "\n");
        }
    }

    /**
     * Extrai campos da migration
     */
    protected function extractDatabaseFields()
    {
        $this->info('Analisando campos da tabela clientes...');

        try {
            // Procura pela migration create_clients_table
            $migrationFiles = glob(database_path('migrations/*_create_clients_table.php'));

            if (empty($migrationFiles)) {
                throw new \Exception('Migration create_clients_table.php não encontrada');
            }

            $content = File::get($migrationFiles[0]);

            // Verifica se a tabela realmente se chama clientes
            if (!preg_match('/Schema\s*::\s*create\s*\(\s*[\'"]clientes[\'"]/i', $content)) {
                // Tenta outras possibilidades
                if (preg_match('/Schema\s*::\s*create\s*\(\s*[\'"]([^\'"]+)[\'"]/i', $content, $tableMatches)) {
                    $this->warn('Tabela encontrada: ' . $tableMatches[1] . ' (esperava: clientes)');
                } else {
                    throw new \Exception('Não foi possível identificar o nome da tabela');
                }
            }

            // Extrai os campos da tabela
            preg_match_all('/\$table\s*->\s*(?:string|integer|boolean|date|datetime|text|float|decimal)\s*\(\s*[\'"]([^\'"]+)[\'"]/i', $content, $matches);

            if (!empty($matches[1])) {
                $this->dbFields = array_unique($matches[1]);
                $this->line('Campos encontrados na tabela: ' . count($this->dbFields));
                File::append($this->logFile, "## Campos da Tabela\n" . implode(", ", $this->dbFields) . "\n\n");

                // Identifica campos obrigatórios (não nullable)
                preg_match_all('/\$table\s*->\s*(?:string|integer|boolean|date|datetime|text|float|decimal)\s*\(\s*[\'"]([^\'"]+)[\'"]\s*\)[^;]*?(?!->nullable)/i', $content, $requiredMatches);

                if (!empty($requiredMatches[1])) {
                    $this->requiredDbFields = array_unique($requiredMatches[1]);
                    $this->line('Campos obrigatórios na tabela: ' . count($this->requiredDbFields));
                    File::append($this->logFile, "## Campos Obrigatórios na Tabela\n" . implode(", ", $this->requiredDbFields) . "\n\n");
                }
            } else {
                $this->warn('Nenhum campo da tabela identificado');
                File::append($this->logFile, "## Campos da Tabela\nNenhum campo identificado\n\n");
            }

        } catch (\Exception $e) {
            $this->warn('Erro ao extrair campos da tabela: ' . $e->getMessage());
            File::append($this->logFile, "AVISO: Erro ao extrair campos da tabela: " . $e->getMessage() . "\n");
        }
    }

    /**
     * Valida os fluxos entre API, formulário e banco
     */
    protected function validateFlows()
    {
        $this->info('Validando fluxos entre API, formulário e banco...');

        // Cria uma lista de todos os campos únicos
        $allFields = array_unique(array_merge($this->apiFields, $this->formFields, $this->dbFields));
        $this->totalFields = count($allFields);

        // Cabeçalho da tabela
        $headers = ['Campo', 'API', 'Formulário', 'Banco', 'Status'];
        $rows = [];

        File::append($this->logFile, "## Validação de Fluxos\n\n");
        File::append($this->logFile, "| Campo | API | Formulário | Banco | Status |\n");
        File::append($this->logFile, "|-------|-----|------------|-------|--------|\n");

        foreach ($allFields as $field) {
            $inApi = in_array($field, $this->apiFields);
            $inForm = in_array($field, $this->formFields);
            $inDb = in_array($field, $this->dbFields);
            $isRequired = in_array($field, $this->requiredDbFields);

            // Determina o status
            $status = $this->determineStatus($inApi, $inForm, $inDb, $isRequired);

            // Símbolos para o status
            $apiSymbol = $inApi ? '✅' : '❌';
            $formSymbol = $inForm ? '✅' : '❌';
            $dbSymbol = $inDb ? '✅' : '❌';

            // Adiciona à tabela
            $rows[] = [
                $field,
                $apiSymbol,
                $formSymbol,
                $dbSymbol,
                $status['symbol']
            ];

            // Adiciona ao log
            File::append(
                $this->logFile,
                "| {$field} | {$apiSymbol} | {$formSymbol} | {$dbSymbol} | {$status['symbol']} |\n"
            );

            // Atualiza contadores
            if ($status['symbol'] === '✅') {
                $this->consistentFields++;
            } elseif ($inApi && !$inForm) {
                $this->apiMissingInForm++;
            } elseif ($isRequired && !$inForm) {
                $this->requiredMissingInForm++;
            } elseif ($inForm && !$inApi && !$inDb) {
                $this->formOnlyFields++;
            }
        }

        // Exibe a tabela
        $this->table($headers, $rows);
    }

    /**
     * Gera um resumo da auditoria
     */
    protected function generateSummary()
    {
        $this->newLine();
        $this->info('Resumo da Auditoria:');

        // Calcula consistência
        $consistencyPercentage = ($this->totalFields > 0)
            ? round(($this->consistentFields / $this->totalFields) * 100)
            : 0;

        $this->line("Total de campos mapeados: <fg=white>{$this->totalFields}</>");
        $this->line("Campos consistentes: <fg=green>{$this->consistentFields}</>");
        $this->line("Campos da API ausentes no formulário: <fg=yellow>{$this->apiMissingInForm}</>");
        $this->line("Campos obrigatórios no banco ausentes no formulário: <fg=red>{$this->requiredMissingInForm}</>");
        $this->line("Campos apenas no formulário: <fg=yellow>{$this->formOnlyFields}</>");
        $this->line("Consistência geral: <fg=white>{$consistencyPercentage}%</>");

        // Sugestões
        $this->newLine();
        $this->info('Sugestões:');

        if ($this->apiMissingInForm > 0) {
            $this->line("<fg=yellow>- Verificar campos da API ausentes no formulário</>");
        }

        if ($this->requiredMissingInForm > 0) {
            $this->line("<fg=red>- Adicionar campos obrigatórios faltantes no formulário</>");
        }

        if ($this->formOnlyFields > 0) {
            $this->line("<fg=yellow>- Revisar campos presentes apenas no formulário</>");
        }

        if ($consistencyPercentage < 70) {
            $this->line("<fg=red>- Revisar todo o fluxo de cadastro, consistência muito baixa</>");
        }

        // Adiciona resumo ao log
        File::append($this->logFile, "\n## Resumo\n\n");
        File::append($this->logFile, "- Total de campos mapeados: {$this->totalFields}\n");
        File::append($this->logFile, "- Campos consistentes: {$this->consistentFields}\n");
        File::append($this->logFile, "- Campos da API ausentes no formulário: {$this->apiMissingInForm}\n");
        File::append($this->logFile, "- Campos obrigatórios no banco ausentes no formulário: {$this->requiredMissingInForm}\n");
        File::append($this->logFile, "- Campos apenas no formulário: {$this->formOnlyFields}\n");
        File::append($this->logFile, "- Consistência geral: {$consistencyPercentage}%\n\n");

        File::append($this->logFile, "## Sugestões\n\n");

        if ($this->apiMissingInForm > 0) {
            File::append($this->logFile, "- Verificar campos da API ausentes no formulário\n");
        }

        if ($this->requiredMissingInForm > 0) {
            File::append($this->logFile, "- Adicionar campos obrigatórios faltantes no formulário\n");
        }

        if ($this->formOnlyFields > 0) {
            File::append($this->logFile, "- Revisar campos presentes apenas no formulário\n");
        }

        $this->newLine();
        $this->info("Relatório completo salvo em: {$this->logFile}");
    }

    /**
     * Determina o status de um campo baseado em sua presença nos diferentes sistemas
     */
    protected function determineStatus($inApi, $inForm, $inDb, $isRequired)
    {
        if ($inApi && $inForm && $inDb) {
            return [
                'symbol' => '✅',
                'color' => 'green',
                'text' => 'presente em API, form e banco'
            ];
        } elseif ($inApi && !$inForm) {
            return [
                'symbol' => '⚠️',
                'color' => 'yellow',
                'text' => 'presente na API, mas não usada'
            ];
        } elseif ($isRequired && !$inForm) {
            return [
                'symbol' => '⚠️',
                'color' => 'red',
                'text' => 'obrigatório no banco, mas ausente no formulário'
            ];
        } elseif ($inForm && !$inDb) {
            return [
                'symbol' => '🟨',
                'color' => 'yellow',
                'text' => 'está no formulário, mas não existe no banco'
            ];
        } elseif (!$inApi && $inForm && $inDb) {
            return [
                'symbol' => '✅',
                'color' => 'green',
                'text' => 'campo adicional corretamente implementado'
            ];
        } else {
            return [
                'symbol' => '❓',
                'color' => 'white',
                'text' => 'situação não mapeada'
            ];
        }
    }

    /**
     * Encontra um arquivo no projeto
     */
    protected function findFile($filename)
    {
        $paths = [
            app_path(),
            app_path('Services'),
            app_path('Http/Services'),
        ];

        foreach ($paths as $path) {
            $files = glob("{$path}/*{$filename}*");
            if (!empty($files)) {
                return $files[0];
            }

            // Busca recursiva
            $files = $this->recursiveFind($path, $filename);
            if (!empty($files)) {
                return $files[0];
            }
        }

        return null;
    }

    /**
     * Busca recursiva por um arquivo
     */
    protected function recursiveFind($path, $filename)
    {
        $directory = new \RecursiveDirectoryIterator($path);
        $iterator = new \RecursiveIteratorIterator($directory);
        $files = [];

        foreach ($iterator as $info) {
            if ($info->isFile() && strpos($info->getFilename(), $filename) !== false) {
                $files[] = $info->getPathname();
            }
        }

        return $files;
    }
}
