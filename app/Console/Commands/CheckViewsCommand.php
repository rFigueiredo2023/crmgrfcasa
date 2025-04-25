<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\Finder;

class CheckViewsCommand extends Command
{
    /**
     * O nome e a assinatura do comando no console.
     *
     * @var string
     */
    protected $signature = 'views:check';

    /**
     * A descrição do comando no console.
     *
     * @var string
     */
    protected $description = 'Verifica arquivos .blade.php e identifica quais estão fora da pasta resources/views/';

    /**
     * Pastas a serem ignoradas durante a verificação.
     *
     * @var array
     */
    protected $ignoredDirectories = [
        'vendor',
        'storage',
        'bootstrap',
        'node_modules',
    ];

    /**
     * Executa o comando no console.
     */
    public function handle()
    {
        $this->info('Escaneando arquivos .blade.php em todo o projeto...');

        // Busca todos os arquivos .blade.php no projeto
        $finder = new Finder();
        $finder->files()
            ->name('*.blade.php')
            ->in(base_path())
            ->exclude($this->ignoredDirectories);

        $correctViews = [];
        $incorrectViews = [];

        // Caminho absoluto para a pasta resources/views
        $viewsPath = resource_path('views');

        foreach ($finder as $file) {
            $filePath = $file->getRealPath();

            // Verifica se o arquivo está dentro da pasta resources/views
            if (strpos($filePath, $viewsPath) === 0) {
                // Calcula o caminho relativo a partir de resources/views
                $relativePath = substr($filePath, strlen($viewsPath) + 1);
                $correctViews[] = $relativePath;
            } else {
                $incorrectViews[] = $filePath;
            }
        }

        // Ordenar as listas para melhor visualização
        sort($correctViews);
        sort($incorrectViews);

        // Exibir resultados para as views corretas
        $this->newLine();
        $this->components->info('📂 Views corretas: ' . count($correctViews));

        if (count($correctViews) > 0) {
            $this->components->twoColumnDetail('Arquivo', 'Status');

            foreach ($correctViews as $view) {
                $this->components->twoColumnDetail(
                    $view,
                    "<fg=green>✅ Correto</>"
                );
            }
        } else {
            $this->components->warn('Nenhuma view encontrada dentro de resources/views/');
        }

        // Exibir resultados para as views incorretas
        $this->newLine();
        $this->components->info('⚠️ Views fora do padrão: ' . count($incorrectViews));

        if (count($incorrectViews) > 0) {
            $this->components->twoColumnDetail('Arquivo', 'Status');

            foreach ($incorrectViews as $view) {
                $this->components->twoColumnDetail(
                    $view,
                    "<fg=yellow>⚠️ Fora do padrão</>"
                );
            }

            $this->newLine();
            $this->line('Sugestão: Considere mover manualmente estes arquivos para a pasta resources/views/');
            $this->line('Isso garantirá que todas as views sejam corretamente gerenciadas pelo Laravel.');
        } else {
            $this->components->info('Parabéns! Não foram encontradas views fora do padrão.');
        }

        // Resumo final
        $this->newLine();
        $totalViews = count($correctViews) + count($incorrectViews);
        $this->info("Escaneamento concluído! {$totalViews} arquivos .blade.php encontrados no total.");

        return Command::SUCCESS;
    }
}
