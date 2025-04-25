<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\Finder;

class AddViewCommentsCommand extends Command
{
    /**
     * O nome e a assinatura do comando no console.
     *
     * @var string
     */
    protected $signature = 'views:add-comments';

    /**
     * A descrição do comando no console.
     *
     * @var string
     */
    protected $description = 'Adiciona comentários descritivos a todos os arquivos .blade.php';

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

        $viewsPath = resource_path('views');

        $updatedFiles = [];
        $genericDescriptionFiles = [];
        $skippedFiles = [];
        $totalFiles = 0;

        foreach ($finder as $file) {
            $totalFiles++;
            $filePath = $file->getRealPath();
            $fileContent = File::get($filePath);

            // Verifica se já existe um comentário Blade no início do arquivo
            if (preg_match('/^\s*{{--.*--}}/m', $fileContent)) {
                $skippedFiles[] = $filePath;
                continue;
            }

            // Gera descrição baseada no caminho do arquivo
            $description = $this->generateDescription($filePath, $viewsPath);

            // Verifica se a descrição é genérica
            $isGeneric = str_contains($description, 'sem descrição detalhada');

            // Adiciona o comentário ao início do arquivo
            $updatedContent = "{{-- {$description} --}}\n" . $fileContent;
            File::put($filePath, $updatedContent);

            // Registra o arquivo na lista apropriada
            if ($isGeneric) {
                $genericDescriptionFiles[] = $filePath;
            } else {
                $updatedFiles[] = $filePath;
            }
        }

        // Exibe resumo dos arquivos atualizados
        $this->newLine();
        $this->components->info('📝 Arquivos atualizados com comentários descritivos: ' . count($updatedFiles));

        if (count($updatedFiles) > 0) {
            $this->components->twoColumnDetail('Arquivo', 'Status');
            foreach ($updatedFiles as $file) {
                $relativePath = $this->getRelativePath($file);
                $this->components->twoColumnDetail(
                    $relativePath,
                    "<fg=green>✅ Comentário adicionado</>"
                );
            }
        }

        // Exibe arquivos com descrição genérica
        $this->newLine();
        $this->components->info('⚠️ Arquivos com descrição genérica (requer revisão manual): ' . count($genericDescriptionFiles));

        if (count($genericDescriptionFiles) > 0) {
            $this->components->twoColumnDetail('Arquivo', 'Status');
            foreach ($genericDescriptionFiles as $file) {
                $relativePath = $this->getRelativePath($file);
                $this->components->twoColumnDetail(
                    $relativePath,
                    "<fg=yellow>⚠️ Descrição genérica</>"
                );
            }
        }

        // Exibe arquivos ignorados
        $this->newLine();
        $this->components->info('ℹ️ Arquivos que já possuíam comentários (ignorados): ' . count($skippedFiles));

        // Resumo final
        $this->newLine();
        $this->info("Processo concluído! {$totalFiles} arquivos .blade.php encontrados no total.");
        $this->info(count($updatedFiles) . " arquivos receberam comentários descritivos.");
        $this->info(count($genericDescriptionFiles) . " arquivos receberam comentários genéricos.");
        $this->info(count($skippedFiles) . " arquivos já possuíam comentários (não modificados).");

        return Command::SUCCESS;
    }

    /**
     * Gera uma descrição baseada no caminho do arquivo
     */
    protected function generateDescription($filePath, $viewsPath)
    {
        // Se estiver na pasta views, pega o caminho relativo
        $relativePath = $filePath;
        if (strpos($filePath, $viewsPath) === 0) {
            $relativePath = substr($filePath, strlen($viewsPath) + 1);
        }

        // Remove a extensão .blade.php
        $nameWithoutExtension = str_replace('.blade.php', '', $relativePath);

        // Divide o caminho em partes
        $parts = explode('/', $nameWithoutExtension);
        $fileName = end($parts);

        // Humaniza o nome do arquivo
        $humanizedName = str_replace(['-', '_', '.'], ' ', $fileName);
        $humanizedName = ucfirst($humanizedName);

        // Identifica o contexto com base no diretório
        $contextName = '';
        if (count($parts) > 1) {
            $contextParts = array_slice($parts, 0, -1);
            $contextName = implode('/', $contextParts);
            $contextName = str_replace(['-', '_'], ' ', $contextName);
        }

        // Gera descrição com base no contexto e nome do arquivo
        if ($nameWithoutExtension == 'index' || $fileName == 'index') {
            if ($contextName) {
                return "Página principal de " . $contextName;
            } else {
                return "Página principal";
            }
        } elseif ($nameWithoutExtension == 'show' || $fileName == 'show') {
            if ($contextName) {
                return "Página de visualização de " . $contextName;
            } else {
                return "Página de visualização";
            }
        } elseif ($nameWithoutExtension == 'edit' || $fileName == 'edit') {
            if ($contextName) {
                return "Página de edição de " . $contextName;
            } else {
                return "Página de edição";
            }
        } elseif ($nameWithoutExtension == 'create' || $fileName == 'create') {
            if ($contextName) {
                return "Página de criação de " . $contextName;
            } else {
                return "Página de criação";
            }
        } elseif (strpos($contextName, 'layout') !== false || strpos($fileName, 'layout') !== false) {
            return "Layout " . $humanizedName;
        } elseif (strpos($contextName, 'component') !== false) {
            return "Componente " . $humanizedName;
        } elseif (strpos($contextName, 'partial') !== false || strpos($contextName, 'includes') !== false) {
            return "Partial incluído em outras views: " . $humanizedName;
        } elseif (strpos($nameWithoutExtension, 'form') !== false) {
            return "Formulário de " . str_replace('form', '', $humanizedName);
        } elseif (strpos($nameWithoutExtension, 'table') !== false || strpos($nameWithoutExtension, 'tabela') !== false) {
            return "Tabela de listagem " . str_replace(['table', 'tabela'], '', $humanizedName);
        } elseif (strpos($nameWithoutExtension, 'modal') !== false) {
            return "Modal de " . str_replace('modal', '', $humanizedName);
        } elseif (strpos($contextName, 'emails') !== false || strpos($contextName, 'email') !== false) {
            return "Template de email: " . $humanizedName;
        } elseif (strpos($nameWithoutExtension, 'card') !== false) {
            return "Card de " . str_replace('card', '', $humanizedName);
        } elseif (strpos($contextName, 'auth') !== false) {
            return "Página de autenticação: " . $humanizedName;
        } else {
            // Se não encontrar um padrão claro, tenta uma descrição com base no contexto
            if ($contextName) {
                return "View de $humanizedName relacionada a $contextName";
            } else {
                return "View sem descrição detalhada. Revisar manualmente.";
            }
        }
    }

    /**
     * Retorna o caminho relativo do arquivo
     */
    protected function getRelativePath($filePath)
    {
        return str_replace(base_path() . '/', '', $filePath);
    }
}
