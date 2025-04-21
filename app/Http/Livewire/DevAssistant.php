<?php

namespace App\Http\Livewire;

use App\Services\OpenAIService;
use Livewire\Component;

class DevAssistant extends Component
{
    public $prompt = '';
    public $response = '';
    public $isLoading = false;
    public $showAssistant = false;

    public function mount()
    {
        // Verifica se existe um erro na sessão
        if (session()->has('dev_error')) {
            $error = session('dev_error');
            $this->generateErrorPrompt($error);
            $this->showAssistant = true;
        }
    }

    /**
     * Gera automaticamente um prompt com base nos dados do erro
     */
    private function generateErrorPrompt(array $error)
    {
        $this->prompt = "Estou desenvolvendo em Laravel. Recebi este erro:\n\n" .
            "Erro: {$error['mensagem']}\n" .
            "Rota: {$error['rota']}\n" .
            "Arquivo: " . basename($error['arquivo']) . "\n" .
            "Linha: {$error['linha']}\n";

        if (!empty($error['dados'])) {
            $this->prompt .= "Dados enviados: {$error['dados']}\n";
        }

        // Adiciona mais informações se disponíveis
        if (!empty($error['trace'])) {
            $shortenedTrace = substr($error['trace'], 0, 500);
            if (strlen($error['trace']) > 500) {
                $shortenedTrace .= '...';
            }
            $this->prompt .= "\nStack Trace (resumido):\n{$shortenedTrace}";
        }
    }

    /**
     * Envia o prompt para a API da OpenAI
     */
    public function ask()
    {
        $this->validate([
            'prompt' => 'required|min:10',
        ]);

        $this->isLoading = true;
        $this->response = '';

        try {
            $openAIService = new OpenAIService();
            $this->response = $openAIService->ask($this->prompt);
        } catch (\Exception $e) {
            $this->response = "Erro ao consultar o assistente: {$e->getMessage()}";
        } finally {
            $this->isLoading = false;
        }
    }

    /**
     * Limpa o formulário
     */
    public function resetForm()
    {
        $this->prompt = '';
        $this->response = '';
    }

    /**
     * Alterna a visibilidade do assistente
     */
    public function toggleAssistant()
    {
        $this->showAssistant = !$this->showAssistant;
    }

    public function render()
    {
        return view('livewire.dev-assistant');
    }
}
