<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use ReflectionClass;
use ReflectionMethod;

class MapFunctionalitiesCommand extends Command
{
    /**
     * O nome e a assinatura do comando no console.
     *
     * @var string
     */
    protected $signature = 'map:functionalities';

    /**
     * A descrição do comando no console.
     *
     * @var string
     */
    protected $description = 'Mapeia todas as funcionalidades relacionando rotas, controllers e views';

    /**
     * Contador para estatísticas
     */
    protected $totalRoutes = 0;
    protected $routesWithViews = 0;
    protected $routesWithoutViews = 0;

    /**
     * Arquivo de saída para o mapeamento
     */
    protected $outputFile;

    /**
     * Executa o comando no console.
     */
    public function handle()
    {
        $this->info('Mapeando funcionalidades do sistema...');

        // Cria diretório para armazenar o mapeamento se não existir
        $mappingDir = storage_path('app/mapping');
        if (!File::exists($mappingDir)) {
            File::makeDirectory($mappingDir, 0755, true);
        }

        // Define arquivo de saída
        $this->outputFile = $mappingDir . '/mapping-rotas-controllers-views.txt';

        // Inicializa o arquivo com cabeçalho
        File::put($this->outputFile, "# Mapeamento de Rotas, Controllers e Views\n\n");
        File::append($this->outputFile, "| Método | URI | Nome da Rota | Controller@Método | View |\n");
        File::append($this->outputFile, "|--------|-----|-------------|-------------------|------|\n");

        // Cabeçalho da tabela no terminal
        $this->newLine();
        $headers = ['Método', 'URI', 'Nome da Rota', 'Controller@Método', 'View'];

        $rows = [];

        // Obtém todas as rotas do sistema
        $routes = Route::getRoutes();

        foreach ($routes as $route) {
            $this->totalRoutes++;

            // Coleta informações da rota
            $methods = implode('|', $route->methods());
            $uri = $route->uri();
            $name = $route->getName() ?: 'Sem nome';

            // Obtém controller e método
            $action = $route->getAction();
            $controllerMethod = $this->getControllerMethod($action);

            // Tenta identificar a view renderizada
            $view = 'N/A';

            if ($controllerMethod !== 'Closure' && $controllerMethod !== 'N/A') {
                $view = $this->identifyViewFromController($controllerMethod);

                if ($view !== 'N/A') {
                    $this->routesWithViews++;
                } else {
                    $this->routesWithoutViews++;
                }
            } else {
                $this->routesWithoutViews++;
            }

            // Adiciona à lista de linhas
            $rows[] = [$methods, $uri, $name, $controllerMethod, $view];

            // Adiciona ao arquivo
            File::append(
                $this->outputFile,
                "| {$methods} | {$uri} | {$name} | {$controllerMethod} | {$view} |\n"
            );
        }

        // Exibe a tabela no terminal
        $this->table($headers, $rows);

        // Adiciona resumo ao arquivo
        File::append($this->outputFile, "\n## Resumo\n\n");
        File::append($this->outputFile, "- Total de rotas: {$this->totalRoutes}\n");
        File::append($this->outputFile, "- Rotas que renderizam views: {$this->routesWithViews}\n");
        File::append($this->outputFile, "- Rotas apenas para processamento de dados: {$this->routesWithoutViews}\n");

        // Exibe resumo no terminal
        $this->newLine();
        $this->info('Resumo:');
        $this->line("- Total de rotas: {$this->totalRoutes}");
        $this->line("- Rotas que renderizam views: {$this->routesWithViews}");
        $this->line("- Rotas apenas para processamento de dados: {$this->routesWithoutViews}");

        $this->newLine();
        $this->info("Mapeamento concluído! Arquivo gerado em: {$this->outputFile}");

        return Command::SUCCESS;
    }

    /**
     * Extrai o controller e método da ação da rota
     */
    protected function getControllerMethod($action)
    {
        if (!isset($action['controller'])) {
            if (isset($action['uses']) && is_string($action['uses'])) {
                return $action['uses'];
            }
            return isset($action['uses']) && $action['uses'] instanceof \Closure
                ? 'Closure'
                : 'N/A';
        }

        return $action['controller'];
    }

    /**
     * Identifica a view renderizada a partir do controller e método
     */
    protected function identifyViewFromController($controllerMethod)
    {
        try {
            // Separa o controller e o método
            if (strpos($controllerMethod, '@') === false) {
                return 'N/A';
            }

            list($controllerClass, $method) = explode('@', $controllerMethod);

            // Verifica se a classe existe
            if (!class_exists($controllerClass)) {
                return 'N/A';
            }

            // Usa Reflection para analisar o método
            $reflectionClass = new ReflectionClass($controllerClass);

            if (!$reflectionClass->hasMethod($method)) {
                return 'N/A';
            }

            $reflectionMethod = $reflectionClass->getMethod($method);

            // Pega o conteúdo do arquivo
            $file = $reflectionMethod->getFileName();
            $startLine = $reflectionMethod->getStartLine();
            $endLine = $reflectionMethod->getEndLine();

            $content = File::get($file);
            $lines = array_slice(file($file), $startLine - 1, $endLine - $startLine + 1);
            $methodContent = implode('', $lines);

            // Procura por retorno de view
            $viewPattern = '/return\s+view\s*\(\s*[\'"]([^\'"]+)[\'"]/';
            if (preg_match($viewPattern, $methodContent, $matches)) {
                return $matches[1];
            }

            // Procura por outros padrões de view
            $inertiaPattern = '/return\s+Inertia\s*::\s*render\s*\(\s*[\'"]([^\'"]+)[\'"]/';
            if (preg_match($inertiaPattern, $methodContent, $matches)) {
                return 'Inertia: ' . $matches[1];
            }

            $compactPattern = '/view\s*\(\s*[\'"]([^\'"]+)[\'"]/';
            if (preg_match($compactPattern, $methodContent, $matches)) {
                return $matches[1];
            }

            if (strpos($methodContent, '->json(') !== false ||
                strpos($methodContent, 'response()->json') !== false) {
                return 'API Response';
            }

            if (strpos($methodContent, '->download(') !== false) {
                return 'Download';
            }

            if (strpos($methodContent, '->redirect(') !== false ||
                strpos($methodContent, 'redirect(') !== false) {
                return 'Redirecionamento';
            }

            return 'View dinâmica ou não identificada';

        } catch (\Exception $e) {
            return 'Erro na análise: ' . $e->getMessage();
        }
    }
}
