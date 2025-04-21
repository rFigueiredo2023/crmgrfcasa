<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAIService
{
    protected $apiKey;
    protected $model;
    protected $apiUrl = 'https://api.openai.com/v1/chat/completions';

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key');
        $this->model = config('services.openai.model', 'gpt-4o');
    }

    /**
     * Envia uma pergunta para a API da OpenAI e retorna a resposta
     *
     * @param string $prompt Pergunta ou prompt a ser enviado
     * @return string Resposta da API formatada
     */
    public function ask(string $prompt): string
    {
        if (empty($this->apiKey)) {
            Log::error('OpenAI API Key não configurada');
            return 'Erro: OpenAI API Key não configurada. Adicione OPENAI_API_KEY no arquivo .env';
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl, [
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => <<<SYS
Você é o assistente técnico pessoal do Roberto, criador do sistema GRF CRM feito em Laravel 11.

✅ Seu papel:
- Diagnosticar problemas técnicos (como erros 500, SQL, rota, validação, etc)
- Analisar como se estivesse vendo o código backend e frontend
- Gerar respostas curtas, diretas, com exemplos de código reais (controller, route, migration, Blade)

📌 Regras:
- Não seja genérico. Dê a causa provável e o que deve ser corrigido agora.
- Use markdown para mostrar códigos.
- No fim da resposta, gere um **prompt para ser enviado ao Cursor**, como:

**Prompt sugerido para o Cursor:**
"Tenho esse trecho do controller... Ele gera esse erro... Pode corrigir e reestruturar?"

🧠 Se a dúvida for mal explicada, peça o trecho do código (controller, rota, etc) para continuar.

Você é parte do time dele.
SYS
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.5,
                'max_tokens' => 2000,
            ]);

            $body = $response->json();

            if (!$response->successful()) {
                Log::error('Erro na API da OpenAI', ['resposta' => $body]);
                return 'Erro na API da OpenAI: ' . ($body['error']['message'] ?? 'Erro desconhecido');
            }

            return $body['choices'][0]['message']['content'] ?? 'Sem resposta do assistente';
        } catch (\Exception $e) {
            Log::error('Exceção ao chamar a API da OpenAI', ['erro' => $e->getMessage()]);
            return 'Erro ao consultar o assistente: ' . $e->getMessage();
        }
    }
}
